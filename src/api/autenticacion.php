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
 * Login de usuario (API) usando tabla `usuarios`
 */
function login() {
    $datos = obtenerCuerpoPeticion();
    validarCamposRequeridos($datos, ['email', 'password']);
    
    $email = $datos['email'];
    $password = $datos['password'];
    
    try {
        // Usar la conexión PDO definida en config/database.php
        $pdo = getDBConnection();
        
        // Buscar usuario interno en tabla `usuarios`
        $stmt = $pdo->prepare("
            SELECT 
                id,
                nombre,
                correo,
                contrasena,
                rol,
                activo
            FROM usuarios
            WHERE correo = :correo
            LIMIT 1
        ");
        $stmt->execute([':correo' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) {
            enviarError('Credenciales inválidas', 401);
        }
        
        // Verificar estado activo si la columna existe
        if (isset($usuario['activo']) && (int)$usuario['activo'] !== 1) {
            enviarError('Usuario inactivo. Contacta con el administrador.', 403);
        }
        
        // Verificar contraseña (hash o texto plano de transición)
        if (!(password_verify($password, $usuario['contrasena']) || $password === $usuario['contrasena'])) {
            enviarError('Credenciales inválidas', 401);
        }
        
        // Generar token
        $token = generarToken($usuario['id'], $usuario['correo'], $usuario['rol']);
        
        // Intentar guardar token en tabla tokens_api si existe (no romper si no está creada)
        try {
            $ip_origen = $_SERVER['REMOTE_ADDR'] ?? null;
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            $fecha_expiracion = date('Y-m-d H:i:s', time() + JWT_EXPIRATION_TIME);
            
            $stmtToken = $pdo->prepare("
                INSERT INTO tokens_api (usuario_id, token, tipo, ip_origen, user_agent, fecha_expiracion) 
                VALUES (:usuario_id, :token, 'acceso', :ip, :ua, :expira)
            ");
            $stmtToken->execute([
                ':usuario_id' => $usuario['id'],
                ':token'      => $token,
                ':ip'         => $ip_origen,
                ':ua'         => $user_agent,
                ':expira'     => $fecha_expiracion,
            ]);
        } catch (Exception $e) {
            // Si la tabla tokens_api no existe u otro error, solo logear y seguir
            error_log('Error guardando token API: ' . $e->getMessage());
        }
        
        enviarRespuesta(true, [
            'token' => $token,
            'tipo' => 'Bearer',
            'expira_en' => JWT_EXPIRATION_TIME,
            'usuario' => [
                'id' => $usuario['id'],
                'nombre' => $usuario['nombre'],
                'email' => $usuario['correo'],
                'rol' => $usuario['rol']
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
    $token = obtenerToken();
    
    if (!$token) {
        enviarError('Token no proporcionado', 400);
    }
    
    try {
        $pdo = getDBConnection();
        
        try {
            $stmt = $pdo->prepare("UPDATE tokens_api SET revocado = 1 WHERE token = :token");
            $stmt->execute([':token' => $token]);
        } catch (Exception $e) {
            // Si la tabla tokens_api no existe u otro problema, solo registrar
            error_log('Error actualizando token API en logout: ' . $e->getMessage());
        }
        
        enviarRespuesta(true, null, 'Logout exitoso', 200);
    } catch (Exception $e) {
        enviarError('Error al cerrar sesión', 500, $e->getMessage());
    }
}
