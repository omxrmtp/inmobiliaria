document.addEventListener("DOMContentLoaded", () => {
  // Variables
  const questions = document.querySelectorAll(".question-container")
  const resultSuccess = document.getElementById("result-success")
  const resultFailure = document.getElementById("result-failure")
  const nextButtons = document.querySelectorAll(".btn-next")
  const prevButtons = document.querySelectorAll(".btn-prev")
  const resultButton = document.querySelector(".btn-result")
  const retryButton = document.querySelector(".btn-retry")
  const showContactFormButton = document.getElementById("show-contact-form")
  const contactForm = document.getElementById("contact-form")

  // Campos ocultos para las respuestas
  const terrenoHidden = document.getElementById("terreno_hidden")
  const ingresosHidden = document.getElementById("ingresos_hidden")
  const calificacionHidden = document.getElementById("calificacion_hidden")

  // Accordion functionality
  const accordionHeaders = document.querySelectorAll(".accordion-header")

  accordionHeaders.forEach((header) => {
    header.addEventListener("click", function () {
      const accordionId = this.getAttribute("data-accordion")
      const content = document.getElementById("accordion-" + accordionId)

      // Toggle active class on header
      this.classList.toggle("active")

      // Toggle active class on content
      content.classList.toggle("active")
    })
  })

  // Pre-evaluación form functionality
  let currentQuestion = 1

  // Guardar respuestas en campos ocultos
  document.querySelectorAll('input[name="terreno"]').forEach(radio => {
    radio.addEventListener('change', function() {
      if (terrenoHidden) terrenoHidden.value = this.value;
    });
  });

  document.querySelectorAll('input[name="ingresos"]').forEach(radio => {
    radio.addEventListener('change', function() {
      if (ingresosHidden) ingresosHidden.value = this.value;
    });
  });

  document.querySelectorAll('input[name="calificacion"]').forEach(radio => {
    radio.addEventListener('change', function() {
      if (calificacionHidden) calificacionHidden.value = this.value;
    });
  });

  // Next question
  nextButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const questionNumber = Number.parseInt(this.getAttribute("data-question"))
      const radioName = questionNumber === 1 ? "terreno" : questionNumber === 2 ? "ingresos" : ""
      const radioButtons = document.getElementsByName(radioName)

      // Check if an option is selected
      let isSelected = false
      radioButtons.forEach((radio) => {
        if (radio.checked) {
          isSelected = true
        }
      })

      if (isSelected) {
        // Hide current question
        document.getElementById(`question-${questionNumber}`).classList.remove("active")

        // Show next question
        document.getElementById(`question-${questionNumber + 1}`).classList.add("active")

        currentQuestion = questionNumber + 1
      } else {
        alert("Por favor selecciona una opción")
      }
    })
  })

  // Previous question
  prevButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const questionNumber = Number.parseInt(this.getAttribute("data-question"))

      // Hide current question
      document.getElementById(`question-${questionNumber}`).classList.remove("active")

      // Show previous question
      document.getElementById(`question-${questionNumber - 1}`).classList.add("active")

      currentQuestion = questionNumber - 1
    })
  })

  // Show result
  if (resultButton) {
    resultButton.addEventListener("click", () => {
      const terrenoValue = document.querySelector('input[name="terreno"]:checked')?.value
      const ingresosValue = document.querySelector('input[name="ingresos"]:checked')?.value
      const calificacionValue = document.querySelector('input[name="calificacion"]:checked')?.value

      if (!calificacionValue) {
        alert("Por favor selecciona una opción")
        return
      }

      // Hide all questions
      questions.forEach((question) => {
        question.classList.remove("active")
      })

      // Show appropriate result
      if (terrenoValue === "si" && ingresosValue === "si" && calificacionValue === "si") {
        resultSuccess.style.display = "block"
        resultFailure.style.display = "none"
      } else {
        resultSuccess.style.display = "none"
        resultFailure.style.display = "block"
      }
    })
  }

  // Show contact form button
  if (showContactFormButton) {
    showContactFormButton.addEventListener("click", function () {
      this.style.display = "none"
      contactForm.style.display = "block"
    })
  }

  // Retry button
  if (retryButton) {
    retryButton.addEventListener("click", () => {
      // Reset form
      document.querySelectorAll('input[type="radio"]').forEach((radio) => {
        radio.checked = false
      })

      // Reset hidden fields
      if (terrenoHidden) terrenoHidden.value = '';
      if (ingresosHidden) ingresosHidden.value = '';
      if (calificacionHidden) calificacionHidden.value = '';

      // Hide results
      resultSuccess.style.display = "none"
      resultFailure.style.display = "none"

      // Show first question
      questions.forEach((question) => {
        question.classList.remove("active")
      })
      document.getElementById("question-1").classList.add("active")

      currentQuestion = 1
    })
  }

  // WhatsApp integration for contact form
  const whatsappLink = document.getElementById("whatsapp-link")
  const nombreInput = document.getElementById("nombre")
  const ciudadInput = document.getElementById("ciudad")
  const telefonoInput = document.getElementById("telefono")

  if (whatsappLink && nombreInput && ciudadInput && telefonoInput) {
    function updateWhatsAppLink() {
      const nombre = nombreInput.value || ""
      const ciudad = ciudadInput.value || ""
      const telefono = telefonoInput.value || ""

      const message = `Hola, soy ${nombre} de ${ciudad}. He realizado la pre-evaluación para el Crédito Mi Vivienda y me gustaría verificar mis requisitos.`
      whatsappLink.href = `https://wa.me/51948734448?text=${encodeURIComponent(message)}`
    }

    nombreInput.addEventListener("input", updateWhatsAppLink)
    ciudadInput.addEventListener("input", updateWhatsAppLink)
    telefonoInput.addEventListener("input", updateWhatsAppLink)

    // Set initial WhatsApp link
    updateWhatsAppLink()
  }

  // Loan Simulator with Range Sliders
  const valorViviendaSlider = document.getElementById("valor-vivienda")
  const valorViviendaDisplay = document.getElementById("valor-vivienda-display")
  const cuotaInicialSlider = document.getElementById("cuota-inicial")
  const cuotaInicialDisplay = document.getElementById("cuota-inicial-display")
  const plazoSlider = document.getElementById("plazo")
  const plazoDisplay = document.getElementById("plazo-display")
  const calcularBtn = document.getElementById("calcular-btn")

  // Format currency function
  function formatCurrency(value) {
    return new Intl.NumberFormat('es-PE', {
      style: 'currency',
      currency: 'PEN',
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(value)
  }

  // Update displays when sliders change
  if (valorViviendaSlider) {
    valorViviendaSlider.addEventListener("input", function() {
      valorViviendaDisplay.textContent = formatCurrency(this.value);
    });
  }

  if (cuotaInicialSlider) {
    cuotaInicialSlider.addEventListener("input", function() {
      cuotaInicialDisplay.textContent = `${this.value}%`;
    });
  }

  if (plazoSlider) {
    plazoSlider.addEventListener("input", function() {
      plazoDisplay.textContent = `${this.value} años`;
    });
  }

  // Calculate loan
  if (calcularBtn) {
    calcularBtn.addEventListener("click", () => {
      const valorVivienda = Number.parseFloat(valorViviendaSlider.value)
      const cuotaInicial = Number.parseFloat(cuotaInicialSlider.value)
      const plazo = Number.parseInt(plazoSlider.value)

      if (isNaN(valorVivienda) || isNaN(cuotaInicial) || isNaN(plazo)) {
        alert("Por favor completa todos los campos correctamente")
        return
      }

      if (valorVivienda < 65200 || valorVivienda > 436100) {
        alert("El valor de la vivienda debe estar entre S/ 65,200 y S/ 436,100")
        return
      }

      // Calcular monto del préstamo
      const montoInicial = valorVivienda * (cuotaInicial / 100)
      const montoPrestamo = valorVivienda - montoInicial

      // Determinar el Bono del Buen Pagador según el valor de la vivienda
      let bonoBBP = 0
      if (valorVivienda >= 65200 && valorVivienda <= 93100) {
        bonoBBP = 25700
      } else if (valorVivienda > 93100 && valorVivienda <= 139400) {
        bonoBBP = 21400
      } else if (valorVivienda > 139400 && valorVivienda <= 232200) {
        bonoBBP = 19600
      } else if (valorVivienda > 232200 && valorVivienda <= 343900) {
        bonoBBP = 10800
      } else if (valorVivienda > 343900 && valorVivienda <= 436100) {
        bonoBBP = 6400
      }

      // Calcular cuota mensual aproximada (usando una tasa de interés referencial del 7.5% anual)
      const tasaInteresMensual = 0.075 / 12
      const numeroCuotas = plazo * 12
      const montoFinanciado = montoPrestamo - bonoBBP

      const cuotaMensual =
        (montoFinanciado * (tasaInteresMensual * Math.pow(1 + tasaInteresMensual, numeroCuotas))) /
        (Math.pow(1 + tasaInteresMensual, numeroCuotas) - 1)

      // Mostrar resultados
      document.getElementById("monto-prestamo").textContent = formatCurrency(montoPrestamo)
      document.getElementById("bono-bbp").textContent = formatCurrency(bonoBBP)
      document.getElementById("cuota-mensual").textContent = formatCurrency(cuotaMensual)

      document.getElementById("loanResult").classList.add("active")
    })
  }

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault()

      const targetId = this.getAttribute("href")
      if (targetId === "#") return

      const targetElement = document.querySelector(targetId)
      if (targetElement) {
        window.scrollTo({
          top: targetElement.offsetTop - 80,
          behavior: "smooth",
        })
      }
    })
  })

  // Set current year in footer
  const currentYearElement = document.getElementById("current-year")
  if (currentYearElement) {
    currentYearElement.textContent = new Date().getFullYear()
  }

  // Animate elements when they come into view
  const animateElements = document.querySelectorAll('.animate-fade-up')
  
  function checkIfInView() {
    animateElements.forEach(element => {
      const elementTop = element.getBoundingClientRect().top
      const windowHeight = window.innerHeight
      
      if (elementTop < windowHeight - 100) {
        element.classList.add('animated')
      }
    })
  }
  
  // Run on load
  checkIfInView()
  
  // Run on scroll
  window.addEventListener('scroll', checkIfInView)
})