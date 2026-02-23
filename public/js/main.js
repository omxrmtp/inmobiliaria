document.addEventListener("DOMContentLoaded", () => {
  // Año actual para el footer
  const yearElement = document.getElementById("current-year");
  if (yearElement) {
    yearElement.textContent = new Date().getFullYear();
  }

  // Toggle menu para dispositivos móviles
  const menuToggle = document.querySelector(".menu-toggle")
  const nav = document.querySelector(".nav")

  if (menuToggle) {
    menuToggle.addEventListener("click", function () {
      nav.classList.toggle("active")

      // Cambiar ícono del menú
      const icon = this.querySelector("i")
      if (icon.classList.contains("fa-bars")) {
        icon.classList.remove("fa-bars")
        icon.classList.add("fa-times")
      } else {
        icon.classList.remove("fa-times")
        icon.classList.add("fa-bars")
      }
    })
  }

  // Cerrar menú al hacer clic fuera
  document.addEventListener("click", (event) => {
    if (nav && nav.classList.contains("active") && !nav.contains(event.target) && !menuToggle.contains(event.target)) {
      nav.classList.remove("active")

      // Restaurar ícono del menú
      const icon = menuToggle.querySelector("i")
      if (icon) {
        icon.classList.remove("fa-times")
        icon.classList.add("fa-bars")
      }
    }
  })

  // Cambiar estilo del header al hacer scroll - DESACTIVADO
  /* 
  const header = document.querySelector(".header")
  window.addEventListener("scroll", () => {
    if (window.scrollY > 100) {
      header.classList.add("scrolled")
    } else {
      header.classList.remove("scrolled")
    }
  })
  */

  // Slider de testimonios
  const testimonialSlides = document.querySelectorAll(".testimonial-slide")
  const testimonialDots = document.querySelectorAll(".testimonial-dot")
  const prevButton = document.querySelector(".testimonial-control.prev")
  const nextButton = document.querySelector(".testimonial-control.next")
  let currentSlide = 0

  function showSlide(index) {
    if (testimonialSlides.length === 0) return;

    testimonialSlides.forEach((slide) => {
      slide.classList.remove("active")
    })
    testimonialDots.forEach((dot) => {
      dot.classList.remove("active")
    })

    if (testimonialSlides[index]) testimonialSlides[index].classList.add("active")
    if (testimonialDots[index]) testimonialDots[index].classList.add("active")
  }

  if (prevButton && nextButton) {
    prevButton.addEventListener("click", () => {
      currentSlide = (currentSlide - 1 + testimonialSlides.length) % testimonialSlides.length
      showSlide(currentSlide)
    })

    nextButton.addEventListener("click", () => {
      currentSlide = (currentSlide + 1) % testimonialSlides.length
      showSlide(currentSlide)
    })

    testimonialDots.forEach((dot, index) => {
      dot.addEventListener("click", () => {
        currentSlide = index
        showSlide(currentSlide)
      })
    })

    // Auto cambio de slide cada 5 segundos
    if (testimonialSlides.length > 0) {
      setInterval(() => {
        currentSlide = (currentSlide + 1) % testimonialSlides.length
        showSlide(currentSlide)
      }, 5000)
    }
  }

  // Slider de hero
  const heroSlides = document.querySelectorAll(".hero-slider .slide")
  let currentHeroSlide = 0

  function showHeroSlide(index) {
    heroSlides.forEach((slide) => {
      slide.classList.remove("active")
    })
    if (heroSlides[index]) heroSlides[index].classList.add("active")
  }

  if (heroSlides.length > 1) {
    setInterval(() => {
      currentHeroSlide = (currentHeroSlide + 1) % heroSlides.length
      showHeroSlide(currentHeroSlide)
    }, 5000)
  }

  // Botón para mostrar asistente virtual
  const assistantBtn = document.querySelector(".assistant-btn")

  if (assistantBtn) {
    assistantBtn.addEventListener("click", () => {
      window.location.href = "chat.html"
    })
  }

  // Animación al hacer scroll
  const animateOnScroll = () => {
    const elements = document.querySelectorAll(".feature-card, .property-card, .testimonial-slide")

    elements.forEach((element) => {
      const elementPosition = element.getBoundingClientRect().top
      const screenPosition = window.innerHeight / 1.3

      if (elementPosition < screenPosition) {
        element.classList.add("fade-in")
      }
    })
  }

  // Ejecutar animación al cargar la página
  animateOnScroll()

  // Ejecutar animación al hacer scroll
  window.addEventListener("scroll", animateOnScroll)

  // Dropdown menu
  const dropdowns = document.querySelectorAll('.nav-dropdown-toggle');

  // Cerrar dropdowns cuando se hace clic fuera
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.nav-dropdown')) {
      dropdowns.forEach(dropdown => {
        dropdown.closest('.nav-dropdown').classList.remove('active');
      });
    }
  });

  // Toggle dropdown al hacer clic
  dropdowns.forEach(dropdown => {
    dropdown.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();

      const parent = this.closest('.nav-dropdown');

      // Cerrar otros dropdowns
      dropdowns.forEach(other => {
        const otherParent = other.closest('.nav-dropdown');
        if (otherParent !== parent) {
          otherParent.classList.remove('active');
        }
      });

      // Toggle el actual
      parent.classList.toggle('active');
    });
  });
})
