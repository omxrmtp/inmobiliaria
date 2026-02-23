<?php
require_once 'config/database.php';

// Verificar si es una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $formData = [];
    
    // Intentar obtener datos JSON primero
    $jsonData = json_decode(file_get_contents('php://input'), true);
    if ($jsonData) {
        $formData = $jsonData;
    } else {
        // Si no hay JSON, usar datos de formulario normal
        $formData = $_POST;
    }
    
    // Registrar los datos recibidos para depuración
    error_log('Datos recibidos: ' . print_r($formData, true));
    
    // Extraer datos del formulario
    $full_name = isset($formData['full_name']) ? trim($formData['full_name']) : '';
    $email = isset($formData['email']) ? trim($formData['email']) : '';
    $phone = isset($formData['phone']) ? trim($formData['phone']) : '';
    $city = isset($formData['city']) ? trim($formData['city']) : '';
    $own_property = isset($formData['propiedad']) ? $formData['propiedad'] : 'no';
    $income_exceeds = isset($formData['ingresos']) ? $formData['ingresos'] : 'no';
    $previous_support = isset($formData['apoyo']) ? $formData['apoyo'] : 'no';
    $has_family = isset($formData['familia']) ? $formData['familia'] : 'no';
    $has_savings = isset($formData['ahorro']) ? $formData['ahorro'] : 'no';
    $message = isset($formData['message']) ? trim($formData['message']) : '';
    
    // Validar datos requeridos
    if (empty($full_name) || empty($phone)) {
        $response = [
            'success' => false,
            'message' => 'Por favor complete los campos obligatorios (nombre y teléfono).'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Si el email está vacío, usar un valor por defecto
    if (empty($email)) {
        $email = 'sin_email@example.com';
    }
    
    // Determinar elegibilidad
    $eligible = 0;
    if ($own_property === 'no' && 
        $income_exceeds === 'no' && 
        $previous_support === 'no' && 
        $has_family === 'si' && 
        $has_savings === 'si') {
        $eligible = 1;
    }
    
    try {
        // Conectar a la base de datos
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
                own_property ENUM('no', 'yes_land', 'yes_house') DEFAULT 'no',
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
        
        // Preparar la consulta
        $stmt = $conn->prepare("INSERT INTO eligibility_checks 
            (full_name, email, phone, city, own_property, income_exceeds, previous_support, has_family, has_savings, message, eligible, created_at) 
            VALUES 
            (:full_name, :email, :phone, :city, :own_property, :income_exceeds, :previous_support, :has_family, :has_savings, :message, :eligible, NOW())");
        
        // Vincular parámetros
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
        
        // Ejecutar la consulta
        $stmt->execute();
        
        // Responder con éxito
        $response = [
            'success' => true,
            'message' => 'Datos guardados correctamente',
            'eligible' => $eligible == 1
        ];
    } catch (PDOException $e) {
        // Registrar el error
        error_log('Error en save_techo_propio.php: ' . $e->getMessage());
        
        // Responder con error
        $response = [
            'success' => false,
            'message' => 'Error al guardar los datos: ' . $e->getMessage()
        ];
    }
    
    // Enviar respuesta como JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Si no es una solicitud POST, redirigir a la página principal
header('Location: techo-propio.html');
exit;
?>
