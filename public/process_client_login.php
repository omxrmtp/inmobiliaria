<?php
session_start();

// CORS headers primero
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Cargar archivos de configuración
try {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/jwt.php';
} catch (Exception $e) {
    error_log("Error cargando configuración: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Error de configuración del servidor',
        'debug' => $e->getMessage()
    ]);
    exit();
}

// Leer JSON del request
$input = json_decode(file_get_contents('php://input'), true);
$email = $input['email'] ?? null;
$password = $input['password'] ?? null;

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Email y contraseña requeridos']);
    exit();
}

try {
    // Debug: log del email recibido
    error_log("Login attempt for email: " . $email);

    // Usar API local que se conecta directamente a la BD
    // En producción, esto apuntará a la misma URL que el portal
    $isProduction = !in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', 'localhost:8080']);
    $apiUrl = $isProduction 
        ? 'https://' . $_SERVER['HTTP_HOST'] . '/public/api-login-local.php'
        : 'http://' . $_SERVER['HTTP_HOST'] . '/public/api-login-local.php';
    $url = $apiUrl;

    error_log("Conectando a CRM en: " . $url);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['correo' => $email, 'password' => $password]));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    error_log("CRM Response Code: $httpCode");
    error_log("CRM Response: " . substr($response, 0, 500));

    if ($response === false) {
        error_log("CURL Error: $curlErr");
        http_response_code(500);
        echo json_encode(['error' => 'Error conectando con el servidor CRM: ' . $curlErr]);
        exit();
    }

    if ($httpCode >= 400) {
        error_log("Error login-cliente CRM: HTTP $httpCode - Resp: $response");
        http_response_code($httpCode ?: 500);
        
        // Decodificar la respuesta del CRM para obtener el mensaje específico
        $errorData = json_decode($response, true);
        $errorMessage = $errorData['error'] ?? $errorData['message'] ?? 'Credenciales inválidas o error del servidor';
        
        echo json_encode(['error' => $errorMessage]);
        exit();
    }

    $data = json_decode($response, true);
    error_log("Decoded response: " . json_encode($data));
    
    if (!$data || empty($data['token'])) {
        error_log("No token in response");
        http_response_code(401);
        echo json_encode(['error' => 'Credenciales inválidas']);
        exit();
    }

    // Guardar en sesión
    $_SESSION['client_id'] = $data['cliente']['id'] ?? null;
    $_SESSION['client_name'] = trim(($data['cliente']['nombre'] ?? '') . ' ' . ($data['cliente']['apellido'] ?? ''));
    $_SESSION['client_email'] = $data['cliente']['correo'] ?? $email;
    $_SESSION['token'] = $data['token'];

    // Respuesta JSON
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'token' => $data['token'],
        'cliente' => $data['cliente'] ?? null
    ]);
    
} catch (PDOException $e) {
    error_log("Error de BD en login: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'error' => 'Error de conexión a la base de datos',
        'message' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
} catch (Exception $e) {
    error_log("Error en login: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'error' => 'Error en el servidor',
        'message' => $e->getMessage()
    ]);
}
?>
