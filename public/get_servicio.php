<?php
/**
 * API para obtener contenido de servicios desde el CRM
 * Endpoints:
 * - /get_servicio.php?tipo=techo_propio
 * - /get_servicio.php?tipo=credito_mivivienda
 * - /get_servicio.php?tipo=habilitaciones
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/client_db_config.php';

try {
    $pdo = getDBConnection();
    
    // Obtener tipo de servicio de la URL
    $tipo = $_GET['tipo'] ?? '';
    
    if (empty($tipo)) {
        throw new Exception('Tipo de servicio no especificado');
    }
    
    // Obtener datos de la página de servicio
    $stmt = $pdo->prepare("
        SELECT 
            id,
            tipo_servicio,
            titulo,
            subtitulo,
            descripcion_corta,
            descripcion_larga,
            imagen_banner,
            video_banner,
            requisitos,
            beneficios,
            pasos,
            preguntas,
            activo,
            orden_mostrar,
            meta_title,
            meta_description,
            created_at,
            updated_at
        FROM paginas_servicios
        WHERE tipo_servicio = ?
        AND activo = 1
        LIMIT 1
    ");
    
    $stmt->execute([$tipo]);
    $servicio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$servicio) {
        // Si no existe la página, retornar estructura por defecto
        echo json_encode([
            'success' => false,
            'message' => 'Servicio no encontrado',
            'tipo' => $tipo,
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    
    // Decodificar JSON fields
    $servicio['requisitos'] = json_decode($servicio['requisitos'] ?? '[]');
    $servicio['beneficios'] = json_decode($servicio['beneficios'] ?? '[]');
    $servicio['pasos'] = json_decode($servicio['pasos'] ?? '[]');
    $servicio['preguntas'] = json_decode($servicio['preguntas'] ?? '[]');
    
    // Obtener proyectos relacionados con este tipo de servicio
    $stmtProyectos = $pdo->prepare("
        SELECT 
            id,
            nombre,
            descripcion,
            imagen,
            precio,
            ubicacion,
            estado,
            tipo_proyecto,
            categoria,
            galeria,
            caracteristicas,
            created_at
        FROM proyectos
        WHERE tipo_proyecto = ?
        AND activo = 1
        AND mostrar_en_web = 1
        ORDER BY orden_web ASC, created_at DESC
        LIMIT 12
    ");
    
    $stmtProyectos->execute([$tipo]);
    $proyectos = $stmtProyectos->fetchAll(PDO::FETCH_ASSOC);
    
    // Decodificar JSON en proyectos
    foreach ($proyectos as &$proyecto) {
        $proyecto['galeria'] = json_decode($proyecto['galeria'] ?? '[]');
        $proyecto['caracteristicas'] = json_decode($proyecto['caracteristicas'] ?? '{}');
        $proyecto['precio_formateado'] = $proyecto['precio'] 
            ? 'S/ ' . number_format($proyecto['precio'], 2) 
            : 'Consultar precio';
    }
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'servicio' => $servicio,
        'proyectos' => $proyectos,
        'total_proyectos' => count($proyectos),
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    error_log("Error en get_servicio.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error de base de datos',
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    error_log("Error en get_servicio.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
?>
