<?php
session_start();
require_once 'config/database.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Función para registrar logs
function writeLog($message) {
    $logFile = 'logs/inquiry.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    
    // Crear directorio de logs si no existe
    if (!file_exists('logs')) {
        mkdir('logs', 0777, true);
    }
    
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Debug: Registrar todos los datos POST recibidos
writeLog("Datos POST recibidos: " . print_r($_POST, true));

// Verificar si es una solicitud POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    writeLog("Error: Método no permitido - " . $_SERVER["REQUEST_METHOD"]);
    $_SESSION['error'] = "Método no permitido";
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

// Validar datos requeridos
$required_fields = ['name', 'email', 'phone', 'message'];
$missing_fields = [];

foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    writeLog("Error: Faltan campos requeridos: " . implode(', ', $missing_fields));
    $_SESSION['error'] = "Faltan campos requeridos: " . implode(', ', $missing_fields);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

// Obtener y sanitizar datos
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
$property_id = isset($_POST['property_id']) ? filter_input(INPUT_POST, 'property_id', FILTER_VALIDATE_INT) : null;

writeLog("Datos sanitizados - Nombre: $name, Email: $email, Teléfono: $phone, Mensaje: $message, Property ID: " . ($property_id ?? 'no especificado'));

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    writeLog("Error: Email inválido - $email");
    $_SESSION['error'] = "Email inválido";
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

try {
    $conn = connectDB();
    writeLog("Conexión a base de datos establecida");
    
    // Debug: Verificar la conexión
    writeLog("Estado de la conexión: " . ($conn ? "Exitosa" : "Fallida"));
    
    // Crear el asunto
    $subject = "Consulta sobre propiedad";
    if ($property_id) {
        // Obtener el título de la propiedad
        $stmt = $conn->prepare("SELECT title FROM properties WHERE id = :property_id");
        $stmt->bindParam(':property_id', $property_id);
        $stmt->execute();
        
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $subject .= ": " . $row['title'];
        }
    }

    writeLog("Preparando inserción en base de datos con asunto: $subject");
    
    // Debug: Mostrar la consulta SQL que se va a ejecutar
    $sql = "INSERT INTO inquiries (name, email, phone, subject, message, property_id, status, created_at) 
            VALUES (:name, :email, :phone, :subject, :message, :property_id, 'pending', NOW())";
    writeLog("SQL a ejecutar: $sql");
    
    // Insertar la consulta en la base de datos
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':subject', $subject);
    $stmt->bindParam(':message', $message);
    $stmt->bindParam(':property_id', $property_id);
    
    // Debug: Mostrar los valores que se van a insertar
    $debug_values = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'subject' => $subject,
        'message' => $message,
        'property_id' => $property_id
    ];
    writeLog("Valores a insertar: " . print_r($debug_values, true));
    
    if ($stmt->execute()) {
        writeLog("Consulta guardada en base de datos correctamente. ID insertado: " . $conn->lastInsertId());
        
        try {
            $mail = new PHPMailer(true);

            //Configuración del servidor
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'inmobiliaria.dpropiedades@gmail.com';
            $mail->Password   = 'glzc bbbp eqks lulm';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            //Correo al administrador
            $mail->setFrom('inmobiliaria.dpropiedades@gmail.com', 'Inmobiliaria');
            $mail->addAddress('inmobiliaria.dpropiedades@gmail.com');
            $mail->Subject = $subject;
            
            // Cuerpo del mensaje
            $email_message = "Nueva consulta de propiedad:<br><br>";
            $email_message .= "Nombre: $name<br>";
            $email_message .= "Email: $email<br>";
            $email_message .= "Teléfono: $phone<br>";
            $email_message .= "Mensaje: $message<br>";
            
            if ($property_id) {
                $email_message .= "<br>ID de Propiedad: $property_id<br>";
            }
            
            $mail->isHTML(true);
            $mail->Body = $email_message;
            
            if($mail->send()) {
                writeLog("Correo enviado correctamente a $to");
                
                // Enviar confirmación al cliente
                $mail->clearAddresses();
                $mail->addAddress($email);
                $mail->Subject = "Recibimos tu consulta - Inmobiliaria";
                
                $client_message = "Hola $name,<br><br>";
                $client_message .= "Hemos recibido tu consulta y nos pondremos en contacto contigo pronto.<br><br>";
                $client_message .= "Detalles de tu consulta:<br>";
                $client_message .= "Asunto: $subject<br>";
                $client_message .= "Mensaje: $message<br><br>";
                $client_message .= "Saludos cordiales,<br>";
                $client_message .= "Equipo Inmobiliario";
                
                $mail->Body = $client_message;
                
                if($mail->send()) {
                    writeLog("Correo de confirmación enviado al cliente: $email");
                } else {
                    writeLog("Error al enviar correo de confirmación al cliente: $email");
                }
            } else {
                writeLog("Error al enviar el correo: " . $mail->ErrorInfo);
            }
            
            $_SESSION['success'] = "Tu consulta ha sido enviada correctamente. Nos pondremos en contacto contigo pronto.";
            
        } catch (Exception $e) {
            writeLog("Error en el envío de correo: " . $e->getMessage());
            // No mostrar el error al usuario, solo registrarlo
        }
    } else {
        throw new Exception("Error al insertar la consulta");
    }
    
    writeLog("Proceso completado exitosamente");
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
    
} catch (Exception $e) {
    writeLog("Error: " . $e->getMessage());
    $_SESSION['error'] = "Error al enviar la consulta: " . $e->getMessage();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
?>

