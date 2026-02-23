<?php
session_start();

// Limpiar todas las variables de sesión del cliente
unset($_SESSION['client_id']);
unset($_SESSION['client_name']);
unset($_SESSION['client_email']);
unset($_SESSION['client_phone']);
unset($_SESSION['client_logged_in']);

// Destruir la sesión completamente
session_destroy();

// Iniciar nueva sesión para mostrar mensaje
session_start();
$_SESSION['success'] = 'Has cerrado sesión exitosamente.';

// Redirigir a la página de login
header('Location: /client-login.php');
exit();
?>
