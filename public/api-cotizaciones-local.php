<?php
/**
 * API para obtener cotizaciones del cliente/lead - Versión Local (sin depender del CRM)
 * Se conecta directamente a la BD MySQL
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
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/jwt.php';

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

    // Obtener cotizaciones del contacto desde la BD
    $sql = "SELECT 
                c.id,
                c.numero_cotizacion,
                c.fecha_creacion,
                c.fecha_vencimiento,
                c.estado,
                c.total,
                c.moneda,
                c.descripcion,
                p.nombre as proyecto_nombre,
                p.ubicacion as proyecto_ubicacion
            FROM cotizacion c
            LEFT JOIN proyecto p ON c.proyecto_id = p.id
            WHERE c.contacto_id = :contactoId
            ORDER BY c.fecha_creacion DESC";

    $cotizaciones = fetchAll($sql, ['contactoId' => $contactoId]);

    // Retornar cotizaciones
    echo json_encode([
        'success' => true,
        'datos' => $cotizaciones,
        'count' => count($cotizaciones)
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    error_log("Error en api-cotizaciones-local.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
