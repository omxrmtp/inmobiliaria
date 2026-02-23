<?php
session_start();

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
    $dni = filter_input(INPUT_POST, 'dni', FILTER_SANITIZE_STRING);
    $monthly_income = filter_input(INPUT_POST, 'monthly_income', FILTER_VALIDATE_FLOAT);
    $family_members = filter_input(INPUT_POST, 'family_members', FILTER_VALIDATE_INT);
    $own_property = filter_input(INPUT_POST, 'own_property', FILTER_SANITIZE_STRING);
    $modality = filter_input(INPUT_POST, 'modality', FILTER_SANITIZE_STRING);
    $savings = filter_input(INPUT_POST, 'savings', FILTER_VALIDATE_FLOAT);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    
    // Validar datos
    if (empty($full_name) || empty($dni) || empty($monthly_income) || empty($family_members) || 
        empty($own_property) || empty($modality) || empty($savings) || empty($email) || empty($phone)) {
        $_SESSION['error'] = "Por favor, completa todos los campos obligatorios.";
        header('Location: ../public/techo-propio.html');
        exit();
    }
    
    // Verificar elegibilidad
    $eligible = true;
    $message = "";
    
    // Verificar ingresos según modalidad
    if ($modality === 'new_home') {
        if ($monthly_income > 3715) {
            $eligible = false;
            $message .= "Tus ingresos mensuales superan el límite de S/. 3,715 para la modalidad de Adquisición de Vivienda Nueva. ";
        }
    } elseif ($modality === 'build' || $modality === 'improve') {
        if ($monthly_income > 2706) {
            $eligible = false;
            $message .= "Tus ingresos mensuales superan el límite de S/. 2,706 para la modalidad seleccionada. ";
        }
    }
    
    // Verificar propiedad según modalidad
    if ($modality === 'new_home' && $own_property !== 'no') {
        $eligible = false;
        $message .= "Para la modalidad de Adquisición de Vivienda Nueva, no debes tener una propiedad. ";
    } elseif ($modality === 'build' && $own_property !== 'yes_land') {
        $eligible = false;
        $message .= "Para la modalidad de Construcción en Sitio Propio, debes tener un terreno. ";
    } elseif ($modality === 'improve' && $own_property !== 'yes_house') {
        $eligible = false;
        $message .= "Para la modalidad de Mejoramiento de Vivienda, debes tener una casa. ";
    }
    
    // Verificar ahorro mínimo según modalidad
    $min_savings = 0;
    if ($modality === 'new_home') {
        $min_savings = 3000; // Ejemplo: 10% del valor mínimo de una vivienda
    } elseif ($modality === 'build') {
        $min_savings = 1000; // Ejemplo: 4% del valor de la obra
    } elseif ($modality === 'improve') {
        $min_savings = 500; // Ejemplo: 4% del valor de la mejora
    }
    
    if ($savings < $min_savings) {
        $eligible = false;
        $message .= "Tu ahorro de S/. " . number_format($savings, 2) . " es menor al mínimo requerido de S/. " . number_format($min_savings, 2) . " para la modalidad seleccionada. ";
    }
    
    // Mensaje final
    if ($eligible) {
        $message = "Basado en la información proporcionada, cumples con los requisitos básicos para acceder al programa Techo Propio en la modalidad seleccionada. Un asesor se pondrá en contacto contigo para brindarte más información y guiarte en el proceso de postulación.";
    } else {
        $message .= "Te recomendamos contactar a uno de nuestros asesores para explorar otras opciones o para recibir orientación sobre cómo podrías calificar en el futuro.";
    }
    
    try {
        $conn = connectDB();
        
        // Guardar verificación en la base de datos
        $stmt = $conn->prepare("
            INSERT INTO eligibility_checks (
                full_name, dni, monthly_income, family_members, own_property, 
                modality, savings, email, phone, eligible, message, created_at
            ) VALUES (
                :full_name, :dni, :monthly_income, :family_members, :own_property, 
                :modality, :savings, :email, :phone, :eligible, :message, NOW()
            )
        ");
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':dni', $dni);
        $stmt->bindParam(':monthly_income', $monthly_income);
        $stmt->bindParam(':family_members', $family_members);
        $stmt->bindParam(':own_property', $own_property);
        $stmt->bindParam(':modality', $modality);
        $stmt->bindParam(':savings', $savings);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':eligible', $eligible, PDO::PARAM_BOOL);
        $stmt->bindParam(':message', $message);
        $stmt->execute();
        
        // Guardar resultado en la sesión
        $_SESSION['eligibility_result'] = [
            'full_name' => $full_name,
            'dni' => $dni,
            'monthly_income' => $monthly_income,
            'family_members' => $family_members,
            'own_property' => $own_property,
            'modality' => $modality,
            'savings' => $savings,
            'email' => $email,
            'phone' => $phone,
            'eligible' => $eligible,
            'message' => $message
        ];
        
        // Redirigir a la página de resultados
        header('Location: ../public/eligibility_result.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al procesar la verificación: " . $e->getMessage();
        header('Location: ../public/techo-propio.html');
        exit();
    }
} else {
    // Si alguien intenta acceder directamente a este archivo
    header('Location: ../public/techo-propio.html');
    exit();
}
?>
