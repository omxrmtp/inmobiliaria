<?php
session_start();
require_once 'config/database.php';

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Obtener datos del formulario
  $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
  $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
  $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
  $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
  $testimonial = filter_input(INPUT_POST, 'testimonial', FILTER_SANITIZE_STRING);
  $video_url = filter_input(INPUT_POST, 'video_url', FILTER_SANITIZE_URL);
  
  // Depuración
  error_log("Datos recibidos: nombre=$name, email=$email, role=$role, rating=$rating, video_url=$video_url");
  
  // Validar datos
  if (empty($name) || empty($email) || empty($role) || empty($rating) || empty($testimonial)) {
      $_SESSION['error'] = "Por favor, completa todos los campos obligatorios.";
      header('Location: testimonials.html');
      exit();
  }
  
  // Validar rating
  if ($rating < 1 || $rating > 5) {
      $_SESSION['error'] = "La calificación debe estar entre 1 y 5 estrellas.";
      header('Location: testimonials.html');
      exit();
  }
  
  // Procesar foto
  $photo_path = null;
  if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
      $upload_dir = 'uploads/testimonials/photos/';
      
      // Crear directorio si no existe
      if (!file_exists($upload_dir)) {
          mkdir($upload_dir, 0777, true);
      }
      
      $file_extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
      $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
      
      if (in_array($file_extension, $allowed_extensions)) {
          $file_name = time() . '_' . uniqid() . '.' . $file_extension;
          $upload_path = $upload_dir . $file_name;
          
          if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
              $photo_path = $upload_path;
          }
      }
  }
  
  try {
      // 1) Intentar enviar al CRM primero
      $crmApiBase = 'http://host.docker.internal:5000/api';
      $crmUrl = rtrim($crmApiBase, '/') . '/public/testimonials';

      $payload = [
        'nombre' => $name,
        'correo' => $email,
        'calificacion' => $rating,
        'testimonio' => $testimonial,
        // Opcionales
        'videoUrl' => $video_url ?: null,
      ];

      $ch = curl_init($crmUrl);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, 7);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
      $crmResp = curl_exec($ch);
      $crmCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $crmErr  = curl_error($ch);
      curl_close($ch);

      if ($crmResp && $crmCode >= 200 && $crmCode < 300) {
        $data = json_decode($crmResp, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($data['success']) && $data['success'] === true) {
          $_SESSION['success'] = "¡Gracias! Tu testimonio fue enviado y está pendiente de aprobación.";
          header('Location: testimonials.html');
          exit();
        }
      }

      // 2) Fallback: guardar localmente en MySQL como 'pending'
      $conn = connectDB();
      if ($conn) {
        // Asegurar columna video_url
        $stmt = $conn->query("SHOW COLUMNS FROM testimonials LIKE 'video_url'");
        if ($stmt->rowCount() === 0) {
          $conn->exec("ALTER TABLE testimonials ADD COLUMN video_url VARCHAR(255) DEFAULT NULL AFTER media");
        }

        $sql = "INSERT INTO testimonials (
                  nombre, correo, calificacion, testimonio, foto, video_url, estado, creado_en
                ) VALUES (
                  :name, :email, :rating, :testimonial, :photo, :video_url, 'pending', NOW()
                )";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);

        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':testimonial', $testimonial);
        $stmt->bindParam(':photo', $photo_path);
        $stmt->bindParam(':video_url', $video_url);
        $stmt->execute();
      }

      $_SESSION['success'] = "¡Gracias! Tu testimonio fue recibido y está pendiente de aprobación.";
      header('Location: testimonials.html');
      exit();
  } catch (PDOException $e) {
      error_log("Error en process_testimonial.php: " . $e->getMessage());
      $_SESSION['error'] = "Error al enviar el testimonio: " . $e->getMessage();
      header('Location: testimonials.html');
      exit();
  }
} else {
  // Si alguien intenta acceder directamente a este archivo
  header('Location: testimonials.html');
  exit();
}
?>