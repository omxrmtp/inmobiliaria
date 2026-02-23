<?php
session_start();
require_once 'config/database.php';

// Registrar los datos recibidos para depuración
error_log('POST data: ' . print_r($_POST, true));

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    if (empty($email)) {
        $email = 'sin_email@example.com';
    }
    $pre_evaluation_result = filter_input(INPUT_POST, 'pre_evaluation_result', FILTER_SANITIZE_STRING);
    
    // Obtener información de la preevaluación de la sesión si existe
    $own_property = isset($_SESSION['propiedad']) ? $_SESSION['propiedad'] : 'no';
    $income_exceeds = isset($_SESSION['ingresos']) ? $_SESSION['ingresos'] : 'no';
    $previous_support = isset($_SESSION['apoyo']) ? $_SESSION['apoyo'] : 'no';
    $has_family = isset($_SESSION['familia']) ? $_SESSION['familia'] : 'no';
    $has_savings = isset($_SESSION['ahorro']) ? $_SESSION['ahorro'] : 'no';
    
    // También obtener de POST por si vienen directamente del formulario
    if (isset($_POST['propiedad'])) $own_property = $_POST['propiedad'];
    if (isset($_POST['ingresos'])) $income_exceeds = $_POST['ingresos'];
    if (isset($_POST['apoyo'])) $previous_support = $_POST['apoyo'];
    if (isset($_POST['familia'])) $has_family = $_POST['familia'];
    if (isset($_POST['ahorro'])) $has_savings = $_POST['ahorro'];
    
    // Validate input
    if (empty($full_name) || empty($phone)) {
        $_SESSION['error'] = 'Por favor, completa todos los campos obligatorios.';
        header('Location: techo-propio.html');
        exit();
    }
    
    // Determine eligibility based on criteria
    $eligible = 0;
    $message = '';
    
    // If coming from pre-evaluation form
    if ($pre_evaluation_result === 'eligible') {
        $eligible = 1;
        $message = 'Felicidades, según la pre-evaluación cumples con los requisitos básicos para el programa Techo Propio en la modalidad de Adquisición de Vivienda Nueva. Uno de nuestros asesores se pondrá en contacto contigo para verificar los requisitos y continuar con el proceso.';
    } else {
        // Determinar elegibilidad basada en las respuestas de la preevaluación
        if ($own_property === 'no' && 
            $income_exceeds === 'no' && 
            $previous_support === 'no' && 
            $has_family === 'si' && 
            $has_savings === 'si') {
            $eligible = 1;
            $message = 'Felicidades, cumples con los requisitos básicos para el programa Techo Propio. Uno de nuestros asesores se pondrá en contacto contigo para verificar los requisitos y continuar con el proceso.';
        } else {
            $eligible = 0;
            $message = 'Según tus respuestas, es posible que no cumplas con todos los requisitos básicos para el programa Techo Propio. Sin embargo, existen otras opciones que podrían ajustarse a tu situación.';
        }
    }
    
    try {
        // Connect to database
        $conn = connectDB();
        
        // Verificar si la tabla existe, si no, crearla
        $tableExists = $conn->query("SHOW TABLES LIKE 'eligibility_checks'")->rowCount() > 0;
        if (!$tableExists) {
            $conn->exec("CREATE TABLE eligibility_checks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                full_name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(50) NOT NULL,
                city VARCHAR(100),
                own_property ENUM('no', 'yes_land', 'yes_house', 'si') DEFAULT 'no',
                income_exceeds ENUM('si', 'no') DEFAULT 'no',
                previous_support ENUM('si', 'no') DEFAULT 'no',
                has_family ENUM('si', 'no') DEFAULT 'no',
                has_savings ENUM('si', 'no') DEFAULT 'no',
                message TEXT,
                eligible BOOLEAN DEFAULT 0,
                contacted BOOLEAN DEFAULT 0,
                contacted_at DATETIME,
                contact_notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");
        }
        
        // Registrar en el log para depuración
        error_log("Datos a insertar: \n" .
            "full_name: $full_name\n" .
            "email: $email\n" .
            "phone: $phone\n" .
            "city: $city\n" .
            "own_property: $own_property\n" .
            "income_exceeds: $income_exceeds\n" .
            "previous_support: $previous_support\n" .
            "has_family: $has_family\n" .
            "has_savings: $has_savings\n" .
            "eligible: $eligible");
        
        // Save eligibility check to database
        $stmt = $conn->prepare("INSERT INTO eligibility_checks 
            (full_name, email, phone, city, own_property, income_exceeds, previous_support, has_family, has_savings, message, eligible, created_at) 
            VALUES 
            (:full_name, :email, :phone, :city, :own_property, :income_exceeds, :previous_support, :has_family, :has_savings, :message, :eligible, NOW())");
        
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':own_property', $own_property);
        $stmt->bindParam(':income_exceeds', $income_exceeds);
        $stmt->bindParam(':previous_support', $previous_support);
        $stmt->bindParam(':has_family', $has_family);
        $stmt->bindParam(':has_savings', $has_savings);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':eligible', $eligible);
        $stmt->execute();
        
        // Registrar el éxito para depuración
        error_log('Datos guardados correctamente en la base de datos');
        
        // Enviar correo electrónico al administrador
        $to = "inmobiliaria.dpropiedades@gmail.com"; // Correo de la empresa
        $subject = "Nueva solicitud de Techo Propio";
        
        $email_message = "Se ha recibido una nueva solicitud de Techo Propio:\n\n";
        $email_message .= "Nombre: $full_name\n";
        $email_message .= "Ciudad: $city\n";
        $email_message .= "Teléfono: $phone\n";
        $email_message .= "Email: $email\n";
        
        $email_message .= "\nResultados de pre-evaluación:\n";
        $email_message .= "¿Tiene propiedad?: " . ($own_property === 'no' ? 'No' : 'Sí') . "\n";
        $email_message .= "¿Ingresos exceden el límite?: " . ($income_exceeds === 'no' ? 'No' : 'Sí') . "\n";
        $email_message .= "¿Recibió apoyo previo?: " . ($previous_support === 'no' ? 'No' : 'Sí') . "\n";
        $email_message .= "¿Tiene familia?: " . ($has_family === 'si' ? 'Sí' : 'No') . "\n";
        $email_message .= "¿Tiene ahorros?: " . ($has_savings === 'si' ? 'Sí' : 'No') . "\n";
        $email_message .= "Elegible: " . ($eligible ? 'Sí' : 'No') . "\n";
        $email_message .= "\nMensaje: $message\n";
        
        $headers = "From: noreply@inmobiliariapro.com";
        
        mail($to, $subject, $email_message, $headers);
        
        // Limpiar variables de sesión
        unset($_SESSION['propiedad']);
        unset($_SESSION['ingresos']);
        unset($_SESSION['apoyo']);
        unset($_SESSION['familia']);
        unset($_SESSION['ahorro']);
        
        // Store result in session for display
        $_SESSION['eligibility_result'] = [
            'eligible' => $eligible,
            'message' => $message,
            'full_name' => $full_name,
            'email' => $email,
            'phone' => $phone,
            'city' => $city
        ];
        
        // Redirigir a la página de resultados o mostrar un mensaje de éxito
        $_SESSION['success'] = 'Gracias por tu interés. Hemos recibido tus datos y un asesor se pondrá en contacto contigo pronto.';
        header('Location: techo-propio.html#success');
        exit();
    } catch (PDOException $e) {
        // Registrar el error para depuración
        error_log('Error en process_eligibility.php: ' . $e->getMessage());
        
        $_SESSION['error'] = 'Error al procesar la verificación: ' . $e->getMessage();
        header('Location: techo-propio.html#error');
        exit();
    }
}
?>


