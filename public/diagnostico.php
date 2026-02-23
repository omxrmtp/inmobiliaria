<?php
/**
 * Script de diagnóstico para verificar la configuración
 */

header('Content-Type: text/html; charset=UTF-8');

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Diagnóstico del Sistema</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #1a1a1a;
            color: #00ff00;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: #00ff00;
            text-align: center;
            border-bottom: 2px solid #00ff00;
            padding-bottom: 10px;
        }
        h2 {
            color: #ffff00;
            margin-top: 2rem;
        }
        .section {
            background: #2a2a2a;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid #00ff00;
        }
        .ok {
            color: #00ff00;
        }
        .warning {
            color: #ffff00;
        }
        .error {
            color: #ff0000;
        }
        pre {
            background: #000;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
        }
        .status.ok {
            background: #004400;
        }
        .status.warning {
            background: #444400;
        }
        .status.error {
            background: #440000;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 DIAGNÓSTICO DEL SISTEMA - PORTAL CLIENTE</h1>";

// 1. Verificar carga de archivos
echo "<div class='section'>
        <h2>📦 1. CARGA DE ARCHIVOS</h2>";

$files = [
    'config/database.php' => __DIR__ . '/../config/database.php',
    'includes/jwt.php' => __DIR__ . '/../includes/jwt.php',
    'includes/cors.php' => __DIR__ . '/../includes/cors.php'
];

foreach ($files as $name => $path) {
    $exists = file_exists($path);
    $status = $exists ? "<span class='status ok'>✅ OK</span>" : "<span class='status error'>❌ NO ENCONTRADO</span>";
    echo "<p>$name: $status</p>";
    if ($exists) {
        $size = filesize($path);
        $modified = date('Y-m-d H:i:s', filemtime($path));
        echo "<p style='margin-left: 20px; color: #888;'>Tamaño: $size bytes | Modificado: $modified</p>";
    }
}
echo "</div>";

// 2. Verificar variables de entorno
echo "<div class='section'>
        <h2>🔐 2. VARIABLES DE ENTORNO</h2>";

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    echo "<p><span class='status ok'>✅ Archivo .env encontrado</span></p>";
    echo "<p>Última modificación: " . date('Y-m-d H:i:s', filemtime($envFile)) . "</p>";
    
    // Leer .env
    $envContent = file_get_contents($envFile);
    if (preg_match('/JWT_SECRET=(.+)/', $envContent, $matches)) {
        $secret = trim($matches[1]);
        echo "<p><span class='status ok'>✅ JWT_SECRET encontrado en .env</span></p>";
        echo "<pre>JWT_SECRET=" . substr($secret, 0, 20) . "..." . substr($secret, -10) . " (ocultado parcialmente)</pre>";
    } else {
        echo "<p><span class='status error'>❌ JWT_SECRET no encontrado en .env</span></p>";
    }
} else {
    echo "<p><span class='status error'>❌ Archivo .env NO encontrado</span></p>";
}

// Verificar getenv
$jwtSecret = getenv('JWT_SECRET');
if ($jwtSecret) {
    echo "<p><span class='status ok'>✅ JWT_SECRET cargado vía getenv()</span></p>";
    echo "<pre>Valor: " . substr($jwtSecret, 0, 20) . "..." . substr($jwtSecret, -10) . "</pre>";
} else {
    echo "<p><span class='status warning'>⚠️ JWT_SECRET no cargado vía getenv()</span></p>";
}

echo "</div>";

// 3. Probar JWT
echo "<div class='section'>
        <h2>🔑 3. PRUEBA DE JWT</h2>";

try {
    require_once __DIR__ . '/../includes/jwt.php';
    
    echo "<p><span class='status ok'>✅ Clase JWT cargada correctamente</span></p>";
    
    // Generar token de prueba
    $testPayload = [
        'usuarioId' => 'test-123',
        'email' => 'test@example.com',
        'rol' => 'CLIENTE'
    ];
    
    $testToken = JWT::encode($testPayload, 60);
    echo "<p><span class='status ok'>✅ Token generado correctamente</span></p>";
    echo "<pre>" . wordwrap($testToken, 80, "\n", true) . "</pre>";
    
    // Decodificar
    $parts = explode('.', $testToken);
    $payloadEncoded = $parts[1];
    $decoded = json_decode(base64_decode(strtr($payloadEncoded, '-_', '+/')), true);
    
    echo "<p><span class='status ok'>✅ Token decodificado:</span></p>";
    echo "<pre>" . json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
    
    // Verificar que tiene 'rol' y no 'tipo'
    if (isset($decoded['rol']) && $decoded['rol'] === 'CLIENTE') {
        echo "<p><span class='status ok'>✅ Token tiene 'rol': 'CLIENTE' (CORRECTO)</span></p>";
    } else if (isset($decoded['tipo'])) {
        echo "<p><span class='status error'>❌ Token tiene 'tipo' en lugar de 'rol' (INCORRECTO)</span></p>";
    }
    
} catch (Exception $e) {
    echo "<p><span class='status error'>❌ Error: " . $e->getMessage() . "</span></p>";
}

echo "</div>";

// 4. Verificar base de datos
echo "<div class='section'>
        <h2>🗄️ 4. CONEXIÓN A BASE DE DATOS</h2>";

try {
    require_once __DIR__ . '/../config/database.php';
    
    $pdo = getDBConnection();
    if ($pdo) {
        echo "<p><span class='status ok'>✅ Conexión a BD exitosa</span></p>";
        
        // Verificar tablas
        $tables = ['contactos', 'cliente_passwords', 'cotizaciones', 'oportunidades_clientes'];
        foreach ($tables as $table) {
            try {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                $result = $stmt->fetch();
                echo "<p>Tabla '$table': <span class='ok'>{$result['count']} registros</span></p>";
            } catch (Exception $e) {
                echo "<p>Tabla '$table': <span class='error'>Error - {$e->getMessage()}</span></p>";
            }
        }
        
        // Verificar clientes con acceso web
        $stmt = $pdo->query("
            SELECT COUNT(*) as count 
            FROM contactos c 
            INNER JOIN cliente_passwords cp ON c.id = cp.contacto_id 
            WHERE c.tipo = 'CLIENTE'
        ");
        $result = $stmt->fetch();
        echo "<p>Clientes con acceso web: <span class='ok'>{$result['count']}</span></p>";
        
    } else {
        echo "<p><span class='status error'>❌ No se pudo conectar a la BD</span></p>";
    }
} catch (Exception $e) {
    echo "<p><span class='status error'>❌ Error de BD: " . $e->getMessage() . "</span></p>";
}

echo "</div>";

// 5. Verificar APIs locales de PGDP
echo "<div class='section'>
        <h2>🚀 5. APIS LOCALES DE PGDP</h2>";

$isDevelopment = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', 'localhost:8080']);

if ($isDevelopment) {
    echo "<p><span class='status warning'>⚠️ Ambiente: DESARROLLO LOCAL</span></p>";
    echo "<p>En desarrollo, el CRM Express debe estar corriendo en puerto 5000</p>";
    echo "<p class='warning'>Comando: <code>cd Crm-Delgado/server && npm run dev</code></p>";
} else {
    echo "<p><span class='status ok'>✅ Ambiente: PRODUCCIÓN (Hostinger)</span></p>";
    echo "<p>Las APIs locales están configuradas para conectarse directamente a la BD</p>";
    echo "<p>APIs disponibles:</p>";
    echo "<ul>";
    echo "<li><code>/public/api-login-local.php</code> - Login de clientes</li>";
    echo "<li><code>/public/api-cotizaciones-local.php</code> - Obtener cotizaciones</li>";
    echo "<li><code>/public/api-descargar-pdf-local.php</code> - Descargar PDFs</li>";
    echo "</ul>";
}

echo "</div>";

// 6. Recomendaciones
echo "<div class='section'>
        <h2>💡 6. RECOMENDACIONES</h2>
        <ul>
            <li>Si hay tokens con 'tipo' en lugar de 'rol', limpia la sesión en: <a href='clear-session.php' style='color: #00ff00;'>clear-session.php</a></li>
            <li>Para decodificar un token existente: <a href='test-decode-token.php' style='color: #00ff00;'>test-decode-token.php</a></li>
            <li>Asegúrate de que el Backend Express esté corriendo</li>
            <li>Verifica que el JWT_SECRET sea el mismo en ambos sistemas</li>
        </ul>
    </div>";

echo "<div style='text-align: center; margin-top: 2rem; padding: 20px; border-top: 2px solid #00ff00;'>
        <p>Diagnóstico completado el " . date('Y-m-d H:i:s') . "</p>
    </div>
    
    </div>
</body>
</html>";
?>
