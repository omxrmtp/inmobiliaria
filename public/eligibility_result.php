<?php
session_start();
// Load composer autoload to include config files declared in composer.json
require_once __DIR__ . '/../vendor/autoload.php';

// Verificar si hay resultados en la sesión
if (!isset($_SESSION['eligibility_result'])) {
    header('Location: techo-propio.html');
    exit();
}

$result = $_SESSION['eligibility_result'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resultado de Elegibilidad - InmobiliariaPro</title>
  
  <!-- Fuentes -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  
  <!-- Estilos CSS -->
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/animations.css">
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/eligibility-result.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>InmobiliariaPro</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.html">Inicio</a></li>
                    <li><a href="properties.html">Propiedades</a></li>
                    <li><a href="techo-propio.html" class="active">Techo Propio</a></li>
                    <li><a href="testimonials.html">Testimonios</a></li>
                    <li><a href="team.html">Equipo</a></li>
                    <li><a href="contact.html">Contacto</a></li>
                    <li><a href="login.php" class="btn-login">Iniciar Sesión</a></li>
                </ul>
            </nav>
            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>

    <section class="page-banner">
        <div class="container">
            <h2>Resultado de Elegibilidad</h2>
            <p>Programa Techo Propio</p>
        </div>
    </section>

    <section class="eligibility-result">
        <div class="container">
            <div class="result-card">
                <div class="result-header <?php echo isset($result['eligible']) && $result['eligible'] ? 'eligible' : 'not-eligible'; ?>">
                    <?php if (isset($result['eligible']) && $result['eligible']): ?>
                        <i class="fas fa-check-circle"></i>
                        <h2>¡Felicidades! Eres elegible para el programa Techo Propio</h2>
                    <?php else: ?>
                        <i class="fas fa-times-circle"></i>
                        <h2>Lo sentimos, no cumples con todos los requisitos</h2>
                    <?php endif; ?>
                </div>
                
                <div class="result-content">
                    <div class="applicant-info">
                        <h3>Información del Solicitante</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="label">Nombre:</span>
                                <span class="value"><?php echo isset($result['full_name']) ? $result['full_name'] : 'No disponible'; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">DNI:</span>
                                <span class="value"><?php echo isset($result['dni']) ? $result['dni'] : 'No disponible'; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Ingreso Mensual:</span>
                                <span class="value">S/. <?php echo isset($result['monthly_income']) ? number_format($result['monthly_income'], 2) : '0.00'; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Miembros de Familia:</span>
                                <span class="value"><?php echo isset($result['family_members']) ? $result['family_members'] : 'No disponible'; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label">Propiedad Actual:</span>
                                <span class="value">
                                    <?php 
                                    if (isset($result['own_property'])) {
                                        switch ($result['own_property']) {
                                            case 'no':
                                                echo 'No tiene propiedad';
                                                break;
                                            case 'yes_land':
                                                echo 'Tiene terreno';
                                                break;
                                            case 'yes_house':
                                                echo 'Tiene casa';
                                                break;
                                            default:
                                                echo $result['own_property'];
                                        }
                                    } else {
                                        echo 'No disponible';
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="label">Modalidad:</span>
                                <span class="value">
                                    <?php 
                                    if (isset($result['modality'])) {
                                        switch ($result['modality']) {
                                            case 'new_home':
                                                echo 'Adquisición de Vivienda Nueva';
                                                break;
                                            case 'build':
                                                echo 'Construcción en Sitio Propio';
                                                break;
                                            case 'improve':
                                                echo 'Mejoramiento de Vivienda';
                                                break;
                                            default:
                                                echo $result['modality'];
                                        }
                                    } else {
                                        echo 'No disponible';
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="label">Ahorro:</span>
                                <span class="value">S/. <?php echo isset($result['savings']) ? number_format($result['savings'], 2) : '0.00'; ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="result-message">
                        <h3>Resultado de la Evaluación</h3>
                        <p><?php echo isset($result['message']) ? $result['message'] : 'No hay información disponible sobre el resultado.'; ?></p>
                    </div>
                    
                    <?php if (isset($result['eligible']) && $result['eligible']): ?>
                        <div class="next-steps">
                            <h3>Próximos Pasos</h3>
                            <ol>
                                <li>Uno de nuestros asesores se pondrá en contacto contigo en las próximas 24-48 horas para brindarte más información.</li>
                                <li>Prepara los documentos necesarios: DNI, comprobantes de ingresos, declaración jurada de no propiedad (si aplica).</li>
                                <li>Explora nuestras propiedades que califican para el programa Techo Propio.</li>
                            </ol>
                            <div class="action-buttons">
                                <a href="properties.html?filter=techo-propio" class="btn">Ver Propiedades Techo Propio</a>
                                <a href="contact.html" class="btn btn-secondary">Contactar Asesor</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alternatives">
                            <h3>Alternativas</h3>
                            <p>Aunque no calificas para el programa Techo Propio en este momento, podemos ayudarte a explorar otras opciones:</p>
                            <ul>
                                <li>Asesoría para mejorar tu perfil financiero</li>
                                <li>Opciones de financiamiento tradicional</li>
                                <li>Propiedades que se ajusten a tu presupuesto actual</li>
                            </ul>
                            <div class="action-buttons">
                                <a href="properties.html" class="btn">Ver Todas las Propiedades</a>
                                <a href="contact.html" class="btn btn-secondary">Solicitar Asesoría</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>InmobiliariaPro</h3>
                    <p>Tu mejor opción para encontrar el hogar de tus sueños.</p>
                </div>
                <div class="footer-section">
                    <h3>Contacto</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Av. Principal 123, Lima</p>
                    <p><i class="fas fa-phone"></i> (01) 555-1234</p>
                    <p><i class="fas fa-envelope"></i> info@inmobiliariapro.com</p>
                </div>
                <div class="footer-section">
                    <h3>Síguenos</h3>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 InmobiliariaPro. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>

