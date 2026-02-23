/**
 * Archivo JavaScript para la página de Habilitaciones
 * Maneja la carga, filtrado y visualización de proyectos
 */

document.addEventListener("DOMContentLoaded", () => {
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
  const etiquetaFiltro = document.body.getAttribute('data-etiqueta-filtro') || 'habilitaciones'

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

    console.log(`URL final: ${url}`)
    
    fetch(url, {
      method: "GET",
      headers: { "X-Requested-With": "XMLHttpRequest" },
    })
      .then((response) => {
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

      const btnContactar = clone.querySelector(".btn-view-details")
      if (btnContactar) {
        btnContactar.setAttribute("data-property-id", property.id)
        btnContactar.onclick = function(e) {
          e.preventDefault()
          // Ir a contacto
          window.location.href = `contact.html?project=${property.id}`
        }
      }

      const btnViewModal = clone.querySelector(".btn-view-modal")
      if (btnViewModal) {
        btnViewModal.setAttribute("data-property-id", property.id)
        btnViewModal.onclick = function(e) {
          e.preventDefault()
          openPropertyModal(property)
        }
      }

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
            <a href="contact.html?project=${property.id}" class="btn btn-primary">Contactar</a>
            <a href="contact.html?project=${property.id}" class="btn btn-outline">Más información</a>
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
