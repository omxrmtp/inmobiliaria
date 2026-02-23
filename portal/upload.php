<?php
/**
 * API para subir documentos del cliente
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/cors.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit();
}

try {
    $clienteId = verificarSesion();
    
    // Verificar que se haya subido un archivo
    if (!isset($_FILES['documento'])) {
        http_response_code(400);
        echo json_encode(['error' => 'No se recibió ningún archivo']);
        exit();
    }
    
    $file = $_FILES['documento'];
    $tipoDocumento = $_POST['tipo'] ?? 'GENERAL';
    $descripcion = $_POST['descripcion'] ?? null;
    
    // Validar archivo
    $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $maxSize = 10 * 1024 * 1024; // 10MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['error' => 'Tipo de archivo no permitido']);
        exit();
    }
    
    if ($file['size'] > $maxSize) {
        http_response_code(400);
        echo json_encode(['error' => 'El archivo es demasiado grande (máx 10MB)']);
        exit();
    }
    
    // Crear directorio si no existe
    $uploadDir = __DIR__ . '/../../uploads/clientes/' . $clienteId . '/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generar nombre único
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $nombreArchivo = uniqid() . '_' . time() . '.' . $extension;
    $rutaCompleta = $uploadDir . $nombreArchivo;
    
    // Mover archivo
    if (!move_uploaded_file($file['tmp_name'], $rutaCompleta)) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al guardar el archivo']);
        exit();
    }
    
    // Guardar en BD
    $sql = "INSERT INTO cliente_documentos 
            (contacto_id, nombre_archivo, nombre_original, ruta_archivo, tipo_documento, 
             descripcion, tamano_bytes, mime_type, subido_en, estado)
            VALUES 
            (:clienteId, :nombreArchivo, :nombreOriginal, :rutaArchivo, :tipoDocumento,
             :descripcion, :tamano, :mimeType, NOW(), 'PENDIENTE')";
    
    executeQuery($sql, [
        'clienteId' => $clienteId,
        'nombreArchivo' => $nombreArchivo,
        'nombreOriginal' => $file['name'],
        'rutaArchivo' => $rutaCompleta,
        'tipoDocumento' => $tipoDocumento,
        'descripcion' => $descripcion,
        'tamano' => $file['size'],
        'mimeType' => $file['type']
    ]);
    
    $documentoId = getDBConnection()->lastInsertId();
    
    // Registrar actividad
    executeQuery(
        "INSERT INTO cliente_actividades 
         (contacto_id, tipo, descripcion, fecha)
         VALUES (:id, 'DOCUMENTO_SUBIDO', :desc, NOW())",
        [
            'id' => $clienteId,
            'desc' => "Subió documento: " . $file['name']
        ]
    );
    
    echo json_encode([
        'success' => true,
        'data' => [
            'id' => $documentoId,
            'nombreArchivo' => $nombreArchivo,
            'nombreOriginal' => $file['name'],
            'tamano' => $file['size'],
            'tipo' => $tipoDocumento
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Error al subir documento: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al subir el documento'
    ]);
}
?>
