<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Verificar si la tabla projects existe
    $query = "SHOW TABLES LIKE 'projects'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $tableExists = $stmt->rowCount() > 0;
    
    if (!$tableExists) {
        echo json_encode([
            'success' => false,
            'error' => 'La tabla projects no existe'
        ]);
        exit;
    }
    
    // Verificar la estructura de la tabla
    $query = "DESCRIBE projects";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Verificar si hay datos
    $query = "SELECT COUNT(*) as count FROM projects WHERE type = 'habilitacion'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo json_encode([
        'success' => true,
        'data' => [
            'table_exists' => $tableExists,
            'columns' => $columns,
            'project_count' => $count
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
