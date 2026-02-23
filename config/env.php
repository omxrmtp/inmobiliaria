<?php
/**
 * Cargar variables de entorno desde archivo .env
 */

function loadEnv($filePath = null) {
    if ($filePath === null) {
        $filePath = __DIR__ . '/../.env';
    }

    error_log("Cargando .env desde: " . $filePath);
    error_log("Archivo existe: " . (file_exists($filePath) ? 'SI' : 'NO'));

    if (!file_exists($filePath)) {
        // Si no existe .env, usar valores por defecto (desarrollo)
        if (!getenv('JWT_SECRET')) {
            putenv('JWT_SECRET=CAMBIAR_generar_token_unico_de_32_caracteres_minimo');
            error_log("Usando JWT_SECRET por defecto (fallback)");
        }
        return;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignorar comentarios
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parsear KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Remover comillas si existen
            if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1)) {
                $value = substr($value, 1, -1);
            } elseif ((strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
                $value = substr($value, 1, -1);
            }

            putenv("{$key}={$value}");
        }
    }
}

// Cargar .env automáticamente
loadEnv();
?>
