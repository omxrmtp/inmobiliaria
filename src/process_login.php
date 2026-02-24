<?php
session_start();

// Cargar configuración principal de base de datos (getDBConnection)
require_once __DIR__ . '/../config/database.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;
    
    // Validate input
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Por favor, completa todos los campos.';
        // Redirigir a la página de login en el DocumentRoot (public/)
        header('Location: /login.php');
        exit();
    }
    
    try {
        // Conectar a la base de datos usando helper global
        $conn = getDBConnection();

        if (!$conn) {
            throw new PDOException('No se pudo establecer conexión con la base de datos.');
        }
        
        // Preparar y ejecutar consulta contra tabla usuarios (esquema real)
        $stmt = $conn->prepare("
            SELECT 
                id,
                nombre,
                correo,
                contrasena,
                rol,
                telefono,
                activo
            FROM usuarios
            WHERE correo = :correo
            LIMIT 1
        ");
        $stmt->bindParam(':correo', $email);
        $stmt->execute();
        
        // Check if user exists
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar si el usuario está activo (si la columna está presente)
            if (isset($user['activo']) && (int)$user['activo'] !== 1) {
                $_SESSION['error'] = 'Usuario inactivo. Contacta con el administrador.';
            } else {
                // Verify password (supports both hash and plain text)
                if (password_verify($password, $user['contrasena']) || $password === $user['contrasena']) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['nombre'];
                    $_SESSION['user_email'] = $user['correo'];
                    $_SESSION['user_role'] = $user['rol'];
                    
                    // Opcional: lógica "recordarme" desactivada porque la tabla no tiene campos remember_token/token_expires
                    
                    // Redirect based on role
                    // Usar rutas absolutas relativas al DocumentRoot (public/)
                    if ($user['rol'] === 'ADMIN') {
                        header('Location: /admin/index.php');
                    } else {
                        header('Location: /index.html');
                    }
                    exit();
                } else {
                    $_SESSION['error'] = 'Correo electrónico o contraseña incorrectos.';
                }
            }
        } else {
            $_SESSION['error'] = 'Correo electrónico o contraseña incorrectos.';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error de conexión: ' . $e->getMessage();
    }
    
    // Volver a la página de login con error (ruta absoluta desde DocumentRoot)
    header('Location: /login.php');
    exit();
}
?>
