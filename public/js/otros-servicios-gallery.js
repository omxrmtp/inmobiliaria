document.addEventListener('DOMContentLoaded', function() {
    // Variables para la galería
    const galleryModal = document.getElementById('galleryModal');
    const galleryContainer = document.querySelector('.gallery-container');
    let currentGalleryItems = [];
    let currentSlideIndex = 0;
    
    // Botones para abrir galería
    document.querySelectorAll('.open-gallery').forEach(button => {
        button.addEventListener('click', function() {
            const serviceType = this.getAttribute('data-service-type');
            loadGallery(serviceType);
        });
    });
    
    // Cargar galería desde la base de datos
    function loadGallery(serviceType) {
        fetch(`get_gallery.php?type=${serviceType}`)
            .then(response => response.json())
            .then(data => {
                if (data.media && data.media.length > 0) {
                    currentGalleryItems = data.media;
                    currentSlideIndex = 0;
                    renderGallery();
                    openModal();
                } else {
                    alert('No hay medios disponibles para este servicio');
                }
            })
            .catch(error => {
                console.error('Error al cargar la galería:', error);
                alert('Error al cargar la galería');
            });
    }
    
    // Renderizar la galería
    function renderGallery() {
        galleryContainer.innerHTML = '';
        
        currentGalleryItems.forEach((item, index) => {
            const slide = document.createElement('div');
            slide.className = `gallery-slide ${index === currentSlideIndex ? 'active' : ''}`;
            
            if (item.type === 'image') {
                slide.innerHTML = `
                    <img src="${item.url}" alt="${item.title}" class="gallery-image">
                    <div class="gallery-caption">${item.title}</div>
                `;
            } else if (item.type === 'video') {
                const embedUrl = convertToEmbedUrl(item.url);
                slide.innerHTML = `
                    <iframe src="${embedUrl}" frameborder="0" allowfullscreen class="gallery-video"></iframe>
                    <div class="gallery-caption">${item.title}</div>
                `;
            }
            
            galleryContainer.appendChild(slide);
        });
        
        // Agregar controles de navegación
        const controls = document.createElement('div');
        controls.className = 'gallery-controls';
        controls.innerHTML = `
            <button class="gallery-prev"><i class="fas fa-chevron-left"></i></button>
            <button class="gallery-next"><i class="fas fa-chevron-right"></i></button>
        `;
        galleryContainer.appendChild(controls);
        
        // Eventos para los controles
        document.querySelector('.gallery-prev').addEventListener('click', prevSlide);
        document.querySelector('.gallery-next').addEventListener('click', nextSlide);
    }
    
    // Función para convertir URLs de video a URLs de embed
    function convertToEmbedUrl(url) {
        try {
            // YouTube
            if (url.includes('youtube.com') || url.includes('youtu.be')) {
                let videoId = '';
                
                // Para URLs de YouTube normales
                if (url.includes('youtube.com/watch?v=')) {
                    videoId = url.split('v=')[1].split('&')[0];
                }
                // Para URLs de YouTube Shorts
                else if (url.includes('youtube.com/shorts/')) {
                    videoId = url.split('shorts/')[1].split('?')[0];
                }
                // Para URLs de youtu.be
                else if (url.includes('youtu.be/')) {
                    videoId = url.split('youtu.be/')[1].split('?')[0];
                }
                
                // Agregar parámetros de tiempo si existen
                const timeParam = url.includes('t=') ? url.split('t=')[1].split('&')[0] : '';
                const timeQuery = timeParam ? `?start=${timeParam.replace('s', '')}` : '';
                
                return `https://www.youtube.com/embed/${videoId}${timeQuery}`;
            }
            // TikTok
            else if (url.includes('tiktok.com')) {
                const videoId = url.split('video/')[1].split('?')[0];
                return `https://www.tiktok.com/embed/v2/${videoId}`;
            }
            // Instagram
            else if (url.includes('instagram.com')) {
                const reelId = url.split('reel/')[1].split('/')[0];
                return `https://www.instagram.com/p/${reelId}/embed/captioned`;
            }
            // Facebook
            else if (url.includes('facebook.com')) {
                return `https://www.facebook.com/plugins/video.php?href=${encodeURIComponent(url)}`;
            }
            
            return url;
        } catch (error) {
            console.error('Error al convertir URL de video:', error);
            return url;
        }
    }
    
    // Funciones de navegación
    function nextSlide() {
        currentSlideIndex = (currentSlideIndex + 1) % currentGalleryItems.length;
        updateGallery();
    }
    
    function prevSlide() {
        currentSlideIndex = (currentSlideIndex - 1 + currentGalleryItems.length) % currentGalleryItems.length;
        updateGallery();
    }
    
    function updateGallery() {
        const slides = document.querySelectorAll('.gallery-slide');
        slides.forEach((slide, index) => {
            slide.classList.toggle('active', index === currentSlideIndex);
        });
    }
    
    // Funciones del modal
    function openModal() {
        galleryModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    
    function closeModal() {
        galleryModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    // Eventos para cerrar el modal
    document.querySelector('.close').addEventListener('click', closeModal);
    window.addEventListener('click', function(event) {
        if (event.target === galleryModal) {
            closeModal();
        }
    });
    
    // Eventos de teclado
    document.addEventListener('keydown', function(event) {
        if (galleryModal.style.display === 'block') {
            if (event.key === 'Escape') {
                closeModal();
            } else if (event.key === 'ArrowLeft') {
                prevSlide();
            } else if (event.key === 'ArrowRight') {
                nextSlide();
            }
        }
    });
}); 