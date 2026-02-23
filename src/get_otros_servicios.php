<?php
header('Content-Type: application/json');

try {
    // 1) Intentar obtener desde el backend del CRM
    $crmApiBase = 'http://host.docker.internal:5000/api';
    $crmUrl = rtrim($crmApiBase, '/') . '/public/otros-servicios';

    $ch = curl_init($crmUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response && $httpCode >= 200 && $httpCode < 300) {
        $data = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($data['success']) && $data['success'] === true) {
            // El JS espera un array directo
            echo json_encode($data['data'] ?? []);
            exit;
        }
    }

    // 2) Fallback a MySQL local
    if (!function_exists('connectDB')) {
        require_once __DIR__ . '/config/settings.php';
        require_once __DIR__ . '/config/database.php';
    }
    $conn = connectDB();
    if (!$conn) {
        echo json_encode([]);
        exit;
    }

    $stmt = $conn->query("SELECT id, titulo, descripcion, caracteristicas, imagenes, enlace_imagen, enlace_video, tipo_servicio, creado_en, actualizado_en FROM otros_servicios ORDER BY creado_en DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $services = array_map(function($r) {
        // Parsear JSON de caracteristicas e imagenes si existen
        $caracteristicas = !empty($r['caracteristicas']) ? json_decode($r['caracteristicas'], true) : [];
        $imagenes = !empty($r['imagenes']) ? json_decode($r['imagenes'], true) : [];
        
        return [
            'id' => $r['id'],
            'title' => $r['titulo'],
            'description' => $r['descripcion'],
            'caracteristicas' => $caracteristicas,
            'imagenes' => $imagenes,
            'image_link' => $r['enlace_imagen'],
            'video_link' => $r['enlace_video'],
            'service_type' => strtolower($r['tipo_servicio'] ?? 'general'),
            'created_at' => $r['creado_en'] ?? null,
            'updated_at' => $r['actualizado_en'] ?? null,
        ];
    }, $rows);

    echo json_encode($services);
} catch (Throwable $e) {
    echo json_encode([]);
}
