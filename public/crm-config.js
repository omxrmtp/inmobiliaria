/**
 * CRM Configuration for Web Form
 * Centralizes all lead capture configuration
 */

(function (window) {
  const CRM_CONFIG = {
    // API Configuration
    api: {
      baseUrl: window.CRM_API_BASE || '/api',
      endpoints: {
        lead: '/leads',
        contact: '/contact'
      },
      timeout: 5000 // 5 segundos
    },

    // WhatsApp Configuration
    whatsapp: {
      number: '51948734448',
      prefix: '+51'
    },

    // Lead Form Configuration
    form: {
      id: 'lwf-form',
      fields: {
        nombre: 'lwf-nombre',
        apellido: 'lwf-apellido',
        email: 'lwf-email',
        telefono: 'lwf-telefono',
        interes: 'lwf-interes', // radio button group
        origen: 'lwf-origen',
        mensaje: 'lwf-mensaje'
      },
      requiredFields: ['nombre', 'apellido', 'email', 'telefono', 'interes'],
      defaultOrigen: 'Página Web'
    },

    // Interest/Etiqueta Mapping
    intereses: {
      'Techo Propio': { label: 'Techo Propio', icon: '🏠' },
      'Crédito MiVivienda': { label: 'Crédito MiVivienda', icon: '💳' },
      'Comprador': { label: 'Comprador', icon: '👤' },
      'Vendedor': { label: 'Vendedor', icon: '📊' }
    },

    // UI Messages
    messages: {
      validation: {
        required: 'Por favor, completa los campos obligatorios.',
        email: 'Por favor, ingresa un email válido.',
        phone: 'Por favor, ingresa un teléfono válido.'
      },
      submit: {
        pending: 'Enviando...',
        success: '✅ Lead enviado al CRM',
        error: 'Error al guardar el lead',
        whatsapp: 'Abriendo WhatsApp...'
      }
    },

    /**
     * Normalize phone number - remove non-digits
     */
    normalizePhone: function (phone) {
      return (phone || '').toString().replace(/[^0-9]/g, '');
    },

    /**
     * Validate email format
     */
    isValidEmail: function (email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(email);
    },

    /**
     * Validate phone number
     */
    isValidPhone: function (phone) {
      const normalized = this.normalizePhone(phone);
      return normalized.length >= 7 && normalized.length <= 15;
    },

    /**
     * Get full API endpoint URL
     */
    getEndpointUrl: function (endpoint) {
      return this.api.baseUrl.replace(/\/$/, '') + endpoint;
    },

    /**
     * Send lead to CRM
     */
    sendLead: async function (leadData) {
      try {
        const url = this.getEndpointUrl(this.api.endpoints.lead);
        console.log('📤 Enviando lead a CRM:', url);

        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), this.api.timeout);

        const response = await fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            nombre: leadData.nombre,
            apellido: leadData.apellido,
            email: leadData.email,
            telefono: leadData.telefono,
            interes: leadData.interes,
            origen: leadData.origen || this.form.defaultOrigen,
            mensaje: leadData.mensaje || ''
          }),
          signal: controller.signal
        });

        clearTimeout(timeout);

        if (!response.ok) {
          throw new Error(`HTTP Error: ${response.status}`);
        }

        const data = await response.json();

        if (data.exito) {
          console.log('✅ Lead guardado en CRM:', data);
          return { success: true, data: data };
        } else {
          console.error('❌ Error al guardar lead:', data.mensaje);
          return { success: false, message: data.mensaje };
        }
      } catch (error) {
        console.error('❌ Error al conectar con CRM:', error);
        return { success: false, message: error.message };
      }
    },

    /**
     * Open WhatsApp with formatted message
     */
    openWhatsApp: function (leadData) {
      const lines = [
        'Hola, deseo más información sobre propiedades',
        '',
        'Datos del interesado:',
        `- Nombre: ${leadData.nombre} ${leadData.apellido}`,
        `- Email: ${leadData.email}`,
        `- Teléfono: ${leadData.telefono}`,
        `- Interés: ${leadData.interes}`
      ];

      if (leadData.origen && leadData.origen !== 'Web') {
        lines.push(`- Origen: ${leadData.origen}`);
      }

      if (leadData.mensaje) {
        lines.push('');
        lines.push(`Mensaje: ${leadData.mensaje}`);
      }

      const text = encodeURIComponent(lines.join('\n'));
      const url = `https://wa.me/${this.whatsapp.number}?text=${text}`;

      console.log('📱 Abriendo WhatsApp');
      window.open(url, '_blank');
    },

    /**
     * Log event for analytics
     */
    logEvent: function (eventName, eventData) {
      if (window.gtag) {
        gtag('event', eventName, eventData);
      }
      console.log(`📊 Event: ${eventName}`, eventData);
    }
  };

  // Expose globally
  window.CRM_CONFIG = CRM_CONFIG;

  // Also expose individual functions for backward compatibility
  window.sendLeadToCRM = CRM_CONFIG.sendLead.bind(CRM_CONFIG);
  window.openWhatsApp = CRM_CONFIG.openWhatsApp.bind(CRM_CONFIG);

})(window);
