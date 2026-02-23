<?php
session_start();

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
        // Connect to database
        $conn = connectDB();
        
        // Prepare and execute query
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        // Check if user exists
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify password (supports both hash and plain text)
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                // Set remember me cookie if checked
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $expires = time() + (30 * 24 * 60 * 60); // 30 days
                    
                    // Store token in database
                    $stmt = $conn->prepare("UPDATE users SET remember_token = :token, token_expires = :expires WHERE id = :id");
                    $stmt->bindParam(':token', $token);
                    $stmt->bindParam(':expires', date('Y-m-d H:i:s', $expires));
                    $stmt->bindParam(':id', $user['id']);
                    $stmt->execute();
                    
                    // Set cookie
                    setcookie('remember_token', $token, $expires, '/', '', false, true);
                }
                
                // Redirect based on role
                // Usar rutas absolutas relativas al DocumentRoot (public/)
                if ($user['role'] === 'admin') {
                    header('Location: /admin/index.php');
                } else {
                    header('Location: /index.html');
                }
                exit();
            } else {
                $_SESSION['error'] = 'Correo electrónico o contraseña incorrectos.';
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
