<?php
/**
 * API para listar otros servicios
 * Consumida por la página web pública
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/cors.php';

try {
    $tipo = $_GET['tipo'] ?? null;
    
    $sql = "SELECT 
                id,
                titulo,
                descripcion,
                caracteristicas,
                imagenes,
                enlace_imagen,
                enlace_video,
                tipo_servicio,
                creado_en
            FROM otros_servicios
            ORDER BY creado_en DESC";
    
    $params = [];
    if ($tipo) {
        $sql = str_replace('ORDER BY', 'WHERE tipo_servicio = :tipo ORDER BY', $sql);
        $params['tipo'] = $tipo;
    }
    
    $servicios = fetchAll($sql, $params);
    
    // Parsear JSON de caracteristicas e imagenes
    foreach ($servicios as &$servicio) {
        $servicio['caracteristicas'] = !empty($servicio['caracteristicas']) ? json_decode($servicio['caracteristicas'], true) : [];
        $servicio['imagenes'] = !empty($servicio['imagenes']) ? json_decode($servicio['imagenes'], true) : [];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $servicios,
        'count' => count($servicios)
    ]);
    
} catch (Exception $e) {
    error_log("Error en API servicios: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener servicios'
    ]);
}
?>
