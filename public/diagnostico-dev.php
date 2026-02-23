<?php
/**
 * Diagnóstico de Base de Datos - Desarrollo
 * Visita: http://localhost:8081/diagnostico-dev.php
 */

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Diagnóstico de Base de Datos - Desarrollo</h1>";

// 1. Verificar configuración
echo "<h2>1. Variables de Entorno</h2>";
echo "<pre>";
echo "DB_HOST (env): " . (getenv('DB_HOST') ?: 'NO DEFINIDO') . "\n";
echo "DB_NAME (env): " . (getenv('DB_NAME') ?: 'NO DEFINIDO') . "\n";
echo "DB_USER (env): " . (getenv('DB_USER') ?: 'NO DEFINIDO') . "\n";
echo "DB_PASS (env): " . (getenv('DB_PASS') ? '***' : 'NO DEFINIDO') . "\n";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'NO DEFINIDO') . "\n";
echo "</pre>";

// 2. Cargar configuración
require_once __DIR__ . '/../config/database.php';

echo "<h2>2. Configuración Cargada</h2>";
echo "<pre>";
echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NO DEFINIDO') . "\n";
echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NO DEFINIDO') . "\n";
echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'NO DEFINIDO') . "\n";
echo "DB_PASS: " . (defined('DB_PASS') ? '***' : 'NO DEFINIDO') . "\n";
echo "</pre>";

// 3. Intentar conexión
echo "<h2>3. Prueba de Conexión</h2>";
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    echo "<p style='color: green;'>✅ Conexión exitosa</p>";
    
    // 4. Verificar tablas
    echo "<h2>4. Tablas Disponibles</h2>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
    // 5. Verificar datos
    echo "<h2>5. Conteo de Datos</h2>";
    echo "<ul>";
    
    if (in_array('testimonios', $tables)) {
        $count = $pdo->query("SELECT COUNT(*) FROM testimonios WHERE estado = 'aprobado'")->fetchColumn();
        echo "<li>Testimonios aprobados: <strong>$count</strong></li>";
    } else {
        echo "<li style='color: red;'>❌ Tabla 'testimonios' NO existe</li>";
    }
    
    if (in_array('proyectos', $tables)) {
        $count = $pdo->query("SELECT COUNT(*) FROM proyectos WHERE activo = 1")->fetchColumn();
        echo "<li>Proyectos activos: <strong>$count</strong></li>";
    } else {
        echo "<li style='color: red;'>❌ Tabla 'proyectos' NO existe</li>";
    }
    
    if (in_array('miembros_equipo', $tables)) {
        $count = $pdo->query("SELECT COUNT(*) FROM miembros_equipo WHERE activo = 1")->fetchColumn();
        echo "<li>Miembros de equipo activos: <strong>$count</strong></li>";
    } else {
        echo "<li style='color: red;'>❌ Tabla 'miembros_equipo' NO existe</li>";
    }
    
    if (in_array('otros_servicios', $tables)) {
        $count = $pdo->query("SELECT COUNT(*) FROM otros_servicios")->fetchColumn();
        echo "<li>Otros servicios: <strong>$count</strong></li>";
    } else {
        echo "<li style='color: red;'>❌ Tabla 'otros_servicios' NO existe</li>";
    }
    
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error de conexión: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>Nota:</strong> Si ves errores, verifica que la base de datos local tenga las tablas correctas.</p>";
?>
