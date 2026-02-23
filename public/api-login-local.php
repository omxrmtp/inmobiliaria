<?php
/**
 * API de Login para portal de clientes - Versión Local (sin depender del CRM)
 * Se conecta directamente a la BD MySQL
 */

session_start();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/jwt.php';

    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'] ?? $input['correo'] ?? null;
    $password = $input['password'] ?? null;

    if (!$email || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'Email y contraseña requeridos']);
        exit();
    }

    // Buscar en tabla cliente primero
    $sql = "SELECT c.id, c.nombre, c.apellido, c.correo, c.telefono,
                   cp.contrasena_web as password_web, cp.activo
            FROM cliente c
            LEFT JOIN cliente_password cp ON c.id = cp.id_contacto
            WHERE c.correo = :email 
            LIMIT 1";

    $cliente = fetchOne($sql, ['email' => $email]);

    // Si no encuentra en cliente, buscar en lead
    if (!$cliente) {
        $sql = "SELECT l.id, l.nombre, l.apellido, l.correo, l.telefono,
                       cp.contrasena_web as password_web, cp.activo
                FROM lead l
                LEFT JOIN cliente_password cp ON l.id = cp.id_contacto
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
    if (!$cliente['password_web']) {
        http_response_code(403);
        echo json_encode(['error' => 'Tu cuenta no tiene acceso configurado. Por favor, contacta con el administrador.']);
        exit();
    }

    // Verificar que la cuenta esté activa
    if ($cliente['activo'] === 0 || $cliente['activo'] === '0') {
        http_response_code(403);
        echo json_encode(['error' => 'Tu cuenta no tiene acceso activo. Por favor, contacta con el administrador.']);
        exit();
    }

    // Verificar password
    if (!password_verify($password, $cliente['password_web'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Credenciales inválidas']);
        exit();
    }

    // Iniciar sesión
    $_SESSION['client_id'] = $cliente['id'];
    $_SESSION['client_name'] = $cliente['nombre'] . ' ' . ($cliente['apellido'] ?? '');
    $_SESSION['client_email'] = $cliente['correo'];

    // Generar JWT token
    $payload = [
        'usuarioId' => $cliente['id'],
        'email' => $cliente['correo'],
        'rol' => 'CLIENTE'
    ];

    $token = JWT::encode($payload, 86400); // 24 horas

    $_SESSION['token'] = $token;

    // Registrar último acceso
    executeQuery(
        "INSERT INTO cliente_accesos (contacto_id, fecha_acceso, ip_address) VALUES (:id, NOW(), :ip)",
        [
            'id' => $cliente['id'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
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
    error_log("Error en api-login-local.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error en el servidor']);
}
?>
