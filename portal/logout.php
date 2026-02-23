<?php
/**
 * API para cerrar sesión
 */

require_once __DIR__ . '/../includes/cors.php';

session_start();
session_destroy();

echo json_encode([
    'success' => true,
    'message' => 'Sesión cerrada correctamente'
]);
?>
