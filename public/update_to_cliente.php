<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$email = $input['email'] ?? null;

if (!$email) {
    http_response_code(400);
    echo json_encode(['error' => 'Email requerido']);
    exit();
}

try {
    // Buscar el usuario por email
    $usuario = fetchOne(
        "SELECT id, nombre, apellido, correo, tipo FROM contactos WHERE correo = :email LIMIT 1",
        ['email' => $email]
    );

    if (!$usuario) {
        http_response_code(404);
        echo json_encode(['error' => 'Usuario no encontrado con ese email']);
        exit();
    }

    // Si ya es CLIENTE, no hacer nada
    if ($usuario['tipo'] === 'CLIENTE') {
        echo json_encode([
            'success' => true,
            'message' => 'El usuario ya es tipo CLIENTE',
            'usuario' => [
                'id' => $usuario['id'],
                'nombre' => $usuario['nombre'] . ' ' . ($usuario['apellido'] ?? ''),
                'email' => $usuario['correo'],
                'tipo' => $usuario['tipo']
            ]
        ]);
        exit();
    }

    // Actualizar a tipo CLIENTE
    executeQuery(
        "UPDATE contactos SET tipo = 'CLIENTE' WHERE id = :id",
        ['id' => $usuario['id']]
    );

    echo json_encode([
        'success' => true,
        'message' => 'Usuario actualizado correctamente a tipo CLIENTE',
        'usuario' => [
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'] . ' ' . ($usuario['apellido'] ?? ''),
            'email' => $usuario['correo'],
            'tipo' => 'CLIENTE'
        ]
    ]);

} catch (Exception $e) {
    error_log("Error al actualizar usuario: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Error en el servidor',
        'message' => $e->getMessage()
    ]);
}
?>
