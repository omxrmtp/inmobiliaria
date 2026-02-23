<?php
// Proxy público para procesar login usando la lógica en src/process_login.php
// Debe aceptar POST y reenviar las cabeceras/redirects tal como lo hace el script original.

// Intentar cargar composer/autoload si existe, sino incluir settings/database
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    if (file_exists(__DIR__ . '/../src/config/settings.php')) {
        require_once __DIR__ . '/../src/config/settings.php';
    }
    if (file_exists(__DIR__ . '/../src/config/database.php')) {
        require_once __DIR__ . '/../src/config/database.php';
    }
}

// Asegurar que la función connectDB esté disponible
if (!function_exists('connectDB') && file_exists(__DIR__ . '/../src/config/database.php')) {
    require_once __DIR__ . '/../src/config/database.php';
}

// Incluir el script que maneja el POST
require_once __DIR__ . '/../src/process_login.php';

// El script incluido maneja la lógica y realiza los redirect/session
exit;
