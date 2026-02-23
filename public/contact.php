<?php $page = 'contact'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - DelgadoPropiedades</title>

    <!-- Fuentes -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&family=Open+Sans:wght@300;400;600;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Estilos CSS -->
    <link rel="shortcut icon" href="images/propiedad.png" type="image/png">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/animations.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/contact.css">

</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <section class="hero-banner contact-banner">
        <div class="overlay"></div>
        <div class="container">
            <br> <br> <br>
            <h1>Contáctanos</h1>
            <p>Estamos listos para ayudarte a encontrar la propiedad de tus sueños</p>
        </div>
    </section>

    <section class="contact-info">
        <div class="container">
            <div class="section-header animate-fade-up">
                <h2>Información de Contacto</h2>
                <div class="separator"><span></span></div>
            </div>
            <br>
            <div class="contact-grid">
                <div class="contact-card animate-fade-up">
                    <div class="card-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Dirección</h3>
                    <p>Mariscal Nieto 480, Centro Comercial Boulevard Interior C1-C2 Segundo Piso</p>
                    <a href="https://maps.app.goo.gl/1hVLc8Uno39zv5p28" target="_blank" class="btn-link">Ver en Mapa</a>
                </div>
                <div class="contact-card animate-fade-up delay-1">
                    <div class="card-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h3>Teléfono</h3>
                    <p>+51 948 734 448</p>
                    <a href="tel:+51 948 734 448" class="btn-link">Llamar Ahora</a>
                </div>

                <div class="contact-card animate-fade-up delay-2">
                    <div class="card-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Email</h3>
                    <center>
                        <p>inmobiliaria.dpropiedades@gmail.com</p>
                    </center>
                    <a href="mailto:inmobiliaria.dpropiedades@gmail.com" class="btn-link">Enviar Email</a>
                </div>
                <div class="contact-card animate-fade-up delay-3">
                    <div class="card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Horario de Atención</h3>
                    <p>Lun - Vie: 9:00 am - 5:00 pm</p>
                    <p>Sáb: 9:00 am - 5:00 pm</p>
                </div>
            </div>
        </div>
    </section>
    <br>
    <section class="contact-form-section">
        <div class="contact-container">
            <div class="contact-section">
                <div class="contact-content">
                    <h2>Contáctanos</h2>
                    <div class="action-buttons">
                        <a href="tel:+51948734448" class="btn btn-primary">
                            <i class="fas fa-phone"></i>
                            <span>Llamar ahora<br><small>+51 948 734 448</small></span>
                        </a>
                        <a href="https://wa.me/51948734448" target="_blank" class="btn btn-whatsapp">
                            <i class="fab fa-whatsapp"></i>
                            <span>Contactar por WhatsApp</span>
                        </a>
                    </div>
                    <div class="social-connect">
                        <h3>Síguenos en redes sociales</h3>
                        <div class="social-icons">
                            <a href="https://web.facebook.com/MDelgadoPropiedades" target="_blank" class="social-link">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://www.instagram.com/delgado.propiedades" target="_blank" class="social-link">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="https://www.tiktok.com/@delgado_propiedades" target="_blank" class="social-link">
                                <i class="fab fa-tiktok"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="map-section">
                <div class="map-container">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.955910386936!2d-79.83439392546119!3d-6.775221066255427!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x904cef2b72004633%3A0xfc15af020347bf0!2sBoulevard!5e0!3m2!1ses-419!2spe!4v1742323011864!5m2!1ses-419!2spe"
                        allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <section class="faq">
        <div class="container">
            <div class="section-header animate-fade-up">
                <h2>Preguntas Frecuentes</h2>
                <div class="separator"><span></span></div>
            </div>
            <div class="accordion animate-fade-up delay-1">
                <div class="accordion-item">
                    <div class="accordion-header">¿Cómo puedo agendar una visita a una propiedad?</div>
                    <div class="accordion-content">
                        <p>Puedes agendar una visita a cualquiera de nuestras propiedades llamando a nuestro número de
                            contacto +51 948 734 448, enviando un correo a inmobiliaria.dpropiedades@gmail.com o
                            completando el formulario de contacto en esta página.</p><br>
                    </div>
                </div>
                <div class="accordion-item">
                    <div class="accordion-header">¿Cuáles son los requisitos para comprar una propiedad?</div>
                    <div class="accordion-content">
                        <p>Los requisitos varían según el tipo de financiamiento que elijas. En general, necesitarás
                            documentos de identidad, comprobantes de ingresos, y un porcentaje de cuota inicial. Para
                            más detalles, te recomendamos contactar a uno de nuestros asesores.</p>
                        <br>
                    </div>
                </div>
                <div class="accordion-item">
                    <div class="accordion-header">¿Ofrecen asesoría para obtener un crédito hipotecario?</div>
                    <div class="accordion-content">
                        <p>Sí, contamos con asesores especializados que te guiarán en todo el proceso para obtener un
                            crédito hipotecario con las mejores condiciones según tu perfil financiero.</p>
                        <br>
                    </div>
                </div>
                <div class="accordion-item">
                    <div class="accordion-header">¿Trabajan con el programa Techo Propio?</div>
                    <div class="accordion-content">
                        <p>Sí, contamos con agentes inmobiliarios acreditados por el Ministerio de Vivienda para llevar
                            a cabo tu postulación al programa Techo Propio con éxito.</p>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <br><br>
    
    <?php include 'includes/footer.php'; ?>

    <script src="js/accordion.js"></script>
    <script src="js/assistant.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Cargar propiedades para el select
            fetch('get_properties.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const propertySelect = document.getElementById('property');

                        data.properties.forEach(property => {
                            const option = document.createElement('option');
                            option.value = property.id;
                            option.textContent = property.title + ' - S/. ' + parseFloat(property.price).toLocaleString('es-PE');
                            propertySelect.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error al cargar propiedades:', error));

            // Validación del formulario
            const contactForm = document.getElementById('contactForm');

            if (contactForm) {
                contactForm.addEventListener('submit', function (event) {
                    const name = document.getElementById('name').value;
                    const email = document.getElementById('email').value;
                    const phone = document.getElementById('phone').value;
                    const subject = document.getElementById('subject').value;
                    const message = document.getElementById('message').value;

                    if (!name || !email || !phone || !subject || !message) {
                        event.preventDefault();
                        alert('Por favor, completa todos los campos obligatorios.');
                        return;
                    }

                    // Validar email
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        event.preventDefault();
                        alert('Por favor, ingresa un correo electrónico válido.');
                        return;
                    }

                    // Validar teléfono (formato peruano)
                    const phoneRegex = /^[0-9]{9}$/;
                    if (!phoneRegex.test(phone)) {
                        event.preventDefault();
                        alert('Por favor, ingresa un número de teléfono válido (9 dígitos).');
                        return;
                    }
                });
            }

            // Callback form
            const callbackForm = document.querySelector('.callback-form');

            if (callbackForm) {
                callbackForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    const phoneInput = this.querySelector('input[type="tel"]');

                    if (!phoneInput.value) {
                        alert('Por favor, ingresa tu número de teléfono.');
                        return;
                    }

                    // Validar teléfono (formato peruano)
                    const phoneRegex = /^[0-9]{9}$/;
                    if (!phoneRegex.test(phoneInput.value)) {
                        alert('Por favor, ingresa un número de teléfono válido (9 dígitos).');
                        return;
                    }

                    // Aquí se podría enviar el formulario mediante AJAX
                    alert('Gracias. Te llamaremos pronto al número ' + phoneInput.value);
                    phoneInput.value = '';
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
