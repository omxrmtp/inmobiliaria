<?php
// Proxy público para ../src/get_team.php
// Cargar configuración y base de datos
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
	require_once __DIR__ . '/../vendor/autoload.php';
}

if (file_exists(__DIR__ . '/../src/config/settings.php')) {
	require_once __DIR__ . '/../src/config/settings.php';
}

if (file_exists(__DIR__ . '/../src/config/database.php')) {
	require_once __DIR__ . '/../src/config/database.php';
}

// Proteger la salida para que siempre devolvamos JSON válido
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
ob_start();

try {
	// Incluir el archivo que obtiene los datos del equipo
	require_once __DIR__ . '/../src/get_team.php';
} catch (Throwable $t) {
	ob_end_clean();
	http_response_code(500);
	echo json_encode([
		'success' => false, 
		'error' => 'Error interno al procesar la solicitud', 
		'details' => $t->getMessage()
	], JSON_UNESCAPED_UNICODE);
	exit;
}

$content = ob_get_clean();
$trim = ltrim($content);

// Verificar si el contenido es JSON válido
if (strlen($trim) > 0 && (strpos($trim, '{') === 0 || strpos($trim, '[') === 0)) {
	echo $content;
	exit;
}

// Si no es JSON, devolver error
$safe = strip_tags($content);
http_response_code(500);
echo json_encode([
	'success' => false, 
	'error' => 'El servidor devolvió contenido inesperado en lugar de JSON', 
	'details' => substr($safe, 0, 2000)
], JSON_UNESCAPED_UNICODE);
exit;
