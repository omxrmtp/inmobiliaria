<?php
/**
 * API para descargar cotización en PDF
 * Requiere autenticación JWT y ID de cotización
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Obtener ID de cotización
    $cotizacionId = $_GET['id'] ?? null;
    
    if (!$cotizacionId) {
        http_response_code(400);
        throw new Exception('ID de cotización no proporcionado');
    }
    
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
    
    // URL de la API local que se conecta directamente a la BD
    $isProduction = !in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', 'localhost:8080']);
    $apiUrl = ($isProduction 
        ? 'https://' . $_SERVER['HTTP_HOST'] . '/public/api-descargar-pdf-local.php'
        : 'http://' . $_SERVER['HTTP_HOST'] . '/public/api-descargar-pdf-local.php') . '?id=' . urlencode($cotizacionId);
    
    error_log("URL de descarga: $apiUrl");
    error_log("Token: " . substr($token, 0, 20) . "...");
    
    // Realizar petición a la API del CRM
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 30,
            'method' => 'GET',
            'header' => "Authorization: Bearer $token\r\n"
        ]
    ]);
    
    error_log("Descargando PDF de cotización desde: $apiUrl");
    
    $response = @file_get_contents($apiUrl, false, $ctx);
    
    if ($response === false) {
        $error = error_get_last();
        $errorMsg = $error ? $error['message'] : 'Error desconocido';
        error_log("Error al conectar con CRM: $errorMsg");
        
        // Intentar con curl como alternativa
        error_log("Intentando con curl...");
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $token"
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            error_log("Curl HTTP Code: $httpCode");
            error_log("Curl Error: $curlError");
            
            if ($response === false) {
                throw new Exception("No se pudo descargar el PDF con curl. Detalles: $curlError");
            }
        } else {
            throw new Exception("No se pudo descargar el PDF. Detalles: $errorMsg");
        }
    }
    
    // Validar que recibimos datos binarios válidos
    if (empty($response)) {
        throw new Exception("La respuesta del servidor está vacía");
    }
    
    error_log("Tamaño del PDF recibido: " . strlen($response) . " bytes");
    
    // Enviar el PDF al cliente
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="cotizacion_' . $cotizacionId . '.pdf"');
    header('Content-Length: ' . strlen($response));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Enviar datos binarios correctamente
    echo $response;
    exit();
    
} catch (Exception $e) {
    error_log("Error en descargar_cotizacion_pdf.php: " . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
