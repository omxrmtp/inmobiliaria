<?php
/**
 * API para guardar leads desde el formulario web
 * Guarda directamente en la base de datos
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/cors.php';

header('Content-Type: application/json');

try {
    // Solo aceptar POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Método no permitido']);
        exit;
    }
    
    // Leer datos JSON del body
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
        exit;
    }
    
    // Validar campos requeridos
    $required = ['nombre', 'apellido', 'email', 'telefono', 'interes'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => "Campo requerido: $field"]);
            exit;
        }
    }
    
    // Preparar datos
    $nombre = trim($data['nombre']);
    $apellido = trim($data['apellido']);
    $email = trim($data['email']);
    $telefono = trim($data['telefono']);
    $interes = trim($data['interes']); // Se guardará en etiquetas
    $origen = isset($data['origen']) ? trim($data['origen']) : 'Página Web';
    $mensaje = isset($data['mensaje']) ? trim($data['mensaje']) : '';
    
    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Email inválido']);
        exit;
    }
    
    // Generar UUID para el ID
    $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
    
    // Guardar en la base de datos
    $pdo = getDBConnection();
    
    // Obtener un ID de usuario válido (el primero disponible o crear uno por defecto)
    $stmtUser = $pdo->query("SELECT id FROM usuarios LIMIT 1");
    $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        // Si no hay usuarios, crear uno por defecto para el sistema (esquema real de la tabla usuarios)
        $defaultUserId = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
        
        $pdo->exec("
            INSERT INTO usuarios (
                id,
                nombre,
                correo,
                contrasena,
                rol,
                avatar,
                telefono,
                activo,
                creadoEn,
                actualizadoEn
            ) VALUES (
                '$defaultUserId',
                'Sistema',
                'sistema@delgadopropiedades.com',
                '',
                'ADMIN',
                NULL,
                NULL,
                1,
                NOW(3),
                NOW(3)
            )
        ");
        $idUsuario = $defaultUserId;
    } else {
        $idUsuario = $usuario['id'];
    }
    
    // Nota: La tabla usa 'fuente' para origen y 'etiquetas' para interes
    $sql = "INSERT INTO leads 
            (id, nombre, apellido, correo, telefono, fuente, etiquetas, notas, estado, prioridad, creado_en, actualizado_en, id_usuario) 
            VALUES 
            (:id, :nombre, :apellido, :correo, :telefono, :fuente, :etiquetas, :notas, 'NUEVO', 'MEDIA', NOW(), NOW(), :id_usuario)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $uuid,
        ':nombre' => $nombre,
        ':apellido' => $apellido,
        ':correo' => $email,
        ':telefono' => $telefono,
        ':fuente' => $origen,
        ':etiquetas' => $interes,
        ':notas' => $mensaje,
        ':id_usuario' => $idUsuario
    ]);
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'exito' => true,
        'mensaje' => 'Lead guardado correctamente',
        'data' => [
            'id' => $uuid,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'email' => $email
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Error en API leads: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al guardar el lead',
        'details' => $e->getMessage()
    ]);
}
