document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    if (tabBtns.length > 0) {
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons and contents
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Show corresponding content
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });
    }
    
    // Form validation for login
    const loginForm = document.querySelector('form[action="process_login.php"]');
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            
            if (!validateEmail(email)) {
                event.preventDefault();
                alert('Por favor, ingresa un correo electrónico válido.');
                return;
            }
            
            if (password.length < 6) {
                event.preventDefault();
                alert('La contraseña debe tener al menos 6 caracteres.');
                return;
            }
        });
    }
    
    // Form validation for registration
    const registerForm = document.querySelector('form[action="process_register.php"]');
    if (registerForm) {
        registerForm.addEventListener('submit', function(event) {
            const name = document.getElementById('register-name').value;
            const email = document.getElementById('register-email').value;
            const phone = document.getElementById('register-phone').value;
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('register-confirm-password').value;
            const terms = document.getElementById('terms').checked;
            
            if (name.trim() === '') {
                event.preventDefault();
                alert('Por favor, ingresa tu nombre completo.');
                return;
            }
            
            if (!validateEmail(email)) {
                event.preventDefault();
                alert('Por favor, ingresa un correo electrónico válido.');
                return;
            }
            
            if (!validatePhone(phone)) {
                event.preventDefault();
                alert('Por favor, ingresa un número de teléfono válido.');
                return;
            }
            
            if (password.length < 6) {
                event.preventDefault();
                alert('La contraseña debe tener al menos 6 caracteres.');
                return;
            }
            
            if (password !== confirmPassword) {
                event.preventDefault();
                alert('Las contraseñas no coinciden.');
                return;
            }
            
            if (!terms) {
                event.preventDefault();
                alert('Debes aceptar los términos y condiciones para registrarte.');
                return;
            }
        });
    }
    
    // Helper function to validate email
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    // Helper function to validate phone number
    function validatePhone(phone) {
        // Simple validation for Peruvian phone numbers
        const re = /^[0-9]{9}$/;
        return re.test(phone);
    }
});
