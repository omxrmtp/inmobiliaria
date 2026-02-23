<?php
/**
 * API Endpoint: Autenticación
 * Maneja login y generación de tokens
 */

require_once __DIR__ . '/../config/config_api.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'POST':
        $ruta = $_GET['accion'] ?? 'login';
        
        if ($ruta === 'login') {
            login();
        } elseif ($ruta === 'refrescar') {
            refrescarToken();
        } elseif ($ruta === 'logout') {
            logout();
        } else {
            enviarError('Acción no válida', 404);
        }
        break;
        
    default:
        enviarError('Método no permitido', 405);
        break;
}

/**
 * Login de usuario
 */
function login() {
    global $conn;
    
    $datos = obtenerCuerpoPeticion();
    validarCamposRequeridos($datos, ['email', 'password']);
    
    $email = $datos['email'];
    $password = $datos['password'];
    
    try {
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows === 0) {
            enviarError('Credenciales inválidas', 401);
        }
        
        $usuario = $resultado->fetch_assoc();
        
        // Verificar contraseña
        // IMPORTANTE: En tu sistema actual las contraseñas no están hasheadas
        // Deberías implementar password_hash() y password_verify()
        if ($usuario['password'] !== $password) {
            // Para producción usar: password_verify($password, $usuario['password'])
            enviarError('Credenciales inválidas', 401);
        }
        
        // Generar token
        $token = generarToken($usuario['id'], $usuario['email'], $usuario['role']);
        
        // Guardar token en la base de datos
        $ip_origen = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $fecha_expiracion = date('Y-m-d H:i:s', time() + JWT_EXPIRATION_TIME);
        
        $stmt_token = $conn->prepare("
            INSERT INTO tokens_api (usuario_id, token, tipo, ip_origen, user_agent, fecha_expiracion) 
            VALUES (?, ?, 'acceso', ?, ?, ?)
        ");
        $stmt_token->bind_param("issss", $usuario['id'], $token, $ip_origen, $user_agent, $fecha_expiracion);
        $stmt_token->execute();
        
        enviarRespuesta(true, [
            'token' => $token,
            'tipo' => 'Bearer',
            'expira_en' => JWT_EXPIRATION_TIME,
            'usuario' => [
                'id' => $usuario['id'],
                'nombre' => $usuario['name'],
                'email' => $usuario['email'],
                'rol' => $usuario['role']
            ]
        ], 'Login exitoso', 200);
        
    } catch (Exception $e) {
        enviarError('Error en el servidor', 500, $e->getMessage());
    }
}

/**
 * Refrescar token
 */
function refrescarToken() {
    $payload = verificarAutenticacion();
    
    // Generar nuevo token
    $nuevo_token = generarToken($payload['sub'], $payload['email'], $payload['rol']);
    
    enviarRespuesta(true, [
        'token' => $nuevo_token,
        'tipo' => 'Bearer',
        'expira_en' => JWT_EXPIRATION_TIME
    ], 'Token refrescado', 200);
}

/**
 * Logout (revocar token)
 */
function logout() {
    global $conn;
    
    $token = obtenerToken();
    
    if (!$token) {
        enviarError('Token no proporcionado', 400);
    }
    
    try {
        $stmt = $conn->prepare("UPDATE tokens_api SET revocado = 1 WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        
        enviarRespuesta(true, null, 'Logout exitoso', 200);
    } catch (Exception $e) {
        enviarError('Error al cerrar sesión', 500, $e->getMessage());
    }
}
