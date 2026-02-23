document.addEventListener("DOMContentLoaded", () => {
  // Variables para la galería
  let currentGalleryItems = []
  let currentGalleryIndex = 0

  // Función para cargar los servicios desde el CRM
  function loadServicios() {
    // Mostrar spinner de carga
    const container = document.getElementById("servicios-container")
    if (container) {
      container.innerHTML = `
        <div class="loading-spinner">
          <i class="fas fa-spinner fa-spin fa-3x"></i>
          <p>Cargando servicios...</p>
        </div>
      `
    }

    // Obtener la URL base de la API
    const apiUrl = window.CRM_API_BASE || '/api'

    fetch('api/servicios.php')
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`)
        }
        return response.json()
      })
      .then((response) => {
        console.log("Respuesta completa del CRM:", response) // Para depuración

        // Extraer el array de datos
        let data = response.data || response || []

        console.log("Datos extraídos:", data)
        console.log("Tipo de datos:", typeof data)
        console.log("Es array:", Array.isArray(data))
        console.log("Cantidad de servicios:", Array.isArray(data) ? data.length : 'N/A')

        // Si data es un objeto con propiedades, convertirlo a array
        if (data && typeof data === 'object' && !Array.isArray(data)) {
          console.log("Convirtiendo objeto a array...")
          data = Object.values(data)
        }

        // Asegurar que es un array
        if (!Array.isArray(data)) {
          console.warn("Los datos no son un array, intentando extraer...")
          data = []
        }

        console.log("Datos finales a mostrar:", data)
        displayServicios(data)
      })
      .catch((error) => {
        console.error("Error al cargar los servicios del CRM:", error)
        if (container) {
          container.innerHTML = `
            <div class="error-message" style="grid-column: 1 / -1; text-align: center; padding: 2rem;">
              <i class="fas fa-exclamation-triangle fa-3x" style="color: #e74c3c; margin-bottom: 1rem;"></i>
              <p>Error al cargar los servicios. Por favor, inténtalo de nuevo más tarde.</p>
              <p style="font-size: 0.9rem; color: #777; margin-top: 0.5rem;">${error.message}</p>
            </div>
          `
        }
      })
  }

  // Función para mostrar los servicios en la página
  function displayServicios(servicios) {
    const container = document.getElementById("servicios-container")
    container.innerHTML = "" // Limpiar el contenedor

    if (!servicios || servicios.length === 0) {
      container.innerHTML = `
        <div style="text-align: center; padding: 2rem;">
          <p>No hay servicios adicionales disponibles en este momento.</p>
        </div>
      `
      return
    }

    // Crear servicios alternados (izquierda-derecha)
    servicios.forEach((servicio, index) => {
      // Mapear campos en español a inglés para compatibilidad
      const mappedServicio = {
        id: servicio.id,
        title: servicio.title || servicio.titulo,
        description: servicio.description || servicio.descripcion,
        caracteristicas: servicio.caracteristicas || [],
        imagenes: servicio.imagenes || [],
        enlace_imagen: servicio.enlace_imagen || servicio.image_link,
        enlace_video: servicio.enlace_video || servicio.video_link,
        tipo_servicio: servicio.tipo_servicio || servicio.service_type
      };

      const featuredService = document.createElement("div")
      featuredService.className = `featured-service animate-fade-up ${index % 2 === 1 ? 'reverse' : ''}`

      // Crear contenido del servicio
      let serviceHTML = `
        <div class="featured-service-content">
          <h3>${mappedServicio.title}</h3>
          <p>${mappedServicio.description || 'Servicio especializado para complementar tu experiencia inmobiliaria.'}</p>
      `

      // Agregar características si existen
      if (mappedServicio.caracteristicas && mappedServicio.caracteristicas.length > 0) {
        serviceHTML += `<ul class="service-features">`
        mappedServicio.caracteristicas.forEach(caracteristica => {
          serviceHTML += `<li><i class="fas fa-check-circle"></i> ${caracteristica}</li>`
        })
        serviceHTML += `</ul>`
      }

      // Agregar botón si hay imágenes o video
      if ((mappedServicio.imagenes && mappedServicio.imagenes.length > 0) || (mappedServicio.enlace_video && mappedServicio.enlace_video.trim())) {
        serviceHTML += `
          <button class="btn btn-primary service-btn open-gallery" data-service-index="${index}">
            <i class="fas fa-images"></i> Ver Ejemplos
          </button>
        `
      }

      serviceHTML += `
        </div>
        <div class="featured-service-image">
      `

      // Agregar primera imagen como thumbnail
      if (mappedServicio.imagenes && mappedServicio.imagenes.length > 0 && mappedServicio.imagenes[0].trim()) {
        serviceHTML += `
          <img src="${mappedServicio.imagenes[0]}" alt="${mappedServicio.title}" class="gallery-thumbnail" data-service-index="${index}">
        `
      } else {
        serviceHTML += `
          <div class="no-image-placeholder">
            <i class="fas fa-camera"></i>
            <span>Imagen no disponible</span>
          </div>
        `
      }

      serviceHTML += `
        </div>
      `

      featuredService.innerHTML = serviceHTML
      container.appendChild(featuredService)

      // Agregar separador entre servicios (excepto el último)
      if (index < servicios.length - 1) {
        const separator = document.createElement("div")
        separator.className = "service-separator"
        container.appendChild(separator)
      }
    })

    // Agregar event listeners para las galerías
    setupEventListeners()
  }


  // Configurar listeners para imágenes
  function setupImageListeners() {
    const serviceImages = document.querySelectorAll(".service-img, .open-image")
    serviceImages.forEach((img) => {
      img.addEventListener("click", function () {
        const imgSrc = this.getAttribute("data-full-img") || this.getAttribute("data-img") || this.src
        openImageModal(imgSrc)
      })
    })
  }

  // Configurar listeners para videos
  function setupVideoListeners() {
    const videoButtons = document.querySelectorAll(".video-btn, .preview-video")
    videoButtons.forEach((button) => {
      button.addEventListener("click", function () {
        const videoUrl = this.getAttribute("data-video") || this.getAttribute("data-url")
        openVideoModal(videoUrl)
      })
    })
  }

  // Función para abrir el modal de imagen
  function openImageModal(imgSrc) {
    // Crear modal si no existe
    let modal = document.getElementById("imageModal")
    if (!modal) {
      modal = document.createElement("div")
      modal.id = "imageModal"
      modal.className = "modal"
      modal.innerHTML = `
        <span class="modal-close">&times;</span>
        <img class="modal-content" id="modalImage">
      `
      document.body.appendChild(modal)

      // Agregar event listener para cerrar
      modal.querySelector(".modal-close").addEventListener("click", () => {
        modal.style.display = "none"
      })

      // Cerrar al hacer clic fuera
      modal.addEventListener("click", (e) => {
        if (e.target === modal) {
          modal.style.display = "none"
        }
      })
    }

    // Mostrar la imagen
    document.getElementById("modalImage").src = imgSrc
    modal.style.display = "block"
  }

  // Función para abrir el modal de video
  function openVideoModal(videoSrc) {
    // Crear modal si no existe
    let modal = document.getElementById("videoModal")
    if (!modal) {
      modal = document.createElement("div")
      modal.id = "videoModal"
      modal.className = "modal"
      modal.innerHTML = `
        <span class="modal-close">&times;</span>
        <div class="modal-video-container">
          <iframe id="modalVideo" class="modal-video" frameborder="0" allowfullscreen></iframe>
        </div>
      `
      document.body.appendChild(modal)

      // Agregar event listener para cerrar
      modal.querySelector(".modal-close").addEventListener("click", () => {
        modal.style.display = "none"
        document.getElementById("modalVideo").src = ""
      })

      // Cerrar al hacer clic fuera
      modal.addEventListener("click", (e) => {
        if (e.target === modal) {
          modal.style.display = "none"
          document.getElementById("modalVideo").src = ""
        }
      })
    }

    // Mostrar el video
    document.getElementById("modalVideo").src = videoSrc
    modal.style.display = "block"
  }

  // Configurar event listeners
  function setupEventListeners() {
    // Botones para abrir la galería
    const galleryButtons = document.querySelectorAll(".gallery-btn, .open-gallery")
    galleryButtons.forEach((button) => {
      button.addEventListener("click", function () {
        const serviceIndex = this.getAttribute("data-service-index")
        if (serviceIndex !== null) {
          loadGalleryFromService(parseInt(serviceIndex))
        }
      })
    })

    // Botones de navegación de la galería
    const prevButton = document.querySelector(".gallery-prev")
    if (prevButton) {
      prevButton.addEventListener("click", prevGalleryItem)
    }

    const nextButton = document.querySelector(".gallery-next")
    if (nextButton) {
      nextButton.addEventListener("click", nextGalleryItem)
    }

    // Cerrar modal
    const closeButton = document.querySelector(".modal-close")
    if (closeButton) {
      closeButton.addEventListener("click", closeGalleryModal)
    }

    // Cerrar modal al hacer clic fuera
    const modal = document.getElementById("galleryModal")
    if (modal) {
      modal.addEventListener("click", function (e) {
        if (e.target === this) {
          closeGalleryModal()
        }
      })
    }

    // Teclas de navegación
    document.addEventListener("keydown", (e) => {
      const galleryModal = document.getElementById("galleryModal")
      if (galleryModal && galleryModal.style.display === "block") {
        if (e.key === "ArrowLeft") {
          prevGalleryItem()
        } else if (e.key === "ArrowRight") {
          nextGalleryItem()
        } else if (e.key === "Escape") {
          closeGalleryModal()
        }
      }
    })
  }

  // Función para convertir URL de YouTube a formato embebible
  function getYouTubeEmbedUrl(url) {
    if (!url) return null

    // Extraer ID del video de diferentes formatos de YouTube
    let videoId = null

    // Formato: https://www.youtube.com/watch?v=VIDEO_ID
    const match1 = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/)
    if (match1) {
      videoId = match1[1]
    }

    // Formato: https://m.youtube.com/watch?v=VIDEO_ID
    const match2 = url.match(/m\.youtube\.com\/watch\?v=([^&\n?#]+)/)
    if (match2) {
      videoId = match2[1]
    }

    if (videoId) {
      return `https://www.youtube.com/embed/${videoId}?autoplay=0&controls=1&modestbranding=1`
    }

    // Si no es YouTube, devolver la URL original
    return url
  }

  // Función para cargar la galería desde los datos del servicio
  function loadGalleryFromService(serviceIndex) {
    // Obtener servicios del CRM nuevamente para acceder a los datos completos
    const apiUrl = window.CRM_API_BASE || '/api'

    fetch(`${apiUrl}/servicios`)
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`)
        }
        return response.json()
      })
      .then((response) => {
        let servicios = response.data || response || []

        if (!Array.isArray(servicios)) {
          servicios = []
        }

        if (servicios.length > serviceIndex) {
          const servicio = servicios[serviceIndex]

          // Mapear campos para compatibilidad
          const title = servicio.title || servicio.titulo || '';
          const enlaceVideo = servicio.enlace_video || servicio.video_link || servicio.enlaceVideo || '';
          const imagenes = servicio.imagenes || [];

          // Preparar los elementos de la galería
          currentGalleryItems = []

          // Agregar imágenes
          if (imagenes && Array.isArray(imagenes)) {
            imagenes.forEach((imagen) => {
              if (imagen && imagen.trim()) {
                currentGalleryItems.push({
                  type: "image",
                  url: imagen,
                  title: title,
                })
              }
            })
          }

          // Agregar video si existe
          if (enlaceVideo && enlaceVideo.trim()) {
            const embedUrl = getYouTubeEmbedUrl(enlaceVideo)
            currentGalleryItems.push({
              type: "video",
              url: embedUrl,
              title: title,
            })
          }

          // Si hay elementos, mostrar la galería
          if (currentGalleryItems.length > 0) {
            currentGalleryIndex = 0
            showGalleryModal()
            updateGalleryItem()
          } else {
            alert("No hay imágenes o videos disponibles para mostrar.")
          }
        }
      })
      .catch((error) => {
        console.error("Error al cargar la galería:", error)
      })
  }

  // Función para mostrar el modal de galería
  function showGalleryModal() {
    const modal = document.getElementById("galleryModal")
    modal.style.display = "block"

    // Bloquear el scroll del body
    document.body.style.overflow = "hidden"
  }

  // Función para actualizar el elemento actual de la galería
  function updateGalleryItem() {
    const galleryContainer = document.querySelector(".gallery-container")
    if (!galleryContainer) {
      console.error("No se encontró .gallery-container")
      return
    }

    galleryContainer.innerHTML = ""

    if (currentGalleryItems.length === 0) return

    const item = currentGalleryItems[currentGalleryIndex]
    const galleryItem = document.createElement("div")
    galleryItem.className = "gallery-item active"

    if (item.type === "image") {
      const img = document.createElement("img")
      img.src = item.url
      img.alt = item.title || "Imagen de galería"
      img.style.maxWidth = "100%"
      img.style.maxHeight = "80vh"
      img.style.objectFit = "contain"
      galleryItem.appendChild(img)
    } else if (item.type === "video") {
      const iframe = document.createElement("iframe")
      iframe.src = item.url
      iframe.setAttribute("allowfullscreen", "true")
      iframe.setAttribute("allow", "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture")
      iframe.style.width = "100%"
      iframe.style.height = "70vh"
      iframe.style.border = "none"
      iframe.style.borderRadius = "8px"
      galleryItem.appendChild(iframe)
    }

    // Agregar título si existe
    if (item.title) {
      const title = document.createElement("div")
      title.className = "gallery-title"
      title.textContent = item.title
      galleryItem.appendChild(title)
    }

    galleryContainer.appendChild(galleryItem)

    // Actualizar contador
    const counter = document.createElement("div")
    counter.className = "gallery-counter"
    counter.textContent = `${currentGalleryIndex + 1} / ${currentGalleryItems.length}`
    galleryContainer.appendChild(counter)
  }

  // Función para navegar a la imagen anterior
  function prevGalleryItem() {
    if (currentGalleryItems.length === 0) return

    currentGalleryIndex--
    if (currentGalleryIndex < 0) {
      currentGalleryIndex = currentGalleryItems.length - 1
    }

    updateGalleryItem()
  }

  // Función para navegar a la siguiente imagen
  function nextGalleryItem() {
    if (currentGalleryItems.length === 0) return

    currentGalleryIndex++
    if (currentGalleryIndex >= currentGalleryItems.length) {
      currentGalleryIndex = 0
    }

    updateGalleryItem()
  }

  // Función para cerrar el modal de galería
  function closeGalleryModal() {
    const modal = document.getElementById("galleryModal")
    modal.style.display = "none"

    // Restaurar el scroll del body
    document.body.style.overflow = ""

    // Si hay un video, detenerlo
    const activeVideo = document.querySelector(".gallery-item.active iframe")
    if (activeVideo) {
      const videoSrc = activeVideo.src
      activeVideo.src = ""
      setTimeout(() => {
        activeVideo.src = videoSrc
      }, 100)
    }
  }

  // Inicializar los listeners para las secciones destacadas
  //setupImageListeners()
  //setupVideoListeners()

  // Cargar los servicios al cargar la página
  loadServicios()

  // Configurar event listeners
  setupEventListeners()
})
