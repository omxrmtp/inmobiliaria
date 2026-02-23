<?php
/**
 * Script para limpiar la sesión y forzar un nuevo login
 */
session_start();

// Limpiar todas las variables de sesión
$_SESSION = [];

// Destruir la cookie de sesión
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Destruir la sesión
session_destroy();

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Sesión Cerrada</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 500px;
        }
        h1 {
            color: #667eea;
            margin-bottom: 1rem;
            font-size: 2rem;
        }
        p {
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .success-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s;
            margin: 0.5rem;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .info-box {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 2rem 0;
            text-align: left;
        }
        .info-box h3 {
            color: #333;
            margin-top: 0;
            font-size: 1.1rem;
        }
        .info-box ul {
            margin: 0.5rem 0;
            padding-left: 1.5rem;
        }
        .info-box li {
            margin: 0.5rem 0;
            color: #666;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='success-icon'>✅</div>
        <h1>Sesión Cerrada Exitosamente</h1>
        <p>Se ha limpiado tu sesión anterior. Ahora puedes iniciar sesión nuevamente con un token actualizado.</p>
        
        <div class='info-box'>
            <h3>¿Por qué necesitas hacer esto?</h3>
            <ul>
                <li>Se actualizó el formato del token JWT</li>
                <li>El sistema ahora usa 'rol' en lugar de 'tipo'</li>
                <li>El nuevo token es compatible con el backend Express</li>
            </ul>
        </div>
        
        <a href='client-login.php' class='btn'>🔑 Iniciar Sesión Nuevamente</a>
        <a href='/' class='btn'>🏠 Volver al Inicio</a>
    </div>
    
    <script>
        // Limpiar también localStorage
        localStorage.clear();
        console.log('✅ Sesión y localStorage limpiados');
    </script>
</body>
</html>";
?>
