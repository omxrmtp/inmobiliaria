document.addEventListener("DOMContentLoaded", () => {
  // Variables
  const questions = document.querySelectorAll(".question-container")
  const resultSuccess = document.getElementById("result-success")
  const resultFailure = document.getElementById("result-failure")
  const nextButtons = document.querySelectorAll(".btn-next")
  const prevButtons = document.querySelectorAll(".btn-prev")
  const resultButton = document.querySelector(".btn-result")
  const retryButton = document.querySelector(".btn-retry")
  const contactForm = document.getElementById("contact-form")
  const whatsappButton = document.querySelector(".btn-whatsapp-link")

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

  // Next question
  nextButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const questionNumber = parseInt(this.getAttribute("data-question"))
      let radioName

      // Determine which radio group to check based on question number
      switch (questionNumber) {
        case 1:
          radioName = "propiedad"
          break
        case 2:
          radioName = "ingresos"
          break
        case 3:
          radioName = "apoyo"
          break
        case 4:
          radioName = "familia"
          break
        case 5:
          radioName = "ahorro"
          break
        default:
          radioName = ""
      }

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
        const currentQuestionElement = document.getElementById(`question-${questionNumber}`)
        if (currentQuestionElement) {
          currentQuestionElement.classList.remove("active")
        }

        // Show next question
        const nextQuestionElement = document.getElementById(`question-${questionNumber + 1}`)
        if (nextQuestionElement) {
          nextQuestionElement.classList.add("active")
          currentQuestion = questionNumber + 1
        }
      } else {
        alert("Por favor selecciona una opción")
      }
    })
  })

  // Previous question
  prevButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const questionNumber = parseInt(this.getAttribute("data-question"))

      // Hide current question
      const currentQuestionElement = document.getElementById(`question-${questionNumber}`)
      if (currentQuestionElement) {
        currentQuestionElement.classList.remove("active")
      }

      // Show previous question
      const prevQuestionElement = document.getElementById(`question-${questionNumber - 1}`)
      if (prevQuestionElement) {
        prevQuestionElement.classList.add("active")
        currentQuestion = questionNumber - 1
      }
    })
  })

  // Show result
  if (resultButton) {
    resultButton.addEventListener("click", function () {
      // Hide all questions
      questions.forEach((question) => {
        question.style.display = "none"
        question.classList.remove("active")
      })

      // Determinar si el usuario califica basado en sus respuestas
      const propiedadValue = document.querySelector('input[name="propiedad"]:checked')?.value || '';
      const ingresosValue = document.querySelector('input[name="ingresos"]:checked')?.value || '';
      const apoyoValue = document.querySelector('input[name="apoyo"]:checked')?.value || '';
      const familiaValue = document.querySelector('input[name="familia"]:checked')?.value || '';
      const ahorroValue = document.querySelector('input[name="ahorro"]:checked')?.value || '';
      
      // Criterios para calificar (simplificados para este ejemplo)
      const esElegible = propiedadValue === 'no' && 
                         ingresosValue === 'no' && 
                         apoyoValue === 'no' && 
                         (familiaValue === 'si' || familiaValue === 'no') && 
                         ahorroValue === 'si';
      
      if (esElegible) {
        // Mostrar el resultado positivo
        if (resultSuccess) {
          resultSuccess.style.display = "block";
          resultSuccess.style.opacity = "1";
          resultSuccess.style.transform = "translateY(0)";
        }
        
        if (resultFailure) {
          resultFailure.style.display = "none";
        }
      } else {
        // Mostrar el resultado negativo
        if (resultFailure) {
          resultFailure.style.display = "block";
          resultFailure.style.opacity = "1";
          resultFailure.style.transform = "translateY(0)";
        }
        
        if (resultSuccess) {
          resultSuccess.style.display = "none";
        }
      }
    })
  }

  // Retry button
  if (retryButton) {
    retryButton.addEventListener("click", () => {
      // Reset form
      document.querySelectorAll('input[type="radio"]').forEach((radio) => {
        radio.checked = false
      })

      // Hide results
      if (resultSuccess) resultSuccess.style.display = "none";
      if (resultFailure) resultFailure.style.display = "none";

      // Show first question
      questions.forEach((question) => {
        question.classList.remove("active")
      })
      document.getElementById("question-1").classList.add("active")

      currentQuestion = 1
    })
  }

  // WhatsApp integration for contact form
  if (contactForm && whatsappButton) {
    const nombreInput = document.getElementById("full_name")
    const ciudadInput = document.getElementById("city")
    const telefonoInput = document.getElementById("phone")

    if (nombreInput && ciudadInput && telefonoInput) {
      function updateWhatsAppLink() {
        const nombre = nombreInput.value || "[Tu nombre]"
        const ciudad = ciudadInput.value || "[Tu ciudad]"
        const telefono = telefonoInput.value || "[Tu teléfono]"

        const message = `Hola, soy ${nombre} de ${ciudad}. He realizado la pre-evaluación para el programa Techo Propio y me gustaría verificar mis requisitos.`
        whatsappButton.href = `https://wa.me/51948734448?text=${encodeURIComponent(message)}`
      }

      nombreInput.addEventListener("input", updateWhatsAppLink)
      ciudadInput.addEventListener("input", updateWhatsAppLink)
      telefonoInput.addEventListener("input", updateWhatsAppLink)

      // Set initial WhatsApp link
      updateWhatsAppLink()
    }
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
  const animateElements = document.querySelectorAll(".animate-fade-up")

  function checkIfInView() {
    animateElements.forEach((element) => {
      const elementTop = element.getBoundingClientRect().top
      const windowHeight = window.innerHeight

      if (elementTop < windowHeight - 100) {
        element.classList.add("animated")
      }
    })
  }

  // Run on load
  checkIfInView()

  // Run on scroll
  window.addEventListener("scroll", checkIfInView)

  // ==================== PROYECTOS SECTION ====================
  // Elementos del DOM
  const propertiesGrid = document.getElementById("properties-grid")
  const propertiesCount = document.getElementById("properties-count")
  const sortBySelect = document.getElementById("sort-by")
  const paginationContainer = document.getElementById("pagination")

  // Variables de estado
  let properties = []
  let filteredProperties = []
  let currentPage = 1
  const propertiesPerPage = 9
  let currentSort = "date-desc"
  
  // Obtener etiqueta de filtro desde data attribute
  const etiquetaFiltro = document.body.getAttribute('data-etiqueta-filtro') || 'techo propio'

  // Cargar propiedades al iniciar
  loadProperties()

  // Event Listeners
  if (sortBySelect) {
    sortBySelect.addEventListener("change", function () {
      currentSort = this.value
      sortProperties()
      displayProperties()
    })
  }

  /**
   * Carga las propiedades desde el servidor
   * @param {string} queryParams - Parámetros de búsqueda opcionales
   */
  function loadProperties(queryParams = "") {
    // Mostrar spinner de carga
    propertiesGrid.innerHTML = `
      <div class="loading-spinner">
        <i class="fas fa-spinner fa-spin"></i>
        <p>Cargando proyectos...</p>
      </div>
    `

    // Construir URL con timestamp para evitar caché
    let url = "get_properties.php"
    const params = new URLSearchParams()
    
    // Agregar parámetros de búsqueda si existen
    if (queryParams) {
      const searchParams = new URLSearchParams(queryParams)
      for (const [key, value] of searchParams) {
        params.append(key, value)
      }
    }
    
    // Agregar etiqueta de filtro
    params.append('etiqueta', etiquetaFiltro)
    
    // Agregar timestamp
    params.append('_', new Date().getTime())
    
    if (params.toString()) {
      url += "?" + params.toString()
    } else {
      url += "?_=" + new Date().getTime()
    }

    // Realizar petición AJAX con fetch y timeout
    const controller = new AbortController()
    const timeoutId = setTimeout(() => controller.abort(), 10000) // 10 segundos de timeout

    console.log(`URL final: ${url}`)
    
    fetch(url, {
      method: "GET",
      headers: { "X-Requested-With": "XMLHttpRequest" },
      signal: controller.signal,
    })
      .then((response) => {
        clearTimeout(timeoutId)
        console.log(`Respuesta del servidor: ${response.status}`)
        if (!response.ok) {
          throw new Error(`Error en la respuesta del servidor: ${response.status} ${response.statusText}`)
        }
        return response.json()
      })
      .then((data) => {
        console.log(`Datos recibidos:`, data)
        
        if (data.success) {
          properties = data.properties
          
          // Debug: mostrar propiedades cargadas
          console.log(`Propiedades cargadas: ${properties.length}`)
          properties.forEach(p => {
            console.log(`- ${p.title}: etiquetas = [${p.etiquetas?.join(', ') || 'ninguna'}]`)
          })

          // Aplicar filtro y ordenamiento actuales
          filterProperties()
          sortProperties()
          displayProperties()
        } else {
          propertiesGrid.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
              <i class="fas fa-exclamation-circle" style="font-size: 48px; color: #ccc; margin-bottom: 20px; display: block;"></i>
              <p style="color: #999; font-size: 18px;">Error al cargar los proyectos</p>
              <p style="color: #bbb; font-size: 14px;">${data.message || 'Intenta más tarde'}</p>
            </div>
          `
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        if (error.name === "AbortError") {
          propertiesGrid.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
              <i class="fas fa-clock" style="font-size: 48px; color: #ccc; margin-bottom: 20px; display: block;"></i>
              <p style="color: #999; font-size: 18px;">La solicitud tardó demasiado</p>
              <p style="color: #bbb; font-size: 14px;">Por favor, intenta de nuevo</p>
            </div>
          `
        } else {
          propertiesGrid.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
              <i class="fas fa-wifi-slash" style="font-size: 48px; color: #ccc; margin-bottom: 20px; display: block;"></i>
              <p style="color: #999; font-size: 18px;">No se pudo establecer conexión</p>
              <p style="color: #bbb; font-size: 14px;">Verifica tu conexión a internet o contacta al administrador</p>
            </div>
          `
        }
      })
  }

  /**
   * Filtra las propiedades según el filtro actual
   */
  function filterProperties() {
    filteredProperties = [...properties]

    // Actualizar contador
    if (propertiesCount) {
      propertiesCount.textContent = filteredProperties.length
    }

    // Actualizar paginación
    updatePagination()
  }

  /**
   * Ordena las propiedades según el criterio actual
   */
  function sortProperties() {
    switch (currentSort) {
      case "price-asc":
        filteredProperties.sort((a, b) => Number.parseFloat(a.price) - Number.parseFloat(b.price))
        break
      case "price-desc":
        filteredProperties.sort((a, b) => Number.parseFloat(b.price) - Number.parseFloat(a.price))
        break
      case "date-asc":
        filteredProperties.sort((a, b) => new Date(a.created_at) - new Date(b.created_at))
        break
      case "date-desc":
        filteredProperties.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
        break
      default:
        // Por defecto, ordenar por fecha descendente
        filteredProperties.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    }
  }

  /**
   * Muestra las propiedades en la página
   */
  function displayProperties() {
    if (!propertiesGrid) return

    // Limpiar grid
    propertiesGrid.innerHTML = ""

    // Si no hay propiedades, mostrar mensaje
    if (filteredProperties.length === 0) {
      propertiesGrid.innerHTML = `
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
          <i class="fas fa-search" style="font-size: 48px; color: #ccc; margin-bottom: 20px; display: block;"></i>
          <p style="color: #999; font-size: 18px;">No se encontraron proyectos</p>
          <p style="color: #bbb; font-size: 14px;">Intenta con otros criterios de búsqueda</p>
        </div>
      `
      return
    }

    // Obtener propiedades de la página actual
    const startIndex = (currentPage - 1) * propertiesPerPage
    const endIndex = startIndex + propertiesPerPage
    const propertiesPage = filteredProperties.slice(startIndex, endIndex)

    // Renderizar propiedades
    propertiesPage.forEach((property) => {
      const template = document.getElementById("property-card-template")
      if (!template) return

      const clone = template.content.cloneNode(true)

      // Llenar datos
      const card = clone.querySelector(".property-card")
      if (card) {
        card.setAttribute("data-id", property.id)
        card.setAttribute("data-type", property.property_type)
      }

      const image = clone.querySelector(".property-image img")
      if (image) {
        image.src = property.images && property.images.length > 0 ? property.images[0].image_path : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300'%3E%3Crect fill='%23f0f0f0' width='400' height='300'/%3E%3Ctext x='50%25' y='50%25' font-size='18' fill='%23999' text-anchor='middle' dominant-baseline='middle'%3ESin imagen%3C/text%3E%3C/svg%3E"
        image.alt = property.title
      }

      const tag = clone.querySelector(".property-tag")
      if (tag) {
        const statusMap = {
          'disponible': 'Disponible',
          'reservado': 'Reservado',
          'vendido': 'Vendido',
          'construccion': 'En Construcción',
          'finalizado': 'Finalizado'
        }
        tag.textContent = statusMap[property.status] || property.status
      }

      const title = clone.querySelector(".property-title")
      if (title) title.textContent = property.title

      const location = clone.querySelector(".property-location span")
      if (location) location.textContent = property.city || "Ubicación no especificada"

      const price = clone.querySelector(".property-price")
      if (price) price.textContent = `S/. ${property.price ? property.price.toLocaleString("es-PE") : "Precio no especificado"}`

      // Botones eliminados: Los botones "¿Califico?" y "Ver más" han sido removidos del template
      // Solo se muestran las tarjetas con información básica del proyecto

      propertiesGrid.appendChild(clone)
    })
  }

  /**
   * Actualiza la paginación
   */
  function updatePagination() {
    if (!paginationContainer) return

    paginationContainer.innerHTML = ""

    const totalPages = Math.ceil(filteredProperties.length / propertiesPerPage)

    if (totalPages <= 1) return

    // Botón anterior
    const prevBtn = document.createElement("button")
    prevBtn.textContent = "← Anterior"
    prevBtn.className = "btn btn-outline"
    prevBtn.disabled = currentPage === 1
    prevBtn.onclick = () => {
      if (currentPage > 1) {
        currentPage--
        displayProperties()
        updatePagination()
        window.scrollTo({ top: propertiesGrid.offsetTop - 100, behavior: "smooth" })
      }
    }
    paginationContainer.appendChild(prevBtn)

    // Números de página
    for (let i = 1; i <= totalPages; i++) {
      const pageBtn = document.createElement("button")
      pageBtn.textContent = i
      pageBtn.className = i === currentPage ? "btn btn-primary" : "btn btn-outline"
      pageBtn.onclick = () => {
        currentPage = i
        displayProperties()
        updatePagination()
        window.scrollTo({ top: propertiesGrid.offsetTop - 100, behavior: "smooth" })
      }
      paginationContainer.appendChild(pageBtn)
    }

    // Botón siguiente
    const nextBtn = document.createElement("button")
    nextBtn.textContent = "Siguiente →"
    nextBtn.className = "btn btn-outline"
    nextBtn.disabled = currentPage === totalPages
    nextBtn.onclick = () => {
      if (currentPage < totalPages) {
        currentPage++
        displayProperties()
        updatePagination()
        window.scrollTo({ top: propertiesGrid.offsetTop - 100, behavior: "smooth" })
      }
    }
    paginationContainer.appendChild(nextBtn)
  }

  /**
   * Abre el modal con los detalles de la propiedad
   */
  function openPropertyModal(property) {
    const modal = document.getElementById("property-modal")
    const modalBody = document.getElementById("modal-body")
    
    if (!modal || !modalBody) return

    // Construir contenido del modal
    const statusMap = {
      'disponible': 'Disponible',
      'reservado': 'Reservado',
      'vendido': 'Vendido',
      'construccion': 'En Construcción',
      'finalizado': 'Finalizado'
    }

    const images = property.images && property.images.length > 0 
      ? property.images.map(img => img.image_path)
      : []

    let imagesHTML = ''
    if (images.length > 0) {
      imagesHTML = `
        <div class="modal-gallery">
          <div class="main-image">
            <img id="main-image" src="${images[0]}" alt="${property.title}">
          </div>
          ${images.length > 1 ? `
            <div class="thumbnail-gallery">
              ${images.map((img, idx) => `
                <img src="${img}" alt="Imagen ${idx + 1}" class="thumbnail" onclick="document.getElementById('main-image').src='${img}'">
              `).join('')}
            </div>
          ` : ''}
        </div>
      `
    }

    modalBody.innerHTML = `
      <div class="modal-property-details">
        ${imagesHTML}
        <div class="modal-info">
          <div class="modal-header">
            <h2>${property.title}</h2>
            <span class="property-status">${statusMap[property.status] || property.status}</span>
          </div>
          
          <div class="modal-location">
            <i class="fas fa-map-marker-alt"></i>
            <span>${property.city || 'Ubicación no especificada'}</span>
          </div>

          <div class="modal-price">
            <span>Precio:</span>
            <strong>S/. ${property.price ? property.price.toLocaleString("es-PE") : "Consultar"}</strong>
          </div>

          ${property.description ? `
            <div class="modal-description">
              <h3>Descripción</h3>
              <p>${property.description}</p>
            </div>
          ` : ''}

          <div class="modal-actions">
            <button class="btn btn-primary" onclick="closeModalAndScroll()">¿Califico?</button>
            <a href="contact.html?project=${property.id}" class="btn btn-outline">Contactar</a>
          </div>
        </div>
      </div>
    `

    modal.style.display = "flex"
  }

  // Cerrar modal
  const modalClose = document.querySelector(".modal-close")
  const modal = document.getElementById("property-modal")
  
  if (modalClose && modal) {
    modalClose.onclick = () => {
      modal.style.display = "none"
    }

    window.onclick = (event) => {
      if (event.target === modal) {
        modal.style.display = "none"
      }
    }
  }
})

/**
 * Cierra el modal y hace scroll a la pre-evaluación
 */
function closeModalAndScroll() {
  const modal = document.getElementById("property-modal")
  if (modal) {
    modal.style.display = "none"
  }
  
  const preEvalForm = document.getElementById("pre-evaluation-form")
  if (preEvalForm) {
    setTimeout(() => {
      window.scrollTo({
        top: preEvalForm.offsetTop - 80,
        behavior: "smooth",
      })
    }, 300)
  }
}
