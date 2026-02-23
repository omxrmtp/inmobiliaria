document.addEventListener("DOMContentLoaded", function() {
    const teamContainer = document.getElementById('team-container');
    
    // Cargar miembros del equipo
    function loadTeamMembers() {
        teamContainer.innerHTML = `
            <div class="loading animate-fade-up">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Cargando equipo...</p>
            </div>
        `;
        
        fetch('get_team.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.team && data.team.length > 0) {
                    let teamHTML = '';
                    
                    data.team.forEach((member, index) => {
                        // Verificar si la imagen existe
                        const photoUrl = member.photo ? member.photo : '/placeholder.php?height=150&width=150';
                        
                        // Crear el HTML para cada miembro del equipo
                        teamHTML += `
                            <div class="team-member animate-fade-up" style="animation-delay: ${index * 0.1}s">
                                <div class="member-photo">
                                    <img src="${photoUrl}" alt="${member.name}" onerror="this.src='/placeholder.php?height=150&width=150'; this.onerror=null;">
                                </div>
                                <div class="member-content">
                                    <h3>${member.name}</h3>
                                    <p class="position">${member.position}</p>
                                    <div class="member-details">
                                        <p class="description">${member.description || 'Asesor inmobiliario profesional.'}</p>
                                        <div class="contact-info">
                                            <a href="tel:${member.phone}" class="contact-item">
                                                <i class="fas fa-phone"></i>
                                                <span>${member.phone}</span>
                                            </a>
                                            <a href="mailto:${member.email}" class="contact-item">
                                                <i class="fas fa-envelope"></i>
                                                <span>${member.email}</span>
                                            </a>
                                        </div> 
                                        <div class="social-links">
                                            ${member.whatsapp ? `<a href="${member.whatsapp}" target="_blank" class="social-link whatsapp" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>` : ''}
                                            ${member.facebook ? `<a href="${member.facebook}" target="_blank" class="social-link facebook" title="Facebook"><i class="fab fa-facebook-f"></i></a>` : ''}
                                            ${member.instagram ? `<a href="${member.instagram}" target="_blank" class="social-link instagram" title="Instagram"><i class="fab fa-instagram"></i></a>` : ''}
                                            ${member.tiktok ? `<a href="${member.tiktok}" target="_blank" class="social-link tiktok" title="TikTok"><i class="fab fa-tiktok"></i></a>` : ''}
                                            ${member.linkedin ? `<a href="${member.linkedin}" target="_blank" class="social-link linkedin" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>` : ''}
                                            ${member.youtube ? `<a href="${member.youtube}" target="_blank" class="social-link youtube" title="YouTube"><i class="fab fa-youtube"></i></a>` : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    teamContainer.innerHTML = teamHTML;
                } else {
                    // Mostrar mensaje de que no hay asesores disponibles
                    teamContainer.innerHTML = `
                        <div class="no-team-members">
                            <i class="fas fa-user-slash"></i>
                            <p>No hay asesores disponibles en este momento.</p>
                            <p>Por favor, contacta con nosotros directamente.</p>
                        </div>
                    `;
                }
                
                // Inicializar animaciones después de cargar el contenido
                initAnimations();
            })
            .catch(error => {
                console.error('Error al cargar el equipo:', error);
                // Mostrar mensaje de error 
                teamContainer.innerHTML = `
                    <div class="no-team-members">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Hubo un problema al cargar la información del equipo.</p>
                        <p>Por favor, intenta nuevamente más tarde.</p>
                    </div>
                `;
            });
    }
    
    // Inicializar animaciones
    function initAnimations() {
        const animatedElements = document.querySelectorAll('.animate-fade-up, .animate-fade-in, .animate-fade-right');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        animatedElements.forEach(element => {
            observer.observe(element);
        });
    }
    
    // Cargar miembros del equipo al cargar la página
    loadTeamMembers();
    
    // Inicializar animaciones para elementos estáticos
    initAnimations();
});