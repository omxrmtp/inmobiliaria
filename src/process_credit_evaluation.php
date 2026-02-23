<?php
session_start();

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $full_name = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $city = filter_input(INPUT_POST, 'ciudad', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    // Datos de pre-evaluación (si están disponibles)
    $has_land = filter_input(INPUT_POST, 'terreno', FILTER_SANITIZE_STRING);
    $has_income = filter_input(INPUT_POST, 'ingresos', FILTER_SANITIZE_STRING);
    $good_credit = filter_input(INPUT_POST, 'calificacion', FILTER_SANITIZE_STRING);
    
    // Validar datos básicos
    if (empty($full_name) || empty($phone)) {
        $_SESSION['error'] = "Por favor, complete los campos obligatorios.";
        header('Location: ../public/credito.html');
        exit();
    }
    
    // Determinar elegibilidad
    $eligible = false;
    $message = '';
    
    if ($has_land === 'si' && $has_income === 'si' && $good_credit === 'si') {
        $eligible = true;
        $message = 'El cliente cumple con los requisitos básicos para el Crédito Mi Vivienda.';
    } elseif (isset($has_land) || isset($has_income) || isset($good_credit)) {
        $message = 'El cliente no cumple con todos los requisitos para el Crédito Mi Vivienda, pero podría calificar para otros productos.';
    } else {
        $message = 'El cliente solicita información sobre Crédito Mi Vivienda.';
    }
    
    try {
        // Conectar a la base de datos
        $conn = connectDB();
        
        // Guardar pre-evaluación en la base de datos
        $stmt = $conn->prepare("INSERT INTO credit_evaluations (full_name, city, phone, email, has_land, has_income, good_credit, eligible, message, created_at) VALUES (:full_name, :city, :phone, :email, :has_land, :has_income, :good_credit, :eligible, :message, NOW())");
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':has_land', $has_land);
        $stmt->bindParam(':has_income', $has_income);
        $stmt->bindParam(':good_credit', $good_credit);
        $stmt->bindParam(':eligible', $eligible, PDO::PARAM_BOOL);
        $stmt->bindParam(':message', $message);
        $stmt->execute();
        
        // Enviar correo electrónico al administrador
        $to = "inmobiliaria.dpropiedades@gmail.com"; // Correo de la empresa
        $subject = "Nueva solicitud de Crédito Mi Vivienda";
        
        $email_message = "Se ha recibido una nueva solicitud de Crédito Mi Vivienda:\n\n";
        $email_message .= "Nombre: $full_name\n";
        $email_message .= "Ciudad: $city\n";
        $email_message .= "Teléfono: $phone\n";
        
        if (!empty($email)) {
            $email_message .= "Email: $email\n";
        }
        
        if (isset($has_land) && isset($has_income) && isset($good_credit)) {
            $email_message .= "\nResultados de pre-evaluación:\n";
            $email_message .= "¿Tiene terreno propio?: " . ($has_land === 'si' ? 'Sí' : 'No') . "\n";
            $email_message .= "¿Tiene ingresos sustentables?: " . ($has_income === 'si' ? 'Sí' : 'No') . "\n";
            $email_message .= "¿Está bien calificado en el sistema financiero?: " . ($good_credit === 'si' ? 'Sí' : 'No') . "\n";
            $email_message .= "Elegible: " . ($eligible ? 'Sí' : 'No') . "\n";
        }
        
        $email_message .= "\nMensaje: $message\n";
        $email_message .= "\nEl cliente solicita ser contactado por un asesor para obtener más información sobre Crédito Mi Vivienda.";
        
        $headers = "From: noreply@inmobiliariapro.com";
        
        mail($to, $subject, $email_message, $headers);
        
        // Mensaje de éxito
        $_SESSION['success'] = "¡Gracias por tu interés! Hemos recibido tu solicitud y un asesor se pondrá en contacto contigo pronto.";
        
        // Redirigir de vuelta a la página
        header('Location: ../public/credito.html');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al procesar la solicitud: " . $e->getMessage();
        header('Location: ../public/credito.html');
        exit();
    }
} else {
    // Si alguien intenta acceder directamente a este archivo
    header('Location: ../public/credito.html');
    exit();
}
?>

