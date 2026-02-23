<?php
/**
 * API para descargar cotización en PDF - Versión Local (sin depender del CRM)
 * Se conecta directamente a la BD MySQL
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
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/jwt.php';

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

    // Decodificar y validar token
    $decoded = JWT::decode($token);
    if (!$decoded || !isset($decoded['usuarioId'])) {
        http_response_code(401);
        throw new Exception('Token inválido');
    }

    $contactoId = $decoded['usuarioId'];

    // Verificar que la cotización pertenece al contacto autenticado
    $sql = "SELECT c.id, c.numero_cotizacion, c.pdf_contenido, c.pdf_nombre
            FROM cotizacion c
            WHERE c.id = :cotizacionId AND c.contacto_id = :contactoId
            LIMIT 1";

    $cotizacion = fetchOne($sql, [
        'cotizacionId' => $cotizacionId,
        'contactoId' => $contactoId
    ]);

    if (!$cotizacion) {
        http_response_code(404);
        throw new Exception('Cotización no encontrada o no tienes permiso para acceder');
    }

    // Verificar que el PDF existe
    if (empty($cotizacion['pdf_contenido'])) {
        http_response_code(404);
        throw new Exception('El PDF de esta cotización no está disponible');
    }

    // Obtener el contenido del PDF (puede ser base64 o binario)
    $pdfContent = $cotizacion['pdf_contenido'];

    // Si está en base64, decodificar
    if (base64_encode(base64_decode($pdfContent, true)) === $pdfContent) {
        $pdfContent = base64_decode($pdfContent);
    }

    // Enviar el PDF al cliente
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . ($cotizacion['pdf_nombre'] ?? 'cotizacion_' . $cotizacionId . '.pdf') . '"');
    header('Content-Length: ' . strlen($pdfContent));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Enviar datos binarios
    echo $pdfContent;
    exit();

} catch (Exception $e) {
    error_log("Error en api-descargar-pdf-local.php: " . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
