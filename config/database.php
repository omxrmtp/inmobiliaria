<?php
/**
 * Configuración de conexión a MySQL
 * Funciona tanto para Docker (desarrollo) como Hostinger (producción)
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
    
    if (!defined('DB_HOST')) {
        define('DB_HOST', $db_host);
    }
    if (!defined('DB_NAME')) {
        define('DB_NAME', getenv('DB_NAME') ?: 'crm_delgado');
    }
    if (!defined('DB_USER')) {
        define('DB_USER', getenv('DB_USER') ?: 'root');
    }
    if (!defined('DB_PASS')) {
        define('DB_PASS', getenv('DB_PASS') ?: '00617');
    }
} else {
    // Configuración para Hostinger (producción)
    if (!defined('DB_HOST')) {
        define('DB_HOST', 'srv448.hstgr.io');
    }
    if (!defined('DB_NAME')) {
        define('DB_NAME', 'u476108630_crm_delgado');
    }
    if (!defined('DB_USER')) {
        define('DB_USER', 'u476108630_delgadoUser');
    }
    if (!defined('DB_PASS')) {
        define('DB_PASS', 'Rmdpropiedades23');
    }
}

if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', 'utf8mb4');
}

// Crear conexión PDO
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Error de conexión a BD: " . $e->getMessage());
            http_response_code(500);
            
            // En desarrollo, mostrar detalles del error
            if (getenv('ENVIRONMENT') === 'development' || ini_get('display_errors') == '1') {
                die(json_encode([
                    'error' => 'Error de conexión a la base de datos',
                    'details' => $e->getMessage(),
                    'host' => DB_HOST,
                    'database' => DB_NAME,
                    'user' => DB_USER
                ]));
            }
            
            // En producción, mensaje genérico
            die(json_encode(['error' => 'Error de conexión a la base de datos']));
        }
    }
    
    return $pdo;
}

// Función helper para ejecutar consultas preparadas
function executeQuery($sql, $params = []) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Error en consulta: " . $e->getMessage());
        throw $e;
    }
}

// Función para obtener un registro
function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch();
}

// Función para obtener múltiples registros
function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll();
}
?>
