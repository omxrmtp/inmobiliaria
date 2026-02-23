<?php
session_start();
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iniciar Sesión - DelgadoPropiedades</title>

<!-- Fuentes -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<!-- Estilos CSS -->
<link rel="shortcut icon" href="images/propiedad.png" type="image/png">
<link rel="stylesheet" href="css/base.css">
<link rel="stylesheet" href="css/components.css">
<link rel="stylesheet" href="css/animations.css">
<link rel="stylesheet" href="css/header.css">
<link rel="stylesheet" href="css/footer.css">
<link rel="stylesheet" href="css/login.css">
<link rel="stylesheet" href="css/admin-dropdowns.css">
<link rel="stylesheet" href="css/admin-theme.css">
<link rel="stylesheet" href="css/admin-base.css">
</head>
<body class="dark-mode">
<header class="header">
    <div class="container">
        <div class="logo">
        <a href="index.html" class="logo">
                    <img src="logo/propiedadedelgado.png" alt="TechoPropio Logo" class="header-image">
                </a>
        </div>
        <nav class="nav">
            <ul class="nav-list">
                <li class="nav-item"><a href="index.html" class="nav-link">Inicio</a></li>
                <li class="nav-item"><a href="proyectos.html" class="nav-link">Proyectos</a></li>
                <li class="nav-item nav-dropdown">
                    <a href="#" class="nav-link nav-dropdown-toggle">
                        Nosotros <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="nav-dropdown-content">
                        <a href="team.html" class="nav-dropdown-item">Equipo</a>
                        <a href="testimonials.html" class="nav-dropdown-item">Testimonios</a>
                        <a href="otros-servicios.php" class="nav-dropdown-item">Otros Servicios</a>
                    </div>
                </li>
                <li class="nav-item"><a href="contact.html" class="nav-link">Contacto</a></li>
            </ul>
            <div style="display: flex; gap: 12px; align-items: center;">
                <a href="client-login.php" class="btn-login">
                    <i class="fas fa-user"></i> Portal Cliente
                </a>
            </div>
        </nav>
        <div class="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</header>


<section class="login-section">
    <div class="container">
        <div class="login-container animate-fade-up">
            <div class="login-form">
                <div class="form-header">
                    <h2>Iniciar Sesión</h2>
                    <p>Ingresa tus credenciales para acceder</p>
                </div>
                
                <?php if (!empty($error)):
                    echo "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> " . htmlspecialchars($error) . "</div>";
                endif; ?>
                
                <form action="process_login.php" method="POST">
                    <div class="form-group">
                        <label for="email">Correo electrónico:</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" required>
                            <button type="button" class="toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-options">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Recordarme</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                </form>
            </div>
            <div class="login-info">
                <h3>Panel de Administración</h3>
                <p>Este acceso está reservado para el personal administrativo de DelgadoPropiedades.</p>
                <p>Si eres cliente y necesitas información, por favor utiliza nuestro formulario de contacto o el asistente virtual.</p>
                <div class="info-features">
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Soporte 24/7</h4>
                            <p>Nuestro equipo está disponible para ayudarte en cualquier momento.</p>
                        </div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Acceso Seguro</h4>
                            <p>Utilizamos protocolos de seguridad avanzados para proteger tu información.</p>
                        </div>
                    </div>
                </div>
                <a href="contact.html" class="btn btn-secondary">Ir a Contacto</a>
                <a href="index.html" class="btn btn-tertiary">Volver al Inicio</a>
            </div>
        </div>
    </div>
</section>

       <!-- Footer -->
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-top">
                <div class="footer-info">
                    <div class="footer-logo">
                        <i class="fas fa-building footer-logo-icon"></i>
                        <h3>DelgadoPropiedades</h3>
                    </div>
                    <p class="footer-description">
                        Ofrecemos las mejores propiedades con asesoramiento personalizado para que encuentres el hogar de tus sueños.
                    </p>
                    <div class="social-links">
                        <a href="https://web.facebook.com/MDelgadoPropiedades" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/delgado.propiedades" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.tiktok.com/@delgado_propiedades" class="social-link"><i class="fab fa-tiktok"></i></a>
                        <a href="https://wa.me/51948734448" class="social-link"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                
   
                
                <div class="footer-links-container">
                    <h4 class="footer-title">Servicios</h4>
                    <ul class="footer-links">
                        <li><a href="properties.html"><i class="fas fa-chevron-right"></i> Compra de propiedades</a></li>
                        <li><a href="properties.html"><i class="fas fa-chevron-right"></i> Venta de propiedades</a></li>
                        <li><a href="credito.html"><i class="fas fa-chevron-right"></i> Credito MiVivienda</a></li>
                        <li><a href="techo-propio.html"><i class="fas fa-chevron-right"></i> Programa Techo Propio</a></li>
                    </ul>
                </div>
                
                <div class="footer-contact">
                    <h4 class="footer-title">Contáctanos</h4>
                    <ul class="footer-contact-list">
                      <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Mariscal Nieto 480, Centro Comercial Boulevard Interior C1-C2 Segundo Piso</span>
                      </li>
                      <li>
                        <i class="fas fa-phone"></i>
                        <span>+51 948 734 448</span>
                      </li>
                      <li>
                        <i class="fas fa-envelope"></i>
                        <span>inmobiliaria.dpropiedades@gmail.com</span>
                      </li>
                      <li>
                        <i class="fas fa-clock"></i>
                        <span>Lun - Sab: 9 AM - 5 PM</span>
                      </li>
                    </ul>
                  </div>
                </div>
            
            <div class="footer-bottom">
                <p>&copy; <span id="current-year"></span> DelgadoPropiedades. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>


<script src="js/main.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
      // Toggle password visibility
      const togglePassword = document.querySelector('.toggle-password');
      
      if (togglePassword) {
          togglePassword.addEventListener('click', function() {
              const passwordInput = document.getElementById('password');
              const icon = this.querySelector('i');
              
              if (passwordInput.type === 'password') {
                  passwordInput.type = 'text';
                  icon.classList.remove('fa-eye');
                  icon.classList.add('fa-eye-slash');
              } else {
                  passwordInput.type = 'password';
                  icon.classList.remove('fa-eye-slash');
                  icon.classList.add('fa-eye');
              }
          });
      }
      
      // Inicializar animaciones
      function initAnimations() {
          const animatedElements = document.querySelectorAll('.animate-fade-up, .animate-fade-in, .animate-fade-right');
          
          const observer = new IntersectionObserver((entries) => {
              entries.forEach(entry => {
                  if (entry.isIntersecting) {
                      entry.target.classList.add('animated');
                      observer.unobserve(entry.target);
                  }
              });
          }, { threshold: 0.1 });
          
          animatedElements.forEach(element => {
              observer.observe(element);
          });
      }
      
      // Inicializar animaciones
      initAnimations();
  });
</script>
</body>
</html>
