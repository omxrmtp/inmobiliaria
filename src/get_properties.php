<?php
/**
 * API para obtener propiedades/proyectos directamente desde la BD
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

try {
    // Conexión a la BD desde .env
    $dbHost = getenv('DB_HOST') ?: 'srv448.hstgr.io';
    $dbUser = getenv('DB_USER') ?: 'u476108630_delgadoUser';
    $dbPass = getenv('DB_PASS') ?: 'Rmdpropiedades23';
    $dbName = getenv('DB_NAME') ?: 'u476108630_crm_delgado';
    
    // Conectar a la BD
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    
    // Configurar charset
    $conn->set_charset("utf8mb4");
    
    // Parámetros de filtro
    $etiqueta = isset($_GET['etiqueta']) ? $_GET['etiqueta'] : null;
    $estado = isset($_GET['estado']) ? $_GET['estado'] : null;
    
    // Construir query
    $sql = "SELECT 
                p.id,
                p.nombre as title,
                p.descripcion,
                p.precio as price,
                p.estado as status,
                p.ubicacion as city,
                p.creado_en as created_at,
                p.tipo_proyecto as property_type,
                p.imagen,
                GROUP_CONCAT(DISTINCT e.nombre SEPARATOR ',') as etiquetas_str
            FROM proyectos p
            LEFT JOIN etiquetas_proyectos ep ON p.id = ep.id_proyecto
            LEFT JOIN etiquetas e ON ep.id_etiqueta = e.id
            WHERE p.activo = 1 AND (p.estado = 'DISPONIBLE' OR p.estado = 'RESERVADO')";
    
    // Filtrar por etiqueta si existe
    if ($etiqueta) {
        $etiquetas = array_map('trim', explode(',', $etiqueta));
        $etiquetasEscapadas = array_map(function($e) use ($conn) {
            return "'" . $conn->real_escape_string($e) . "'";
        }, $etiquetas);
        $sql .= " AND e.nombre IN (" . implode(',', $etiquetasEscapadas) . ")";
    }
    
    // Filtrar por estado si existe
    if ($estado) {
        $estadoEscapado = $conn->real_escape_string($estado);
        $sql .= " AND p.estado = '$estadoEscapado'";
    }
    
    $sql .= " GROUP BY p.id ORDER BY p.creado_en DESC";
    
    // Log para debugging
    error_log("Ejecutando query: $sql");
    
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    
    $properties = [];
    while ($row = $result->fetch_assoc()) {
        // Procesar etiquetas
        $etiquetas = [];
        if ($row['etiquetas_str']) {
            $etiquetas = array_filter(array_map('trim', explode(',', $row['etiquetas_str'])));
        }
        
        // Procesar imágenes
        $images = [];
        if (!empty($row['imagen'])) {
            $images[] = [
                'id' => uniqid(),
                'image_path' => $row['imagen']
            ];
        }
        
        $properties[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'description' => $row['descripcion'],
            'price' => floatval($row['price']),
            'status' => $row['status'],
            'city' => $row['city'],
            'created_at' => $row['created_at'],
            'property_type' => $row['property_type'],
            'etiquetas' => $etiquetas,
            'images' => $images
        ];
    }
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'properties' => $properties,
        'count' => count($properties)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("Error en get_properties.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Error al conectar con el servidor. Por favor, intenta nuevamente.',
        'details' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
