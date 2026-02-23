<?php $page = 'habilitaciones'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Habilitaciones - DelgadoPropiedades</title>
    
    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="images/propiedad.png" type="image/png">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Estilos CSS -->
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/animations.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/habilitaciones.css">
</head>
<body data-etiqueta-filtro="habilitaciones">
    <?php include 'includes/navbar.php'; ?>

    <!-- Proyectos Disponibles Section -->
    <section class="properties-section" style="padding: 60px 0; background: #f9fafb;">
        <div class="container">
            <div class="properties-header">
                <div class="properties-count">
                    <h2>Proyectos de Habilitaciones</h2>
                    <p>Mostrando <span id="properties-count">0</span> proyectos</p>
                </div>
                <div class="properties-sort">
                    <label for="sort-by">Ordenar por:</label>
                    <select id="sort-by">
                        <option value="price-asc">Precio: Menor a Mayor</option>
                        <option value="price-desc">Precio: Mayor a Menor</option>
                        <option value="date-desc">Más Recientes</option>
                        <option value="date-asc">Más Antiguos</option>
                    </select>
                </div>
            </div>
            
            <div id="properties-grid" class="properties-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px; margin: 40px 0;">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Cargando proyectos...</p>
                </div>
            </div>
            
            <div class="pagination" id="pagination" style="display: flex; justify-content: center; gap: 8px; margin-top: 40px; flex-wrap: wrap;"></div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

  <!-- Template para las tarjetas de proyectos Habilitaciones -->
  <template id="property-card-template">
    <div class="property-card" data-id="" data-type="">
      <div class="property-image">
        <img src="/placeholder.svg" alt="">
        <span class="property-tag"></span>
      </div>
      <div class="property-info">
        <h3 class="property-title"></h3>
        <div class="property-location">
          <i class="fas fa-map-marker-alt"></i>
          <span></span>
        </div>
        <div class="property-price"></div>
        <div class="property-buttons">
          <button class="btn btn-primary btn-view-details" data-property-id="">Contactar</button>
          <button class="btn btn-outline btn-view-modal" data-property-id="">Ver más</button>
        </div>
      </div>
    </div>
  </template>

  <!-- Modal de Detalles de Propiedad -->
  <div id="property-modal" class="modal" style="display: none;">
    <div class="modal-content">
      <span class="modal-close">&times;</span>
      <div id="modal-body">
        <!-- Se llenará dinámicamente -->
      </div>
    </div>
  </div>

<script>document.getElementById('current-year').textContent = new Date().getFullYear();</script>

    <script src="js/main.js"></script>
    <script src="js/habilitaciones.js"></script>
</body>
</html>
