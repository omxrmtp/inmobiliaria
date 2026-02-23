import { Chart } from "@/components/ui/chart"
/**
 * Admin Panel JavaScript
 * Handles sidebar toggle, dropdowns, and other interactive elements
 */

document.addEventListener("DOMContentLoaded", () => {
  // Toggle sidebar on mobile
  const toggleSidebarBtn = document.getElementById("toggle-sidebar")
  const sidebar = document.querySelector(".sidebar")
  const content = document.querySelector(".content")

  if (toggleSidebarBtn) {
    toggleSidebarBtn.addEventListener("click", () => {
      sidebar.classList.toggle("active")

      // Add overlay when sidebar is active on mobile
      if (sidebar.classList.contains("active")) {
        const overlay = document.createElement("div")
        overlay.className = "sidebar-overlay"
        overlay.style.position = "fixed"
        overlay.style.top = "0"
        overlay.style.left = "0"
        overlay.style.width = "100%"
        overlay.style.height = "100%"
        overlay.style.backgroundColor = "rgba(0, 0, 0, 0.5)"
        overlay.style.zIndex = "999"
        document.body.appendChild(overlay)

        overlay.addEventListener("click", function () {
          sidebar.classList.remove("active")
          this.remove()
        })
      } else {
        const overlay = document.querySelector(".sidebar-overlay")
        if (overlay) {
          overlay.remove()
        }
      }
    })
  }

  // Dropdown menus
  const dropdownBtns = document.querySelectorAll(".dropdown-btn")

  dropdownBtns.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.stopPropagation()

      // Close all other dropdowns
      document.querySelectorAll(".dropdown-content").forEach((dropdown) => {
        if (dropdown !== this.nextElementSibling) {
          dropdown.classList.remove("show")
        }
      })

      // Toggle current dropdown
      this.nextElementSibling.classList.toggle("show")
    })
  })

  // Close dropdowns when clicking outside
  document.addEventListener("click", (e) => {
    if (!e.target.matches(".dropdown-btn")) {
      document.querySelectorAll(".dropdown-content").forEach((dropdown) => {
        dropdown.classList.remove("show")
      })
    }
  })

  // Filter tabs
  const filterBtns = document.querySelectorAll(".filter-btn")
  const filterItems = document.querySelectorAll(".filter-item")

  if (filterBtns.length > 0 && filterItems.length > 0) {
    filterBtns.forEach((btn) => {
      btn.addEventListener("click", function () {
        // Remove active class from all buttons
        filterBtns.forEach((b) => b.classList.remove("active"))

        // Add active class to clicked button
        this.classList.add("active")

        // Get filter value
        const filter = this.getAttribute("data-filter")

        // Show/hide items based on filter
        filterItems.forEach((item) => {
          if (filter === "all" || item.classList.contains(filter)) {
            item.style.display = ""
          } else {
            item.style.display = "none"
          }
        })
      })
    })
  }

  // Read more functionality for truncated text
  const readMoreLinks = document.querySelectorAll(".show-full-text")

  readMoreLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault()

      const messageText = this.closest(".message-text")
      const readMore = messageText.querySelector(".read-more")
      const fullText = messageText.querySelector(".full-text")

      if (fullText.style.display === "none") {
        readMore.style.display = "none"
        fullText.style.display = "block"
      } else {
        readMore.style.display = "inline"
        fullText.style.display = "none"
      }
    })
  })

  // Form validation
  const forms = document.querySelectorAll("form")

  forms.forEach((form) => {
    form.addEventListener("submit", (e) => {
      const requiredFields = form.querySelectorAll("[required]")
      let isValid = true

      requiredFields.forEach((field) => {
        if (!field.value.trim()) {
          isValid = false
          field.classList.add("error")

          // Add error message if it doesn't exist
          let errorMessage = field.nextElementSibling
          if (!errorMessage || !errorMessage.classList.contains("error-message")) {
            errorMessage = document.createElement("div")
            errorMessage.className = "error-message"
            errorMessage.textContent = "Este campo es obligatorio"
            field.parentNode.insertBefore(errorMessage, field.nextSibling)
          }
        } else {
          field.classList.remove("error")

          // Remove error message if it exists
          const errorMessage = field.nextElementSibling
          if (errorMessage && errorMessage.classList.contains("error-message")) {
            errorMessage.remove()
          }
        }
      })

      if (!isValid) {
        e.preventDefault()
      }
    })
  })

  // Dark mode toggle
  const darkModeToggle = document.getElementById("dark-mode-toggle")

  if (darkModeToggle) {
    // Check for saved theme preference or use system preference
    const savedTheme = localStorage.getItem("theme")
    const systemPrefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches

    if (savedTheme === "dark" || (!savedTheme && systemPrefersDark)) {
      document.documentElement.classList.add("dark")
      darkModeToggle.checked = true
    }

    darkModeToggle.addEventListener("change", function () {
      if (this.checked) {
        document.documentElement.classList.add("dark")
        localStorage.setItem("theme", "dark")
      } else {
        document.documentElement.classList.remove("dark")
        localStorage.setItem("theme", "light")
      }
    })
  }

  // Initialize any charts or data visualizations
  initializeCharts()
})

/**
 * Initialize charts and data visualizations
 */
function initializeCharts() {
  // Check if Chart.js is available
  if (typeof Chart !== "undefined") {
    // Sample chart for dashboard
    const ctx = document.getElementById("statsChart")

    if (ctx) {
      new Chart(ctx, {
        type: "line",
        data: {
          labels: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
          datasets: [
            {
              label: "Propiedades",
              data: [12, 19, 15, 17, 22, 24, 20, 25, 28, 30, 35, 40],
              borderColor: "#2563eb",
              backgroundColor: "rgba(37, 99, 235, 0.1)",
              tension: 0.3,
              fill: true,
            },
            {
              label: "Consultas",
              data: [5, 10, 8, 15, 12, 18, 20, 22, 24, 20, 25, 30],
              borderColor: "#10b981",
              backgroundColor: "rgba(16, 185, 129, 0.1)",
              tension: 0.3,
              fill: true,
            },
          ],
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: "top",
            },
            title: {
              display: true,
              text: "Estadísticas Anuales",
            },
          },
          scales: {
            y: {
              beginAtZero: true,
            },
          },
        },
      })
    }
  }
}

