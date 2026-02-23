<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="pageTitle">Servicios - DelgadoPropiedades</title>
    <meta name="description" content="" id="pageDescription">
    
    <!-- Fuentes y estilos -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/techo-propio.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .loading-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 400px;
        }
        
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #FCBA00;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .error-container {
            text-align: center;
            padding: 40px 20px;
        }
        
        .error-container i {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 20px;
        }
        
        .proyectos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .proyecto-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .proyecto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .proyecto-imagen {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .proyecto-contenido {
            padding: 20px;
        }
        
        .proyecto-titulo {
            font-size: 1.25rem;
            font-weight: 600;
            color: #303030;
            margin-bottom: 10px;
        }
        
        .proyecto-precio {
            font-size: 1.5rem;
            font-weight: 700;
            color: #FCBA00;
            margin: 15px 0;
        }
        
        .proyecto-ubicacion {
            color: #666;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .proyecto-estado {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 10px;
        }
        
        .estado-disponible {
            background: #d4edda;
            color: #155724;
        }
        
        .estado-reservado {
            background: #fff3cd;
            color: #856404;
        }
        
        .requisitos-list, .beneficios-list {
            list-style: none;
            padding: 0;
        }
        
        .requisitos-list li, .beneficios-list li {
            padding: 10px 0;
            padding-left: 30px;
            position: relative;
        }
        
        .requisitos-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #FCBA00;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .beneficios-list li:before {
            content: "★";
            position: absolute;
            left: 0;
            color: #FCBA00;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .pasos-proceso {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .paso-item {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .paso-numero {
            width: 50px;
            height: 50px;
            background: #FCBA00;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 auto 15px;
        }
        
        .faq-item {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .faq-pregunta {
            font-weight: 600;
            color: #303030;
            margin-bottom: 10px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .faq-respuesta {
            color: #666;
            line-height: 1.6;
            padding-top: 10px;
            border-top: 1px solid #f0f0f0;
        }
    </style>
</head>
<body>
    <!-- Incluir header de navegación (usa el mismo de tu sitio) -->
    <nav class="navbar">
        <!-- Tu navbar existente -->
    </nav>
    
    <!-- Contenido dinámico -->
    <div id="contenido-principal">
        <div class="loading-container">
            <div class="loading-spinner"></div>
        </div>
    </div>
    
    <script>
        // Obtener el tipo de servicio de la URL
        const urlParams = new URLSearchParams(window.location.search);
        const tipoServicio = urlParams.get('tipo') || 'techo_propio';
        
        // Cargar contenido del servicio
        async function cargarServicio() {
            try {
                const response = await fetch(`/get_servicio.php?tipo=${tipoServicio}`);
                const data = await response.json();
                
                if (!data.success) {
                    mostrarError('Servicio no encontrado');
                    return;
                }
                
                // Actualizar meta tags
                document.getElementById('pageTitle').textContent = data.servicio.meta_title || data.servicio.titulo;
                document.getElementById('pageDescription').content = data.servicio.meta_description || data.servicio.descripcion_corta;
                
                // Renderizar contenido
                renderizarServicio(data.servicio, data.proyectos);
                
            } catch (error) {
                console.error('Error al cargar servicio:', error);
                mostrarError('Error al cargar el contenido');
            }
        }
        
        function renderizarServicio(servicio, proyectos) {
            const contenedor = document.getElementById('contenido-principal');
            
            let html = `
                <!-- Banner -->
                <section class="techo-propio-banner" style="background-image: url('${servicio.imagen_banner || 'img/placeholder-banner.jpg'}');">
                    <div class="container">
                        <div class="banner-content">
                            <h1>${servicio.titulo}</h1>
                            ${servicio.subtitulo ? `<p class="subtitle">${servicio.subtitulo}</p>` : ''}
                        </div>
                    </div>
                </section>
                
                <!-- Descripción -->
                <section class="section-padding">
                    <div class="container">
                        <div class="section-header text-center">
                            <h2>Sobre ${servicio.titulo}</h2>
                        </div>
                        <div class="content-text">
                            ${servicio.descripcion_larga || servicio.descripcion_corta || ''}
                        </div>
                    </div>
                </section>
            `;
            
            // Requisitos
            if (servicio.requisitos && servicio.requisitos.length > 0) {
                html += `
                    <section class="section-padding bg-light">
                        <div class="container">
                            <h2 class="section-title">Requisitos</h2>
                            <ul class="requisitos-list">
                                ${servicio.requisitos.map(req => `<li>${req}</li>`).join('')}
                            </ul>
                        </div>
                    </section>
                `;
            }
            
            // Beneficios
            if (servicio.beneficios && servicio.beneficios.length > 0) {
                html += `
                    <section class="section-padding">
                        <div class="container">
                            <h2 class="section-title">Beneficios</h2>
                            <ul class="beneficios-list">
                                ${servicio.beneficios.map(ben => `<li>${ben}</li>`).join('')}
                            </ul>
                        </div>
                    </section>
                `;
            }
            
            // Pasos del proceso
            if (servicio.pasos && servicio.pasos.length > 0) {
                html += `
                    <section class="section-padding bg-light">
                        <div class="container">
                            <h2 class="section-title text-center">Proceso</h2>
                            <div class="pasos-proceso">
                                ${servicio.pasos.map((paso, index) => `
                                    <div class="paso-item">
                                        <div class="paso-numero">${index + 1}</div>
                                        <h4>${paso.titulo || paso}</h4>
                                        ${paso.descripcion ? `<p>${paso.descripcion}</p>` : ''}
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </section>
                `;
            }
            
            // Proyectos relacionados
            if (proyectos && proyectos.length > 0) {
                html += `
                    <section class="section-padding">
                        <div class="container">
                            <h2 class="section-title text-center">Proyectos Disponibles</h2>
                            <div class="proyectos-grid">
                                ${proyectos.map(proyecto => `
                                    <div class="proyecto-card">
                                        <img src="${proyecto.imagen || 'img/placeholder-property.jpg'}" 
                                             alt="${proyecto.nombre}" 
                                             class="proyecto-imagen"
                                             onerror="this.src='img/placeholder-property.jpg'">
                                        <div class="proyecto-contenido">
                                            <h3 class="proyecto-titulo">${proyecto.nombre}</h3>
                                            <div class="proyecto-ubicacion">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <span>${proyecto.ubicacion || 'Ubicación no especificada'}</span>
                                            </div>
                                            <div class="proyecto-precio">${proyecto.precio_formateado}</div>
                                            <span class="proyecto-estado estado-${proyecto.estado.toLowerCase()}">
                                                ${proyecto.estado}
                                            </span>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </section>
                `;
            }
            
            // Preguntas frecuentes
            if (servicio.preguntas && servicio.preguntas.length > 0) {
                html += `
                    <section class="section-padding bg-light">
                        <div class="container">
                            <h2 class="section-title text-center">Preguntas Frecuentes</h2>
                            <div class="faq-container">
                                ${servicio.preguntas.map((faq, index) => `
                                    <div class="faq-item">
                                        <div class="faq-pregunta" onclick="toggleFAQ(${index})">
                                            <span>${faq.pregunta || faq}</span>
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                        <div class="faq-respuesta" id="faq-${index}" style="display: none;">
                                            ${faq.respuesta || ''}
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </section>
                `;
            }
            
            contenedor.innerHTML = html;
        }
        
        function mostrarError(mensaje) {
            const contenedor = document.getElementById('contenido-principal');
            contenedor.innerHTML = `
                <div class="error-container">
                    <i class="fas fa-exclamation-circle"></i>
                    <h2>${mensaje}</h2>
                    <p>Por favor, intenta nuevamente más tarde.</p>
                    <a href="/index.html" class="btn btn-primary">Volver al inicio</a>
                </div>
            `;
        }
        
        function toggleFAQ(index) {
            const respuesta = document.getElementById(`faq-${index}`);
            respuesta.style.display = respuesta.style.display === 'none' ? 'block' : 'none';
        }
        
        // Cargar al inicio
        cargarServicio();
    </script>
</body>
</html>
