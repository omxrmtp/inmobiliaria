<?php
session_start();
require_once 'config/database.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $terms = isset($_POST['terms']) ? true : false;
    
    // Validate input
    if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = 'Por favor, completa todos los campos.';
        header('Location: login.php');
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Por favor, ingresa un correo electrónico válido.';
        header('Location: login.php');
        exit();
    }
    
    if (strlen($password) < 6) {
        $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres.';
        header('Location: login.php');
        exit();
    }
    
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Las contraseñas no coinciden.';
        header('Location: login.php');
        exit();
    }
    
    if (!$terms) {
        $_SESSION['error'] = 'Debes aceptar los términos y condiciones para registrarte.';
        header('Location: login.php');
        exit();
    }
    
    try {
        // Connect to database
        $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = 'Este correo electrónico ya está registrado.';
            header('Location: login.php');
            exit();
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role, created_at) VALUES (:name, :email, :phone, :password, 'user', NOW())");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();
        
        // Set success message
        $_SESSION['success'] = 'Registro exitoso. Ahora puedes iniciar sesión.';
        
        // Redirect to login page
        header('Location: login.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error de registro: ' . $e->getMessage();
        header('Location: login.php');
        exit();
    }
}
?>
