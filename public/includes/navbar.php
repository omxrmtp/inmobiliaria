    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="index.php" class="logo">
                    <img src="logo/propiedadedelgado.png" alt="TechoPropio Logo" class="header-image">
                </a>
            </div>
            
            <nav class="nav">
                <ul class="nav-list">
                    <li class="nav-item"><a href="index.php" class="nav-link <?php echo (isset($page) && $page == 'home') ? 'active' : ''; ?>">Inicio</a></li>
                    <li class="nav-item"><a href="proyectos.php" class="nav-link <?php echo (isset($page) && $page == 'proyectos') ? 'active' : ''; ?>">Proyectos</a></li>
                    <li class="nav-item nav-dropdown">
                        <a href="javascript:void(0)" class="nav-link nav-dropdown-toggle">
                            Nosotros <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="nav-dropdown-content">
                            <a href="team.php" class="nav-dropdown-item <?php echo (isset($page) && $page == 'team') ? 'active' : ''; ?>">Equipo</a>
                            <a href="otros-servicios.php" class="nav-dropdown-item <?php echo (isset($page) && $page == 'otros-servicios') ? 'active' : ''; ?>">Otros Servicios</a>
                        </div>
                    </li>
                    <li class="nav-item"><a href="contact.php" class="nav-link <?php echo (isset($page) && $page == 'contact') ? 'active' : ''; ?>">Contacto</a></li>
                </ul>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <a href="client-login.php" class="btn-login">
                        <i class="fas fa-user"></i> Portal Cliente
                    </a>
                </div>
            </nav>
            
            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>
