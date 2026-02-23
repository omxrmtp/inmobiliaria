<?php

// Establecer cabeceras para JSON
header('Content-Type: application/json');

try {
    // 1) Intentar obtener desde el backend del CRM
    $crmApiBase = 'http://host.docker.internal:5000/api';
    $crmUrl = rtrim($crmApiBase, '/') . '/public/testimonials';

    $ch = curl_init($crmUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response && $httpCode >= 200 && $httpCode < 300) {
        $data = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($data['success']) && $data['success'] === true) {
            $items = $data['data'] ?? [];
            echo json_encode(['success' => true, 'testimonials' => $items]);
            exit;
        }
    }

    // 2) Fallback a MySQL local (solo aprobados)
    $conn = connectDB();
    if (!$conn) {
        echo json_encode(['success' => true, 'testimonials' => []]);
        exit;
    }

    $stmt = $conn->query("SELECT id, nombre as name, calificacion as rating, testimonio as content, foto as photo, media, video_url, creado_en as created_at, aprobado_en as approved_at FROM testimonios WHERE estado = 'aprobado' ORDER BY aprobado_en DESC");
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($testimonials as &$t) {
        $t['photo'] = !empty($t['photo']) ? str_replace('\\\\', '/', $t['photo']) : '/placeholder.php?height=150&width=150';
        $t['date'] = isset($t['approved_at']) ? date('d/m/Y', strtotime($t['approved_at'])) : (isset($t['created_at']) ? date('d/m/Y', strtotime($t['created_at'])) : '');
    }

    echo json_encode(['success' => true, 'testimonials' => $testimonials]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>