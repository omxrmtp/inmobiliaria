<?php
session_start();
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="theme-color" content="#667eea">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Registro de Cliente - DelgadoPropiedades</title>
    
    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="images/propiedad.png" type="image/png">
    
    <!-- Estilos -->
    <link rel="stylesheet" href="css/client-portal.css">
</head>
<body>
    <div class="client-auth-page">
        <div class="auth-container">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1>Crear Cuenta</h1>
                <p>Únete a DelgadoPropiedades y encuentra tu hogar ideal</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert-ios alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>
            
            <form class="auth-form" action="process_client_register.php" method="POST" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="name">Nombre completo</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="form-control" 
                            placeholder="Juan Pérez"
                            required
                            autocomplete="name"
                        >
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control" 
                            placeholder="tu@email.com"
                            required
                            autocomplete="email"
                        >
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="phone">Teléfono</label>
                    <div class="input-wrapper">
                        <i class="fas fa-phone"></i>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            class="form-control" 
                            placeholder="+51 999 999 999"
                            required
                            autocomplete="tel"
                        >
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            placeholder="••••••••"
                            required
                            autocomplete="new-password"
                            minlength="6"
                        >
                        <button type="button" class="toggle-password-btn" onclick="togglePassword('password', 'toggleIcon1')">
                            <i class="fas fa-eye" id="toggleIcon1"></i>
                        </button>
                    </div>
                    <small style="color: var(--ios-gray-5); font-size: 0.85rem; margin-top: 4px; display: block;">
                        Mínimo 6 caracteres
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="password_confirm">Confirmar contraseña</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input 
                            type="password" 
                            id="password_confirm" 
                            name="password_confirm" 
                            class="form-control" 
                            placeholder="••••••••"
                            required
                            autocomplete="new-password"
                            minlength="6"
                        >
                        <button type="button" class="toggle-password-btn" onclick="togglePassword('password_confirm', 'toggleIcon2')">
                            <i class="fas fa-eye" id="toggleIcon2"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn-ios btn-primary-ios">
                    <i class="fas fa-user-plus"></i>
                    <span>Crear Cuenta</span>
                </button>
            </form>
            
            <div class="divider">
                <span>¿Ya tienes cuenta?</span>
            </div>
            
            <div class="auth-links">
                <a href="client-login.php" class="auth-link">
                    <i class="fas fa-sign-in-alt"></i> Iniciar sesión
                </a>
            </div>
            
            <div class="divider">
                <span>o</span>
            </div>
            
            <div class="auth-links">
                <a href="index.html" class="auth-link">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        function validateForm() {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            
            if (password !== passwordConfirm) {
                alert('Las contraseñas no coinciden. Por favor, verifica e intenta nuevamente.');
                return false;
            }
            
            if (password.length < 6) {
                alert('La contraseña debe tener al menos 6 caracteres.');
                return false;
            }
            
            return true;
        }
        
        // Animación de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.auth-container');
            container.classList.add('fade-in');
        });
    </script>
</body>
</html>
