<?php
/**
 * Librería para generar y verificar JWT tokens en PHP
 * Compatible con la implementación del backend Express.js
 */

class JWT {
    private static $secret;
    private static $algorithm = 'HS256';

    /**
     * Inicializar con el secret key
     */
    public static function init($secret = null) {
        if ($secret === null) {
            // Usar el mismo secret que el backend Express.js
            $secret = getenv('JWT_SECRET') ?: 'tu_jwt_secret_super_seguro_aqui_2024';
            error_log("JWT Secret from env: " . ($secret === 'tu_jwt_secret_super_seguro_aqui_2024' ? 'USANDO FALLBACK' : 'OK'));
            error_log("JWT Secret length: " . strlen($secret));
        }
        self::$secret = $secret;
    }

    /**
     * Generar JWT token
     * @param array $payload Datos a incluir en el token
     * @param int $expiresIn Segundos hasta expiración (default 24 horas)
     * @return string Token JWT
     */
    public static function encode($payload, $expiresIn = 86400) {
        self::init();

        // Agregar campos de expiración
        $payload['iat'] = time();
        $payload['exp'] = time() + $expiresIn;

        // Codificar header
        $header = [
            'typ' => 'JWT',
            'alg' => self::$algorithm
        ];

        $headerEncoded = self::base64urlEncode(json_encode($header));
        $payloadEncoded = self::base64urlEncode(json_encode($payload));

        // Crear firma
        $signatureInput = $headerEncoded . '.' . $payloadEncoded;
        $signature = hash_hmac('sha256', $signatureInput, self::$secret, true);
        $signatureEncoded = self::base64urlEncode($signature);

        return $signatureInput . '.' . $signatureEncoded;
    }

    /**
     * Verificar JWT token
     * @param string $token Token JWT a verificar
     * @return array|false Array con payload si es válido, false si no
     */
    public static function decode($token) {
        self::init();

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;

        // Verificar firma
        $signatureInput = $headerEncoded . '.' . $payloadEncoded;
        $signature = hash_hmac('sha256', $signatureInput, self::$secret, true);
        $expectedSignature = self::base64urlEncode($signature);

        if ($signatureEncoded !== $expectedSignature) {
            return false;
        }

        // Decodificar payload
        $payload = json_decode(self::base64urlDecode($payloadEncoded), true);

        // Verificar expiración
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }

        return $payload;
    }

    /**
     * Verificar si un token es válido
     * @param string $token Token JWT
     * @return bool
     */
    public static function isValid($token) {
        return self::decode($token) !== false;
    }

    /**
     * Base64 URL-safe encoding
     */
    private static function base64urlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL-safe decoding
     */
    private static function base64urlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 4 - strlen($data) % 4));
    }
}

// Inicializar JWT
JWT::init();
?>
