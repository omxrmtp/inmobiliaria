<?php
// Intentar cargar composer autoload; si no existe, incluir settings/database como fallback
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    if (file_exists(__DIR__ . '/../src/config/settings.php')) {
        require_once __DIR__ . '/../src/config/settings.php';
    }
    if (file_exists(__DIR__ . '/../src/config/database.php')) {
        require_once __DIR__ . '/../config/database.php';
    }
}

// Asegurar connectDB
if (!function_exists('connectDB') && file_exists(__DIR__ . '/../src/config/database.php')) {
    require_once __DIR__ . '/../config/database.php';
}

// Intentar conectar, pero no provocar fatal si falla
$conn = null;
try {
    if (function_exists('connectDB')) {
        $conn = connectDB();
    }
} catch (Throwable $e) {
    $conn = null;
}

// Obtener servicios destacados (topografía y dron) con sus medios
if ($conn) {
    $servicios = [];
    $query = $conn->query("SELECT * FROM otros_servicios WHERE service_type IN ('topografia', 'dron')");
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $servicios[$row['service_type']] = $row;
    }

    // Obtener servicios generales
    $query_general = $conn->query("SELECT * FROM otros_servicios WHERE service_type = 'general'");
    $servicios_generales = $query_general->fetchAll(PDO::FETCH_ASSOC);
} else {
    $servicios = [];
    $servicios_generales = [];
}
$page = 'otros-servicios';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otros Servicios - DelgadoPropiedades</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/otros-servicios.css">
    <link rel="shortcut icon" href="images/propiedad.png" type="image/png">
    <style>
        /* Estilos para el modal de galería mejorado */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.95);
            overflow: hidden;
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            width: 90%;
            max-width: 1000px;
            max-height: 85vh;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            gap: 30px;
        }
        
        .close {
            position: absolute;
            top: 20px;
            right: 30px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
            cursor: pointer;
            z-index: 2;
        }
        
        .close:hover {
            color: var(--color-primary);
            transform: scale(1.1);
        }
        
        .gallery-container {
            width: 100%;
            max-width: 700px;
            height: auto;
            max-height: 75vh;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .gallery-item {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: auto;
            max-height: 75vh;
        }
        
        .gallery-item img {
            max-width: 100%;
            max-height: 70vh;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        }
        
        .gallery-item iframe {
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        }
        
        .gallery-counter {
            color: #fff;
            font-size: 14px;
            margin-top: 15px;
            text-align: center;
        }
        
        .gallery-slide {
            display: none;
            width: 100%;
            height: 100%;
            text-align: center;
        }
        
        .gallery-slide.active {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .gallery-image {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
            border-radius: 8px;
        }
        
        .gallery-video {
            width: 80%;
            height: 80vh;
            border: none;
            border-radius: 8px;
        }
        
        .gallery-caption {
            margin-top: 15px;
            color: #fff;
            font-size: 1.2em;
            text-align: center;
        }
        
        .gallery-controls {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            transform: translateY(-50%);
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 1;
        }
        
        .gallery-prev,
        .gallery-next {
            background: rgba(0,0,0,0.7);
            color: white;
            border: 2px solid rgba(255,255,255,0.3);
            padding: 15px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 32px;
            font-weight: bold;
            flex-shrink: 0;
            width: 60px;
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .gallery-prev:hover,
        .gallery-next:hover {
            background: var(--color-primary);
            border-color: var(--color-primary);
            transform: scale(1.15);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                padding: 10px;
            }
            
            .gallery-video {
                width: 95%;
                height: 50vh;
            }
            
            .gallery-prev,
            .gallery-next {
                width: 40px;
                height: 40px;
                font-size: 16px;
                margin: 0 10px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <section class="hero-banner otros-servicios-banner">
        <div class="overlay"></div>
        <div class="container">
            <br><br><br>
            <h1 class="animate-fade-up">Otros Servicios</h1>
            <p class="animate-fade-up delay-1">Descubre nuestros servicios adicionales para complementar tu experiencia inmobiliaria</p>
        </div>
    </section>

    <section class="featured-services-section">
        <div class="container">
            <div class="section-title animate-fade-up">
                <h2>Servicios Destacados</h2>
                <p>Nuestros servicios más solicitados para complementar tu experiencia inmobiliaria</p>
            </div>

            <!-- Contenedor dinámico para servicios desde el CRM -->
            <div id="servicios-container">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin fa-3x"></i>
                    <p>Cargando servicios...</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="overlay"></div>
        <div class="container">
            <div class="cta-content animate-fade-up">
                <h2>¿Interesado en nuestros servicios adicionales?</h2>
                <p>Contáctanos para obtener más información y solicitar una cotización</p>
                <a href="contact.php" class="btn btn-primary">Contactar Ahora</a>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- Modal de Galería -->
    <div id="galleryModal" class="modal">
        <span class="close">&times;</span>
        <div class="modal-content">
            <button class="gallery-prev" title="Anterior">❮</button>
            <div class="gallery-container"></div>
            <button class="gallery-next" title="Siguiente">❯</button>
        </div>
    </div>
    
    <script>
        // Configurar URL de la API local
        window.CRM_API_URL = '/public';
    </script>
    
    <script src="js/otros-servicios.js"></script>
</body>
</html>