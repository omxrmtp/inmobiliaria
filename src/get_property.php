<?php

// Establecer cabeceras para JSON y CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
  // Verificar si se proporcionó un ID
  if (!isset($_GET['id']) || empty($_GET['id'])) {
      throw new Exception("ID de propiedad no proporcionado");
  }

  $property_id = $_GET['id'];

  // Crear conexión
  $conn = connectDB();

  // Consulta para obtener los detalles de la propiedad
  $sql = "SELECT * FROM properties WHERE id = :id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':id', $property_id, PDO::PARAM_INT);
  $stmt->execute();
  $property = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$property) {
      throw new Exception("Propiedad no encontrada");
  }

  // Consulta para obtener todas las imágenes de la propiedad
  $img_query = "SELECT * FROM property_images WHERE property_id = :property_id ORDER BY is_main DESC, id ASC";
  $img_stmt = $conn->prepare($img_query);
  $img_stmt->bindParam(':property_id', $property_id, PDO::PARAM_INT);
  $img_stmt->execute();

  $images = [];
  while ($img_row = $img_stmt->fetch(PDO::FETCH_ASSOC)) {
      $image_path = $img_row['image_path'];
      
      // No necesitamos normalizar la ruta ya que ahora son URLs completas
      $img_row['image_path'] = $image_path;
      $images[] = $img_row;
  }

  // Si no hay imágenes, agregar una imagen por defecto
  if (empty($images)) {
      $property_type = strtolower($property['property_type']);
      $default_image_path = '';
      
      switch ($property_type) {
          case 'house':
              $default_image_path = 'https://via.placeholder.com/800x600.png?text=Casa+No+Imagen';
              break;
          case 'apartment':
              $default_image_path = 'https://via.placeholder.com/800x600.png?text=Departamento+No+Imagen';
              break;
          case 'land':
              $default_image_path = 'https://via.placeholder.com/800x600.png?text=Terreno+No+Imagen';
              break;
          case 'commercial':
              $default_image_path = 'https://via.placeholder.com/800x600.png?text=Local+Comercial+No+Imagen';
              break;
          case 'office':
              $default_image_path = 'https://via.placeholder.com/800x600.png?text=Oficina+No+Imagen';
              break;
          default:
              $default_image_path = 'https://via.placeholder.com/800x600.png?text=Propiedad+No+Imagen';
      }
      
      $images[] = [
          'id' => 0,
          'property_id' => $property_id,
          'image_path' => $default_image_path,
          'is_main' => 1
      ];
  }

  // Agregar las imágenes a la propiedad
  $property['images'] = $images;

  // Devolver los resultados en formato JSON
  echo json_encode($property);

} catch (Exception $e) {
  // Devolver error
  echo json_encode(['error' => $e->getMessage()]);
}
?>