<?php
/**
 * Script de prueba para verificar la conexión a la base de datos
 * y las funciones de registro de clientes
 */

// Incluir configuración de base de datos
require_once 'client_db_config.php';

// Habilitar visualización de errores para pruebas
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Prueba de Conexión y Registro de Clientes</h1>";

// 1. Probar conexión a la base de datos
echo "<h2>1. Probando conexión a la base de datos...</h2>";
try {
    $pdo = getDBConnection();
    echo "✅ Conexión exitosa a la base de datos<br>";
    echo "📍 Host: " . DB_HOST . "<br>";
    echo "📍 Base de datos: " . DB_NAME . "<br>";
    echo "📍 Usuario: " . DB_USER . "<br>";
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
    exit();
}

// 2. Verificar si las tablas necesarias existen
echo "<h2>2. Verificando tablas necesarias...</h2>";

$tablas_necesarias = ['contactos', 'cliente_passwords'];
foreach ($tablas_necesarias as $tabla) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$tabla'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Tabla '$tabla' existe<br>";
        } else {
            echo "❌ Tabla '$tabla' NO existe<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error verificando tabla '$tabla': " . $e->getMessage() . "<br>";
    }
}

// 3. Probar función emailExists
echo "<h2>3. Probando función emailExists...</h2>";
$email_prueba = 'test@example.com';
try {
    $existe = emailExists($email_prueba);
    echo "📧 Email '$email_prueba' " . ($existe ? "YA existe" : "NO existe") . "<br>";
} catch (Exception $e) {
    echo "❌ Error en emailExists: " . $e->getMessage() . "<br>";
}

// 4. Probar función registerUser (solo si no existe el email)
echo "<h2>4. Probando función registerUser...</h2>";
if (!$existe) {
    try {
        $datos_usuario = [
            'name' => 'Usuario Prueba',
            'email' => $email_prueba,
            'phone' => '+51 999 999 999',
            'password' => password_hash('password123', PASSWORD_DEFAULT)
        ];
        
        $user_id = registerUser($datos_usuario);
        if ($user_id) {
            echo "✅ Usuario registrado con ID: $user_id<br>";
            
            // Verificar que se haya creado correctamente
            $existe_despues = emailExists($email_prueba);
            echo "📧 Verificación: Email '$email_prueba' " . ($existe_despues ? "AHORA existe" : "aún NO existe") . "<br>";
        } else {
            echo "❌ No se pudo registrar el usuario<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error en registerUser: " . $e->getMessage() . "<br>";
    }
} else {
    echo "⚠️ Omitiendo prueba de registro (email ya existe)<br>";
}

// 5. Probar función authenticateUser
echo "<h2>5. Probando función authenticateUser...</h2>";
try {
    $auth_result = authenticateUser($email_prueba, 'password123');
    if ($auth_result) {
        echo "✅ Autenticación exitosa:<br>";
        echo "   - ID: " . $auth_result['id'] . "<br>";
        echo "   - Nombre: " . $auth_result['name'] . "<br>";
        echo "   - Email: " . $auth_result['email'] . "<br>";
        echo "   - Teléfono: " . $auth_result['phone'] . "<br>";
        echo "   - Tipo: " . $auth_result['tipo'] . "<br>";
    } else {
        echo "❌ Autenticación fallida<br>";
    }
} catch (Exception $e) {
    echo "❌ Error en authenticateUser: " . $e->getMessage() . "<br>";
}

echo "<h2>✅ Prueba completada</h2>";
echo "<p><a href='client-register.php'>Ir al formulario de registro</a></p>";
?>
