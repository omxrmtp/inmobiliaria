<?php
/**
 * API de Login para portal de clientes
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/cors.php';
require_once __DIR__ . '/../includes/jwt.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit();
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $email = $input['email'] ?? $input['correo'] ?? null;
    $password = $input['password'] ?? null;
    
    if (!$email || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'Email y contraseña requeridos']);
        exit();
    }
    
    // Buscar primero en tabla CLIENTES (esquema actual)
    $sql = "SELECT 
                c.id,
                c.nombre,
                c.apellido,
                c.correo,
                c.telefono,
                cp.password_web,
                cp.activo,
                'CLIENTE' AS tipo
            FROM clientes c
            LEFT JOIN cliente_passwords cp ON c.id = cp.contacto_id
            WHERE c.correo = :email
            LIMIT 1";
    
    $cliente = fetchOne($sql, ['email' => $email]);
    
    // Si no encuentra en CLIENTES, buscar en LEADS
    if (!$cliente) {
        $sql = "SELECT 
                    l.id,
                    l.nombre,
                    l.apellido,
                    l.correo,
                    l.telefono,
                    cp.password_web,
                    cp.activo,
                    'LEAD' AS tipo
                FROM leads l
                LEFT JOIN cliente_passwords cp ON l.id = cp.contacto_id
                WHERE l.correo = :email
                LIMIT 1";
        
        $cliente = fetchOne($sql, ['email' => $email]);
    }
    
    if (!$cliente) {
        http_response_code(401);
        echo json_encode(['error' => 'Credenciales inválidas']);
        exit();
    }
    
    // Verificar que tenga contraseña web configurada
    if (empty($cliente['password_web'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Tu cuenta no tiene acceso configurado. Por favor, contacta con el administrador.']);
        exit();
    }
    
    // Verificar password
    if (!password_verify($password, $cliente['password_web'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Credenciales inválidas']);
        exit();
    }
    
    // Iniciar sesión
    session_start();
    $_SESSION['client_id'] = $cliente['id'];
    $_SESSION['client_name'] = $cliente['nombre'] . ' ' . ($cliente['apellido'] ?? '');
    $_SESSION['client_email'] = $cliente['correo'];
    
    // Generar JWT token con el mismo payload que el backend Express
    $payload = [
        'usuarioId' => $cliente['id'], // Usando contactoId como usuarioId
        'email' => $cliente['correo'],
        'rol' => 'CLIENTE'  // El backend ahora acepta 'CLIENTE' como rol
    ];
    
    $token = JWT::encode($payload, 86400); // 24 horas
    
    // Guardar token en la sesión
    $_SESSION['token'] = $token;
    
    // Registrar último acceso (estructura real de la tabla)
    executeQuery(
        "INSERT INTO cliente_accesos (id, contacto_id, fecha_acceso, ip_address, user_agent)
         VALUES (UUID(), :id, NOW(), :ip, :ua)",
        [
            'id' => $cliente['id'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'ua' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]
    );
    
    echo json_encode([
        'success' => true,
        'token' => $token,
        'cliente' => [
            'id' => $cliente['id'],
            'nombre' => $cliente['nombre'],
            'apellido' => $cliente['apellido'],
            'email' => $cliente['correo'],
            'telefono' => $cliente['telefono']
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Error en login: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error en el servidor']);
}
?>
