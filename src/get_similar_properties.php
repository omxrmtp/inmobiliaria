<?php

// Establecer cabeceras para JSON y CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Crear conexión
    $conn = connectDB();

    // Verificar si se proporcionaron los parámetros necesarios
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("ID de propiedad no proporcionado");
    }

    $property_id = $_GET['id'];
    $property_type = isset($_GET['type']) ? $_GET['type'] : '';
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 3;

    // Obtener propiedades similares
    $sql = "SELECT * FROM properties WHERE id != :id";
    $params = [':id' => $property_id];

    // Filtrar por tipo de propiedad si se proporciona
    if (!empty($property_type)) {
        $sql .= " AND property_type = :type";
        $params[':type'] = $property_type;
    }

    // Limitar resultados y ordenar aleatoriamente
    $sql .= " ORDER BY RAND() LIMIT :limit";
    $params[':limit'] = $limit;

    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . implode(", ", $conn->errorInfo()));
    }

    // Vincular parámetros
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . implode(", ", $stmt->errorInfo()));
    }

    // Obtener los resultados
    $properties = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Consulta para obtener TODAS las imágenes de la propiedad
        $img_query = "SELECT * FROM property_images WHERE property_id = :property_id ORDER BY is_main DESC, id ASC";
        $img_stmt = $conn->prepare($img_query);
        
        if (!$img_stmt) {
            throw new Exception("Error en la preparación de la consulta de imágenes: " . implode(", ", $conn->errorInfo()));
        }
        
        $img_stmt->bindParam(':property_id', $row['id'], PDO::PARAM_INT);
        
        if (!$img_stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta de imágenes: " . implode(", ", $img_stmt->errorInfo()));
        }
        
        $images = [];
        if ($img_stmt->rowCount() > 0) {
            while ($img_row = $img_stmt->fetch(PDO::FETCH_ASSOC)) {
                $image_path = $img_row['image_path'];
                
                // Normalizar la ruta de la imagen (mantener la ruta original de la BD)
                // Solo eliminar "../" si existe al principio
                $image_path = preg_replace('/^\.\.\//', '', $image_path);
                
                // Asegurarse de que la ruta comience con / si no es una URL completa
                if (substr($image_path, 0, 1) !== '/' && 
                    substr($image_path, 0, 7) !== 'http://' && 
                    substr($image_path, 0, 8) !== 'https://') {
                    $image_path = '/' . $image_path;
                }
                
                $img_row['image_path'] = $image_path;
                $images[] = $img_row;
            }
            
            // Asignar la primera imagen como imagen principal para la vista de lista
            $row['image_path'] = $images[0]['image_path'];
        } else {
            // Si no hay imágenes, usar una imagen por defecto según el tipo de propiedad
            $property_type = strtolower($row['property_type']);
            
            switch ($property_type) {
                case 'house':
                    $default_image = '/img/properties/default/house.jpg';
                    break;
                case 'apartment':
                    $default_image = '/img/properties/default/apartment.jpg';
                    break;
                case 'land':
                    $default_image = '/img/properties/default/land.jpg';
                    break;
                case 'commercial':
                    $default_image = '/img/properties/default/commercial.jpg';
                    break;
                case 'office':
                    $default_image = '/img/properties/default/office.jpg';
                    break;
                default:
                    $default_image = '/img/properties/default/property-default.jpg';
            }
            
            $row['image_path'] = $default_image;
            $images[] = [
                'id' => 0,
                'property_id' => $row['id'],
                'image_path' => $default_image,
                'is_main' => 1
            ];
        }
        
        // Agregar todas las imágenes a la propiedad
        $row['images'] = $images;
        
        $properties[] = $row;
    }

    // Devolver los resultados en formato JSON
    echo json_encode(['success' => true, 'properties' => $properties]);

} catch (Exception $e) {
    // Devolver error
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}