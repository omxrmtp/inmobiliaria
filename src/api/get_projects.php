<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Habilitar todos los errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar que el archivo de configuración existe (usar ruta basada en el directorio actual del archivo)
$configPath = __DIR__ . '/../config/database.php';
if (!file_exists($configPath)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Archivo de configuración no encontrado',
        'path_checked' => $configPath
    ]);
    exit;
}

// Requerir la configuración (si no se ha cargado aún)
if (!class_exists('Database')) {
    require_once $configPath;
}

try {
    // Crear conexión
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception('No se pudo establecer la conexión con la base de datos');
    }
    
    // Preparar y ejecutar la consulta
    $query = "SELECT id, title, description, image_urls, type, created_at, updated_at FROM projects WHERE type = 'habilitacion' ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta');
    }
    
    $stmt->execute();
    
    // Procesar resultados
    $projects = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Decodificar el JSON de image_urls
        $images = json_decode($row['image_urls'], true);
        error_log('Image URLs para proyecto ' . $row['id'] . ': ' . print_r($images, true));
        
        // Guardar todas las imágenes en el array
        $row['images'] = is_array($images) ? $images : [];
        $projects[] = $row;
    }
    
    error_log('Total de proyectos encontrados: ' . count($projects));
    
    // Devolver respuesta exitosa
    $response = [
        'success' => true,
        'data' => $projects
    ];
    
    error_log('Respuesta JSON: ' . json_encode($response));
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log('Error en get_projects.php: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener los proyectos: ' . $e->getMessage(),
        'details' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}