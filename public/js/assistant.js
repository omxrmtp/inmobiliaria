document.addEventListener("DOMContentLoaded", () => {
    const chatMessages = document.getElementById("chat-messages")
    const userMessage = document.getElementById("user-message")
    const sendMessage = document.getElementById("send-message")
  
    // Respuestas predefinidas del asistente
    const responses = {
      hola: "¡Hola! ¿En qué puedo ayudarte hoy?",
      propiedades:
        "Tenemos una amplia variedad de propiedades disponibles. Puedes verlas en la sección de Propiedades o decirme qué tipo de propiedad estás buscando.",
      "techo propio":
        "El programa Techo Propio es una iniciativa del gobierno peruano que facilita la adquisición, construcción o mejoramiento de viviendas a familias de bajos recursos. ¿Te gustaría saber más sobre los requisitos?",
      requisitos:
        "Los requisitos principales incluyen: ser mayor de edad, conformar una familia, tener ingresos mensuales dentro de los límites establecidos, y no tener otra propiedad. ¿Necesitas información más específica?",
      contacto:
        "Puedes contactarnos llamando al (01) 555-1234, enviando un correo a info@inmobiliariapro.com o visitando nuestra oficina en Av. Principal 123, Lima.",
      gracias: "¡De nada! Estoy aquí para ayudarte. ¿Hay algo más en lo que pueda asistirte?",
      adios: "¡Hasta pronto! Si tienes más preguntas, no dudes en volver a consultarme.",
      default:
        "Lo siento, no tengo información específica sobre eso. ¿Puedes reformular tu pregunta o consultar sobre propiedades, Techo Propio, financiamiento o contacto?",
    }
  
    // Función para agregar mensajes al chat
    function addMessage(message, sender) {
      const messageElement = document.createElement("div")
      messageElement.classList.add("message", sender)
      messageElement.innerHTML = `<p>${message}</p>`
      chatMessages.appendChild(messageElement)
  
      // Scroll al último mensaje
      chatMessages.scrollTop = chatMessages.scrollHeight
    }
  
    // Función para obtener respuesta del asistente
    function getResponse(message) {
      message = message.toLowerCase()
  
      // Buscar coincidencias en las respuestas predefinidas
      for (const [key, response] of Object.entries(responses)) {
        if (message.includes(key)) {
          return response
        }
      }
  
      // Si no hay coincidencias, devolver respuesta por defecto
      return responses.default
    }
  
    // Función para enviar mensaje
    function sendUserMessage() {
      const message = userMessage.value.trim()
  
      if (message) {
        // Agregar mensaje del usuario
        addMessage(message, "user")
  
        // Limpiar input
        userMessage.value = ""
  
        // Simular respuesta del asistente después de un breve retraso
        setTimeout(() => {
          const response = getResponse(message)
          addMessage(response, "assistant")
        }, 500)
      }
    }
  
    // Event listeners
    if (sendMessage) {
      sendMessage.addEventListener("click", sendUserMessage)
    }
  
    if (userMessage) {
      userMessage.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
          sendUserMessage()
        }
      })
    }
  })
  
  