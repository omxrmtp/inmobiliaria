<?php
/**
 * Configuración de CORS y headers comunes
 */

// Permitir acceso desde tu dominio
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // Cambiar a tu dominio en producción
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>
