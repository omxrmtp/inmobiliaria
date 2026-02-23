<?php
/**
 * API para obtener cotizaciones del cliente/lead desde el CRM
 * Requiere autenticación JWT
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Obtener token del header
    $headers = getallheaders();
    $token = null;
    
    if (isset($headers['Authorization'])) {
        $auth_header = $headers['Authorization'];
        if (preg_match('/Bearer\s+(.+)/', $auth_header, $matches)) {
            $token = $matches[1];
        }
    }
    
    if (!$token) {
        http_response_code(401);
        throw new Exception('Token no proporcionado');
    }
    
    // URL de la API del CRM
    $crmApiBase = 'http://host.docker.internal:5000/api';
    $apiUrl = $crmApiBase . '/cotizaciones/cliente';
    
    // Realizar petición a la API del CRM con el token
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 10,
            'method' => 'GET',
            'header' => "Content-Type: application/json\r\nAuthorization: Bearer $token\r\n"
        ]
    ]);
    
    error_log("Obteniendo cotizaciones desde: $apiUrl");
    
    $response = @file_get_contents($apiUrl, false, $ctx);
    
    if ($response === false) {
        $error = error_get_last();
        $errorMsg = $error ? $error['message'] : 'Error desconocido';
        throw new Exception("No se pudo conectar con el servidor del CRM. Detalles: $errorMsg");
    }
    
    $data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Respuesta inválida del CRM: " . json_last_error_msg());
    }
    
    // Retornar cotizaciones
    echo json_encode([
        'success' => true,
        'cotizaciones' => $data['datos'] ?? [],
        'count' => count($data['datos'] ?? [])
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("Error en get_cotizaciones.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
