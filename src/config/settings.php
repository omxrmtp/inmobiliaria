<?php
define('ROOT_PATH', dirname(dirname(__DIR__)) . '/');
define('SRC_PATH', ROOT_PATH . 'src/');
define('PUBLIC_PATH', ROOT_PATH . 'public/');

// Cargar variables desde un archivo .env en la raíz del proyecto, si existe
$dotenvPath = ROOT_PATH . '.env';
if (file_exists($dotenvPath)) {
    $lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }
        // Separar clave y valor
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
                // Quitar comillas simples o dobles que envuelva el valor, si las hubiera
                // Usamos una expresión que remueve una comilla al inicio o al final
                $value = preg_replace('/(^[\'\"]|[\'\"]$)/', '', $value);
            if ($key !== '') {
                // Establecer en variables de entorno si no están ya definidas
                if (getenv($key) === false) {
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }
    }
}

// Environment settings
$env = getenv('APP_ENV') ?: 'development';

define('APP_ENV', $env);

// Mostrar errores en development
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// Leer credenciales desde variables de entorno si están definidas
$envDbHost = getenv('DB_HOST');
$envDbUser = getenv('DB_USER');
$envDbPass = getenv('DB_PASS');
$envDbName = getenv('DB_NAME');

// Configuración para Hostinger (producción)
// Si las variables de entorno no están definidas, usar credenciales de Hostinger por defecto
if ($envDbHost) {
    define('DB_HOST', $envDbHost);
} else {
    define('DB_HOST', 'srv448.hstgr.io');
}

if ($envDbUser) {
    define('DB_USER', $envDbUser);
} else {
    define('DB_USER', 'u476108630_delgadoUser');
}

if ($envDbPass) {
    define('DB_PASS', $envDbPass);
} else {
    define('DB_PASS', 'Rmdpropiedades23');
}

if ($envDbName) {
    define('DB_NAME', $envDbName);
} else {
    define('DB_NAME', 'u476108630_crm_delgado');
}
?>