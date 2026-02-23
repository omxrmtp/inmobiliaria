<?php
require_once 'database.php';

try {
    $conn = connectDB();
    
    // Crear tabla inquiries si no existe
    $sql = "CREATE TABLE IF NOT EXISTS inquiries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        property_id INT,
        status ENUM('pending', 'in_progress', 'resolved') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        resolved_at TIMESTAMP NULL
    )";
    
    $conn->exec($sql);
    echo "Tabla 'inquiries' creada o verificada correctamente.<br>";
    
} catch(PDOException $e) {
    echo "Error al crear la tabla: " . $e->getMessage();
}
?> 