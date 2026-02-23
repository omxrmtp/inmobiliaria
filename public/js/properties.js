/**
 * Archivo JavaScript para la página de propiedades
 * Maneja la carga, filtrado y visualización de propiedades
 */

document.addEventListener("DOMContentLoaded", () => {
  // Elementos del DOM
  const propertiesGrid = document.getElementById("properties-grid")
  const propertiesCount = document.getElementById("properties-count")
  const sortBySelect = document.getElementById("sort-by")
  const filterTabs = document.querySelectorAll(".filter-tab")
  const searchForm = document.getElementById("property-search-form")
  const paginationContainer = document.getElementById("pagination")

  // Variables de estado
  let properties = []
  let filteredProperties = []
  let currentPage = 1
  const propertiesPerPage = 9
  let currentFilter = "all"
  let currentSort = "date-desc"
  
  // Obtener etiqueta de filtro desde data attribute o parámetro
  const etiquetaFiltro = document.body.getAttribute('data-etiqueta-filtro') || 
                         new URLSearchParams(window.location.search).get('etiqueta') || 
                         null

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

  if (filterTabs) {
    filterTabs.forEach((tab) => {
      tab.addEventListener("click", function () {
        // Remover clase active de todas las pestañas
        filterTabs.forEach((t) => t.classList.remove("active"))

        // Agregar clase active a la pestaña clickeada
        this.classList.add("active")

        // Aplicar filtro
        currentFilter = this.getAttribute("data-filter")
        filterProperties()
        currentPage = 1 // Resetear a la primera página
        displayProperties()
      })
    })
  }

  if (searchForm) {
    searchForm.addEventListener("submit", function (e) {
      e.preventDefault()

      // Obtener valores del formulario
      const formData = new FormData(this)
      const searchParams = new URLSearchParams()

      for (const [key, value] of formData.entries()) {
        if (value) {
          searchParams.append(key, value)
        }
      }

      // Cargar propiedades con los filtros aplicados
      loadProperties(searchParams.toString())
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
                <p>Cargando propiedades...</p>
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
    
    // Agregar etiqueta de filtro si existe
    if (etiquetaFiltro) {
      params.append('etiqueta', etiquetaFiltro)
    }
    
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

          // Mostrar propiedades
          displayProperties()
        } else {
          throw new Error(data.error || "Error al cargar las propiedades")
        }
      })
      .catch((error) => {
        console.error("Error:", error)

        // Mensaje de error más detallado
        let errorMessage = "Error al conectar con el servidor. Por favor, intenta nuevamente."

        if (error.name === "AbortError") {
          errorMessage =
            "La conexión al servidor ha tardado demasiado tiempo. Por favor, verifica tu conexión a internet."
        } else if (error.message) {
          errorMessage += "<br><small>Detalles: " + error.message + "</small>"
        }

        propertiesGrid.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>${errorMessage}</p>
                    <div class="error-actions">
                        <button id="retry-button" class="btn btn-primary">Reintentar</button>
                        <button id="check-connection-button" class="btn btn-secondary">Verificar conexión</button>
                    </div>
                </div>
            `

        // Agregar evento al botón de reintentar
        const retryButton = document.getElementById("retry-button")
        if (retryButton) {
          retryButton.addEventListener("click", () => {
            loadProperties(queryParams)
          })
        }

        // Agregar evento al botón de verificar conexión
        const checkConnectionButton = document.getElementById("check-connection-button")
        if (checkConnectionButton) {
          checkConnectionButton.addEventListener("click", () => {
            // Intentar cargar un archivo simple para verificar la conexión
            fetch("check_connection.php?_=" + new Date().getTime())
              .then((response) => response.text())
              .then((data) => {
                alert("Conexión al servidor establecida correctamente. Intenta cargar las propiedades nuevamente.")
              })
              .catch((error) => {
                alert(
                  "No se pudo establecer conexión con el servidor. Verifica tu conexión a internet o contacta al administrador.",
                )
              })
          })
        }
      })
  }

  /**
   * Filtra las propiedades según el filtro actual
   */
  function filterProperties() {
    if (currentFilter === "all") {
      filteredProperties = [...properties]
    } else {
      // Filtrar por etiqueta en lugar de property_type
      filteredProperties = properties.filter((property) => {
        if (!property.etiquetas || property.etiquetas.length === 0) {
          console.log(`Propiedad "${property.title}" sin etiquetas`)
          return false
        }
        
        const coincide = property.etiquetas.some(etiqueta => {
          const etiquetaLower = etiqueta.toLowerCase()
          const filterLower = currentFilter.toLowerCase()
          return etiquetaLower.includes(filterLower) || filterLower.includes(etiquetaLower)
        })
        
        if (!coincide) {
          console.log(`Propiedad "${property.title}" etiquetas: ${property.etiquetas.join(', ')} - No coincide con filtro: ${currentFilter}`)
        }
        
        return coincide
      })
    }

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
          <p style="color: #999; font-size: 18px;">No se encontraron propiedades</p>
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

      const button = clone.querySelector(".btn-view-details")
      if (button) {
        button.setAttribute("data-property-id", property.id)
        button.onclick = function(e) {
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

    const totalPages = Math.ceil(filteredProperties.length / propertiesPerPage)

    // Limpiar paginación
    paginationContainer.innerHTML = ""

    if (totalPages <= 1) {
      return
    }

    // Crear elementos de paginación
    const createPageItem = (page, text, isActive = false, isDisabled = false) => {
      const pageItem = document.createElement("a")
      pageItem.href = "#"
      pageItem.className = "pagination-item"

      if (isActive) {
        pageItem.classList.add("active")
      }

      if (isDisabled) {
        pageItem.classList.add("disabled")
        pageItem.style.pointerEvents = "none"
      }

      pageItem.textContent = text

      if (!isDisabled) {
        pageItem.addEventListener("click", (e) => {
          e.preventDefault()
          currentPage = page
          displayProperties()

          // Scroll al inicio de la sección
          const propertiesSection = document.querySelector(".properties-section")
          if (propertiesSection) {
            propertiesSection.scrollIntoView({ behavior: "smooth" })
          }

          // Actualizar paginación
          updatePagination()
        })
      }

      return pageItem
    }

    // Botón anterior
    paginationContainer.appendChild(createPageItem(currentPage - 1, "<", false, currentPage === 1))

    // Páginas
    const maxVisiblePages = 5
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2))
    const endPage = Math.min(totalPages, startPage + maxVisiblePages - 1)

    if (endPage - startPage + 1 < maxVisiblePages) {
      startPage = Math.max(1, endPage - maxVisiblePages + 1)
    }

    // Primera página
    if (startPage > 1) {
      paginationContainer.appendChild(createPageItem(1, "1"))

      if (startPage > 2) {
        const ellipsis = document.createElement("span")
        ellipsis.className = "pagination-ellipsis"
        ellipsis.textContent = "..."
        paginationContainer.appendChild(ellipsis)
      }
    }

    // Páginas visibles
    for (let i = startPage; i <= endPage; i++) {
      paginationContainer.appendChild(createPageItem(i, i.toString(), i === currentPage))
    }

    // Última página
    if (endPage < totalPages) {
      if (endPage < totalPages - 1) {
        const ellipsis = document.createElement("span")
        ellipsis.className = "pagination-ellipsis"
        ellipsis.textContent = "..."
        paginationContainer.appendChild(ellipsis)
      }

      paginationContainer.appendChild(createPageItem(totalPages, totalPages.toString()))
    }

    // Botón siguiente
    paginationContainer.appendChild(createPageItem(currentPage + 1, ">", false, currentPage === totalPages))
  }
})
