<?php
session_start();

require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/config/database.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $terms = isset($_POST['terms']) ? true : false;
    
    // Validate input
    if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = 'Por favor, completa todos los campos.';
        header('Location: /login.php');
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Por favor, ingresa un correo electrónico válido.';
        header('Location: /login.php');
        exit();
    }
    
    if (strlen($password) < 6) {
        $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres.';
        header('Location: /login.php');
        exit();
    }
    
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Las contraseñas no coinciden.';
        header('Location: /login.php');
        exit();
    }
    
    if (!$terms) {
        $_SESSION['error'] = 'Debes aceptar los términos y condiciones para registrarte.';
        header('Location: /login.php');
        exit();
    }
    
    try {
        // Connect to database (PDO) usando configuración de src/config
        $conn = connectDB();
        if (!$conn) {
            throw new PDOException('No se pudo establecer conexión con la base de datos.');
        }

        // Comprobar si el correo ya existe en tabla usuarios (columna correo)
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = :correo");
        $stmt->bindParam(':correo', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = 'Este correo electrónico ya está registrado.';
            header('Location: /login.php');
            exit();
        }
        
        // Generar UUID para id (mismo formato que leads/otros)
        $id = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Rol por defecto para usuarios registrados vía formulario (ajustable a necesidad)
        $rol = 'AGENTE';
        
        // Insertar en tabla usuarios mapeando columnas reales
        $stmt = $conn->prepare("
            INSERT INTO usuarios (
                id,
                nombre,
                correo,
                telefono,
                contrasena,
                rol,
                avatar,
                activo,
                creadoEn,
                actualizadoEn
            ) VALUES (
                :id,
                :nombre,
                :correo,
                :telefono,
                :contrasena,
                :rol,
                :avatar,
                1,
                NOW(3),
                NOW(3)
            )
        ");
        
        $avatar = null;
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $name);
        $stmt->bindParam(':correo', $email);
        $stmt->bindParam(':telefono', $phone);
        $stmt->bindParam(':contrasena', $hashed_password);
        $stmt->bindParam(':rol', $rol);
        $stmt->bindParam(':avatar', $avatar);
        
        $stmt->execute();
        
        // Set success message
        $_SESSION['success'] = 'Registro exitoso. Ahora puedes iniciar sesión.';
        
        // Redirect to login page
        header('Location: /login.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error de registro: ' . $e->getMessage();
        header('Location: /login.php');
        exit();
    }
}
?>
