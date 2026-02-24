<?php
/**
 * CONFIGURACIÓN DE BASE DE DATOS PARA PORTAL DE CLIENTES
 * Conecta con la base de datos del CRM para autenticación de clientes
 */

// Detectar si estamos en desarrollo (local) o producción (Hostinger)
$isDevelopment = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', 'localhost:8080']);

if ($isDevelopment) {
    // Configuración para desarrollo local (Docker)
    $possible_hosts = [
        getenv('DB_HOST'),
        'localhost',
        'crm-mysql-dev',
        'crm-mysql',
        'mysql'
    ];
    
    $db_host = 'localhost';
    foreach ($possible_hosts as $host) {
        if (!empty($host)) {
            $db_host = $host;
            break;
        }
    }
    
    define('DB_HOST', $db_host);
    define('DB_NAME', getenv('DB_NAME') ?: 'crm_delgado');
    define('DB_USER', getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') ?: '00617');
} else {
    // Configuración para Hostinger (producción)
    define('DB_HOST', 'srv448.hstgr.io');
    define('DB_NAME', 'u476108630_crm_delgado');
    define('DB_USER', 'u476108630_delgadoUser');
    define('DB_PASS', 'Rmdpropiedades23');
}
define('DB_CHARSET', 'utf8mb4');

/**
 * Obtiene la conexión PDO a la base de datos del CRM
 * @return PDO
 * @throws PDOException
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    
    return $pdo;
}

// ============================================
// EJEMPLO: Función de autenticación
// ============================================

/**
 * Autentica un usuario por email y contraseña
 * 
 * @param string $email Email del usuario
 * @param string $password Contraseña sin hashear
 * @return array|false Datos del usuario o false si falla
 */
function authenticateUser($email, $password) {
    try {
        $pdo = getDBConnection();
        
        // Consultar contactos con acceso web (JOIN con cliente_passwords)
        $stmt = $pdo->prepare("
            SELECT 
                c.id,
                c.nombre,
                c.apellido,
                c.correo,
                c.telefono,
                c.tipo,
                cp.password_web as password_hash
            FROM contactos c
            INNER JOIN cliente_passwords cp ON c.id = cp.contacto_id
            WHERE c.correo = ?
            AND (c.tipo = 'CLIENTE' OR c.tipo = 'LEAD')
            LIMIT 1
        ");
        
        $stmt->execute([$email]);
        $cliente = $stmt->fetch();
        
        // Verificar si existe y la contraseña es correcta
        if ($cliente && password_verify($password, $cliente['password_hash'])) {
            return [
                'id' => $cliente['id'],
                'name' => trim($cliente['nombre'] . ' ' . $cliente['apellido']),
                'email' => $cliente['correo'],
                'phone' => $cliente['telefono'],
                'tipo' => $cliente['tipo']
            ];
        }
        
        return false;
        
    } catch (PDOException $e) {
        error_log("Error en authenticateUser: " . $e->getMessage());
        return false;
    }
}

// ============================================
// EJEMPLO: Función de registro
// ============================================

/**
 * Registra un nuevo usuario en el sistema
 * 
 * @param array $data Datos del usuario (name, email, phone, password)
 * @return int|false ID del usuario creado o false si falla
 */
function registerUser($data) {
    try {
        $pdo = getDBConnection();
        
        // Iniciar transacción
        $pdo->beginTransaction();
        
        // Separar nombre y apellido
        $name_parts = explode(' ', trim($data['name']), 2);
        $nombre = $name_parts[0];
        $apellido = isset($name_parts[1]) ? $name_parts[1] : '';
        
        // Insertar en tabla contactos
        $stmt = $pdo->prepare("
            INSERT INTO contactos (nombre, apellido, correo, telefono, tipo, fecha_creacion, estado) 
            VALUES (?, ?, ?, ?, 'CLIENTE', NOW(), 'ACTIVO')
        ");
        
        $stmt->execute([
            $nombre,
            $apellido,
            $data['email'],
            $data['phone']
        ]);
        
        $contacto_id = $pdo->lastInsertId();
        
        // Insertar contraseña en tabla cliente_passwords
        $stmt = $pdo->prepare("
            INSERT INTO cliente_passwords (contacto_id, password_web, fecha_creacion) 
            VALUES (?, ?, NOW())
        ");
        
        $stmt->execute([
            $contacto_id,
            $data['password'] // Ya debe estar hasheado
        ]);
        
        // Confirmar transacción
        $pdo->commit();
        
        return $contacto_id;
        
    } catch (PDOException $e) {
        // Revertir transacción en caso de error
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Error en registerUser: " . $e->getMessage());
        return false;
    }
}

// ============================================
// EJEMPLO: Verificar si email existe
// ============================================

/**
 * Verifica si un email ya está registrado
 * 
 * @param string $email Email a verificar
 * @return bool True si existe, false si no
 */
function emailExists($email) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT id FROM contactos 
            WHERE correo = ? 
            AND (tipo = 'CLIENTE' OR tipo = 'LEAD')
            LIMIT 1
        ");
        
        $stmt->execute([$email]);
        
        return $stmt->rowCount() > 0;
        
    } catch (PDOException $e) {
        error_log("Error en emailExists: " . $e->getMessage());
        return false;
    }
}

// ============================================
// EJEMPLO: Obtener datos del usuario
// ============================================

/**
 * Obtiene los datos de un usuario por su ID
 * 
 * @param int $user_id ID del usuario
 * @return array|false Datos del usuario o false si no existe
 */
function getUserById($user_id) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT 
                c.id,
                c.nombre,
                c.apellido,
                c.correo,
                c.telefono,
                c.tipo
            FROM contactos c
            WHERE c.id = ?
            AND (c.tipo = 'CLIENTE' OR c.tipo = 'LEAD')
            LIMIT 1
        ");
        
        $stmt->execute([$user_id]);
        $cliente = $stmt->fetch();
        
        if ($cliente) {
            return [
                'id' => $cliente['id'],
                'name' => trim($cliente['nombre'] . ' ' . $cliente['apellido']),
                'email' => $cliente['correo'],
                'phone' => $cliente['telefono'],
                'tipo' => $cliente['tipo']
            ];
        }
        
        return false;
        
    } catch (PDOException $e) {
        error_log("Error en getUserById: " . $e->getMessage());
        return false;
    }
}

// ============================================
// EJEMPLO: Actualizar última actividad
// ============================================

/**
 * Actualiza la última fecha de actividad del usuario
 * 
 * @param int $user_id ID del usuario
 * @return bool True si se actualizó, false si falló
 */
function updateUserActivity($user_id) {
    try {
        $pdo = getDBConnection();
        
        // Verificar si la tabla cliente_accesos existe
        $tableCheck = $pdo->query("SHOW TABLES LIKE 'cliente_accesos'");
        if ($tableCheck->rowCount() === 0) {
            // La tabla no existe, solo logear y retornar true
            error_log("Tabla cliente_accesos no existe - skip auditoría");
            return true;
        }
        
        // Registrar acceso en la tabla de auditoría
        $stmt = $pdo->prepare("
            INSERT INTO cliente_accesos (id, contacto_id, ip_address, user_agent, fecha_acceso)
            VALUES (UUID(), ?, ?, ?, NOW())
        ");
        
        return $stmt->execute([
            $user_id,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
        
    } catch (PDOException $e) {
        error_log("Error en updateUserActivity: " . $e->getMessage());
        // No fallar el login si la auditoría falla
        return true;
    }
}

// ============================================
// NOTAS DE IMPLEMENTACIÓN
// ============================================

/*
ESTRUCTURA DE DATOS ESPERADA:

Usuario debe tener estos campos:
- id: int (identificador único)
- name/nombre: string (nombre completo)
- email: string (correo electrónico único)
- phone/telefono: string (número de teléfono)
- password: string (contraseña hasheada con password_hash())
- role: string ('user' o 'cliente' para clientes del portal)
- created_at: datetime (fecha de creación)
- updated_at: datetime (fecha de última actualización) [opcional]

VARIABLES DE SESIÓN REQUERIDAS:
Después de autenticación exitosa, establecer:
- $_SESSION['client_id'] = ID del usuario
- $_SESSION['client_name'] = Nombre del usuario
- $_SESSION['client_email'] = Email del usuario
- $_SESSION['client_phone'] = Teléfono del usuario
- $_SESSION['client_logged_in'] = true

SEGURIDAD:
- Siempre usar prepared statements
- Hashear contraseñas con password_hash()
- Verificar contraseñas con password_verify()
- Sanitizar inputs con filter_var()
- Validar datos antes de insertar
*/

?>
