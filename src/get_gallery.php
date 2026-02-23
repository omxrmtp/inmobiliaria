<?php

header('Content-Type: application/json');

if (!isset($_GET['type'])) {
    echo json_encode(['error' => 'Tipo de servicio no especificado']);
    exit();
}

$serviceType = $_GET['type'];

try {
    $conn = connectDB();
    
    // Modificamos la consulta para obtener TODAS las entradas del mismo tipo
    $stmt = $conn->prepare("SELECT * FROM otros_servicios WHERE service_type = :type");
    $stmt->bindParam(':type', $serviceType);
    $stmt->execute();
    
    // Obtenemos todas las entradas, no solo una
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($services)) {
        echo json_encode(['media' => []]);
        exit();
    }

    // Preparar los medios (imágenes y videos)
    $media = [];
    $mainTitle = $services[0]['title']; // Usamos el título del primer servicio
    
    // Recorremos todas las entradas encontradas
    foreach ($services as $service) {
        // Agregar imagen principal si existe
        if (!empty($service['image_link'])) {
            $media[] = [
                'url' => $service['image_link'],
                'type' => 'image',
                'title' => $mainTitle // Mantenemos el título consistente
            ];
        }
        
        // Agregar video principal si existe
        if (!empty($service['video_link'])) {
            $media[] = [
                'url' => $service['video_link'],
                'type' => 'video',
                'title' => $mainTitle // Mantenemos el título consistente
            ];
        }
    }

    echo json_encode(['media' => $media]);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
}