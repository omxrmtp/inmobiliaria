<?php $page = 'home'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DelgadoPropiedades - Encuentra el hogar de tus sueños</title>
    <!-- Fuentes de Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos CSS -->
    <link rel="shortcut icon" href="images/propiedad.png" type="image/png">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/animations.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/testimonios.css">
    <style>
      /* Scoped styles for Lead WhatsApp Form - MEJORADO */
      #lead-whatsapp { 
        padding: 40px 0 60px 0; 
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
      }
      #lead-whatsapp .lwf-container { 
        max-width: 900px; 
        margin: 0 auto; 
        padding: 0 16px; 
      }
      #lead-whatsapp .lwf-card { 
        background: #ffffff; 
        border: none;
        border-radius: 20px; 
        box-shadow: 0 10px 40px rgba(0,0,0,0.08); 
        overflow: hidden;
        transition: box-shadow 0.3s ease;
      }
      #lead-whatsapp .lwf-card:hover {
        box-shadow: 0 15px 50px rgba(0,0,0,0.12);
      }
      #lead-whatsapp .lwf-header { 
        padding: 32px 28px 24px 28px;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: white;
      }
      #lead-whatsapp .lwf-title { 
        margin: 0 0 8px 0; 
        font-size: 28px; 
        font-weight: 800; 
        letter-spacing: -0.5px;
        color: white;
      }
      #lead-whatsapp .lwf-subtitle { 
        margin: 0; 
        color: #cbd5e1; 
        font-size: 14px;
        font-weight: 400;
      }
      #lead-whatsapp .lwf-body { 
        padding: 32px 28px; 
      }
      #lead-whatsapp .lwf-grid { 
        display: grid; 
        grid-template-columns: repeat(2, minmax(0, 1fr)); 
        gap: 20px; 
      }
      #lead-whatsapp .lwf-col-span-2 { 
        grid-column: span 2; 
      }
      #lead-whatsapp .lwf-field { 
        display: flex; 
        flex-direction: column; 
      }
      #lead-whatsapp .lwf-label { 
        font-size: 13px; 
        font-weight: 700; 
        color: #1e293b; 
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }
      #lead-whatsapp .lwf-input, 
      #lead-whatsapp .lwf-select, 
      #lead-whatsapp .lwf-textarea { 
        border: 2px solid #e2e8f0; 
        border-radius: 12px; 
        padding: 14px 16px; 
        font-size: 15px; 
        outline: none; 
        background: #f8fafc;
        font-family: inherit;
        transition: all 0.3s ease;
      }
      #lead-whatsapp .lwf-input::placeholder,
      #lead-whatsapp .lwf-select::placeholder,
      #lead-whatsapp .lwf-textarea::placeholder {
        color: #94a3b8;
      }
      #lead-whatsapp .lwf-textarea { 
        min-height: 110px; 
        resize: vertical;
      }
      #lead-whatsapp .lwf-input:focus, 
      #lead-whatsapp .lwf-select:focus, 
      #lead-whatsapp .lwf-textarea:focus { 
        border-color: #FCBA00;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(252, 186, 0, 0.1);
      }
      #lead-whatsapp .lwf-input.success,
      #lead-whatsapp .lwf-select.success,
      #lead-whatsapp .lwf-textarea.success {
        border-color: #10b981;
        background: #f0fdf4;
      }
      #lead-whatsapp .lwf-input.error,
      #lead-whatsapp .lwf-select.error,
      #lead-whatsapp .lwf-textarea.error {
        border-color: #ef4444;
        background: #fef2f2;
      }
      #lead-whatsapp .lwf-help { 
        font-size: 12px; 
        color: #94a3b8; 
        margin-top: 6px;
        font-style: italic;
      }
      #lead-whatsapp .lwf-error {
        font-size: 12px;
        color: #ef4444;
        margin-top: 6px;
        font-weight: 500;
      }
      #lead-whatsapp .lwf-radios { 
        display: flex; 
        flex-wrap: wrap; 
        gap: 12px;
      }
      #lead-whatsapp .lwf-radio { 
        display: inline-flex; 
        align-items: center; 
        gap: 8px; 
        border: 2px solid #e2e8f0; 
        border-radius: 10px; 
        padding: 10px 14px; 
        font-size: 14px; 
        cursor: pointer; 
        background: #f8fafc;
        transition: all 0.2s ease;
        font-weight: 500;
      }
      #lead-whatsapp .lwf-radio:hover {
        border-color: #FCBA00;
        background: #fffbeb;
      }
      #lead-whatsapp .lwf-radio input { 
        appearance: none; 
        width: 16px; 
        height: 16px; 
        border: 2px solid #cbd5e1; 
        border-radius: 50%; 
        display: inline-block; 
        position: relative;
        cursor: pointer;
        transition: all 0.2s ease;
      }
      #lead-whatsapp .lwf-radio input:checked { 
        border-color: #FCBA00;
        background: #FCBA00;
      }
      #lead-whatsapp .lwf-radio input:checked::after { 
        content: "✓"; 
        position: absolute; 
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 11px;
        font-weight: bold;
      }
      #lead-whatsapp .lwf-actions { 
        display: flex; 
        justify-content: center;
        gap: 12px;
        padding: 0;
        margin-top: 24px;
      }
      #lead-whatsapp .lwf-note { 
        font-size: 12px; 
        color: #64748b; 
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
        text-align: center;
      }
      #lead-whatsapp .btn {
        padding: 14px 32px;
        font-size: 15px;
        font-weight: 700;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }
      #lead-whatsapp .btn-primary {
        background: linear-gradient(135deg, #FCBA00 0%, #E5A200 100%);
        color: #1e293b;
        flex: 1;
        max-width: 300px;
      }
      #lead-whatsapp .btn-primary:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(252, 186, 0, 0.3);
      }
      #lead-whatsapp .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
      }
      @media (max-width: 768px) {
        #lead-whatsapp { 
          padding: 30px 0 50px 0;
        }
        #lead-whatsapp .lwf-header {
          padding: 24px 20px 18px 20px;
        }
        #lead-whatsapp .lwf-title {
          font-size: 24px;
        }
        #lead-whatsapp .lwf-body {
          padding: 24px 20px;
        }
        #lead-whatsapp .lwf-grid { 
          grid-template-columns: 1fr; 
          gap: 16px;
        }
        #lead-whatsapp .lwf-col-span-2 { 
          grid-column: span 1; 
        }
        #lead-whatsapp .lwf-input,
        #lead-whatsapp .lwf-select,
        #lead-whatsapp .lwf-textarea {
          font-size: 16px;
          padding: 12px 14px;
        }
        #lead-whatsapp .lwf-radios {
          gap: 8px;
        }
        #lead-whatsapp .lwf-radio {
          padding: 8px 12px;
          font-size: 13px;
        }
        #lead-whatsapp .lwf-actions {
          flex-direction: column;
        }
        #lead-whatsapp .btn {
          width: 100%;
          max-width: none;
        }
      }
    </style>
    <script src="config.js"></script>
    <script src="crm-config.js"></script>
    <script>
      // Detectar si estamos en desarrollo o producción
      const isDevelopment = window.location.hostname === 'localhost' || 
                           window.location.hostname === '127.0.0.1' ||
                           window.location.hostname.includes('192.168');
      
      window.CRM_API_BASE = window.CRM_API_BASE || '/api';
    </script>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <br>
        <div class="hero-slider">
                
            <div class="slide active" style="background-image: url('img/fondo.jpg');"></div>

        </div>
        <div class="hero-overlay"></div>
        <div class="container">
             <div class="hero-buttons slide-in-up">
                <a href="properties.php" class="btn btn-primary">Ver Propiedades</a>
                <a href="techo-propio.php" class="btn btn-outline">Programa Techo Propio</a>
                <a href="credito.php" class="btn btn-outline">Crédito MiVivienda</a>
            </div>
        </div>
    </section>

    <!-- Lead para CRM con Toast Notifications -->
    <section id="lead-whatsapp" aria-labelledby="lead-whatsapp-title">
      <div class="lwf-container">
        <div class="lwf-card">
          <div class="lwf-header">
            <h2 id="lead-whatsapp-title" class="lwf-title">Contáctanos</h2>
            <p class="lwf-subtitle">Completa tus datos y al enviar el asesor se contactará contigo.</p>
          </div>
          <div class="lwf-body">
            <!-- Toast Container -->
            <div id="lwf-toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; max-width: 400px;"></div>

            <form id="lwf-form" novalidate>
              <div class="lwf-grid">
                <div class="lwf-field">
                  <label class="lwf-label" for="lwf-nombre">Nombre *</label>
                  <input class="lwf-input" type="text" id="lwf-nombre" name="nombre" placeholder="Solo letras y espacios" required>
                  <div class="lwf-error" id="lwf-nombre-error"></div>
                </div>
                <div class="lwf-field">
                  <label class="lwf-label" for="lwf-apellido">Apellido *</label>
                  <input class="lwf-input" type="text" id="lwf-apellido" name="apellido" placeholder="Solo letras y espacios" required>
                  <div class="lwf-error" id="lwf-apellido-error"></div>
                </div>
                <div class="lwf-field">
                  <label class="lwf-label" for="lwf-email">Email *</label>
                  <input class="lwf-input" type="email" id="lwf-email" name="email" placeholder="ejemplo@correo.com" required>
                  <div class="lwf-error" id="lwf-email-error"></div>
                </div>
                <div class="lwf-field">
                  <label class="lwf-label" for="lwf-telefono">Teléfono *</label>
                  <input class="lwf-input" type="tel" id="lwf-telefono" name="telefono" placeholder="999 999 999" maxlength="9" required>
                  <div class="lwf-error" id="lwf-telefono-error"></div>
                </div>

                <div class="lwf-field lwf-col-span-2">
                  <span class="lwf-label">Interés *</span>
                  <div class="lwf-radios">
                    <label class="lwf-radio"><input type="radio" name="interes" value="Techo Propio" required>Techo Propio</label>
                    <label class="lwf-radio"><input type="radio" name="interes" value="MiVivienda" required>Crédito MiVivienda</label>
                    <label class="lwf-radio"><input type="radio" name="interes" value="Comprar" required>Comprar</label>
                    <label class="lwf-radio"><input type="radio" name="interes" value="Vender" required>Vender</label>
                    <label class="lwf-radio"><input type="radio" name="interes" value="Alquilar" required>Alquilar</label>
                    <label class="lwf-radio"><input type="radio" name="interes" value="Construccion" required>Construcción</label>
                  </div>
                  <div class="lwf-error" id="lwf-interes-error"></div>
                </div>

                <div class="lwf-field">
                  <label class="lwf-label" for="lwf-origen">¿Cómo nos conociste?</label>
                  <select class="lwf-select" id="lwf-origen" name="origen">
                    <option value="">Selecciona</option>
                    <option value="TikTok">TikTok</option>
                    <option value="YouTube">YouTube</option>
                    <option value="Instagram">Instagram</option>
                    <option value="Facebook">Facebook</option>
                    <option value="Recomendación">Recomendación</option>
                  </select>
                  <div class="lwf-help">Opcional</div>
                </div>

                <div class="lwf-field">
                  <label class="lwf-label" for="lwf-mensaje">Mensaje</label>
                  <textarea class="lwf-textarea" id="lwf-mensaje" name="mensaje" placeholder="Cuéntanos brevemente tu consulta"></textarea>
                  <div class="lwf-help">Opcional</div>
                </div>
              </div>
              <div class="lwf-actions">
                <button type="submit" class="btn btn-primary" id="lwf-submit-btn">Contactar</button>
              </div>
              <div class="lwf-note">Tus datos se guardarán en nuestro sistema y te contactaremos pronto. También recibirás un mensaje por WhatsApp.</div>
            </form>
          </div>
        </div>
      </div>
    </section>

    <style>
      /* Toast Styles */
      .lwf-toast {
        padding: 16px 20px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: flex-start;
        gap: 12px;
        animation: slideInRight 0.3s ease-out;
        min-width: 300px;
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

      @keyframes slideOutRight {
        from {
          transform: translateX(0);
          opacity: 1;
        }
        to {
          transform: translateX(400px);
          opacity: 0;
        }
      }

      .lwf-toast.removing {
        animation: slideOutRight 0.3s ease-out;
      }

      .lwf-toast-success {
        background: #ecfdf5;
        border: 1px solid #d1fae5;
        color: #065f46;
      }

      .lwf-toast-error {
        background: #fef2f2;
        border: 1px solid #fee2e2;
        color: #991b1b;
      }

      .lwf-toast-info {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e3a8a;
      }

      .lwf-toast-warning {
        background: #fffbeb;
        border: 1px solid #fcd34d;
        color: #78350f;
      }

      .lwf-toast-icon {
        font-size: 20px;
        flex-shrink: 0;
        line-height: 1;
      }

      .lwf-toast-content {
        flex: 1;
        font-size: 14px;
        line-height: 1.5;
      }

      .lwf-toast-close {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 18px;
        padding: 0;
        flex-shrink: 0;
        opacity: 0.6;
        transition: opacity 0.2s;
      }

      .lwf-toast-close:hover {
        opacity: 1;
      }

      /* Error Messages */
      .lwf-error {
        font-size: 12px;
        color: #dc2626;
        margin-top: 4px;
        min-height: 16px;
      }

      /* Input error state */
      .lwf-input.error,
      .lwf-select.error,
      .lwf-textarea.error {
        border-color: #fca5a5;
        background-color: #fef2f2;
      }

      .lwf-input.success,
      .lwf-select.success,
      .lwf-textarea.success {
        border-color: #86efac;
        background-color: #f0fdf4;
      }

      /* Loading spinner */
      .lwf-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(0,0,0,0.1);
        border-top-color: currentColor;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
      }

      @keyframes spin {
        to { transform: rotate(360deg); }
      }

      .lwf-submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
      }
    </style>

    <!-- Services Section -->
    <section class="features">
        <div class="container">
            <div class="section-title">
                <h2>Nuestros Servicios</h2>
                <p>Soluciones inmobiliarias adaptadas a tus necesidades</p>
            </div>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <h3 class="feature-title">Propiedades</h3>
                    <p class="feature-description">Amplia variedad de casas, departamentos, terrenos y locales comerciales en las mejores ubicaciones.</p>
                    <a href="properties.php" class="btn btn-sm">Ver propiedades</a>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3 class="feature-title">Asesoramiento</h3>
                    <p class="feature-description">Nuestros asesores te guiarán en todo el proceso de compra o venta según tus necesidades y presupuesto.</p>
                    <a href="contact.php" class="btn btn-sm">Contactar asesor</a>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="feature-title">Techo Propio</h3>
                    <p class="feature-description">Te ayudamos a acceder al programa Techo Propio y cumplir el sueño de tener tu casa propia.</p>
                    <a href="techo-propio.php" class="btn btn-sm">Más información</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonios Section -->
    <section class="testimonios-section">
        <div class="container">
            <div class="section-title">
                <h2>Lo que dicen nuestros clientes</h2>
                <p>Testimonios de clientes satisfechos con nuestro servicio</p>
            </div>
            <div class="testimonios-carousel">
                <div class="carousel-container">
                    <div class="testimonios-track" id="testimonios-track">
                        <!-- Los testimonios se cargarán aquí dinámicamente -->
                    </div>
                    <button class="carousel-btn prev" id="prev-btn">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="carousel-btn next" id="next-btn">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="carousel-dots" id="carousel-dots">
                    <!-- Los puntos se generarán dinámicamente -->
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- Inline JS for Lead WhatsApp Form - MEJORADO -->
    <script>
      (function() {
        // ============ UTILIDADES DE TOAST ==============
        var toastContainer = document.getElementById('lwf-toast-container');

        function showToast(message, type = 'info', duration = 5000) {
          var icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
          };

          var toast = document.createElement('div');
          toast.className = 'lwf-toast lwf-toast-' + type;
          toast.innerHTML = `
            <span class="lwf-toast-icon">${icons[type] || icons['info']}</span>
            <div class="lwf-toast-content">${message}</div>
            <button class="lwf-toast-close" type="button" aria-label="Cerrar notificación">×</button>
          `;

          toastContainer.appendChild(toast);

          var closeBtn = toast.querySelector('.lwf-toast-close');
          closeBtn.onclick = function() {
            removeToast(toast);
          };

          if (duration > 0) {
            setTimeout(function() {
              removeToast(toast);
            }, duration);
          }

          function removeToast(el) {
            el.classList.add('removing');
            setTimeout(function() {
              el.remove();
            }, 300);
          }
        }

        // ============ VALIDACIÓN DEL FORMULARIO ==============
        var form = document.getElementById('lwf-form');
        if (!form) return;

        function normalizePhone(v) {
          return (v || '').toString().replace(/[^0-9]/g, '');
        }

        function validateEmail(email) {
          var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          return re.test(email);
        }

        function validatePhone(phone) {
          var normalized = normalizePhone(phone);
          return normalized.length === 9; // exactamente 9 dígitos
        }

        function validateTextOnly(text) {
          // Solo letras, espacios y acentos
          var re = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
          return re.test(text.trim());
        }

        function clearErrors() {
          form.querySelectorAll('.lwf-input, .lwf-select, .lwf-textarea').forEach(function(el) {
            el.classList.remove('error', 'success');
          });
          form.querySelectorAll('.lwf-error').forEach(function(el) {
            el.textContent = '';
          });
        }

        function showError(fieldId, message) {
          var field = document.getElementById(fieldId);
          var errorDiv = document.getElementById(fieldId + '-error');
          if (field) {
            field.classList.add('error');
            field.classList.remove('success');
          }
          if (errorDiv) {
            errorDiv.textContent = message;
          }
        }

        function showSuccess(fieldId) {
          var field = document.getElementById(fieldId);
          if (field) {
            field.classList.remove('error');
            field.classList.add('success');
          }
        }

        function validateForm() {
          clearErrors();
          var isValid = true;

          // Validar nombre
          var nombre = document.getElementById('lwf-nombre').value.trim();
          if (!nombre) {
            showError('lwf-nombre', 'El nombre es requerido');
            isValid = false;
          } else if (!validateTextOnly(nombre)) {
            showError('lwf-nombre', 'El nombre solo debe contener letras y espacios');
            isValid = false;
          } else {
            showSuccess('lwf-nombre');
          }

          // Validar apellido
          var apellido = document.getElementById('lwf-apellido').value.trim();
          if (!apellido) {
            showError('lwf-apellido', 'El apellido es requerido');
            isValid = false;
          } else if (!validateTextOnly(apellido)) {
            showError('lwf-apellido', 'El apellido solo debe contener letras y espacios');
            isValid = false;
          } else {
            showSuccess('lwf-apellido');
          }

          // Validar email
          var email = document.getElementById('lwf-email').value.trim();
          if (!email) {
            showError('lwf-email', 'El email es requerido');
            isValid = false;
          } else if (!validateEmail(email)) {
            showError('lwf-email', 'El email no es válido');
            isValid = false;
          } else {
            showSuccess('lwf-email');
          }

          // Validar teléfono
          var telefono = document.getElementById('lwf-telefono').value.trim();
          if (!telefono) {
            showError('lwf-telefono', 'El teléfono es requerido');
            isValid = false;
          } else if (!validatePhone(telefono)) {
            showError('lwf-telefono', 'El teléfono debe tener 9 dígitos');
            isValid = false;
          } else {
            showSuccess('lwf-telefono');
          }

          // Validar interés
          var interes = document.querySelector('input[name="interes"]:checked');
          if (!interes) {
            showError('lwf-interes', 'Debes seleccionar una opción de interés');
            isValid = false;
          }

          return isValid;
        }

        // ============ MANEJO DEL ENVÍO ==============
        form.addEventListener('submit', function(e) {
          e.preventDefault();

          if (!validateForm()) {
            showToast('Por favor, corrige los errores en el formulario.', 'error');
            return;
          }

          var submitBtn = document.getElementById('lwf-submit-btn');
          var originalBtnText = submitBtn.textContent;
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<span class="lwf-spinner"></span> Enviando...';

          // Recoger datos
          var formData = new FormData(form);
          var data = {};
          formData.forEach(function(value, key) {
            data[key] = value;
          });

          // Enviar al CRM
          fetch('api/leads.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
          })
          .then(function(response) {
            if (!response.ok) {
              return response.json().then(function(err) { throw err; });
            }
            return response.json();
          })
          .then(function(result) {
            showToast('¡Gracias! Tus datos se han enviado correctamente.', 'success');
            form.reset();
            clearErrors();
            
            // Redirigir a WhatsApp (opcional, según lógica original)
            var phone = '51948734448';
            var text = `Hola, mi nombre es ${data.nombre} ${data.apellido}. Estoy interesado en ${data.interes}. ${data.mensaje ? 'Mensaje: ' + data.mensaje : ''}`;
            var whatsappUrl = `https://wa.me/${phone}?text=${encodeURIComponent(text)}`;
            
            setTimeout(function() {
              window.open(whatsappUrl, '_blank');
            }, 1500);
          })
          .catch(function(error) {
            console.error('Error:', error);
            showToast('Hubo un error al enviar tus datos. Por favor, intenta de nuevo.', 'error');
          })
          .finally(function() {
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;
          });
        });

        // Validación en tiempo real
        var inputs = form.querySelectorAll('.lwf-input, .lwf-select, .lwf-textarea');
        inputs.forEach(function(input) {
          input.addEventListener('blur', function() {
            // Validar solo este campo
            // (Simplificado: podrías llamar a validateForm() o lógica específica)
          });
          input.addEventListener('input', function() {
            if (this.classList.contains('error')) {
              this.classList.remove('error');
              var errorDiv = document.getElementById(this.id + '-error');
              if (errorDiv) errorDiv.textContent = '';
            }
          });
        });

      })();
    </script>
    <script src="js/testimonios-carousel.js"></script>
</body>
</html>
