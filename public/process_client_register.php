<?php
session_start();

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /client-register.php');
    exit();
}

// Obtener y sanitizar datos del formulario
$name = trim($_POST['name'] ?? '');
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

// Validar campos requeridos
if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($password_confirm)) {
    $_SESSION['error'] = 'Por favor, completa todos los campos.';
    header('Location: /client-register.php');
    exit();
}

// Validar formato de email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'El formato del correo electrónico no es válido.';
    header('Location: /client-register.php');
    exit();
}

// Validar longitud de contraseña
if (strlen($password) < 6) {
    $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres.';
    header('Location: /client-register.php');
    exit();
}

// Validar que las contraseñas coincidan
if ($password !== $password_confirm) {
    $_SESSION['error'] = 'Las contraseñas no coinciden.';
    header('Location: /client-register.php');
    exit();
}

// Validar longitud del nombre
if (strlen($name) < 3) {
    $_SESSION['error'] = 'El nombre debe tener al menos 3 caracteres.';
    header('Location: /client-register.php');
    exit();
}

try {
    // Incluir configuración de base de datos
    require_once __DIR__ . '/client_db_config.php';
    
    // Verificar si el email ya existe
    if (emailExists($email)) {
        $_SESSION['error'] = 'Este correo electrónico ya está registrado. Por favor, inicia sesión o usa otro correo.';
        header('Location: /client-register.php');
        exit();
    }
    
    // Hashear la contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Crear nuevo usuario
    $user_data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'password' => $password_hash
    ];
    
    $user_id = registerUser($user_data);
    
    if ($user_id) {
        // Establecer variables de sesión
        $_SESSION['client_id'] = $user_id;
        $_SESSION['client_name'] = $name;
        $_SESSION['client_email'] = $email;
        $_SESSION['client_phone'] = $phone;
        $_SESSION['client_logged_in'] = true;
        
        // Mensaje de éxito
        $_SESSION['success'] = '¡Cuenta creada exitosamente! Bienvenido a DelgadoPropiedades.';
        
        // Redirigir al portal de clientes
        header('Location: /client-portal.php');
        exit();
    } else {
        throw new Exception('No se pudo crear la cuenta.');
    }
    
} catch (Exception $e) {
    error_log("Error en registro de cliente: " . $e->getMessage());
    $_SESSION['error'] = 'Error al crear la cuenta. Por favor, intenta nuevamente.';
    header('Location: /client-register.php');
    exit();
}
?>
