<?php
session_start();
require_once __DIR__ . '/../includes/jwt.php';

header('Content-Type: application/json');

$diagnostico = [
    'sesion_activa' => isset($_SESSION['client_id']),
    'token_en_sesion' => isset($_SESSION['token']),
    'client_id' => $_SESSION['client_id'] ?? null,
    'client_name' => $_SESSION['client_name'] ?? null,
    'client_email' => $_SESSION['client_email'] ?? null,
];

if (isset($_SESSION['token'])) {
    $token = $_SESSION['token'];
    $diagnostico['token'] = $token;
    
    // Decodificar token
    $decoded = JWT::decode($token);
    if ($decoded) {
        $diagnostico['token_valido'] = true;
        $diagnostico['payload'] = $decoded;
        $diagnostico['tiene_usuarioId'] = isset($decoded['usuarioId']);
        $diagnostico['tiene_email'] = isset($decoded['email']);
        $diagnostico['tiene_rol'] = isset($decoded['rol']);
        $diagnostico['tiene_tipo'] = isset($decoded['tipo']);
        
        if (isset($decoded['tipo']) && !isset($decoded['rol'])) {
            $diagnostico['problema'] = 'TOKEN ANTIGUO - Tiene "tipo" en lugar de "rol"';
            $diagnostico['solucion'] = 'Hacer logout y login nuevamente';
        } else if (isset($decoded['rol'])) {
            $diagnostico['problema'] = null;
            $diagnostico['token_correcto'] = true;
        }
    } else {
        $diagnostico['token_valido'] = false;
        $diagnostico['problema'] = 'Token no se puede decodificar';
    }
}

echo json_encode($diagnostico, JSON_PRETTY_PRINT);
?>
