document.addEventListener("DOMContentLoaded", () => {
    // Manejar la selección de archivos para mostrar el nombre del archivo
    const fileInputs = document.querySelectorAll('input[type="file"]')
  
    fileInputs.forEach((input) => {
      input.addEventListener("change", function () {
        const fileNameSpan = this.parentElement.querySelector(".file-name")
  
        if (this.files.length > 0) {
          const fileName = this.files[0].name
          fileNameSpan.textContent = fileName
  
          // Verificar si es un video y mostrar un mensaje de confirmación
          if (this.id === "media" && this.files[0].type.startsWith("video/")) {
            console.log("Video detectado:", this.files[0].type)
            alert("Video seleccionado: " + fileName)
          }
        } else {
          fileNameSpan.textContent = "Ningún archivo seleccionado"
        }
      })
    })
  })
  
  