<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_POST['value'])) {
    $name = $_POST['name'];
    $value = $_POST['value'];
    
    // Guardar en la sesión
    $_SESSION[$name] = $value;
    
    // Responder con éxito
    echo json_encode(['success' => true]);
} else {
    // Responder con error
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
}
?>
