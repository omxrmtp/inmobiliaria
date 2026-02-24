<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['client_id'])) {
    header('Location: /client-login.php');
    exit();
}

// Obtener información del cliente
$client_name = $_SESSION['client_name'] ?? 'Cliente';
$client_email = $_SESSION['client_email'] ?? '';
$client_token = $_SESSION['token'] ?? null; // Token JWT para APIs del backend

// Manejar mensajes de éxito
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['success']); // Limpiar mensaje después de mostrarlo

// Debug: verificar que el token existe
if (!$client_token) {
    error_log("ADVERTENCIA: No hay token en la sesión para el cliente: " . $_SESSION['client_id']);
    error_log("Sesión completa: " . json_encode($_SESSION));
}

// Obtener iniciales para el avatar
$initials = '';
$name_parts = explode(' ', $client_name);
if (count($name_parts) >= 2) {
    $initials = strtoupper(substr($name_parts[0], 0, 1) . substr($name_parts[1], 0, 1));
} else {
    $initials = strtoupper(substr($client_name, 0, 2));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="theme-color" content="#FCBA00">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Mi Portal - DelgadoPropiedades</title>
    
    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="images/propiedad.png" type="image/png">
    
    <style>
        :root {
            --primary: #FCBA00;
            --primary-dark: #e0a800;
            --primary-light: #ffd54f;
            --secondary: #303030;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --white: #ffffff;
            --shadow-sm: 0 1px 2px rgba(15,23,42,.06);
            --shadow-md: 0 8px 24px rgba(15,23,42,.08), 0 1px 0 rgba(15,23,42,.04);
            --shadow-lg: 0 14px 40px rgba(15,23,42,.12), 0 2px 0 rgba(15,23,42,.05);
            --shadow-xl: 0 22px 48px rgba(15,23,42,.16), 0 3px 0 rgba(15,23,42,.06);
            --shadow-2xl: 0 30px 64px rgba(15,23,42,.18), 0 4px 0 rgba(15,23,42,.08);
            --radius-sm: .5rem;
            --radius-md: .75rem;
            --radius-lg: 1rem;
            --radius-xl: 1.25rem;
            --radius-2xl: 1.75rem;
            --transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
        }


        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: radial-gradient(1200px 600px at 10% -10%, #ffffff 20%, #f6f7fb 60%, #eef1f6 100%);
            color: var(--gray-800);
            line-height: 1.6;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Header Moderno */
        .portal-header {
            background: rgba(255,255,255,.85);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(14px) saturate(140%);
            -webkit-backdrop-filter: blur(14px) saturate(140%);
            border-bottom: 1px solid var(--gray-200);
        }

        .portal-header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .portal-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--secondary);
            font-weight: 700;
            font-size: 1.25rem;
            transition: var(--transition);
        }

        .portal-logo i {
            color: var(--primary);
            font-size: 1.75rem;
        }

        .portal-logo:hover {
            transform: translateY(-2px);
        }

        .portal-user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
            box-shadow: var(--shadow-md);
        }

        .user-name {
            font-weight: 600;
            color: var(--gray-700);
            display: none;
        }

        @media (min-width: 640px) {
            .user-name {
                display: block;
            }
        }

        .btn-logout {
            padding: 0.5rem 1rem;
            background: var(--gray-100);
            color: var(--gray-700);
            border-radius: var(--radius-lg);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-logout:hover {
            background: var(--gray-200);
            transform: translateY(-1px);
        }

        .btn-logout-text {
            display: none;
        }

        @media (min-width: 640px) {
            .btn-logout-text {
                display: inline;
            }
        }

        /* Main Content */
        .portal-main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        /* Welcome Section */
        .welcome-section {
            margin-bottom: 2rem;
            text-align: center;
        }

        .welcome-section h1 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--secondary) 0%, var(--gray-600) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-section p {
            color: var(--gray-600);
            font-size: 1rem;
        }

        @media (min-width: 768px) {
            .welcome-section h1 {
                font-size: 2.5rem;
            }
            .welcome-section p {
                font-size: 1.125rem;
            }
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border: 1px solid var(--gray-200);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 100%);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .stat-card-icon {
            width: 50px;
            height: 50px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-card-icon.primary {
            background: linear-gradient(135deg, rgba(252, 186, 0, 0.1) 0%, rgba(252, 186, 0, 0.2) 100%);
            color: var(--primary);
        }

        .stat-card-icon.success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0.2) 100%);
            color: var(--success);
        }

        .stat-card-icon.info {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.2) 100%);
            color: var(--info);
        }

        .stat-card-icon.warning {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(245, 158, 11, 0.2) 100%);
            color: var(--warning);
        }

        .stat-card h3 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 0.25rem;
        }

        .stat-card p {
            color: var(--gray-600);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .card-loading {
            text-align: center;
            padding: 1rem;
            color: var(--gray-400);
        }

        /* Dashboard Cards */
        .dashboard-card {
            background: var(--white);
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-md);
            padding: 2rem;
            margin-bottom: 2rem;
            transition: var(--transition);
            border: 1px solid var(--gray-200);
        }

        .dashboard-card:hover {
            box-shadow: var(--shadow-lg);
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--gray-100);
        }

        .section-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--secondary);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-header h2 i {
            color: var(--primary);
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 0.85rem 1.25rem;
            border-radius: 999px;
            border: none;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--gray-700);
            padding: 0.85rem 1.25rem;
            border-radius: 999px;
            border: 1px solid var(--gray-200);
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-secondary:hover {
            background: var(--gray-50);
            border-color: var(--gray-300);
        }

        /* Form Styles */
        .perfil-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .perfil-field {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .perfil-field label {
            font-weight: 600;
            color: var(--gray-700);
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .perfil-field label i {
            color: var(--primary);
        }

        .perfil-field p {
            color: var(--gray-800);
            font-size: 1rem;
            padding: 0.75rem;
            background: var(--gray-50);
            border-radius: var(--radius-md);
            word-break: break-word;
            overflow-wrap: break-word;
            word-wrap: break-word;
            white-space: normal;
            max-width: 100%;
        }
            border: 1px solid var(--gray-200);
        }

        .perfil-field input {
            padding: 0.75rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-lg);
            font-size: 1rem;
            font-family: inherit;
            transition: var(--transition);
            background: var(--white);
            word-break: break-word;
            overflow-wrap: break-word;
            word-wrap: break-word;
            white-space: normal;
            max-width: 100%;
            min-width: 0;
        }

        .perfil-field input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(252, 186, 0, 0.1);
        }

        /* Iframe Container */
        .tracking-iframe-container {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 10;
            min-height: 360px;
            border-radius: var(--radius-2xl);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            border: 2px solid var(--gray-100);
            background: linear-gradient(180deg,#eef2f6 0%,#e7ebf0 100%);
        }

        .tracking-iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: none;
            background: transparent;
        }

        .iframe-fallback {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: .75rem;
            color: var(--gray-700);
            padding: 1rem;
            text-align: center;
            background: linear-gradient(180deg,rgba(255,255,255,.65),rgba(255,255,255,.8));
        }

        .iframe-fallback .hint {
            font-size: .95rem;
            color: var(--gray-600);
        }

        @media (max-width: 640px) {
            .tracking-iframe-container { aspect-ratio: 3 / 4; min-height: 260px; border-radius: var(--radius-xl); }
        }

        /* Animations */
        .fade-in {
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) backwards;
        }

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

        /* Modal Testimonio */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(15,23,42,.45);
            display: none; align-items: flex-end; justify-content: center;
            z-index: 9999; padding: 1rem;
        }
        .modal-overlay.show { display: flex; }
        .modal-dialog {
            width: 100%; max-width: 640px; background: #fff; border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-xl); border: 1px solid var(--gray-200);
            transform: translateY(16px); opacity: 0; animation: modalIn .35s ease forwards;
        }
        @keyframes modalIn { to { transform: translateY(0); opacity: 1; } }
        .modal-header { display:flex; align-items:center; justify-content: space-between; gap:12px; padding: 1rem 1.25rem; border-bottom: 1px solid var(--gray-100); }
        .modal-header h3 { margin:0; font-weight:800; color: var(--secondary); letter-spacing:-.2px; }
        .modal-body { padding: 1rem 1.25rem 1.25rem; }
        .modal-close { background: transparent; border: 1px solid var(--gray-200); border-radius: 10px; padding: .45rem .6rem; cursor: pointer; color: var(--gray-700); }
        .modal-close:hover { background: var(--gray-50); }

        /* Scrollbar personalizado */
        #lista-testimonios-modal::-webkit-scrollbar {
            width: 8px;
        }
        #lista-testimonios-modal::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        #lista-testimonios-modal::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        #lista-testimonios-modal::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Loading Spinner */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .spinner {
            animation: spin 1s linear infinite;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .portal-main {
                padding: 1rem 0.75rem;
            }

            .dashboard-card {
                padding: 1.5rem 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .section-header h2 {
                font-size: 1.25rem;
            }
        }

        /* Toast Notifications */
        .toast {
            position: fixed;
            top: 5rem;
            right: 1rem;
            background: var(--white);
            padding: 1rem 1.5rem;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-2xl);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            z-index: 9999;
            animation: slideInRight 0.3s ease-out;
            border-left: 4px solid var(--success);
        }

        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast.error {
            border-left-color: var(--danger);
        }

        .toast.warning {
            border-left-color: var(--warning);
        }

        .toast.info {
            border-left-color: var(--info);
        }
    </style>
</head>
<body class="client-portal">
    <!-- Header -->
    <header class="portal-header">
        <div class="portal-header-content">
            <a href="client-portal.php" class="portal-logo">
                <i class="fas fa-home"></i>
                <span class="portal-logo-text">DelgadoPropiedades</span>
            </a>
            
            <div class="portal-user-menu">
                <div class="user-info">
                    <div class="user-avatar"><?php echo $initials; ?></div>
                    <span class="user-name"><?php echo htmlspecialchars($client_name); ?></span>
                </div>
                <a href="client-logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="btn-logout-text">Cerrar Sesión</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Mensajes de éxito -->
    <?php if (!empty($success_message)): ?>
        <div class="toast success" style="position: fixed; top: 5rem; right: 1rem; background: var(--white); padding: 1rem 1.5rem; border-radius: var(--radius-xl); box-shadow: var(--shadow-2xl); display: flex; align-items: center; gap: 0.75rem; z-index: 9999; animation: slideInRight 0.3s ease-out; border-left: 4px solid var(--success);">
            <i class="fas fa-check-circle" style="color: var(--success); font-size: 1.25rem;"></i>
            <span><?php echo htmlspecialchars($success_message); ?></span>
        </div>
        <script>
            // Auto-ocultar el mensaje después de 5 segundos
            setTimeout(() => {
                const toast = document.querySelector('.toast.success');
                if (toast) {
                    toast.style.animation = 'slideInRight 0.3s ease-out reverse';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 5000);
        </script>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="portal-main">
        <!-- Welcome Section -->
        <section class="welcome-section fade-in">
            <h1>¡Bienvenido, <?php echo htmlspecialchars(explode(' ', $client_name)[0]); ?>! 👋</h1>
            <p>Gestiona tu información y realiza seguimiento de tus trámites</p>
        </section>

        <!-- Testimonios -->
        <section class="dashboard-card fade-in" id="testimonios-section" style="animation-delay: 0.7s; display:none;">
            <div class="section-header">
                <h2><i class="fas fa-comment-dots"></i> Testimonios</h2>
            </div>
            <div style="display:flex; justify-content:flex-end; margin-bottom: 0.5rem;">
                <button class="btn-primary" onclick="openModalTestimonio()"><i class="fas fa-plus"></i> Nuevo testimonio</button>
            </div>
            
        </section>

        <!-- Modal Testimonio -->
        <div class="modal-overlay" id="t-modal" aria-hidden="true">
            <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="t-modal-title">
                <div class="modal-header">
                    <h3 id="t-modal-title"><i class="fas fa-comment-dots"></i> Enviar Testimonio</h3>
                    <button class="modal-close" onclick="closeModalTestimonio()" aria-label="Cerrar"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <form id="form-testimonio" onsubmit="enviarTestimonio(event)">
                        <!-- Campo oculto para el nombre (se auto-llena desde sesión) -->
                        <input type="hidden" id="t-nombre" value="<?php echo htmlspecialchars($client_name); ?>">
                        
                        <div class="perfil-grid">
                            <div class="perfil-field">
                                <label><i class="fas fa-star"></i> Calificación</label>
                                <div id="estrellas-calificacion" style="display: flex; gap: 0.5rem; font-size: 1.5rem;">
                                    <span class="estrella" data-valor="1" style="cursor: pointer; color: #d1d5db; transition: color 0.2s;">★</span>
                                    <span class="estrella" data-valor="2" style="cursor: pointer; color: #d1d5db; transition: color 0.2s;">★</span>
                                    <span class="estrella" data-valor="3" style="cursor: pointer; color: #d1d5db; transition: color 0.2s;">★</span>
                                    <span class="estrella" data-valor="4" style="cursor: pointer; color: #d1d5db; transition: color 0.2s;">★</span>
                                    <span class="estrella" data-valor="5" style="cursor: pointer; color: #d1d5db; transition: color 0.2s;">★</span>
                                </div>
                                <input type="hidden" id="t-calificacion" value="5">
                                <span id="valor-calificacion" style="font-size: 0.875rem; color: var(--gray-600); margin-top: 0.25rem;">5 estrellas</span>
                            </div>
                            <div class="perfil-field" style="grid-column: 1 / -1;">
                                <label><i class="fas fa-quote-left"></i> Tu testimonio (mínimo 10 caracteres)</label>
                                <textarea id="t-testimonio" rows="4" style="width:100%; padding: .75rem 1rem; border: 2px solid var(--gray-200); border-radius: var(--radius-lg); font-family: inherit;" required oninput="validarTestimonio()"></textarea>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                                    <span id="contador-caracteres" style="font-size: 0.875rem; color: var(--gray-600);">0 / 10 caracteres</span>
                                    <span id="advertencia-caracteres" style="font-size: 0.875rem; color: #ef4444; display: none;">⚠️ Mínimo 10 caracteres requeridos</span>
                                </div>
                            </div>
                        </div>
                        <div style="display:flex; gap:12px; margin-top: 16px; justify-content:flex-end; flex-wrap:wrap;">
                            <button type="button" class="btn-secondary" onclick="closeModalTestimonio()">Cancelar</button>
                            <button type="submit" class="btn-primary"><i class="fas fa-paper-plane"></i> Enviar</button>
                        </div>
                    </form>
                    <div class="section-header" style="margin-top: 1rem; padding-bottom: .5rem;">
                        <h2 style="font-size:1.1rem"><i class="fas fa-list"></i> Últimos testimonios</h2>
                    </div>
                    <div id="lista-testimonios-modal" style="max-height: 300px; overflow-y: auto; padding-right: 0.5rem; border-radius: var(--radius-lg);">
                        <div class="card-loading"><i class="fas fa-spinner spinner"></i> Cargando testimonios...</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid fade-in" style="animation-delay: 0.1s;">

            <div class="stat-card" id="testimonios-card" style="cursor: pointer;" onclick="openModalTestimonio()">
                <div class="stat-card-icon warning">
                    <i class="fas fa-star"></i>
                </div>
                <h3>⭐</h3>
                <p>Deja tu Testimonio</p>
            </div>
        </div>

        <!-- Mi Información Personal -->
        <section class="dashboard-card fade-in" style="animation-delay: 0.2s;">
            <div class="section-header">
                <h2><i class="fas fa-user-circle"></i> Mi Información Personal</h2>
                <button onclick="toggleEditarPerfil()" class="btn-primary" id="btn-editar">
                    <i class="fas fa-edit"></i> Editar
                </button>
            </div>
            
            <div id="perfil-view">
                <div class="perfil-grid">
                    <div class="perfil-field">
                        <label><i class="fas fa-user"></i> Nombre Completo</label>
                        <p id="view-nombre">-</p>
                    </div>
                    <div class="perfil-field">
                        <label><i class="fas fa-envelope"></i> Correo Electrónico</label>
                        <p id="view-email">-</p>
                    </div>
                    <div class="perfil-field">
                        <label><i class="fas fa-phone"></i> Teléfono</label>
                        <p id="view-telefono">-</p>
                    </div>
                    <div class="perfil-field">
                        <label><i class="fas fa-id-card"></i> DNI</label>
                        <p id="view-dni">-</p>
                    </div>
                    <div class="perfil-field">
                        <label><i class="fas fa-map-marker-alt"></i> Dirección</label>
                        <p id="view-direccion">-</p>
                    </div>
                    <div class="perfil-field">
                        <label><i class="fas fa-city"></i> Ciudad</label>
                        <p id="view-ciudad">-</p>
                    </div>
                </div>
                <div class="card-loading">
                    <i class="fas fa-spinner spinner"></i> Cargando información...
                </div>
            </div>
            
            <div id="perfil-edit" style="display: none;">
                <form id="form-perfil" onsubmit="guardarPerfil(event)">
                    <div class="perfil-grid">
                        <div class="perfil-field">
                            <label><i class="fas fa-user"></i> Nombre</label>
                            <input type="text" id="edit-nombre" required>
                        </div>
                        <div class="perfil-field">
                            <label><i class="fas fa-user"></i> Apellido</label>
                            <input type="text" id="edit-apellido" required>
                        </div>
                        <div class="perfil-field">
                            <label><i class="fas fa-phone"></i> Teléfono</label>
                            <input type="tel" id="edit-telefono" required>
                        </div>
                        <div class="perfil-field">
                            <label><i class="fas fa-id-card"></i> DNI</label>
                            <input type="text" id="edit-dni" maxlength="8" pattern="[0-9]{8}">
                        </div>
                        <div class="perfil-field" style="grid-column: 1 / -1;">
                            <label><i class="fas fa-map-marker-alt"></i> Dirección</label>
                            <input type="text" id="edit-direccion">
                        </div>
                        <div class="perfil-field">
                            <label><i class="fas fa-city"></i> Ciudad</label>
                            <input type="text" id="edit-ciudad">
                        </div>
                        <div class="perfil-field">
                            <label><i class="fas fa-map"></i> Provincia</label>
                            <input type="text" id="edit-provincia">
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px; margin-top: 24px; justify-content: flex-end; flex-wrap: wrap;">
                        <button type="button" onclick="toggleEditarPerfil()" class="btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Seguimiento de Trámite MiVivienda -->
        <section class="dashboard-card fade-in" style="animation-delay: 0.3s;">
            <div class="section-header">
                <h2><i class="fas fa-home"></i> Seguimiento de Trámite MiVivienda</h2>
            </div>
            <div style="background: linear-gradient(135deg, rgba(252, 186, 0, 0.1) 0%, rgba(252, 186, 0, 0.05) 100%); border: 2px solid var(--primary); border-radius: var(--radius-xl); padding: 2rem; text-align: center;">
                <div style="margin-bottom: 1.5rem;">
                    <i class="fas fa-external-link-alt" style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--secondary); margin-bottom: 0.5rem;">Portal Externo</h3>
                    <p style="color: var(--gray-600); font-size: 0.95rem; margin-bottom: 1rem;">
                        Por razones de seguridad, el portal de MiVivienda debe abrirse en una nueva pestaña. 
                        Consulta el estado de tu trámite de Techo Propio, Crédito MiVivienda y otros programas directamente.
                    </p>
                </div>
                <a 
                    href="https://www.mivivienda.com.pe/PORTALWEB/usuario-busca-viviendas/estados-tramite.aspx" 
                    target="_blank" 
                    rel="noopener noreferrer"
                    class="btn-primary"
                    style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 1rem 2rem; font-size: 1rem;"
                >
                    <i class="fas fa-external-link-alt"></i>
                    Abrir Portal MiVivienda
                </a>
                <p style="color: var(--gray-500); font-size: 0.85rem; margin-top: 1rem; font-style: italic;">
                    Se abrirá en una nueva pestaña de tu navegador
                </p>
            </div>
        </section>

        <!-- Mis Cotizaciones -->
        <section class="dashboard-card fade-in" id="cotizaciones-section" style="display: none; animation-delay: 0.4s;">
            <div class="section-header">
                <h2><i class="fas fa-calculator"></i> Mis Cotizaciones</h2>
            </div>
            <div id="cotizaciones-list"></div>
        </section>

        <!-- Mis Oportunidades -->
        <section class="dashboard-card fade-in" id="oportunidades-section" style="display: none; animation-delay: 0.5s;">
            <div class="section-header">
                <h2><i class="fas fa-chart-line"></i> Mis Oportunidades</h2>
            </div>
            <div id="oportunidades-list"></div>
        </section>

    </main>
    
    <script>
        // Usar la misma URL del portal para las APIs locales
        const API_URL = window.location.protocol + '//' + window.location.host + '/public';
        const TOKEN = '<?php echo $client_token ?? ''; ?>';
        const CLIENT_EMAIL = '<?php echo htmlspecialchars($client_email); ?>';
        
        let clienteData = null;
        
        document.addEventListener('DOMContentLoaded', function() {
            if (TOKEN) {
                loadCRMData();
                loadTestimonios();
            } else {
                showToast('⚠️ No hay sesión activa', 'warning');
                setTimeout(() => {
                    if (confirm('No hay sesión activa. ¿Ir al login?')) {
                        window.location.href = '/client-login.php';
                    }
                }, 2000);
            }

            // Intentar ocultar fallback si el iframe carga
            const miviIframe = document.getElementById('mivi-iframe');
            const miviFallback = document.getElementById('mivi-fallback');
            if (miviIframe && miviFallback) {
                miviIframe.addEventListener('load', () => {
                    // Si carga, escondemos overlay
                    miviFallback.style.display = 'none';
                });
                // Tras 5s, si no se ocultó, dejamos visible el fallback como CTA
                setTimeout(() => {
                    if (getComputedStyle(miviFallback).display !== 'none') {
                        miviFallback.style.display = 'flex';
                    }
                }, 5000);
            }

            // Fallback: enlazar apertura del modal por JS
            const tc = document.getElementById('testimonios-card');
            if (tc) tc.addEventListener('click', (e) => { e.preventDefault(); openModalTestimonio(); });
            const btnNuevo = document.querySelector('button.btn-primary[onclick="openModalTestimonio()"]');
            if (btnNuevo) btnNuevo.addEventListener('click', (e) => { e.preventDefault(); openModalTestimonio(); });
        });
        
        async function loadCRMData() {
            try {
                await Promise.all([
                    loadPerfilCliente(),
                    loadResumen(),
                    loadCotizaciones(),
                    loadOportunidades()
                ]);
            } catch (error) {
                console.error('Error al cargar datos del CRM:', error);
            }
        }
        
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
                <span>${message}</span>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideInRight 0.3s ease-out reverse';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // --- Modal Testimonios: utilidades y lógica ---
        function scrollToSection(id) {
            const el = document.getElementById(id);
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        async function loadTestimonios() {
            const cont = document.getElementById('lista-testimonios-modal');
            if (!cont) return;
            try {
                const resp = await fetch(`${API_URL}/portal-cliente/testimonios`, {
                    headers: { 'Authorization': `Bearer ${TOKEN}` }
                });
                if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
                const items = await resp.json();
                
                // Filtrar: solo testimonios del cliente actual que estén aprobados
                const testimoniosDelCliente = items.filter(t => 
                    t.correo === CLIENT_EMAIL && t.estado === 'APROBADO'
                );
                
                if (!Array.isArray(testimoniosDelCliente) || testimoniosDelCliente.length === 0) {
                    cont.innerHTML = '<div style="color: var(--gray-600);">Aún no tienes testimonios aprobados.</div>';
                    return;
                }
                cont.innerHTML = testimoniosDelCliente.slice(0,10).map(t => `
                    <div class="perfil-field" style="padding:1rem; background: var(--gray-50); border:1px solid var(--gray-200); border-radius: var(--radius-lg); margin-bottom:.75rem;">
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
                            <div style="font-weight:700; color:var(--gray-800);">${(t.nombre||'Anónimo')}</div>
                            <div style="color:#f59e0b; font-weight:700;">${'★'.repeat(t.calificacion||5)}${'☆'.repeat(5-(t.calificacion||5))}</div>
                        </div>
                        <div style="color:var(--gray-700); margin-top:.5rem;">${(t.testimonio||'')}</div>
                    </div>
                `).join('');
            } catch (e) {
                console.error('Error cargando testimonios', e);
                cont.innerHTML = '<div style="color:#b42318;">No se pudieron cargar los testimonios.</div>';
            }
        }

        function validarTestimonio() {
            const testimonio = document.getElementById('t-testimonio').value.trim();
            const contador = document.getElementById('contador-caracteres');
            const advertencia = document.getElementById('advertencia-caracteres');
            const btnEnviar = document.querySelector('#form-testimonio button[type="submit"]');
            const minimo = 10;
            
            contador.textContent = `${testimonio.length} / ${minimo} caracteres`;
            
            if (testimonio.length < minimo) {
                advertencia.style.display = 'inline';
                btnEnviar.disabled = true;
                btnEnviar.style.opacity = '0.5';
                btnEnviar.style.cursor = 'not-allowed';
            } else {
                advertencia.style.display = 'none';
                btnEnviar.disabled = false;
                btnEnviar.style.opacity = '1';
                btnEnviar.style.cursor = 'pointer';
            }
        }

        async function enviarTestimonio(ev) {
            ev.preventDefault();
            const btn = ev.target.querySelector('button[type="submit"]');
            const nombre = document.getElementById('t-nombre').value.trim();
            const calificacion = Number(document.getElementById('t-calificacion').value);
            const testimonio = document.getElementById('t-testimonio').value.trim();
            let email = '<?php echo htmlspecialchars($client_email); ?>';
            
            // Validación adicional
            if (testimonio.length < 10) {
                showToast('❌ El testimonio debe tener al menos 10 caracteres', 'error');
                return;
            }
            
            // Fallback: si email está vacío, usar el del localStorage o generar uno temporal
            if (!email || email.trim() === '') {
                email = localStorage.getItem('client_email') || 'cliente@delgadopropiedades.com';
            }
            
            try {
                btn.disabled = true;
                // Enviar a la API pública de testimonios
                const resp = await fetch(`${API_URL}/public/testimonios`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        nombre, 
                        email,
                        calificacion, 
                        testimonio,
                        foto: null
                    })
                });
                if (!resp.ok) {
                    const errorData = await resp.json();
                    throw new Error(errorData.error || `HTTP ${resp.status}`);
                }
                const data = await resp.json();
                showToast('✅ ' + (data.message || 'Gracias por tu testimonio. Será revisado por nuestro equipo.'));
                ev.target.reset();
                loadTestimonios();
                setTimeout(() => closeModalTestimonio(), 1500);
            } catch (e) {
                console.error('Error enviando testimonio', e);
                showToast('❌ ' + (e.message || 'No se pudo enviar tu testimonio'), 'error');
            } finally {
                btn.disabled = false;
            }
        }

        function inicializarEstrellas() {
            const estrellas = document.querySelectorAll('#estrellas-calificacion .estrella');
            const inputCalificacion = document.getElementById('t-calificacion');
            const valorCalificacion = document.getElementById('valor-calificacion');
            
            estrellas.forEach(estrella => {
                estrella.addEventListener('click', () => {
                    const valor = parseInt(estrella.dataset.valor);
                    inputCalificacion.value = valor;
                    valorCalificacion.textContent = `${valor} ${valor === 1 ? 'estrella' : 'estrellas'}`;
                    
                    // Actualizar color de estrellas
                    estrellas.forEach(e => {
                        if (parseInt(e.dataset.valor) <= valor) {
                            e.style.color = '#FCBA00';
                        } else {
                            e.style.color = '#d1d5db';
                        }
                    });
                });
                
                // Hover effect
                estrella.addEventListener('mouseover', () => {
                    const valor = parseInt(estrella.dataset.valor);
                    estrellas.forEach(e => {
                        if (parseInt(e.dataset.valor) <= valor) {
                            e.style.color = '#FCD34D';
                        } else {
                            e.style.color = '#d1d5db';
                        }
                    });
                });
            });
            
            // Restaurar color al salir del área de estrellas
            document.getElementById('estrellas-calificacion').addEventListener('mouseleave', () => {
                const valor = parseInt(inputCalificacion.value);
                estrellas.forEach(e => {
                    if (parseInt(e.dataset.valor) <= valor) {
                        e.style.color = '#FCBA00';
                    } else {
                        e.style.color = '#d1d5db';
                    }
                });
            });
        }

        function openModalTestimonio() {
            const overlay = document.getElementById('t-modal');
            if (!overlay) return;
            loadTestimonios();
            overlay.classList.add('show');
            overlay.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            overlay.addEventListener('click', (e) => { if (e.target === overlay) closeModalTestimonio(); }, { once: true });
            const escHandler = (ev) => { if (ev.key === 'Escape') { closeModalTestimonio(); document.removeEventListener('keydown', escHandler); } };
            document.addEventListener('keydown', escHandler);
            // Inicializar validación y estrellas
            validarTestimonio();
            inicializarEstrellas();
        }

        function closeModalTestimonio() {
            const overlay = document.getElementById('t-modal');
            if (!overlay) return;
            overlay.classList.remove('show');
            overlay.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }
        
        async function loadResumen() {
            try {
                const response = await fetch(`${API_URL}/portal-cliente/resumen`, {
                    headers: { 
                        'Authorization': `Bearer ${TOKEN}`,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                
                const data = await response.json();
                
                if (data.success && data.data) {
                    const stats = data.data.estadisticas;
                    document.getElementById('cotizaciones-count').textContent = stats.cotizacionesPendientes || 0;
                    document.getElementById('oportunidades-count').textContent = stats.oportunidadesActivas || 0;
                    document.getElementById('documentos-count').textContent = stats.documentosVerificados || 0;
                    document.querySelectorAll('.stat-card .card-loading').forEach(el => el.style.display = 'none');
                }
            } catch (error) {
                console.error('Error loading resumen:', error);
                document.querySelectorAll('.stat-card .card-loading').forEach(el => el.style.display = 'none');
            }
        }
        
        async function loadPerfilCliente() {
            try {
                const response = await fetch(`${API_URL}/portal-cliente/perfil`, {
                    headers: { 
                        'Authorization': `Bearer ${TOKEN}`,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                
                const data = await response.json();
                
                if (data.success && data.data) {
                    clienteData = data.data;
                    mostrarDatosPerfil(clienteData);
                    document.querySelector('#perfil-view .card-loading').style.display = 'none';
                }
            } catch (error) {
                console.error('Error loading perfil:', error);
                document.querySelector('#perfil-view .card-loading').style.display = 'none';
                showToast('❌ Error al cargar perfil', 'error');
            }
        }
        
        function mostrarDatosPerfil(cliente) {
            document.getElementById('view-nombre').textContent = 
                `${cliente.nombre || ''} ${cliente.apellido || ''}`.trim() || 'No especificado';
            document.getElementById('view-email').textContent = cliente.correo || 'No especificado';
            document.getElementById('view-telefono').textContent = cliente.telefono || 'No especificado';
            document.getElementById('view-dni').textContent = cliente.dni || 'No especificado';
            document.getElementById('view-direccion').textContent = cliente.direccion || 'No especificado';
            document.getElementById('view-ciudad').textContent = 
                `${cliente.ciudad || ''}, ${cliente.provincia || ''}`.trim().replace(/^,|,$/g, '') || 'No especificado';
        }
        
        function toggleEditarPerfil() {
            const viewDiv = document.getElementById('perfil-view');
            const editDiv = document.getElementById('perfil-edit');
            const btnEditar = document.getElementById('btn-editar');
            
            if (editDiv.style.display === 'none') {
                if (clienteData) {
                    document.getElementById('edit-nombre').value = clienteData.nombre || '';
                    document.getElementById('edit-apellido').value = clienteData.apellido || '';
                    document.getElementById('edit-telefono').value = clienteData.telefono || '';
                    document.getElementById('edit-dni').value = clienteData.dni || '';
                    document.getElementById('edit-direccion').value = clienteData.direccion || '';
                    document.getElementById('edit-ciudad').value = clienteData.ciudad || '';
                    document.getElementById('edit-provincia').value = clienteData.provincia || '';
                }
                viewDiv.style.display = 'none';
                editDiv.style.display = 'block';
                btnEditar.innerHTML = '<i class="fas fa-times"></i> Cancelar';
                btnEditar.classList.remove('btn-primary');
                btnEditar.classList.add('btn-secondary');
            } else {
                viewDiv.style.display = 'block';
                editDiv.style.display = 'none';
                btnEditar.innerHTML = '<i class="fas fa-edit"></i> Editar';
                btnEditar.classList.remove('btn-secondary');
                btnEditar.classList.add('btn-primary');
            }
        }
        
        async function guardarPerfil(event) {
            event.preventDefault();
            
            if (!TOKEN) {
                showToast('❌ Error: No hay sesión activa', 'error');
                return;
            }
            
            const btnSubmit = event.target.querySelector('button[type="submit"]');
            const originalText = btnSubmit.innerHTML;
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fas fa-spinner spinner"></i> Guardando...';
            
            try {
                const datosActualizados = {
                    nombre: document.getElementById('edit-nombre').value,
                    apellido: document.getElementById('edit-apellido').value,
                    telefono: document.getElementById('edit-telefono').value,
                    dni: document.getElementById('edit-dni').value,
                    direccion: document.getElementById('edit-direccion').value,
                    ciudad: document.getElementById('edit-ciudad').value,
                    provincia: document.getElementById('edit-provincia').value
                };
                
                const response = await fetch(`${API_URL}/portal-cliente/perfil`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${TOKEN}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(datosActualizados)
                });
                
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                
                const data = await response.json();
                
                if (data.success) {
                    clienteData = { ...clienteData, ...datosActualizados };
                    mostrarDatosPerfil(clienteData);
                    toggleEditarPerfil();
                    showToast('✅ Perfil actualizado correctamente', 'success');
                } else {
                    throw new Error(data.error || 'Error desconocido');
                }
            } catch (error) {
                console.error('Error al guardar perfil:', error);
                showToast('❌ Error al guardar: ' + error.message, 'error');
            } finally {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalText;
            }
        }
        
        async function loadCotizaciones() {
            try {
                const response = await fetch(`${API_URL}/portal-cliente/cotizaciones`, {
                    headers: { 
                        'Authorization': `Bearer ${TOKEN}`,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                
                const data = await response.json();
                
                if (data.success && data.data && data.data.length > 0) {
                    mostrarCotizaciones(data.data);
                    document.getElementById('cotizaciones-section').style.display = 'block';
                }
            } catch (error) {
                console.error('Error loading cotizaciones:', error);
            }
        }
        
        function mostrarCotizaciones(cotizaciones) {
            const container = document.getElementById('cotizaciones-list');
            container.innerHTML = cotizaciones.map(cot => `
                <div style="background: var(--white); border: 2px solid var(--gray-200); border-radius: var(--radius-xl); padding: 1.5rem; margin-bottom: 1rem; transition: var(--transition);" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--gray-200)'">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--secondary); margin-bottom: 0.25rem;">
                                ${cot.nombreProyecto || 'Proyecto'}
                            </h3>
                            <p style="font-size: 0.875rem; color: var(--gray-600);">
                                Ref: ${cot.numeroReferencia || 'N/A'}
                            </p>
                        </div>
                        <div style="display: flex; gap: 0.75rem; align-items: start; flex-wrap: wrap;">
                            <span style="padding: 0.5rem 1rem; background: ${
                                cot.estado === 'ENVIADA' ? 'var(--info)' : 
                                cot.estado === 'ACEPTADA' ? 'var(--success)' : 
                                cot.estado === 'BORRADOR' ? 'var(--warning)' : 'var(--gray-400)'
                            }; color: white; border-radius: var(--radius-lg); font-size: 0.875rem; font-weight: 600;">
                                ${cot.estado}
                            </span>
                            <button onclick="descargarCotizacion('${cot.id}', '${cot.numeroReferencia}')" style="padding: 0.5rem 1rem; background: var(--primary); color: white; border: none; border-radius: var(--radius-lg); font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: var(--transition);" onmouseover="this.style.background='var(--primary-dark)'" onmouseout="this.style.background='var(--primary)'">
                                <i class="fas fa-download"></i> Descargar
                            </button>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; padding: 1rem; background: var(--gray-50); border-radius: var(--radius-lg);">
                        <div>
                            <p style="font-size: 0.75rem; color: var(--gray-600); margin-bottom: 0.25rem; font-weight: 500;">Precio Inmueble</p>
                            <p style="font-weight: 700; color: var(--secondary); font-size: 1.125rem;">S/ ${parseFloat(cot.precioInmueble || 0).toLocaleString('es-PE', { minimumFractionDigits: 2 })}</p>
                        </div>
                        <div>
                            <p style="font-size: 0.75rem; color: var(--gray-600); margin-bottom: 0.25rem; font-weight: 500;">Cuota Inicial</p>
                            <p style="font-weight: 700; color: var(--primary); font-size: 1.125rem;">S/ ${parseFloat(cot.cuotaInicial || 0).toLocaleString('es-PE', { minimumFractionDigits: 2 })}</p>
                        </div>
                        <div>
                            <p style="font-size: 0.75rem; color: var(--gray-600); margin-bottom: 0.25rem; font-weight: 500;">Cuota Mensual</p>
                            <p style="font-weight: 700; color: var(--success); font-size: 1.125rem;">S/ ${parseFloat(cot.cuotaMensualEstimada || 0).toLocaleString('es-PE', { minimumFractionDigits: 2 })}</p>
                        </div>
                        <div>
                            <p style="font-size: 0.75rem; color: var(--gray-600); margin-bottom: 0.25rem; font-weight: 500;">Plazo</p>
                            <p style="font-weight: 700; color: var(--secondary); font-size: 1.125rem;">${cot.plazoMeses || 0} meses</p>
                        </div>
                    </div>
                    <p style="font-size: 0.875rem; color: var(--gray-600); margin-top: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-calendar"></i>
                        Creada el ${new Date(cot.creadoEn).toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' })}
                    </p>
                </div>
            `).join('');
        }
        
        function descargarCotizacion(cotizacionId, numeroReferencia) {
            try {
                showToast('📄 Descargando cotización ' + numeroReferencia, 'success');
                
                // Descargar HTML desde la API del CRM
                const apiUrl = `${API_URL}/cotizaciones/${encodeURIComponent(cotizacionId)}/descargar-pdf`;
                
                fetch(apiUrl, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${TOKEN}`
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', {
                        contentType: response.headers.get('content-type'),
                        contentLength: response.headers.get('content-length')
                    });
                    
                    if (!response.ok) {
                        // Si hay error, intentar leer el JSON para ver el mensaje
                        if (response.headers.get('content-type')?.includes('application/json')) {
                            return response.json().then(data => {
                                console.error('Error response:', data);
                                throw new Error(`HTTP ${response.status}: ${data.error || 'Unknown error'}`);
                            });
                        }
                        throw new Error(`HTTP ${response.status}`);
                    }
                    return response.text();
                })
                .then(html => {
                    console.log('HTML size:', html.length, 'bytes');
                    
                    if (!html || html.length === 0) {
                        throw new Error('El HTML descargado está vacío');
                    }
                    
                    // Abrir en ventana nueva para que el usuario pueda imprimir como PDF
                    const w = window.open('about:blank');
                    if (!w) {
                        throw new Error('No se pudo abrir la ventana de impresión');
                    }
                    
                    w.document.write(html);
                    w.document.close();
                    w.focus();
                    
                    // Esperar a que el documento se cargue completamente
                    setTimeout(() => {
                        w.print();
                        showToast('✅ Cotización lista para descargar como PDF', 'success');
                    }, 500);
                })
                .catch(error => {
                    console.error('Error descargando cotización:', error);
                    showToast('❌ Error al descargar la cotización: ' + error.message, 'error');
                });
            } catch (error) {
                console.error('Error:', error);
                showToast('❌ Error al descargar la cotización', 'error');
            }
        }
        
        async function loadOportunidades() {
            try {
                const response = await fetch(`${API_URL}/portal-cliente/oportunidades`, {
                    headers: { 
                        'Authorization': `Bearer ${TOKEN}`,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                
                const data = await response.json();
                
                if (data.success && data.data && data.data.length > 0) {
                    mostrarOportunidades(data.data);
                    document.getElementById('oportunidades-section').style.display = 'block';
                }
            } catch (error) {
                console.error('Error loading oportunidades:', error);
            }
        }
        
        function mostrarOportunidades(oportunidades) {
            const container = document.getElementById('oportunidades-list');
            container.innerHTML = oportunidades.map(op => {
                const etapaColor = {
                    'LEAD': 'var(--info)',
                    'CONTACTO': 'var(--primary)',
                    'VISITA': 'var(--warning)',
                    'NEGOCIACION': 'var(--warning)',
                    'CIERRE': 'var(--success)',
                    'GANADO': 'var(--success)',
                    'PERDIDO': 'var(--danger)'
                };
                
                return `
                    <div style="background: var(--white); border: 2px solid var(--gray-200); border-radius: var(--radius-xl); padding: 1.5rem; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                            <div style="flex: 1;">
                                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--secondary); margin-bottom: 0.5rem;">
                                    ${op.titulo || 'Oportunidad'}
                                </h3>
                                ${op.proyecto ? `<p style="color: var(--gray-600); font-size: 0.875rem; margin-bottom: 0.25rem;"><i class="fas fa-building"></i> ${op.proyecto}</p>` : ''}
                                ${op.propiedad ? `<p style="color: var(--gray-600); font-size: 0.875rem;"><i class="fas fa-home"></i> ${op.propiedad}</p>` : ''}
                            </div>
                            <span style="padding: 0.5rem 1rem; background: ${etapaColor[op.etapa] || 'var(--gray-400)'}; color: white; border-radius: var(--radius-lg); font-size: 0.875rem; font-weight: 600;">
                                ${op.etapa}
                            </span>
                        </div>
                        ${op.valor ? `
                            <div style="background: var(--gray-50); padding: 1rem; border-radius: var(--radius-lg); margin-bottom: 1rem;">
                                <p style="font-size: 0.75rem; color: var(--gray-600); margin-bottom: 0.25rem;">Valor estimado</p>
                                <p style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">S/ ${parseFloat(op.valor).toLocaleString('es-PE', { minimumFractionDigits: 2 })}</p>
                                ${op.probabilidad ? `<p style="font-size: 0.875rem; color: var(--gray-600); margin-top: 0.5rem;">Probabilidad: ${op.probabilidad}%</p>` : ''}
                            </div>
                        ` : ''}
                        ${op.proximaAccion ? `
                            <p style="color: var(--gray-700); font-size: 0.875rem; margin-bottom: 0.75rem; padding: 0.75rem; background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%); border-radius: var(--radius-md); border-left: 3px solid var(--info);">
                                <i class="fas fa-tasks"></i> <strong>Próxima acción:</strong> ${op.proximaAccion}
                            </p>
                        ` : ''}
                        <p style="font-size: 0.875rem; color: var(--gray-500); display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-clock"></i>
                            Última actualización: ${new Date(op.ultimaActividad).toLocaleDateString('es-ES')}
                        </p>
                    </div>
                `;
            }).join('');
        }
        
        async function loadDocumentos() {
            try {
                const response = await fetch(`${API_URL}/portal-cliente/documentos`, {
                    headers: { 
                        'Authorization': `Bearer ${TOKEN}`,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                
                const data = await response.json();
                
                if (data.success && data.data) {
                    mostrarDocumentos(data.data.documentosRequeridos);
                    actualizarProgreso(data.data.progreso);
                    document.getElementById('documentos-section').style.display = 'block';
                }
            } catch (error) {
                console.error('Error loading documentos:', error);
            }
        }
        
        function mostrarDocumentos(documentos) {
            const container = document.getElementById('documentos-list');
            container.innerHTML = documentos.map(doc => {
                const estadoConfig = {
                    'PENDIENTE': { color: 'var(--gray-400)', icon: 'fas fa-clock', text: 'Pendiente' },
                    'SUBIDO': { color: 'var(--info)', icon: 'fas fa-upload', text: 'Subido' },
                    'VERIFICADO': { color: 'var(--success)', icon: 'fas fa-check-circle', text: 'Verificado' },
                    'RECHAZADO': { color: 'var(--danger)', icon: 'fas fa-times-circle', text: 'Rechazado' }
                };
                
                const config = estadoConfig[doc.estado] || estadoConfig['PENDIENTE'];
                
                return `
                    <div style="background: var(--white); border: 2px solid var(--gray-200); border-radius: var(--radius-xl); padding: 1.5rem; margin-bottom: 1rem; display: flex; align-items: start; gap: 1rem;">
                        <div style="width: 50px; height: 50px; border-radius: var(--radius-lg); background: linear-gradient(135deg, ${config.color}15 0%, ${config.color}25 100%); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="${config.icon}" style="font-size: 1.5rem; color: ${config.color};"></i>
                        </div>
                        <div style="flex: 1;">
                            <h4 style="font-weight: 700; color: var(--secondary); margin-bottom: 0.25rem;">${doc.nombre}</h4>
                            <p style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.5rem;">${doc.descripcion}</p>
                            <p style="font-size: 0.75rem; color: var(--gray-500);">${doc.instrucciones}</p>
                            ${doc.subido ? `
                                <p style="margin-top: 0.75rem; font-size: 0.875rem; color: var(--gray-600);">
                                    <i class="fas fa-file"></i> ${doc.subido.nombreArchivo} 
                                    <span style="color: var(--gray-500);">(${new Date(doc.subido.subidoEn).toLocaleDateString('es-ES')})</span>
                                </p>
                            ` : ''}
                        </div>
                        <span style="padding: 0.5rem 1rem; background: ${config.color}; color: white; border-radius: var(--radius-lg); font-size: 0.875rem; font-weight: 600; white-space: nowrap;">
                            ${config.text}
                        </span>
                    </div>
                `;
            }).join('');
        }
        
        function actualizarProgreso(progreso) {
            document.getElementById('progress-bar').style.width = `${progreso.porcentaje}%`;
            document.getElementById('progress-text').textContent = `${progreso.porcentaje}%`;
        }
    </script>
</body>
</html>
