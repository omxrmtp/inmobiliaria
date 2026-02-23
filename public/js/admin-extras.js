/**
 * Funcionalidades adicionales para el panel de administración
 */

document.addEventListener("DOMContentLoaded", () => {
    // Inicializar tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.forEach((tooltipTriggerEl) => {
      new bootstrap.Tooltip(tooltipTriggerEl)
    })
  
    // Animación para las tarjetas de estadísticas
    const statNumbers = document.querySelectorAll(".stat-number")
  
    statNumbers.forEach((statNumber) => {
      const finalValue = Number.parseInt(statNumber.textContent)
      let currentValue = 0
      const duration = 1500 // ms
      const increment = finalValue / (duration / 16) // 60fps
  
      const animateValue = () => {
        currentValue += increment
  
        if (currentValue < finalValue) {
          statNumber.textContent = Math.floor(currentValue)
          requestAnimationFrame(animateValue)
        } else {
          statNumber.textContent = finalValue
        }
      }
  
      // Iniciar animación cuando la tarjeta es visible
      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            animateValue()
            observer.unobserve(entry.target)
          }
        })
      })
  
      observer.observe(statNumber)
    })
  
    // Notificaciones en tiempo real (simuladas)
    const showNotification = (title, message, type = "info") => {
      // Crear elemento de notificación
      const notification = document.createElement("div")
      notification.className = `notification notification-${type}`
  
      // Contenido de la notificación
      notification.innerHTML = `
        <div class="notification-icon">
          <i class="fas ${type === "info" ? "fa-info-circle" : type === "success" ? "fa-check-circle" : "fa-exclamation-circle"}"></i>
        </div>
        <div class="notification-content">
          <h4>${title}</h4>
          <p>${message}</p>
        </div>
        <button class="notification-close"><i class="fas fa-times"></i></button>
      `
  
      // Agregar al DOM
      const notificationsContainer = document.querySelector(".notifications-container")
      if (!notificationsContainer) {
        const container = document.createElement("div")
        container.className = "notifications-container"
        document.body.appendChild(container)
        container.appendChild(notification)
      } else {
        notificationsContainer.appendChild(notification)
      }
  
      // Cerrar notificación
      const closeBtn = notification.querySelector(".notification-close")
      closeBtn.addEventListener("click", () => {
        notification.classList.add("notification-hiding")
        setTimeout(() => {
          notification.remove()
        }, 300)
      })
  
      // Auto-cerrar después de 5 segundos
      setTimeout(() => {
        if (document.body.contains(notification)) {
          notification.classList.add("notification-hiding")
          setTimeout(() => {
            notification.remove()
          }, 300)
        }
      }, 5000)
    }
  
    // Simular notificaciones periódicas
    if (Math.random() > 0.7) {
      setTimeout(() => {
        showNotification("Nueva consulta recibida", "Se ha recibido una nueva consulta de propiedad.", "info")
      }, 10000)
    }
  
    // Búsqueda en tablas
    const tableSearchInputs = document.querySelectorAll(".table-search")
  
    tableSearchInputs.forEach((input) => {
      input.addEventListener("keyup", function () {
        const searchValue = this.value.toLowerCase()
        const tableId = this.getAttribute("data-table")
        const table = document.getElementById(tableId)
  
        if (table) {
          const rows = table.querySelectorAll("tbody tr")
  
          rows.forEach((row) => {
            const text = row.textContent.toLowerCase()
            if (text.indexOf(searchValue) > -1) {
              row.style.display = ""
            } else {
              row.style.display = "none"
            }
          })
        }
      })
    })
  
    // Exportar tablas a CSV
    const exportButtons = document.querySelectorAll(".export-csv")
  
    exportButtons.forEach((button) => {
      button.addEventListener("click", function () {
        const tableId = this.getAttribute("data-table")
        const table = document.getElementById(tableId)
  
        if (table) {
          const rows = table.querySelectorAll("tr")
          let csvContent = "data:text/csv;charset=utf-8,"
  
          rows.forEach((row) => {
            const rowData = []
            const cells = row.querySelectorAll("th, td")
  
            cells.forEach((cell) => {
              // Limpiar texto (eliminar HTML)
              const text = cell.textContent.trim().replace(/,/g, " ")
              rowData.push(`"${text}"`)
            })
  
            csvContent += rowData.join(",") + "\r\n"
          })
  
          const encodedUri = encodeURI(csvContent)
          const link = document.createElement("a")
          link.setAttribute("href", encodedUri)
          link.setAttribute("download", `${tableId}_${new Date().toISOString().slice(0, 10)}.csv`)
          document.body.appendChild(link)
          link.click()
          document.body.removeChild(link)
        }
      })
    })
  })
  
  