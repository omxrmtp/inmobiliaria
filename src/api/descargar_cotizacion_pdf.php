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
    
    // URL de la API del CRM para descargar PDF
    $crmApiBase = 'http://host.docker.internal:5000/api';
    $apiUrl = $crmApiBase . '/cotizaciones/' . urlencode($cotizacionId) . '/descargar-pdf';
    
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
        throw new Exception("No se pudo descargar el PDF. Detalles: $errorMsg");
    }
    
    // Enviar el PDF al cliente
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="cotizacion_' . $cotizacionId . '.pdf"');
    header('Content-Length: ' . strlen($response));
    
    echo $response;
    
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
