<?php
session_start();

// Verificar si el usuario está autenticado (por sesión o localStorage del frontend)
// El token se envía desde el frontend vía JavaScript
$clientToken = $_SESSION['token'] ?? null;
$clientId = $_SESSION['client_id'] ?? null;
$clientName = $_SESSION['client_name'] ?? 'Cliente';
$clientEmail = $_SESSION['client_email'] ?? '';

// Si no hay sesión, mostrar mensaje de no autenticado (el frontend manejará con localStorage)
if (!$clientToken && !isset($_GET['client_name'])) {
    $clientName = 'Cliente';
    $clientEmail = 'email@example.com';
}

// Obtener iniciales
$initials = '';
$name_parts = explode(' ', $clientName);
if (count($name_parts) >= 2) {
    $initials = strtoupper(substr($name_parts[0], 0, 1) . substr($name_parts[1], 0, 1));
} else {
    $initials = strtoupper(substr($clientName, 0, 2));
}

// URL del API local (no se usa en este archivo, pero se mantiene por compatibilidad)
$apiUrl = '/public';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#FCBA00">
    <title>Mi Portal - CRM Delgado</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #F5F5F5;
            color: #333;
        }

        .header {
            background: white;
            border-bottom: 1px solid #E5E7EB;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            font-size: 1.5rem;
            color: #FCBA00;
            text-decoration: none;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #FCBA00;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .logout-btn {
            background: #EF4444;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logout-btn:hover {
            background: #DC2626;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .welcome {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .welcome h1 {
            font-size: 2rem;
            color: #303030;
            margin-bottom: 0.5rem;
        }

        .welcome p {
            color: #6B7280;
            font-size: 1.1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #303030;
        }

        .stat-label {
            color: #6B7280;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .section {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .section h2 {
            font-size: 1.5rem;
            color: #303030;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .opportunity-item {
            background: #F9FAFB;
            border-left: 4px solid #FCBA00;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .opportunity-item:hover {
            background: #F3F4F6;
            transform: translateX(4px);
        }

        .opportunity-title {
            font-weight: 600;
            color: #303030;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .opportunity-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            font-size: 0.9rem;
            color: #6B7280;
            margin: 1rem 0;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stage-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .stage-lead { background: #DBEAFE; color: #0c4a6e; }
        .stage-contact { background: #E9D5FF; color: #5b21b6; }
        .stage-visit { background: #CCFBF1; color: #134e4a; }
        .stage-negotiation { background: #FED7AA; color: #92400e; }
        .stage-proposal { background: #DBEAFE; color: #0c4a6e; }
        .stage-winning { background: #DCFCE7; color: #166534; }
        .stage-lost { background: #FEE2E2; color: #991b1b; }

        .next-action {
            background: #F0F9FF;
            border-left: 3px solid #3B82F6;
            padding: 1rem;
            border-radius: 4px;
            font-size: 0.9rem;
            color: #0c4a6e;
            margin-top: 1rem;
        }

        .documents-list {
            display: grid;
            gap: 1rem;
        }

        .document-item {
            background: #F9FAFB;
            padding: 1rem;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 4px solid #E5E7EB;
        }

        .document-item.pending {
            border-left-color: #FBBF24;
            background: #FFFBEB;
        }

        .document-item.verified {
            border-left-color: #22C55E;
            background: #F0FDF4;
        }

        .document-name {
            font-weight: 500;
            color: #303030;
        }

        .document-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pending { background: #FCD34D; color: #78350f; }
        .status-verified { background: #86EFAC; color: #166534; }
        .status-rejected { background: #FCA5A5; color: #991b1b; }

        .advisor-card {
            background: linear-gradient(135deg, #FCBA00 0%, #F59E0B 100%);
            color: white;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
        }

        .advisor-name {
            font-size: 1.3rem;
            font-weight: 700;
            margin: 1rem 0;
        }

        .advisor-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 1rem 0;
            font-size: 0.9rem;
        }

        .advisor-meta a {
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            background: rgba(255,255,255,0.2);
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .advisor-meta a:hover {
            background: rgba(255,255,255,0.3);
        }

        .contact-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .btn {
            padding: 0.75rem 1rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #303030;
            color: white;
        }

        .btn-primary:hover {
            background: #1F2937;
        }

        .loading {
            text-align: center;
            padding: 3rem;
            color: #6B7280;
        }

        .spinner {
            border: 3px solid #E5E7EB;
            border-top: 3px solid #FCBA00;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error {
            background: #FEE2E2;
            color: #991b1b;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            border-left: 4px solid #EF4444;
        }

        @media (max-width: 768px) {
            .header {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }

            .container {
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .opportunity-meta {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <a href="/portal/dashboard.php" class="logo">
            <i class="fas fa-home"></i>
            <span>CRM Delgado</span>
        </a>
        <div class="user-menu">
            <div class="user-info">
                <div class="avatar"><?php echo $initials; ?></div>
                <div>
                    <div class="user-name"><?php echo htmlspecialchars(explode(' ', $clientName)[0]); ?></div>
                    <small style="color: #6B7280;"><?php echo htmlspecialchars($clientEmail); ?></small>
                </div>
            </div>
            <a href="/portal/logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                Salir
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <!-- Welcome Section -->
        <div class="welcome">
            <h1>¡Hola, <?php echo htmlspecialchars(explode(' ', $clientName)[0]); ?>! 👋</h1>
            <p>Bienvenido a tu portal personal. Aquí puedes ver el estado de tus transacciones y documentos.</p>
        </div>

        <!-- Loading State -->
        <div id="loading" class="loading" style="display: none;">
            <div class="spinner"></div>
            <p>Cargando tu información...</p>
        </div>

        <!-- Error State -->
        <div id="error" class="error" style="display: none;"></div>

        <!-- Stats Grid -->
        <div class="stats-grid" id="stats" style="display: none;">
            <div class="stat-card">
                <div class="stat-icon">🎯</div>
                <div class="stat-value" id="stat-opportunities">0</div>
                <div class="stat-label">Oportunidades Activas</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📄</div>
                <div class="stat-value" id="stat-documents">0</div>
                <div class="stat-label">Documentos Pendientes</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-value" id="stat-quotes">0</div>
                <div class="stat-label">Cotizaciones</div>
            </div>
        </div>

        <!-- Opportunities Section -->
        <div class="section" id="opportunities-section" style="display: none;">
            <h2><i class="fas fa-chart-line"></i> Mis Transacciones en Progreso</h2>
            <div id="opportunities-list" class="opportunities-list"></div>
        </div>

        <!-- Documents Section -->
        <div class="section" id="documents-section" style="display: none;">
            <h2><i class="fas fa-file-alt"></i> Mis Documentos (Requeridos vs Subidos)</h2>
            <div class="documents-list" id="documents-list"></div>
            <div style="margin-top: 1rem; padding: 1rem; background: #F3F4F6; border-radius: 6px;">
                <div style="font-weight: 600; margin-bottom: 0.5rem;">Progreso General:</div>
                <div style="height: 8px; background: #E5E7EB; border-radius: 4px; overflow: hidden;">
                    <div id="progress-bar" style="height: 100%; background: #FCBA00; width: 0%; transition: width 0.3s ease;"></div>
                </div>
                <div style="margin-top: 0.5rem; font-size: 0.9rem; color: #6B7280;">
                    <span id="progress-text">0%</span> completado
                </div>
            </div>
        </div>

        <!-- Advisor Section -->
        <div class="section" id="advisor-section" style="display: none;">
            <h2><i class="fas fa-user-tie"></i> Tu Asesor Asignado</h2>
            <div class="advisor-card" id="advisor-card"></div>
        </div>
    </div>

    <!-- Script -->
    <script>
        // Obtener token del localStorage (lo guardó el login)
        let TOKEN = localStorage.getItem('token');
        let CLIENT_DATA = JSON.parse(localStorage.getItem('client') || '{}');
        
        // Si no hay token, redirigir al login
        if (!TOKEN) {
            window.location.href = '/public/client-login.php';
        }

        const API_URL = window.location.protocol + '//' + window.location.host + '/public';

        async function loadDashboard() {
            const loading = document.getElementById('loading');
            const error = document.getElementById('error');
            const stats = document.getElementById('stats');

            loading.style.display = 'block';
            error.style.display = 'none';

            try {
                // Fetch summary data
                const response = await fetch(`${API_URL}/portal-cliente/resumen`, {
                    headers: {
                        'Authorization': `Bearer ${TOKEN}`
                    }
                });

                if (!response.ok) throw new Error('Error al cargar datos');

                const data = await response.json();
                const { estadisticas, oportunidadesRecientes, cliente, cotizacionesRecientes } = data.data;

                // Update stats
                document.getElementById('stat-opportunities').textContent = estadisticas.oportunidadesActivas;
                document.getElementById('stat-documents').textContent = estadisticas.documentosPendientes;
                document.getElementById('stat-quotes').textContent = cotizacionesRecientes.length;

                // Load opportunities
                await loadOportunidades();

                // Load documents
                await loadDocumentos();

                // Load advisor
                await loadAsesor();

                stats.style.display = 'grid';
                loading.style.display = 'none';

            } catch (err) {
                console.error('Error:', err);
                error.style.display = 'block';
                error.textContent = '❌ Error al cargar tu información. Por favor, recarga la página.';
                loading.style.display = 'none';
            }
        }

        async function loadOportunidades() {
            try {
                const response = await fetch(`${API_URL}/portal-cliente/oportunidades`, {
                    headers: { 'Authorization': `Bearer ${TOKEN}` }
                });

                const data = await response.json();
                const section = document.getElementById('opportunities-section');
                const list = document.getElementById('opportunities-list');

                if (data.data.length === 0) {
                    list.innerHTML = '<p style="color: #6B7280;">No tienes oportunidades en progreso.</p>';
                    section.style.display = 'none';
                    return;
                }

                list.innerHTML = data.data.map(opp => `
                    <div class="opportunity-item">
                        <div style="display: flex; gap: 1rem; align-items: start;">
                            <div style="flex: 1;">
                                <div class="opportunity-title">${opp.titulo}</div>
                                <span class="stage-badge stage-${opp.etapa.toLowerCase()}">
                                    ${opp.etapa}
                                </span>
                                <div class="opportunity-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-tag"></i>
                                        <span>${opp.propiedad || 'Sin especificar'}</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-dollar-sign"></i>
                                        <span>S/ ${parseFloat(opp.valor).toLocaleString('es-PE', { minimumFractionDigits: 2 })}</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-percentage"></i>
                                        <span>${opp.probabilidad}% cierre</span>
                                    </div>
                                </div>
                                ${opp.proximaAccion ? `
                                    <div class="next-action">
                                        <strong>Próxima acción:</strong> ${opp.proximaAccion}
                                    </div>
                                ` : ''}
                            </div>
                            <div style="text-align: right;">
                                <small style="color: #6B7280;">Asesor: ${opp.asesor.nombre}</small>
                            </div>
                        </div>
                    </div>
                `).join('');

                section.style.display = 'block';
            } catch (err) {
                console.error('Error loading opportunities:', err);
            }
        }

        async function loadDocumentos() {
            try {
                const response = await fetch(`${API_URL}/portal-cliente/documentos`, {
                    headers: { 'Authorization': `Bearer ${TOKEN}` }
                });

                const data = await response.json();
                const { documentosRequeridos, progreso } = data.data;
                const section = document.getElementById('documents-section');
                const list = document.getElementById('documents-list');

                // Update progress bar
                document.getElementById('progress-bar').style.width = progreso.porcentaje + '%';
                document.getElementById('progress-text').textContent = progreso.porcentaje + '%';

                list.innerHTML = documentosRequeridos.map(doc => `
                    <div class="document-item ${doc.estado.toLowerCase() === 'verificado' ? 'verified' : 'pending'}">
                        <div>
                            <div class="document-name">
                                ${doc.estado === 'VERIFICADO' ? '✅' : doc.estado === 'PENDIENTE' ? '⏳' : '❌'} 
                                ${doc.nombre}
                            </div>
                            <small style="color: #6B7280;">${doc.descripcion}</small>
                        </div>
                        <span class="document-status status-${doc.estado.toLowerCase()}">
                            ${doc.estado}
                        </span>
                    </div>
                `).join('');

                section.style.display = 'block';
            } catch (err) {
                console.error('Error loading documents:', err);
            }
        }

        async function loadAsesor() {
            try {
                const response = await fetch(`${API_URL}/portal-cliente/asesor`, {
                    headers: { 'Authorization': `Bearer ${TOKEN}` }
                });

                const data = await response.json();
                const asesor = data.data;
                const section = document.getElementById('advisor-section');
                const card = document.getElementById('advisor-card');

                card.innerHTML = `
                    <div>
                        <i class="fas fa-user" style="font-size: 3rem; opacity: 0.8;"></i>
                        <div class="advisor-name">${asesor.nombre}</div>
                        <small>${asesor.estadisticas.clientesActivos} clientes activos • ${asesor.estadisticas.tasaCierre}% de cierre</small>
                        <div class="advisor-meta" style="margin-top: 1rem;">
                            <a href="mailto:${asesor.email}">
                                <i class="fas fa-envelope"></i> Email
                            </a>
                            <a href="tel:${asesor.telefono}">
                                <i class="fas fa-phone"></i> Llamar
                            </a>
                        </div>
                        <div style="margin-top: 1rem; font-size: 0.9rem;">
                            <div>📞 ${asesor.telefono}</div>
                            <div>📧 ${asesor.email}</div>
                        </div>
                        <div style="margin-top: 1rem; font-size: 0.85rem; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 1rem;">
                            <strong>Disponible:</strong> Lun-Vie 09:00-18:00
                        </div>
                    </div>
                `;

                section.style.display = 'block';
            } catch (err) {
                console.error('Error loading advisor:', err);
            }
        }

        // Load dashboard on page load
        document.addEventListener('DOMContentLoaded', loadDashboard);
    </script>
</body>
</html>
