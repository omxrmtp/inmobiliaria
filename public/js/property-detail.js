/**
 * Archivo JavaScript para la página de detalle de propiedad
 * Maneja la carga y visualización de los detalles de una propiedad
 */

document.addEventListener("DOMContentLoaded", () => {
  // Elementos del DOM
  const propertyLoading = document.getElementById("property-loading")
  const propertyNotFound = document.getElementById("property-not-found")
  const propertyContent = document.getElementById("property-detail-content")
  const similarPropertiesContainer = document.getElementById("similar-properties-container")

  // Obtener ID de la propiedad de la URL
  const urlParams = new URLSearchParams(window.location.search)
  const propertyId = urlParams.get("id")

  if (!propertyId) {
    showNotFound()
    return
  }

  // Cargar detalles de la propiedad
  loadPropertyDetails(propertyId)

  // Event Listeners para las pestañas
  const tabButtons = document.querySelectorAll(".tab-btn")
  const tabContents = document.querySelectorAll(".tab-content")

  tabButtons.forEach((button) => {
    button.addEventListener("click", function () {
      // Remover clase active de todos los botones y contenidos
      tabButtons.forEach((btn) => btn.classList.remove("active"))
      tabContents.forEach((content) => content.classList.remove("active"))

      // Agregar clase active al botón clickeado
      this.classList.add("active")

      // Mostrar contenido correspondiente
      const tabId = this.getAttribute("data-tab")
      document.getElementById(tabId).classList.add("active")
    })
  })

  // Event Listener para el formulario de consulta
  const inquiryForm = document.getElementById("inquiry-form")

  if (inquiryForm) {
    inquiryForm.addEventListener("submit", function (e) {
      e.preventDefault()

      // Aquí se podría implementar el envío del formulario por AJAX
      alert("Gracias por tu mensaje. Te contactaremos pronto.")
      this.reset()
    })
  }

  /**
   * Carga los detalles de la propiedad desde el servidor
   * @param {string} id - ID de la propiedad
   */
  function loadPropertyDetails(id) {
    // Mostrar spinner de carga
    propertyLoading.style.display = "flex"
    propertyContent.style.display = "none"
    propertyNotFound.style.display = "none"

    // Realizar petición AJAX
    fetch(`get_property.php?id=${id}&_=${new Date().getTime()}`)
      .then((response) => {
        if (!response.ok) {
          throw new Error("Error en la respuesta del servidor")
        }
        return response.json()
      })
      .then((property) => {
        if (property.error) {
          throw new Error(property.error)
        }

        // Mostrar detalles de la propiedad
        displayPropertyDetails(property)

        // Cargar propiedades similares
        loadSimilarProperties(id, property.property_type)
      })
      .catch((error) => {
        console.error("Error:", error)
        showNotFound()
      })
  }

  /**
   * Muestra los detalles de la propiedad en la página
   * @param {Object} property - Datos de la propiedad
   */
  function displayPropertyDetails(property) {
    // Ocultar spinner y mostrar contenido
    propertyLoading.style.display = "none"
    propertyContent.style.display = "block"

    // Título y ubicación
    document.getElementById("property-title").textContent = property.title
    document.getElementById("property-location").textContent = property.city

    // Precio
    document.getElementById("property-price").textContent =
      `S/. ${Number.parseFloat(property.price).toLocaleString("es-PE")}`

    // Imagen principal
    const mainImage = document.getElementById("property-main-image")

    // Miniaturas
    const thumbnailsContainer = document.getElementById("property-thumbnails")
    thumbnailsContainer.innerHTML = ""

    if (property.images && property.images.length > 0) {
      // Establecer imagen principal
      mainImage.src = property.images[0].image_path
      mainImage.alt = property.title

      // Crear miniaturas
      property.images.forEach((image, index) => {
        const thumbnail = document.createElement("div")
        thumbnail.className = "property-thumbnail"
        if (index === 0) {
          thumbnail.classList.add("active")
        }

        const img = document.createElement("img")
        img.src = image.image_path
        img.alt = `${property.title} - Imagen ${index + 1}`

        thumbnail.appendChild(img)
        thumbnailsContainer.appendChild(thumbnail)

        // Event listener para cambiar imagen principal
        thumbnail.addEventListener("click", function () {
          // Actualizar imagen principal
          mainImage.src = image.image_path
          mainImage.alt = property.title

          // Actualizar clase active
          document.querySelectorAll(".property-thumbnail").forEach((thumb) => {
            thumb.classList.remove("active")
          })
          this.classList.add("active")
        })
      })
    } else {
      // Imagen por defecto
      mainImage.src = "img/properties/default/property-default.jpg"
      mainImage.alt = property.title
    }

    // Mostrar video si existe
    if (property.has_video && property.video_url) {
      // Crear contenedor para el video
      const videoContainer = document.createElement("div")
      videoContainer.className = "property-video"
      videoContainer.innerHTML = "<h3>Video de la Propiedad</h3>"

      // Crear el elemento de video según el tipo
      const videoElement = document.createElement("div")
      videoElement.className = "video-wrapper"

      if (property.video_type === "direct") {
        // Video directo (MP4, WebM, OGG)
        videoElement.innerHTML = `
                    <video controls width="100%">
                        <source src="${property.video_url}" type="video/mp4">
                        Tu navegador no soporta videos HTML5.
                    </video>
                `
      } else if (property.video_type === "youtube" && property.video_id) {
        // YouTube
        videoElement.innerHTML = `
                    <iframe width="100%" height="315" src="https://www.youtube.com/embed/${property.video_id}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                `
      } else if (property.video_type === "vimeo" && property.video_id) {
        // Vimeo
        videoElement.innerHTML = `
                    <iframe src="https://player.vimeo.com/video/${property.video_id}" width="100%" height="315" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                `
      } else {
        // Otro tipo de video o URL desconocida
        videoElement.innerHTML = `
                    <div class="video-fallback">
                        <a href="${property.video_url}" target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt"></i> Ver Video
                        </a>
                    </div>
                `
      }

      videoContainer.appendChild(videoElement)

      // Insertar el video después de la galería de imágenes
      const propertyGallery = document.querySelector(".property-gallery")
      propertyGallery.parentNode.insertBefore(videoContainer, propertyGallery.nextSibling)
    }

    // Detalles
    document.getElementById("property-area").textContent = `${property.area} m²`

    // Mostrar/ocultar dormitorios y baños según el tipo de propiedad
    const bedroomsItem = document.getElementById("property-bedrooms-item")
    const bathroomsItem = document.getElementById("property-bathrooms-item")

    if (property.property_type === "land" || property.property_type === "commercial") {
      bedroomsItem.style.display = "none"
      bathroomsItem.style.display = "none"
    } else {
      bedroomsItem.style.display = "flex"
      bathroomsItem.style.display = "flex"
      document.getElementById("property-bedrooms").textContent = property.bedrooms
      document.getElementById("property-bathrooms").textContent = property.bathrooms
    }

    // Tipo de propiedad
    const propertyTypes = {
      house: "Casa",
      apartment: "Departamento",
      land: "Terreno",
      commercial: "Local Comercial",
      office: "Oficina",
    }

    document.getElementById("property-type").textContent =
      propertyTypes[property.property_type] || property.property_type

    // Estado
    const propertyStatus = {
      available: "Disponible",
      sold: "Vendido",
      reserved: "Reservado",
    }

    document.getElementById("property-status").textContent = propertyStatus[property.status] || property.status

    // Descripción
    document.getElementById("property-description").innerHTML = property.description.replace(/\n/g, '<br>');

    // Dirección
    document.getElementById("property-address").textContent = property.address

    // Mapa
    const mapIframe = document.getElementById("property-map")
    if (property.map_url) {
      mapIframe.src = property.map_url
    }

    // ID para el formulario
    document.getElementById("property-id-input").value = property.id
  }

  /**
   * Carga propiedades similares
   * @param {string} id - ID de la propiedad actual
   * @param {string} type - Tipo de propiedad
   */
  function loadSimilarProperties(id, type) {
    // Realizar petición AJAX
    fetch(`get_similar_properties.php?id=${id}&type=${type}&limit=3&_=${new Date().getTime()}`)
      .then((response) => {
        if (!response.ok) {
          throw new Error("Error en la respuesta del servidor")
        }
        return response.json()
      })
      .then((data) => {
        if (data.success) {
          displaySimilarProperties(data.properties)
        } else {
          throw new Error(data.error || "Error al cargar propiedades similares")
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        similarPropertiesContainer.innerHTML = `
                    <div class="error-message">
                        <p>No se pudieron cargar propiedades similares.</p>
                    </div>
                `
      })
  }

  /**
   * Muestra las propiedades similares
   * @param {Array} properties - Lista de propiedades similares
   */
  function displaySimilarProperties(properties) {
    // Limpiar contenedor
    similarPropertiesContainer.innerHTML = ""

    if (properties.length === 0) {
      similarPropertiesContainer.innerHTML = `
                <div class="no-properties">
                    <p>No hay propiedades similares disponibles.</p>
                </div>
            `
      return
    }

    // Obtener template
    const template = document.getElementById("similar-property-template")

    // Crear tarjetas de propiedades
    properties.forEach((property) => {
      // Clonar template
      const propertyCard = document.importNode(template.content, true)

      // Configurar datos
      propertyCard.querySelector(".property-card").dataset.id = property.id
      propertyCard.querySelector(".property-card").dataset.type = property.property_type

      // Imagen
      const imgElement = propertyCard.querySelector(".property-image img")
      if (property.image_path) {
        imgElement.src = property.image_path
        imgElement.alt = property.title
      } else {
        // Imagen por defecto según el tipo de propiedad
        const defaultImages = {
          house: "img/properties/default/house.jpg",
          apartment: "img/properties/default/apartment.jpg",
          land: "img/properties/default/land.jpg",
          commercial: "img/properties/default/commercial.jpg",
          office: "img/properties/default/office.jpg",
        }

        imgElement.src = defaultImages[property.property_type] || "img/properties/default/property-default.jpg"
        imgElement.alt = property.title
      }

      // Etiqueta de estado
      const tagElement = propertyCard.querySelector(".property-tag")
      if (property.status === "available") {
        tagElement.textContent = "Disponible"
        tagElement.classList.add("available")
      } else if (property.status === "sold") {
        tagElement.textContent = "Vendido"
        tagElement.classList.add("sold")
      } else {
        tagElement.textContent = "Reservado"
        tagElement.classList.add("reserved")
      }

      // Título
      propertyCard.querySelector(".property-title").textContent = property.title

      // Ubicación
      propertyCard.querySelector(".property-location span").textContent = property.city

      // Precio
      propertyCard.querySelector(".property-price").textContent =
        `S/. ${Number.parseFloat(property.price).toLocaleString("es-PE")}`

      // Características
      const bedroomsElement = propertyCard.querySelector(".property-feature.bedrooms span")
      const bathroomsElement = propertyCard.querySelector(".property-feature.bathrooms span")

      if (property.property_type === "land" || property.property_type === "commercial") {
        // Ocultar dormitorios y baños para terrenos y locales comerciales
        propertyCard.querySelector(".property-feature.bedrooms").style.display = "none"
        propertyCard.querySelector(".property-feature.bathrooms").style.display = "none"
      } else {
        bedroomsElement.textContent = property.bedrooms
        bathroomsElement.textContent = property.bathrooms
      }

      // Área
      propertyCard.querySelector(".property-feature.area span").textContent = `${property.area} m²`

      // Enlaces
      const detailLinks = propertyCard.querySelectorAll("a")
      detailLinks.forEach((link) => {
        link.href = `property-detail.html?id=${property.id}`
      })

      // Agregar al grid
      similarPropertiesContainer.appendChild(propertyCard)
    })

    // Añadir animación de entrada
    const cards = similarPropertiesContainer.querySelectorAll(".property-card")
    cards.forEach((card, index) => {
      setTimeout(() => {
        card.classList.add("fade-in")
      }, index * 100)
    })
  }

  /**
   * Muestra el mensaje de propiedad no encontrada
   */
  function showNotFound() {
    propertyLoading.style.display = "none"
    propertyContent.style.display = "none"
    propertyNotFound.style.display = "block"
  }
})
