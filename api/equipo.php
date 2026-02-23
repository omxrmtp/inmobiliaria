<?php
/**
 * API para listar miembros del equipo
 * Consumida por la página web pública
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/cors.php';

try {
    $sql = "SELECT 
                id,
                nombre,
                cargo,
                descripcion,
                foto,
                facebook,
                instagram,
                tiktok,
                orden_mostrar
            FROM miembros_equipo
            WHERE activo = 1
            ORDER BY orden_mostrar ASC, nombre ASC";
    
    $equipo = fetchAll($sql);
    
    echo json_encode([
        'success' => true,
        'data' => $equipo,
        'count' => count($equipo)
    ]);
    
} catch (Exception $e) {
    error_log("Error en API equipo: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener equipo'
    ]);
}
?>
