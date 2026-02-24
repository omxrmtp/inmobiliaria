<?php
/**
 * Diagnóstico de Conexión a BD en Hostinger
 * Sube este archivo a la raíz del proyecto en Hostinger y accede a:
 * https://delgadopropiedades.com/diagnostico-conexion.php
 */

header('Content-Type: application/json; charset=utf-8');

$diagnostico = [
    'timestamp' => date('Y-m-d H:i:s'),
    'servidor' => $_SERVER['HTTP_HOST'] ?? 'CLI',
    'ambiente' => 'unknown',
    'conexion' => null,
    'tablas' => [],
    'errores' => []
];

try {
    // 1. Determinar si es producción o desarrollo
    $isDevelopment = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', 'localhost:8080']);
    $diagnostico['ambiente'] = $isDevelopment ? 'DESARROLLO' : 'PRODUCCIÓN';

    // 2. Cargar configuración
    require_once __DIR__ . '/config/database.php';

    // 3. Verificar constantes definidas
    $diagnostico['configuracion'] = [
        'DB_HOST' => defined('DB_HOST') ? DB_HOST : 'NO DEFINIDO',
        'DB_NAME' => defined('DB_NAME') ? DB_NAME : 'NO DEFINIDO',
        'DB_USER' => defined('DB_USER') ? DB_USER : 'NO DEFINIDO',
        'DB_CHARSET' => defined('DB_CHARSET') ? DB_CHARSET : 'NO DEFINIDO'
    ];

    // 4. Intentar conexión
    $pdo = getDBConnection();
    
    if ($pdo) {
        $diagnostico['conexion'] = 'EXITOSA';

        // 5. Listar todas las tablas
        $stmt = $pdo->query("SHOW TABLES");
        $diagnostico['tablas'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // 6. Verificar estructura de tabla cliente
        $stmt = $pdo->query("DESCRIBE cliente");
        $diagnostico['estructura_cliente'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 7. Verificar datos en cliente
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM cliente");
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $diagnostico['datos_cliente'] = $resultado['total'] ?? 0;

        // 8. Verificar tabla cliente_password
        $stmt = $pdo->query("DESCRIBE cliente_password");
        $diagnostico['estructura_cliente_password'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 9. Verificar datos en cliente_password
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM cliente_password");
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $diagnostico['datos_cliente_password'] = $resultado['total'] ?? 0;

    } else {
        $diagnostico['conexion'] = 'FALLIDA';
        $diagnostico['errores'][] = 'No se pudo establecer conexión a la BD';
    }

} catch (PDOException $e) {
    $diagnostico['conexion'] = 'ERROR';
    $diagnostico['errores'][] = [
        'tipo' => 'PDOException',
        'mensaje' => $e->getMessage(),
        'codigo' => $e->getCode(),
        'archivo' => $e->getFile(),
        'linea' => $e->getLine()
    ];
} catch (Exception $e) {
    $diagnostico['conexion'] = 'ERROR';
    $diagnostico['errores'][] = [
        'tipo' => get_class($e),
        'mensaje' => $e->getMessage(),
        'codigo' => $e->getCode()
    ];
}

echo json_encode($diagnostico, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
