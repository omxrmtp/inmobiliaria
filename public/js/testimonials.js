document.addEventListener("DOMContentLoaded", () => {
  const testimonialsContainer = document.getElementById("testimonials-container")
  const filterButtons = document.querySelectorAll(".filter-btn")

  let allTestimonials = []

  // Cargar testimonios
  function loadTestimonials() {
    testimonialsContainer.innerHTML = `
            <div class="loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Cargando testimonios...</p>
            </div>
        `

    fetch("get_testimonials.php")
      .then((response) => response.json())
      .then((data) => {
        console.log("Datos recibidos:", data) // Para depuración
        if (data.success && data.testimonials && data.testimonials.length > 0) {
          allTestimonials = data.testimonials
          displayTestimonials("all")
        } else {
          testimonialsContainer.innerHTML = `
                        <div class="no-results">
                            <p>No hay testimonios disponibles en este momento.</p>
                            <p>¡Sé el primero en compartir tu experiencia!</p>
                            <a href="#testimonial-form" class="btn btn-primary">Dejar un testimonio</a>
                        </div>
                    `
        }
      })
      .catch((error) => {
        console.error("Error al cargar testimonios:", error)
        testimonialsContainer.innerHTML = `
                    <div class="error-message">
                        <p>Error al cargar testimonios. Por favor, intenta nuevamente.</p>
                        <p class="error-details">Detalles: ${error.message}</p>
                    </div>
                `
      })
  }

  // Mostrar testimonios según el filtro
  function displayTestimonials(filter) {
    let filteredTestimonials = allTestimonials

    if (filter !== "all") {
      filteredTestimonials = allTestimonials.filter((testimonial) => {
        const role = testimonial.role ? testimonial.role.toLowerCase() : ""

        if (filter === "techo-propio" && role.includes("techo propio")) {
          return true
        } else if (filter === "mi-vivienda" && role.includes("vivienda")) {
          return true
        } else if (
          filter === "propiedades" &&
          (role.includes("propiedad") || role.includes("compra") || role.includes("venta"))
        ) {
          return true
        }

        return false
      })
    }

    if (filteredTestimonials.length === 0) {
      testimonialsContainer.innerHTML = `
                <div class="no-results">
                    <p>No hay testimonios disponibles para este filtro.</p>
                    <p>¡Sé el primero en compartir tu experiencia!</p>
                    <a href="#testimonial-form" class="btn btn-primary">Dejar un testimonio</a>
                </div>
            `
      return
    }

    let testimonialsHTML = ""

    filteredTestimonials.forEach((testimonial, index) => {
      // Depuración
      console.log("Procesando testimonio:", testimonial);
      console.log("Video URL:", testimonial.video_url);
      
      // Determinar la ruta de la foto - manejar tanto rutas relativas como absolutas
      const photoPath = testimonial.photo
        ? testimonial.photo.startsWith("http") || testimonial.photo.startsWith("/")
          ? testimonial.photo
          : testimonial.photo
        : "images/propiedad.png"

      // Asegurarse de que el contenido del testimonio esté disponible
      const testimonialContent = testimonial.content || testimonial.testimonial || "Sin comentarios"

      // Generar HTML para el video o enlace social
      let videoHTML = ""
      if (testimonial.video_url) {
        console.log("Encontrado video_url:", testimonial.video_url);
        const videoType = testimonial.video_type || (testimonial.video_url.includes('tiktok.com') ? 'tiktok' : 'instagram')
        console.log("Tipo de video:", videoType);
        
        // Siempre mostrar el enlace con icono para mayor compatibilidad
        const iconClass = videoType === 'tiktok' ? 'fab fa-tiktok' : 'fab fa-instagram'
        const platformName = videoType === 'tiktok' ? 'TikTok' : 'Instagram'
        
        videoHTML = `
          <div class="social-video-link">
            <a href="${testimonial.video_url}" target="_blank" rel="noopener noreferrer">
              <i class="${iconClass}"></i>
              <span>Ver en ${platformName}</span>
            </a>
          </div>
        `
      }

      testimonialsHTML += `
                <div class="testimonial-card animate-fade-up delay-${index % 4}">
                    <div class="testimonial-header">
                        <div class="testimonial-author">
                            <img src="${photoPath}" alt="${testimonial.name}" class="author-image" onerror="this.src='images/propiedad.png'">
                            <div class="author-info">
                                <h3>${testimonial.name}</h3>
                                <p>${testimonial.role || "Cliente"}</p>
                            </div>
                        </div>
                        <div class="testimonial-rating">
                            ${generateRatingStars(testimonial.rating)}
                        </div>
                    </div>
                    <div class="testimonial-content">
                        <p>"${testimonialContent}"</p>
                    </div>
                    ${videoHTML}

                </div>
            `
    })

    testimonialsContainer.innerHTML = testimonialsHTML

    // Inicializar animaciones después de cargar el contenido
    initAnimations()
  }

  // Generar estrellas para la calificación
  function generateRatingStars(rating) {
    let starsHTML = ""

    for (let i = 1; i <= 5; i++) {
      if (i <= rating) {
        starsHTML += '<i class="fas fa-star"></i>'
      } else {
        starsHTML += '<i class="far fa-star"></i>'
      }
    }

    return starsHTML
  }

  // Event listeners para los botones de filtro
  filterButtons.forEach((button) => {
    button.addEventListener("click", function () {
      // Remover clase active de todos los botones
      filterButtons.forEach((btn) => btn.classList.remove("active"))

      // Agregar clase active al botón clickeado
      this.classList.add("active")

      // Obtener el filtro
      const filter = this.getAttribute("data-filter")

      // Mostrar testimonios filtrados
      displayTestimonials(filter)
    })
  })

  // Inicializar animaciones
  function initAnimations() {
    const animatedElements = document.querySelectorAll(".animate-fade-up, .animate-fade-in, .animate-fade-right")

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("animated")
            observer.unobserve(entry.target)
          }
        })
      },
      { threshold: 0.1 },
    )

    animatedElements.forEach((element) => {
      observer.observe(element)
    })
  }

  // Manejar la validación del formulario
  const testimonialForm = document.getElementById("testimonial-form")
  if (testimonialForm) {
    testimonialForm.addEventListener("submit", function(e) {
      const videoUrlInput = document.getElementById("video_url")
      
      if (videoUrlInput && videoUrlInput.value) {
        const url = videoUrlInput.value.trim()
        
        // Verificar si es una URL válida de TikTok o Instagram
        if (!(url.includes('tiktok.com') || url.includes('instagram.com'))) {
          e.preventDefault()
          alert("Por favor, ingresa un enlace válido de TikTok o Instagram.")
          videoUrlInput.focus()
        }
      }
    })
  }

  // Cargar testimonios al cargar la página
  loadTestimonials()

  // Manejar la selección de archivos para mostrar el nombre del archivo
  const fileInputs = document.querySelectorAll('input[type="file"]')

  fileInputs.forEach((input) => {
    input.addEventListener("change", function () {
      const fileNameSpan = this.parentElement.querySelector(".file-name")

      if (this.files.length > 0) {
        const fileName = this.files[0].name
        fileNameSpan.textContent = fileName
      } else {
        fileNameSpan.textContent = "Ningún archivo seleccionado"
      }
    })
  })
  
  // Manejar el botón de cargar más testimonios
  const loadMoreButton = document.getElementById("load-more-testimonials")
  if (loadMoreButton) {
    loadMoreButton.addEventListener("click", function() {
      // Aquí se implementaría la lógica de paginación o carga de más testimonios
      // Por ahora solo recargamos los testimonios existentes
      loadTestimonials()
    })
  }

  // Manejar los botones de navegación suave
  const smoothScrollLinks = document.querySelectorAll('a[href^="#"]')
  smoothScrollLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault()
      const targetId = this.getAttribute('href')
      const targetElement = document.querySelector(targetId)
      
      if (targetElement) {
        // Scroll suave al elemento objetivo
        window.scrollTo({
          top: targetElement.offsetTop - 100, // Offset para no quedar justo al borde
          behavior: 'smooth'
        })
      }
    })
  })
})