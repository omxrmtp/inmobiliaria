<?php
/**
 * Diagnóstico de conexión a BD en Hostinger
 * Sube este archivo a public_html/diagnostico-bd.php
 * Luego visita: https://delgadopropiedades.com/diagnostico-bd.php
 */

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Diagnóstico de Base de Datos - Hostinger</h1>";

// 1. Verificar configuración
echo "<h2>1. Configuración</h2>";
echo "<pre>";
echo "Host: srv448.hstgr.io\n";
echo "Database: u476108630_crm_delgado\n";
echo "User: u476108630_delgadoUser\n";
echo "</pre>";

// 2. Intentar conexión
echo "<h2>2. Prueba de Conexión</h2>";
try {
    $pdo = new PDO(
        'mysql:host=srv448.hstgr.io;dbname=u476108630_crm_delgado;charset=utf8mb4',
        'u476108630_delgadoUser',
        'Rmdpropiedades23',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    echo "<p style='color: green;'>✅ Conexión exitosa a la base de datos</p>";
    
    // 3. Verificar tablas
    echo "<h2>3. Tablas Disponibles</h2>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
    // 4. Verificar datos en testimonios
    echo "<h2>4. Testimonios (primeros 3)</h2>";
    $stmt = $pdo->query("SELECT id, nombre, calificacion FROM testimonios WHERE estado = 'aprobado' LIMIT 3");
    $testimonios = $stmt->fetchAll();
    echo "<pre>" . print_r($testimonios, true) . "</pre>";
    
    // 5. Verificar datos en proyectos
    echo "<h2>5. Proyectos (primeros 3)</h2>";
    $stmt = $pdo->query("SELECT id, nombre, precio, estado FROM proyectos WHERE activo = 1 LIMIT 3");
    $proyectos = $stmt->fetchAll();
    echo "<pre>" . print_r($proyectos, true) . "</pre>";
    
    // 6. Verificar etiquetas
    echo "<h2>6. Etiquetas</h2>";
    $stmt = $pdo->query("SELECT id, nombre, color FROM etiquetas WHERE activa = 1");
    $etiquetas = $stmt->fetchAll();
    echo "<pre>" . print_r($etiquetas, true) . "</pre>";
    
    echo "<p style='color: green; font-weight: bold;'>✅ Todo funciona correctamente</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error de conexión: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>Nota:</strong> Elimina este archivo después de verificar que todo funciona.</p>";
?>
