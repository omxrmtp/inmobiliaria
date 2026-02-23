<?php
/**
 * Configuración de la API REST
 * Este archivo contiene las configuraciones para el sistema de API
 */

// Configuración de CORS
header('Access-Control-Allow-Origin: *'); // En producción, especifica el dominio del CRM
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json; charset=utf-8');

// Manejar peticiones OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir configuración de base de datos
require_once __DIR__ . '/../../config/database.php';

// Configuración JWT
define('JWT_SECRET_KEY', 'TU_CLAVE_SECRETA_AQUI_CAMBIAR_EN_PRODUCCION'); // CAMBIAR EN PRODUCCIÓN
define('JWT_ALGORITHM', 'HS256');
define('JWT_EXPIRATION_TIME', 3600); // 1 hora en segundos
define('JWT_REFRESH_EXPIRATION_TIME', 604800); // 7 días en segundos

// Configuración de la API
define('API_VERSION', 'v1');
define('API_DEBUG_MODE', true); // Cambiar a false en producción

/**
 * Función para enviar respuesta JSON
 */
function enviarRespuesta($exito, $datos = null, $mensaje = '', $codigo_http = 200) {
    http_response_code($codigo_http);
    
    $respuesta = [
        'exito' => $exito,
        'mensaje' => $mensaje,
        'datos' => $datos,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Función para manejar errores
 */
function enviarError($mensaje, $codigo_http = 400, $detalles = null) {
    $respuesta = [
        'exito' => false,
        'mensaje' => $mensaje,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    if (API_DEBUG_MODE && $detalles) {
        $respuesta['detalles'] = $detalles;
    }
    
    http_response_code($codigo_http);
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Función para obtener el token del header
 */
function obtenerToken() {
    $headers = getallheaders();
    
    if (isset($headers['Authorization'])) {
        $matches = [];
        if (preg_match('/Bearer\s+(.*)$/i', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
    }
    
    return null;
}

/**
 * Función para validar el token JWT
 */
function validarToken($token) {
    try {
        // Aquí implementaremos la validación JWT
        // Por ahora retornamos estructura básica
        $partes = explode('.', $token);
        
        if (count($partes) !== 3) {
            return false;
        }
        
        // Decodificar payload
        $payload = json_decode(base64_decode($partes[1]), true);
        
        // Verificar expiración
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }
        
        return $payload;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Función para generar token JWT
 */
function generarToken($usuario_id, $email, $rol = 'user') {
    $header = [
        'typ' => 'JWT',
        'alg' => JWT_ALGORITHM
    ];
    
    $payload = [
        'iss' => 'inmobiliaria-api',
        'sub' => $usuario_id,
        'email' => $email,
        'rol' => $rol,
        'iat' => time(),
        'exp' => time() + JWT_EXPIRATION_TIME
    ];
    
    $header_encoded = base64_encode(json_encode($header));
    $payload_encoded = base64_encode(json_encode($payload));
    
    $signature = hash_hmac('sha256', "$header_encoded.$payload_encoded", JWT_SECRET_KEY, true);
    $signature_encoded = base64_encode($signature);
    
    return "$header_encoded.$payload_encoded.$signature_encoded";
}

/**
 * Middleware para verificar autenticación
 */
function verificarAutenticacion() {
    $token = obtenerToken();
    
    if (!$token) {
        enviarError('Token no proporcionado', 401);
    }
    
    $payload = validarToken($token);
    
    if (!$payload) {
        enviarError('Token inválido o expirado', 401);
    }
    
    return $payload;
}

/**
 * Middleware para verificar rol de administrador
 */
function verificarAdmin() {
    $payload = verificarAutenticacion();
    
    if ($payload['rol'] !== 'admin') {
        enviarError('No tienes permisos de administrador', 403);
    }
    
    return $payload;
}

/**
 * Función para registrar actividad
 */
function registrarActividad($conn, $cliente_id, $tipo_actividad, $descripcion, $realizado_por = null, $metadatos = null) {
    try {
        $stmt = $conn->prepare("
            INSERT INTO registro_actividad_cliente 
            (cliente_id, tipo_actividad, descripcion, realizado_por, metadatos, fecha_creacion) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        $metadatos_json = $metadatos ? json_encode($metadatos) : null;
        $stmt->bind_param("issss", $cliente_id, $tipo_actividad, $descripcion, $realizado_por, $metadatos_json);
        
        return $stmt->execute();
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Función para obtener el cuerpo de la petición
 */
function obtenerCuerpoPeticion() {
    $cuerpo = file_get_contents('php://input');
    return json_decode($cuerpo, true);
}

/**
 * Función para validar campos requeridos
 */
function validarCamposRequeridos($datos, $campos_requeridos) {
    $campos_faltantes = [];
    
    foreach ($campos_requeridos as $campo) {
        if (!isset($datos[$campo]) || empty($datos[$campo])) {
            $campos_faltantes[] = $campo;
        }
    }
    
    if (!empty($campos_faltantes)) {
        enviarError(
            'Campos requeridos faltantes', 
            400, 
            ['campos_faltantes' => $campos_faltantes]
        );
    }
}
