document.addEventListener("DOMContentLoaded", () => {
    const chatMessages = document.getElementById("chat-messages")
    const userMessageInput = document.getElementById("user-message")
    const sendMessageButton = document.getElementById("send-message")
    const suggestionChips = document.querySelectorAll(".suggestion-chip")
  
    // Función para agregar un mensaje al chat
    function addMessage(message, isUser = false) {
      const messageElement = document.createElement("div")
      messageElement.classList.add("message")
      messageElement.classList.add(isUser ? "user" : "assistant")
  
      const now = new Date()
      const hours = now.getHours().toString().padStart(2, "0")
      const minutes = now.getMinutes().toString().padStart(2, "0")
      const timeString = `${hours}:${minutes}`
  
      messageElement.innerHTML = `
              <div class="message-content">
                  <p>${message}</p>
              </div>
              <div class="message-time">${timeString}</div>
          `
  
      chatMessages.appendChild(messageElement)
      chatMessages.scrollTop = chatMessages.scrollHeight
    }
  
    // Función para obtener respuesta del asistente
    function getAssistantResponse(userMessage) {
      // Convertir mensaje a minúsculas para facilitar la comparación
      const message = userMessage.toLowerCase()
  
      // Respuestas predefinidas basadas en palabras clave
      if (message.includes("techo propio")) {
        return "El programa Techo Propio es una iniciativa del gobierno peruano que facilita la adquisición, construcción o mejoramiento de viviendas a familias de bajos recursos económicos. A través del Bono Familiar Habitacional (BFH), el Estado otorga un subsidio directo no reembolsable que cubre parte del valor de la vivienda. ¿Te gustaría conocer los requisitos o el monto del subsidio?";
      } else if (message.includes("requisito")) {
        return "Los requisitos generales incluyen: ser mayor de edad, conformar una familia, tener ingresos mensuales no mayores a S/ 3,715 para comprar y S/ 2,706 para construir o mejorar, no tener otra vivienda, y no haber recibido apoyo habitacional previo del Estado. ¿Necesitas más información sobre algún requisito específico?";
      } else if (message.includes("chiclayo")) {
        return "Actualmente, contamos con propiedades en Chiclayo dentro del programa Techo Propio y en otras zonas de alta demanda. Algunas de las mejores ubicaciones incluyen Pimentel, La Victoria y José Leonardo Ortiz. ¿Tienes una zona específica en mente?";
      } else if (message.includes("beneficio") && message.includes("techo propio")) {
        return "El programa Techo Propio ofrece varios beneficios como el Bono Familiar Habitacional, que cubre hasta S/ 43,312 del costo de la vivienda. Además, permite acceder a tasas de financiamiento bajas y facilita la compra, construcción o mejora de una vivienda. ¿Quieres conocer cómo postular?";
      } else if (message.includes("postular") && message.includes("techo propio")) {
        return "Para postular al programa Techo Propio en Chiclayo, debes inscribirte en el Fondo Mivivienda, elegir una vivienda certificada y cumplir con los requisitos establecidos. ¿Te gustaría que te ayudemos con el proceso de inscripción?";
      } else if (message.includes("propiedad") || message.includes("casa") || message.includes("departamento") || message.includes("chiclayo") || message.includes("lambayeque")) {
        return "Contamos con una amplia variedad de propiedades en diferentes zonas de la ciudad, incluyendo Lambayeque y Chiclayo. Tenemos casas, departamentos, terrenos y locales comerciales. ¿Buscas alguna zona específica o tienes un presupuesto en mente? Puedes visitar nuestra sección de propiedades para ver todas las opciones disponibles.";
      } else if (message.includes("precio") || message.includes("costo") || message.includes("valor")) {
        return "Los precios de nuestras propiedades varían según la ubicación, tamaño y características. Tenemos opciones desde S/ 120,000 para viviendas que califican para Techo Propio, hasta propiedades de lujo. ¿Tienes un presupuesto específico? Podemos ayudarte a encontrar opciones que se ajusten a tus posibilidades.";
      } else if (message.includes("contacto") || message.includes("asesor") || message.includes("visita")) {
        return "Puedes contactar a nuestros asesores llamando al +51 123 456 789 o enviando un correo a info@inmobiliariapro.com. También puedes agendar una visita a cualquiera de nuestras propiedades a través de nuestra página web. ¿Te gustaría que un asesor te contacte directamente?";
      } else if (message.includes("zonas recomendadas") || message.includes("dónde comprar")) {
        return "En Chiclayo, algunas de las zonas más recomendadas para comprar una vivienda son: Pimentel (por su cercanía a la playa), La Victoria (por su infraestructura y servicios) y José Leonardo Ortiz (por sus precios accesibles). ¿Quieres conocer opciones específicas en estas zonas?";
      } else if (message.includes("financiamiento") || message.includes("tasa de interés")) {
        return "El programa Techo Propio ofrece tasas de financiamiento accesibles a través del Fondo Mivivienda. Además, puedes solicitar préstamos hipotecarios en bancos asociados con tasas competitivas. ¿Te gustaría que te ayudemos a calcular la cuota mensual según tu presupuesto?";
      } else if (message.includes("gracias") || message.includes("adios") || message.includes("chau")) {
        return "¡Gracias por contactarnos! Estamos para ayudarte en todo lo que necesites. Si tienes más preguntas en el futuro, no dudes en volver a consultarnos. ¡Que tengas un excelente día!";
      } else {
        return "Entiendo tu consulta. Para brindarte información más precisa, te recomendaría hablar con uno de nuestros asesores inmobiliarios. Ellos podrán atender tu caso específico y ofrecerte las mejores opciones según tus necesidades. ¿Te gustaría que te contactemos o prefieres hacer otra consulta?";
      }
    }

  
    // Función para manejar el envío de mensajes
    function handleSendMessage() {
      const userMessage = userMessageInput.value.trim()
  
      if (userMessage !== "") {
        // Agregar mensaje del usuario
        addMessage(userMessage, true)
  
        // Limpiar input
        userMessageInput.value = ""
  
        // Simular tiempo de respuesta
        setTimeout(() => {
          // Obtener y agregar respuesta del asistente
          const assistantResponse = getAssistantResponse(userMessage)
          addMessage(assistantResponse)
        }, 1000)
      }
    }
  
    // Event listeners
    sendMessageButton.addEventListener("click", handleSendMessage)
  
    userMessageInput.addEventListener("keypress", (e) => {
      if (e.key === "Enter") {
        handleSendMessage()
      }
    })
  
    // Event listeners para los chips de sugerencia
    suggestionChips.forEach((chip) => {
      chip.addEventListener("click", function () {
        const message = this.getAttribute("data-message")
        userMessageInput.value = message
        handleSendMessage()
      })
    })
  })
  
  