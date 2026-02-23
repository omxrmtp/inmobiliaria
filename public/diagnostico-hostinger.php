<?php
/**
 * Diagnóstico de Base de Datos - Hostinger
 * Visita: https://tu-dominio.com/diagnostico-hostinger.php
 */

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnóstico de Base de Datos - Hostinger</h1>";

// 1. Información del servidor
echo "<h2>1. Información del Servidor</h2>";
echo "<pre>";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'NO DEFINIDO') . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'NO DEFINIDO') . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "PDO disponible: " . (extension_loaded('pdo') ? 'SÍ' : 'NO') . "\n";
echo "PDO MySQL disponible: " . (extension_loaded('pdo_mysql') ? 'SÍ' : 'NO') . "\n";
echo "</pre>";

// 2. Verificar archivos de configuración
echo "<h2>2. Archivos de Configuración</h2>";
echo "<pre>";
$configFiles = [
    '../config/database.php',
    '../config/env.php',
    '../src/config/database.php',
    '../src/config/settings.php'
];
foreach ($configFiles as $file) {
    $fullPath = __DIR__ . '/' . $file;
    echo "$file: " . (file_exists($fullPath) ? 'EXISTE' : 'NO EXISTE') . "\n";
}
echo "</pre>";

// 3. Probar configuración principal (config/database.php)
echo "<h2>3. Configuración Principal (config/database.php)</h2>";
try {
    require_once __DIR__ . '/../config/database.php';
    echo "<pre>";
    echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NO DEFINIDO') . "\n";
    echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NO DEFINIDO') . "\n";
    echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'NO DEFINIDO') . "\n";
    echo "DB_PASS: " . (defined('DB_PASS') ? '***' : 'NO DEFINIDO') . "\n";
    echo "</pre>";
    
    // Intentar conexión
    echo "<h3>3.1. Prueba de Conexión</h3>";
    try {
        $pdo = getDBConnection();
        echo "<p style='color: green;'>✅ Conexión exitosa con config/database.php</p>";
        
        // Verificar tablas
        echo "<h3>3.2. Tablas Disponibles</h3>";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
        
        // Verificar datos
        echo "<h3>3.3. Conteo de Datos</h3>";
        echo "<ul>";
        
        if (in_array('testimonios', $tables)) {
            $count = $pdo->query("SELECT COUNT(*) FROM testimonios WHERE estado = 'aprobado'")->fetchColumn();
            echo "<li>Testimonios aprobados: <strong>$count</strong></li>";
            
            // Mostrar algunos registros
            if ($count > 0) {
                $stmt = $pdo->query("SELECT id, nombre, calificacion, LEFT(testimonio, 50) as testimonio_corto FROM testimonios WHERE estado = 'aprobado' LIMIT 3");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "<li>Primeros 3 testimonios:<ul>";
                foreach ($rows as $row) {
                    echo "<li>ID: {$row['id']}, Nombre: {$row['nombre']}, Calificación: {$row['calificacion']}</li>";
                }
                echo "</ul></li>";
            }
        } else {
            echo "<li style='color: red;'>❌ Tabla 'testimonios' NO existe</li>";
        }
        
        if (in_array('proyectos', $tables)) {
            $count = $pdo->query("SELECT COUNT(*) FROM proyectos WHERE activo = 1")->fetchColumn();
            echo "<li>Proyectos activos: <strong>$count</strong></li>";
            
            if ($count > 0) {
                $stmt = $pdo->query("SELECT id, nombre, ubicacion, precio FROM proyectos WHERE activo = 1 LIMIT 3");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "<li>Primeros 3 proyectos:<ul>";
                foreach ($rows as $row) {
                    echo "<li>ID: {$row['id']}, Nombre: {$row['nombre']}, Ubicación: {$row['ubicacion']}</li>";
                }
                echo "</ul></li>";
            }
        } else {
            echo "<li style='color: red;'>❌ Tabla 'proyectos' NO existe</li>";
        }
        
        if (in_array('otros_servicios', $tables)) {
            $count = $pdo->query("SELECT COUNT(*) FROM otros_servicios")->fetchColumn();
            echo "<li>Otros servicios: <strong>$count</strong></li>";
            
            if ($count > 0) {
                $stmt = $pdo->query("SELECT id, titulo, tipo_servicio FROM otros_servicios LIMIT 3");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "<li>Primeros 3 servicios:<ul>";
                foreach ($rows as $row) {
                    echo "<li>ID: {$row['id']}, Título: {$row['titulo']}</li>";
                }
                echo "</ul></li>";
            }
        } else {
            echo "<li style='color: red;'>❌ Tabla 'otros_servicios' NO existe</li>";
        }
        
        if (in_array('miembros_equipo', $tables)) {
            $count = $pdo->query("SELECT COUNT(*) FROM miembros_equipo WHERE activo = 1")->fetchColumn();
            echo "<li>Miembros de equipo activos: <strong>$count</strong></li>";
        } else {
            echo "<li style='color: red;'>❌ Tabla 'miembros_equipo' NO existe</li>";
        }
        
        echo "</ul>";
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Error de conexión: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error al cargar config/database.php: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// 4. Probar configuración alternativa (src/config)
echo "<h2>4. Configuración Alternativa (src/config)</h2>";
try {
    require_once __DIR__ . '/../src/config/settings.php';
    require_once __DIR__ . '/../src/config/database.php';
    
    echo "<pre>";
    echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NO DEFINIDO') . "\n";
    echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NO DEFINIDO') . "\n";
    echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'NO DEFINIDO') . "\n";
    echo "DB_PASS: " . (defined('DB_PASS') ? '***' : 'NO DEFINIDO') . "\n";
    echo "</pre>";
    
    echo "<h3>4.1. Prueba de Conexión</h3>";
    try {
        $conn = connectDB();
        if ($conn) {
            echo "<p style='color: green;'>✅ Conexión exitosa con src/config/database.php</p>";
        } else {
            echo "<p style='color: red;'>❌ No se pudo conectar con src/config/database.php</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error al cargar src/config: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// 5. Probar endpoints de API
echo "<h2>5. Prueba de Endpoints de API</h2>";
echo "<ul>";

$endpoints = [
    'get_testimonials.php' => 'Testimonios',
    'get_team.php' => 'Equipo',
    'get_otros_servicios.php' => 'Otros Servicios'
];

foreach ($endpoints as $file => $name) {
    $url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $file;
    echo "<li><strong>$name</strong>: <a href='$url' target='_blank'>$url</a></li>";
}

echo "</ul>";

echo "<hr>";
echo "<p><strong>Nota:</strong> Este archivo debe ser eliminado después de completar el diagnóstico por razones de seguridad.</p>";
?>
