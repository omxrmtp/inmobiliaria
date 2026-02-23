<?php
/**
 * Funciones de autenticación para portal de clientes
 */

require_once __DIR__ . '/../config/database.php';

// Verificar si el usuario está autenticado
function verificarSesion() {
    session_start();
    
    if (!isset($_SESSION['cliente_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado']);
        exit();
    }
    
    return $_SESSION['cliente_id'];
}

// Login de cliente
function loginCliente($email, $password) {
    $sql = "SELECT id, nombre, apellido, correo, tipo 
            FROM contactos 
            WHERE correo = :email 
            AND tipo = 'CLIENTE' 
            LIMIT 1";
    
    $cliente = fetchOne($sql, ['email' => $email]);
    
    if (!$cliente) {
        return false;
    }
    
    // Por ahora validación simple - implementar hash después
    // Nota: El CRM debe crear un campo 'password_web' en la tabla contactos
    // para permitir login desde web
    
    session_start();
    $_SESSION['cliente_id'] = $cliente['id'];
    $_SESSION['cliente_nombre'] = $cliente['nombre'] . ' ' . ($cliente['apellido'] ?? '');
    $_SESSION['cliente_email'] = $cliente['correo'];
    
    return $cliente;
}

// Cerrar sesión
function logoutCliente() {
    session_start();
    session_destroy();
    return true;
}

// Obtener información del cliente actual
function getClienteActual() {
    $clienteId = verificarSesion();
    
    $sql = "SELECT id, nombre, apellido, correo, telefono, direccion, 
                   ciudad, departamento, provincia, distrito
            FROM contactos 
            WHERE id = :id AND tipo = 'CLIENTE'
            LIMIT 1";
    
    return fetchOne($sql, ['id' => $clienteId]);
}
?>
