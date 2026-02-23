class TestimoniosCarousel {
  constructor() {
    this.track = document.getElementById('testimonios-track');
    this.prevBtn = document.getElementById('prev-btn');
    this.nextBtn = document.getElementById('next-btn');
    this.dotsContainer = document.getElementById('carousel-dots');
    this.testimonios = [];
    this.currentIndex = 0;
    this.itemsPerView = 3;
    this.gap = 24;

    if (!this.track) return;

    this.init();
  }

  async init() {
    await this.loadTestimonios();
    this.setupEventListeners();
    this.updateCarousel();
    this.autoScroll();
  }

  async loadTestimonios() {
    try {
      const response = await fetch('get_testimonials.php');

      if (!response.ok) {
        throw new Error('Error al cargar testimonios');
      }

      const result = await response.json();

      // Extraer el array de testimonios del wrapper de respuesta
      // get_testimonials.php devuelve {success: true, testimonials: [...]}
      this.testimonios = result.testimonials || result.data || result || [];

      if (this.testimonios.length === 0) {
        this.showEmpty();
        return;
      }

      this.renderTestimonios();
      this.renderDots();
    } catch (error) {
      console.error('Error cargando testimonios:', error);
      this.showError();
    }
  }

  renderTestimonios() {
    this.track.innerHTML = '';

    this.testimonios.forEach((testimonio) => {
      const card = document.createElement('div');
      card.className = 'testimonio-card';

      // Manejar tanto nombres en español como en inglés
      const nombre = testimonio.nombre || testimonio.name || 'Anónimo';
      const calificacion = testimonio.calificacion || testimonio.rating || 5;
      const texto = testimonio.testimonio || testimonio.content || '';
      const foto = testimonio.foto || testimonio.photo || null;
      const correo = testimonio.correo || testimonio.email || '';

      // Generar estrellas
      const starsHTML = Array(5)
        .fill(0)
        .map((_, i) => `
          <i class="fas fa-star star ${i < calificacion ? '' : 'empty'}"></i>
        `)
        .join('');

      // Obtener iniciales para avatar
      const initials = nombre
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);

      card.innerHTML = `
        <div class="testimonio-stars">
          ${starsHTML}
        </div>
        <p class="testimonio-text">"${texto}"</p>
        <div class="testimonio-author">
          ${foto && foto !== 'null' && foto !== '/placeholder.php?height=150&width=150'
          ? `<img src="${foto}" alt="${nombre}" class="testimonio-avatar" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
               <div class="testimonio-avatar" style="display:none;">${initials}</div>`
          : `<div class="testimonio-avatar">${initials}</div>`
        }
          <div class="testimonio-author-info">
            <div class="testimonio-author-name">${nombre}</div>
            <div class="testimonio-author-email">${correo}</div>
          </div>
        </div>
      `;

      this.track.appendChild(card);
    });
  }

  renderDots() {
    this.dotsContainer.innerHTML = '';
    const totalDots = Math.ceil(this.testimonios.length / this.itemsPerView);

    for (let i = 0; i < totalDots; i++) {
      const dot = document.createElement('div');
      dot.className = `dot ${i === 0 ? 'active' : ''}`;
      dot.addEventListener('click', () => this.goToSlide(i));
      this.dotsContainer.appendChild(dot);
    }
  }

  setupEventListeners() {
    this.prevBtn.addEventListener('click', () => this.prev());
    this.nextBtn.addEventListener('click', () => this.next());

    // Pausar auto-scroll al interactuar
    this.track.addEventListener('mouseenter', () => this.stopAutoScroll());
    this.track.addEventListener('mouseleave', () => this.autoScroll());
  }

  updateCarousel() {
    const cardWidth = this.track.querySelector('.testimonio-card')?.offsetWidth || 0;
    const offset = -this.currentIndex * (cardWidth + this.gap);
    this.track.style.transform = `translateX(${offset}px)`;

    // Actualizar dots
    const dots = this.dotsContainer.querySelectorAll('.dot');
    dots.forEach((dot, i) => {
      dot.classList.toggle('active', i === Math.floor(this.currentIndex / this.itemsPerView));
    });
  }

  prev() {
    if (this.currentIndex > 0) {
      this.currentIndex--;
      this.updateCarousel();
    }
  }

  next() {
    const maxIndex = Math.max(0, this.testimonios.length - this.itemsPerView);
    if (this.currentIndex < maxIndex) {
      this.currentIndex++;
      this.updateCarousel();
    }
  }

  goToSlide(index) {
    this.currentIndex = index * this.itemsPerView;
    const maxIndex = Math.max(0, this.testimonios.length - this.itemsPerView);
    this.currentIndex = Math.min(this.currentIndex, maxIndex);
    this.updateCarousel();
  }

  autoScroll() {
    this.autoScrollInterval = setInterval(() => {
      const maxIndex = Math.max(0, this.testimonios.length - this.itemsPerView);
      if (this.currentIndex < maxIndex) {
        this.currentIndex++;
      } else {
        this.currentIndex = 0;
      }
      this.updateCarousel();
    }, 5000); // Cambiar cada 5 segundos
  }

  stopAutoScroll() {
    if (this.autoScrollInterval) {
      clearInterval(this.autoScrollInterval);
    }
  }

  showEmpty() {
    this.track.innerHTML = `
      <div class="testimonios-empty" style="width: 100%; text-align: center; padding: 60px 20px;">
        <i class="fas fa-comments"></i>
        <p>No hay testimonios disponibles en este momento</p>
      </div>
    `;
  }

  showError() {
    this.track.innerHTML = `
      <div class="testimonios-empty" style="width: 100%; text-align: center; padding: 60px 20px;">
        <i class="fas fa-exclamation-circle"></i>
        <p>Error al cargar los testimonios. Intenta más tarde.</p>
      </div>
    `;
  }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
  new TestimoniosCarousel();
});
