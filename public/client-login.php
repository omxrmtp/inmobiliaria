<?php
/**
 * Página de login para clientes del CRM - Formulario HTML
 */
session_start();

// Si ya está autenticado, redirigir al portal
if (isset($_SESSION['token'])) {
    header('Location: /client-portal.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portal CRM Delgado</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --brand: #fcba00;
            --brand-dark: #d89d00;
            --bg: #f4f5f7;
            --card: #ffffff;
            --text: #0f172a;
            --subtext: #64748b;
            --border: rgba(15, 23, 42, 0.08);
            --shadow: 0 8px 24px rgba(15,23,42,.08), 0 1px 0 rgba(15,23,42,.04);
            --shadow-lg: 0 14px 40px rgba(15,23,42,.12), 0 2px 0 rgba(15,23,42,.05);
            --radius: 16px;
            --radius-lg: 24px;
            --transition: all .25s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Inter', sans-serif;
            background: radial-gradient(1200px 600px at 10% -10%, #ffffff 20%, #f6f7fb 60%, #eef1f6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: var(--text);
        }

        /* Decoración suave */
        .bg-accent {
            position: absolute; inset: 0; pointer-events: none;
            background:
              radial-gradient(500px 300px at 80% 10%, rgba(252,186,0,.08), transparent 60%),
              radial-gradient(600px 400px at 10% 90%, rgba(15,23,42,.05), transparent 60%);
        }

        .login-container {
            width: 100%;
            max-width: 460px;
            background: var(--card);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            padding: 2.5rem 2rem;
            animation: slideIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            z-index: 1;
            border: 1px solid var(--border);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .logo {
            text-align: center;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .logo-icon {
            width: 80px; height: 80px; margin: 0 auto 1rem;
            background: var(--brand);
            border-radius: 20px; display:flex; align-items:center; justify-content:center;
            font-size: 2rem; color: #111;
            box-shadow: inset 0 -1px 0 rgba(255,255,255,.35), 0 12px 24px rgba(15,23,42,.12);
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .logo-text { font-weight: 800; color: var(--text); font-size: 1.5rem; letter-spacing: -0.3px; }

        h1 { font-size: 1.9rem; font-weight: 800; color: var(--text); margin-bottom: .25rem; text-align:center; letter-spacing:-0.3px; }

        .subtitle { color: var(--subtext); text-align:center; margin-bottom: 2rem; font-size: .98rem; }

        .form-group {
            margin-bottom: 1.25rem;
            position: relative;
        }

        label { display:flex; align-items:center; color: var(--subtext); font-weight:600; margin-bottom:.5rem; font-size:.9rem; letter-spacing:-0.2px; }

        label i { margin-right:.5rem; color: var(--brand); }

        .input-wrapper {
            position: relative;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 0.9rem 1rem; padding-right: 3rem;
            border: 1px solid var(--border);
            border-radius: 12px;
            font-size: 1rem;
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Inter', sans-serif;
            transition: var(--transition);
            background: #fff;
            color: var(--text);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.5);
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--subtext);
            font-size: 1.1rem;
            user-select: none;
            transition: var(--transition);
            z-index: 2;
        }

        .password-toggle:hover {
            color: var(--primary-color);
            transform: translateY(-50%) scale(1.1);
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--brand);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(252, 186, 0, 0.2);
            transform: translateY(-1px);
        }

        .form-error { background: rgba(231,76,60,0.08); color:#b42318; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.25rem; border-left: 4px solid #e74c3c; display:none; font-size:.9rem; font-weight:500; animation: shake .4s cubic-bezier(.36,.07,.19,.97); }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .form-error.show {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn { width:100%; padding: .95rem 1.1rem; background: linear-gradient(180deg,#fff 0%, #f6f7f9 100%); color:#111; border:1px solid rgba(17,17,17,.85); border-radius: 999px; font-weight:700; font-size:1rem; cursor:pointer; transition: var(--transition); margin-top:.5rem; box-shadow: 0 2px 0 rgba(17,17,17,.25), 0 8px 20px rgba(15,23,42,.08); }

        .btn:hover:not(:disabled) { background: linear-gradient(180deg,#111 0%, #111 100%); color:#fff; border-color:#111; box-shadow: 0 2px 0 rgba(17,17,17,.35), 0 12px 24px rgba(15,23,42,.14); transform: translateY(-1px); }

        .btn:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .spinner {
            border: 2.5px solid rgba(255, 255, 255, 0.3);
            border-top: 2.5px solid white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            animation: spin 0.8s linear infinite;
            display: inline-block;
            margin-right: 0.5rem;
            vertical-align: middle;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Efecto de resplandor al hacer focus */
        .form-group:focus-within label {
            color: var(--primary-color);
        }

        .back-button { display:block; text-align:center; margin-top: 1rem; color: var(--subtext); text-decoration:none; font-weight:600; transition: var(--transition); }
        .back-button:hover { color: var(--brand-dark); }

        @media (max-width: 480px) {
            .login-container { padding: 2rem 1.25rem; border-radius: 20px; }
            h1 { font-size: 1.65rem; }
            .logo-icon { width:72px; height:72px; font-size:1.8rem; }
        }

        /* Animación de entrada para los elementos del formulario */
        .form-group {
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) backwards;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .btn { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.3s backwards; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="bg-accent"></div>
    <div class="login-container">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-building"></i>
            </div>
            <div class="logo-text">DelgadoPropiedades</div>
        </div>

        <h1>Bienvenido</h1>
        <p class="subtitle">Accede a tu portal de cliente</p>

        <form id="loginForm">
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="tu@email.com"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> Contraseña
                </label>
                <div class="input-wrapper">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="••••••••"
                        required
                    >
                    <i class="fas fa-eye password-toggle" id="togglePassword" onclick="togglePasswordVisibility()"></i>
                </div>
            </div>

            <div class="form-error" id="errorMsg"></div>

            <button type="submit" class="btn" id="loginBtn">
                <span id="loginText">Iniciar Sesión</span>
            </button>
        </form>
        <a href="index.php" class="back-button">Volver al Inicio</a>
    </div>

    <script>
        const form = document.getElementById('loginForm');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const loginBtn = document.getElementById('loginBtn');
        const errorMsg = document.getElementById('errorMsg');

        // Toggle password visibility
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePassword');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const email = emailInput.value.trim();
            const password = passwordInput.value;

            errorMsg.classList.remove('show');
            errorMsg.textContent = '';

            if (!email || !password) {
                showError('❌ Por favor completa todos los campos');
                return;
            }

            loginBtn.disabled = true;
            const originalText = document.getElementById('loginText').textContent;
            document.getElementById('loginText').innerHTML = '<span class="spinner"></span>Autenticando...';

            try {
                const response = await fetch('process_client_login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();
                
                console.log('Response status:', response.status);
                console.log('Response data:', data);

                if (!response.ok || !data.success) {
                    let errorMessage = data.error || 'Error al iniciar sesión';
                    
                    // Diferenciar entre cuenta desactivada y credenciales inválidas
                    if (response.status === 403) {
                        errorMessage = '⚠️ ' + errorMessage;
                    } else {
                        errorMessage = '❌ ' + errorMessage;
                    }
                    
                    const debugInfo = data.debug ? ` (${data.debug})` : '';
                    showError(errorMessage + debugInfo);
                    loginBtn.disabled = false;
                    document.getElementById('loginText').textContent = originalText;
                    return;
                }

                // Guardar token en localStorage
                localStorage.setItem('token', data.token);
                localStorage.setItem('client', JSON.stringify(data.cliente));

                // Redirigir al portal de cliente
                setTimeout(() => {
                    window.location.href = '/client-portal.php';
                }, 300);

            } catch (error) {
                console.error('Error:', error);
                showError('❌ Error al conectar con el servidor');
                loginBtn.disabled = false;
                document.getElementById('loginText').textContent = originalText;
            }
        });

        function showError(message) {
            errorMsg.textContent = message;
            errorMsg.classList.add('show');
        }
    </script>
</body>
</html>
