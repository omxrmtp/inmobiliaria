/**
 * Configuración de Ambiente para Integración Web → CRM
 * 
 * Este archivo contiene la configuración del ambiente para la integración.
 * Personaliza estos valores según tu ambiente de deployment.
 */

// ============================================================================
// CONFIGURACIÓN DEL API
// ============================================================================

/**
 * API_BASE_URL
 * 
 * Desarrollo: http://localhost:5000/api
 * Producción: https://crm.tudominio.com/api
 * 
 * La URL se detecta automáticamente basada en el hostname,
 * pero puedes sobrescribir aquí si es necesario.
 */
const API_CONFIG = {
  // Desarrollo - Local
  development: {
    hostname: ['localhost', '127.0.0.1', '192.168'],
    apiBase: '/api',
    apiTimeout: 5000  // 5 segundos
  },

  // Producción
  production: {
    apiBase: '/api',
    apiTimeout: 10000  // 10 segundos
  },

  // Staging (opcional)
  staging: {
    apiBase: 'https://staging-crm.delgadopropiedades.com/api',
    apiTimeout: 7000  // 7 segundos
  }
};

// ============================================================================
// CONFIGURACIÓN DE WHATSAPP
// ============================================================================

const WHATSAPP_CONFIG = {
  // Número de WhatsApp (formato: código país + número, sin símbolos)
  number: '51948734448',

  // Prefijo (usado para display)
  prefix: '+51',

  // Nombre a mostrar
  name: 'Delgado Propiedades',

  // Mensaje inicial personalizable
  initialMessage: 'Hola, deseo más información sobre propiedades'
};

// ============================================================================
// CONFIGURACIÓN DE CAMPOS DEL FORMULARIO
// ============================================================================

const FORM_CONFIG = {
  // ID del formulario
  formId: 'lwf-form',

  // IDs de campos
  fields: {
    nombre: 'lwf-nombre',
    apellido: 'lwf-apellido',
    email: 'lwf-email',
    telefono: 'lwf-telefono',
    interes: 'lwf-interes',
    origen: 'lwf-origen',
    mensaje: 'lwf-mensaje'
  },

  // Campos requeridos
  required: ['nombre', 'apellido', 'email', 'telefono', 'interes'],

  // Valores por defecto
  defaults: {
    origen: 'Página Web'
  }
};

// ============================================================================
// MAPEO DE INTERESES
// ============================================================================

const INTEREST_MAP = {
  'Techo Propio': {
    label: 'Techo Propio',
    icon: '🏠',
    tag: 'Techo Propio'
  },
  'Crédito MiVivienda': {
    label: 'Crédito MiVivienda',
    icon: '💳',
    tag: 'Crédito MiVivienda'
  },
  'Comprador': {
    label: 'Comprador',
    icon: '👤',
    tag: 'Comprador'
  },
  'Vendedor': {
    label: 'Vendedor',
    icon: '📊',
    tag: 'Vendedor'
  }
};

// ============================================================================
// MENSAJES Y TEXTOS
// ============================================================================

const MESSAGES = {
  validation: {
    required: 'Por favor, completa los campos obligatorios.',
    email: 'Por favor, ingresa un email válido.',
    phone: 'Por favor, ingresa un teléfono válido.'
  },

  submit: {
    pending: 'Enviando...',
    success: '✅ Lead enviado al CRM',
    error: 'Error al guardar el lead',
    fallback: 'Abriendo WhatsApp...'
  },

  errors: {
    noConnection: 'No hay conexión con el servidor CRM',
    timeout: 'Timeout: CRM no respondió a tiempo',
    invalidResponse: 'Respuesta inválida del servidor',
    network: 'Error de red'
  }
};

// ============================================================================
// FUNCIÓN PARA OBTENER CONFIGURACIÓN DEL AMBIENTE
// ============================================================================

function getEnvironmentConfig() {
  const hostname = window.location.hostname;

  // Verificar si es desarrollo
  for (let devHost of API_CONFIG.development.hostname) {
    if (hostname.includes(devHost)) {
      console.log('🔧 Ambiente: DESARROLLO');
      return {
        apiBase: API_CONFIG.development.apiBase,
        timeout: API_CONFIG.development.apiTimeout,
        isDevelopment: true
      };
    }
  }

  // Por defecto, usar producción
  console.log('🔧 Ambiente: PRODUCCIÓN');
  return {
    apiBase: API_CONFIG.production.apiBase,
    timeout: API_CONFIG.production.apiTimeout,
    isDevelopment: false
  };
}

// ============================================================================
// EXPORTAR CONFIGURACIÓN
// ============================================================================

// Hacer disponible globalmente
window.APP_CONFIG = {
  api: getEnvironmentConfig(),
  whatsapp: WHATSAPP_CONFIG,
  form: FORM_CONFIG,
  interests: INTEREST_MAP,
  messages: MESSAGES
};

// Log en consola para debugging
if (window.APP_CONFIG.api.isDevelopment) {
  console.log('⚙️ Configuración de Desarrollo:');
  console.log('   API Base:', window.APP_CONFIG.api.apiBase);
  console.log('   Timeout:', window.APP_CONFIG.api.timeout, 'ms');
  console.log('   WhatsApp:', window.APP_CONFIG.whatsapp.number);
}
