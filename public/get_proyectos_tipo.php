<?php
/**
 * Obtiene proyectos filtrados por tipo desde el CRM
 * Uso: get_proyectos_tipo.php?tipo=techo_propio
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/client_db_config.php';

try {
    $pdo = getDBConnection();
    
    // Obtener parámetros
    $tipo = $_GET['tipo'] ?? '';
    $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 12;
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $offset = ($pagina - 1) * $limite;
    
    if (empty($tipo)) {
        throw new Exception('Tipo de proyecto no especificado');
    }
    
    // Consulta de proyectos
    $sql = "
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
            activo,
            mostrar_en_web,
            orden_web,
            created_at
        FROM proyectos
        WHERE tipo_proyecto = :tipo
        AND activo = 1
        AND mostrar_en_web = 1
        ORDER BY orden_web ASC, created_at DESC
        LIMIT :limite OFFSET :offset
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Decodificar campos JSON y formatear
    foreach ($proyectos as &$proyecto) {
        $proyecto['galeria'] = json_decode($proyecto['galeria'] ?? '[]', true);
        $proyecto['caracteristicas'] = json_decode($proyecto['caracteristicas'] ?? '{}', true);
        
        // Formatear precio
        if ($proyecto['precio']) {
            $proyecto['precio_formateado'] = 'S/ ' . number_format($proyecto['precio'], 2, '.', ',');
        } else {
            $proyecto['precio_formateado'] = 'Precio a consultar';
        }
        
        // Imagen por defecto si no tiene
        if (empty($proyecto['imagen'])) {
            $proyecto['imagen'] = 'img/placeholder-property.jpg';
        }
    }
    
    // Contar total de proyectos
    $sqlCount = "
        SELECT COUNT(*) as total
        FROM proyectos
        WHERE tipo_proyecto = :tipo
        AND activo = 1
        AND mostrar_en_web = 1
    ";
    
    $stmtCount = $pdo->prepare($sqlCount);
    $stmtCount->bindValue(':tipo', $tipo, PDO::PARAM_STR);
    $stmtCount->execute();
    $totalProyectos = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Respuesta
    echo json_encode([
        'success' => true,
        'tipo' => $tipo,
        'proyectos' => $proyectos,
        'total' => (int)$totalProyectos,
        'pagina' => $pagina,
        'limite' => $limite,
        'total_paginas' => ceil($totalProyectos / $limite),
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    error_log("Error en get_proyectos_tipo.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error de base de datos',
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    error_log("Error en get_proyectos_tipo.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
?>
