<?php
/**
 * Forzar logout completo y limpiar todo
 */

// Iniciar sesión
session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Destruir la cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Limpiar cualquier cookie adicional
setcookie('client_token', '', time() - 3600, '/');
setcookie('PHPSESSID', '', time() - 3600, '/');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Forzado</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 500px;
        }
        h1 {
            color: #2d3748;
            margin-bottom: 20px;
        }
        .success {
            color: #48bb78;
            font-size: 4rem;
            margin-bottom: 20px;
        }
        p {
            color: #718096;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .btn {
            background: #FCBA00;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #E0A600;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(252, 186, 0, 0.3);
        }
        .steps {
            background: #f7fafc;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }
        .steps h3 {
            color: #2d3748;
            margin-bottom: 15px;
        }
        .steps ol {
            margin-left: 20px;
            line-height: 1.8;
        }
        .steps li {
            color: #4a5568;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success">✅</div>
        <h1>Sesión Cerrada Completamente</h1>
        <p>Se han limpiado todas las sesiones, cookies y tokens almacenados.</p>
        
        <div class="steps">
            <h3>📋 Próximos Pasos:</h3>
            <ol>
                <li>Haz click en "Ir al Login"</li>
                <li>Ingresa tu email y contraseña</li>
                <li>El sistema generará un token nuevo y válido</li>
                <li>Podrás ver toda tu información del CRM</li>
            </ol>
        </div>
        
        <a href="/client-login.php" class="btn">🔑 Ir al Login</a>
        
        <p style="margin-top: 30px; font-size: 0.9rem; color: #a0aec0;">
            Si sigues teniendo problemas, limpia el caché del navegador (Ctrl+Shift+Delete)
        </p>
    </div>
    
    <script>
        // Limpiar localStorage y sessionStorage
        localStorage.clear();
        sessionStorage.clear();
        console.log('✅ localStorage y sessionStorage limpiados');
    </script>
</body>
</html>
