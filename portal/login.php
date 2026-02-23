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
    
    // Buscar en tabla cliente primero
    $sql = "SELECT c.id, c.nombre, c.apellido, c.correo, c.telefono,
                   cp.contrasena_web as password_web
            FROM cliente c
            LEFT JOIN cliente_password cp ON c.id = cp.id_contacto
            WHERE c.correo = :email 
            LIMIT 1";
    
    $cliente = fetchOne($sql, ['email' => $email]);
    
    // Si no encuentra en cliente, buscar en lead
    if (!$cliente) {
        $sql = "SELECT l.id, l.nombre, l.apellido, l.correo, l.telefono,
                       cp.contrasena_web as password_web
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
    error_log("Error en login: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error en el servidor']);
}
?>
