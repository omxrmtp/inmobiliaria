<?php
require_once 'config/database.php';

// Verificar si es una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $city = isset($_POST['city']) ? trim($_POST['city']) : '';
    $own_property = isset($_POST['propiedad']) ? $_POST['propiedad'] : '';
    $income = isset($_POST['ingresos']) ? $_POST['ingresos'] : '';
    $previous_support = isset($_POST['apoyo']) ? $_POST['apoyo'] : '';
    $family = isset($_POST['familia']) ? $_POST['familia'] : '';
    $savings = isset($_POST['ahorro']) ? $_POST['ahorro'] : '';
    $modality = isset($_POST['modality']) ? $_POST['modality'] : 'new_home'; // Valor por defecto
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    // Determinar si es elegible basado en las respuestas
    $eligible = ($own_property === 'no' && 
                $income === 'no' && 
                $previous_support === 'no' && 
                $family === 'si' && 
                $savings === 'si') ? 1 : 0;
    
    // Validar datos requeridos
    if (empty($full_name) || empty($email) || empty($phone)) {
        $response = [
            'success' => false,
            'message' => 'Por favor complete todos los campos obligatorios.'
        ];
    } else {
        try {
            // Conectar a la base de datos
            $conn = connectDB();
            
            // Preparar la consulta SQL
            $stmt = $conn->prepare("INSERT INTO eligibility_checks 
                (full_name, email, phone, city, own_property, income_exceeds, previous_support, has_family, has_savings, modality, message, eligible, created_at) 
                VALUES 
                (:full_name, :email, :phone, :city, :own_property, :income, :previous_support, :family, :savings, :modality, :message, :eligible, NOW())");
            
            // Vincular parámetros
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':city', $city);
            $stmt->bindParam(':own_property', $own_property);
            $stmt->bindParam(':income', $income);
            $stmt->bindParam(':previous_support', $previous_support);
            $stmt->bindParam(':family', $family);
            $stmt->bindParam(':savings', $savings);
            $stmt->bindParam(':modality', $modality);
            $stmt->bindParam(':message', $message);
            $stmt->bindParam(':eligible', $eligible);
            
            // Ejecutar la consulta
            $stmt->execute();
            
            $response = [
                'success' => true,
                'message' => 'Su preevaluación ha sido registrada correctamente.',
                'eligible' => $eligible == 1
            ];
        } catch (PDOException $e) {
            $response = [
                'success' => false,
                'message' => 'Error al guardar los datos: ' . $e->getMessage()
            ];
        }
    }
    
    // Devolver respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Si no es una solicitud POST, redirigir a la página principal
header('Location: techo-propio.html');
exit;
?>
