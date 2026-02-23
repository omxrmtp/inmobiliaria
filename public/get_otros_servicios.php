<?php
// Proxy público para servicios
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
  require_once __DIR__ . '/../vendor/autoload.php';
}
header('Content-Type: application/json');
ini_set('display_errors', 0);
ob_start();
try {
  require_once __DIR__ . '/../src/get_otros_servicios.php';
} catch (Throwable $t) {
  ob_end_clean();
  echo json_encode([]);
  exit;
}
echo ob_get_clean();
