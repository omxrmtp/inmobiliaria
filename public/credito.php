<?php $page = 'credito'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crédito MiVivienda - DelgadoPropiedades</title>
  <!-- archivos css -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="css/credito.css">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/home.css">
  <link rel="shortcut icon" href="images/propiedad.png" type="image/png">
  <link rel="stylesheet" href="css/components.css">
</head>
<body data-etiqueta-filtro="credito mi vivienda">
  <?php include 'includes/navbar.php'; ?>

  <br><br>
  <!-- Hero Banner -->
  <section class="hero-banner credito-banner">
    <div class="hero-content">
      <div class="hero-buttons">
        <a href="#pre-evaluacion" class="btn btn-primary btn-lg animate-fade-up delay-1">
          Realiza tu Pre-Evaluación
          <i class="fas fa-chevron-right btn-icon"></i>
        </a>
      </div>
    </div>
  </section>


  <!-- Info Cards Section -->
  <section class="benefits-section section-padding">
    <div class="container">
      <div class="section-title">
        <h2>Beneficios del Crédito MiVivienda</h2>
        <p>Conoce las ventajas que te ofrece este programa para hacer realidad el sueño de tu casa propia</p>
      </div>

      <div class="benefits-grid">
        <div class="benefit-card animate-fade-up">
          <div class="benefit-icon">
            <i class="fas fa-percentage"></i>
          </div>
          <h3>Tasas Preferenciales</h3>
          <p>Accede a tasas de interés más bajas que las del mercado tradicional, haciendo más accesible el pago de tu crédito.</p>
        </div>

        <div class="benefit-card animate-fade-up delay-1">
          <div class="benefit-icon">
            <i class="fas fa-award"></i>
          </div>
          <h3>Bono del Buen Pagador</h3>
          <p>Recibe un subsidio directo que reduce el monto de tu préstamo si mantienes tus pagos al día.</p>
        </div>

        <div class="benefit-card animate-fade-up delay-2">
          <div class="benefit-icon">
            <i class="fas fa-calendar-alt"></i>
          </div>
          <h3>Plazos Flexibles</h3>
          <p>Elige el plazo que mejor se adapte a tu capacidad de pago, con opciones de hasta 20 años para tu comodidad.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Pre-evaluación Section -->
  <section class="pre-evaluacion-section section-padding" id="pre-evaluacion">
    <div class="container">
      <div class="section-title">
        <h2>Obtén un crédito para construir en tu propio terreno</h2>
        <p>Realiza una pre-evaluación rápida para saber si calificas al Nuevo Crédito MiVivienda</p>
      </div>

      <div class="pre-evaluacion-container">
        <div class="pre-evaluacion-form" id="pre-evaluacion-form">
          <h3>Realiza una Pre-Evaluación</h3>
          <p>Responde estas preguntas para saber si calificas al Nuevo Crédito MiVivienda</p>
          
          <div class="question-container active" id="question-1">
            <div class="question">
              <h4>¿Cuentas con un Terreno Propio?</h4>
              <div class="radio-group">
                <label>
                  <input type="radio" name="terreno" value="si"> Sí
                </label>
                <label>
                  <input type="radio" name="terreno" value="no"> No
                </label>
              </div>
            </div>
            <div class="form-buttons">
              <div></div>
              <button class="btn btn-primary btn-next" data-question="1">Siguiente</button>
            </div>
          </div>
          
          <div class="question-container" id="question-2">
            <div class="question">
              <h4>¿Tus ingresos son sustentables (Boletas de pago, Recibos, etc)?</h4>
              <div class="radio-group">
                <label>
                  <input type="radio" name="ingresos" value="si"> Sí
                </label>
                <label>
                  <input type="radio" name="ingresos" value="no"> No
                </label>
              </div>
            </div>
            <div class="form-buttons">
              <button class="btn btn-outline btn-prev" data-question="2">Anterior</button>
              <button class="btn btn-primary btn-next" data-question="2">Siguiente</button>
            </div>
          </div>
          
          <div class="question-container" id="question-3">
            <div class="question">
              <h4>¿Estás bien calificado en el sistema financiero?</h4>
              <div class="radio-group">
                <label>
                  <input type="radio" name="calificacion" value="si"> Sí
                </label>
                <label>
                  <input type="radio" name="calificacion" value="no"> No
                </label>
              </div>
            </div>
            <div class="form-buttons">
              <button class="btn btn-outline btn-prev" data-question="3">Anterior</button>
              <button class="btn btn-primary btn-result">Ver Resultado</button>
            </div>
          </div>
          
          <div class="result-container" id="result-success" style="display: none;">
            <div class="result-icon success">
              <i class="fas fa-check-circle"></i>
            </div>
            <h3>¡Felicidades!</h3>
            <p>Estarías Calificando al Nuevo Crédito MiVivienda</p>
            
            <div class="result-buttons">
              <a href="https://wa.me/51948734448?text=Hola, he realizado la pre-evaluación para el Crédito Mi Vivienda y me gustaría verificar mis requisitos." class="btn btn-primary" target="_blank">
                <i class="fab fa-whatsapp"></i> Contactar por WhatsApp
              </a>
            </div>

            <div class="contact-form" style="margin-top: 20px;">
              <h4>O déjanos tus datos</h4>
              <p>Te contactaremos a la brevedad</p>
              <form action="process_credit_evaluation.php" method="POST">
                <div class="form-group">
                  <label for="nombre">Nombres y Apellidos</label>
                  <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                  <label for="ciudad">Ciudad</label>
                  <input type="text" id="ciudad" name="ciudad" required>
                </div>
                <div class="form-group">
                  <label for="telefono">Teléfono</label>
                  <input type="tel" id="telefono" name="telefono" required>
                </div>
                <input type="hidden" name="terreno" id="terreno_hidden">
                <input type="hidden" name="ingresos" id="ingresos_hidden">
                <input type="hidden" name="calificacion" id="calificacion_hidden">
                <button type="submit" class="btn btn-primary btn-block">Enviar Datos</button>
              </form>
            </div>
          </div>
          
          <div class="result-container" id="result-failure" style="display: none;">
            <div class="result-icon failure">
              <i class="fas fa-times-circle"></i>
            </div>
            <h3>Lo sentimos</h3>
            <p>Según tus respuestas, es posible que no califiques para el Nuevo Crédito MiVivienda en este momento.</p>
            <p>Te recomendamos contactar a uno de nuestros asesores para explorar otras opciones disponibles.</p>
            <div class="result-buttons">
              <button class="btn btn-outline btn-retry">Volver a intentar</button>
              <a href="#contacto" class="btn btn-primary">Contactar Asesor</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Información Adicional -->
  <section class="info-section section-padding bg-light">
    <div class="container">
      <div class="info-grid">
        <div class="info-content">
          <h2>¿Cómo funciona el Crédito MiVivienda para construcción en terreno propio?</h2>
          <p>El Nuevo Crédito MIVIVIENDA te permite financiar la construcción de tu vivienda en un terreno propio ofreciendote de forma adicional un subcidio por parte del estado (Bono de buen Pagador que puede llegar hasta los 25,700)</p>
          
          <div class="accordion" id="credito-accordion">
            <div class="accordion-item">
              <div class="accordion-header" data-accordion="1">
                ¿Cuáles son los requisitos básicos?
              </div>
              <div class="accordion-content" id="accordion-1">
                <ul>
                  <li>Ser mayor de edad</li>
                  <li>Demostrar ingresos que permitan asumir el crédito</li>
                  <li>No haber recibido apoyo habitacional del estado previamente</li>
                  <li>Contar con un terreno inscrito en los Registros públicos (SUNARP), libre de cargas o grabamenes</li>
                  <li>No ser propietario de otra vivienda</li>

                </ul>
              </div>
            </div>
            
            <div class="accordion-item">
              <div class="accordion-header" data-accordion="3">
                ¿Cuál es el monto máximo de financiamiento?
              </div>
              <div class="accordion-content" id="accordion-3">
                <p>El Nuevo Crédito MIVIVIENDA financia la compra de viviendas cuyo valor esté entre S/ 68,800 hasta S/ 362,100. El monto del préstamo dependerá de tu capacidad de pago y el valor de construcción de tu futura vivienda.</p>
              </div>
            </div>
          </div>
          
          <div class="info-buttons">
            <a href="https://www.mivivienda.com.pe" class="btn btn-primary" target="_blank">
              Visitar Página Oficial <i class="fas fa-external-link-alt"></i>
            </a>
            <a href="#contacto" class="btn btn-outline">
              Contactar Asesor <i class="fas fa-phone"></i>
            </a>
          </div>
        </div>
        
        <div class="info-image">
          <img src="images/credito-info.jpg" 
               alt="Imagen informativa sobre el Crédito MiVivienda"
               onerror="this.src='https://via.placeholder.com/600x400?text=Cr%C3%A9dito+Mi+Vivienda'">
        </div>
        
      </div>
    </div>
  </section>

  <!-- Contacto Section -->
  <section id="contacto" class="contacto-section section-padding">
    <div class="container">
      <div class="section-title">
        <h2>¿Necesitas más información?</h2>
        <p>Nuestros asesores especializados están listos para ayudarte en todo el proceso</p>
      </div>

      <div class="contacto-grid">
        <div class="contacto-directo">
          <h3>Contacto directo</h3>
          <p>Comunícate con un asesor ahora mismo</p>
          
          <div class="contacto-metodo">
            <div class="contacto-icon">
              <i class="fas fa-phone"></i>
            </div>
            <div class="contacto-info">
              <h4>Llámanos</h4>
              <p>Lunes a Sábado 9 am - 5 pm</p>
              <p class="contacto-dato">+51 948 734 448</p>
            </div>
          </div>
          
          <div class="contacto-metodo">
            <div class="contacto-icon whatsapp">
              <i class="fab fa-whatsapp"></i>
            </div>
            <div class="contacto-info">
              <h4>WhatsApp</h4>
              <p>Respuesta inmediata</p>
              <a href="https://wa.me/51948734448?text=Hola,%20estoy%20interesado%20en%20el%20Crédito%20Mi%20Vivienda" class="btn btn-whatsapp" target="_blank">
                Contactar por WhatsApp
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>

  <!-- Template para las tarjetas de propiedades -->
  <template id="property-card-template">
    <div class="property-card" data-id="" data-type="">
      <div class="property-image">
        <img src="/placeholder.svg" alt="">
        <span class="property-tag"></span>
      </div>
      <div class="property-info">
        <h3 class="property-title"></h3>
        <div class="property-location">
          <i class="fas fa-map-marker-alt"></i>
          <span></span>
        </div>
        <div class="property-price"></div>
        <div class="property-features">
          <div class="property-feature bedrooms">
            <i class="fas fa-bed"></i>
            <span></span>
          </div>
          <div class="property-feature bathrooms">
            <i class="fas fa-bath"></i>
            <span></span>
          </div>
          <div class="property-feature area">
            <i class="fas fa-ruler-combined"></i>
            <span></span>
          </div>
        </div>
        <a href="" class="btn btn-primary btn-block">Ver más detalles</a>
      </div>
    </div>
  </template>

  <!-- JavaScript -->
  <script src="js/credito-mi-vivienda.js"></script>
  <script src="js/properties.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Manejar el formulario de pre-evaluación
      const preEvaluacionForm = document.getElementById('pre-evaluacion-form');
      const resultBtn = document.querySelector('.btn-result');

      // Manejar las preguntas y respuestas
      document.querySelectorAll('input[name="terreno"], input[name="ingresos"], input[name="calificacion"]').forEach(radio => {
        radio.addEventListener('change', function() {
          // Solo registrar la selección
          console.log(`${this.name}: ${this.value}`);
        });
      });

      // Actualizar año actual en el footer
      document.getElementById('current-year').textContent = new Date().getFullYear();
    });
  </script>
</body>
</html>
