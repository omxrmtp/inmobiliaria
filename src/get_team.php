<?php

// Establecer cabeceras para JSON
header('Content-Type: application/json; charset=utf-8');

// Incluir configuración
if (!defined('DB_HOST')) {
    require_once __DIR__ . '/../config/database.php';
}

// Helper para formatear estructura de equipo
function formatTeamArray($arr) {
    return array_map(function($member) {
        // Permitir tanto claves del CRM como de la BD local
        return [
            'id' => $member['id'] ?? null,
            'name' => $member['name'] ?? ($member['nombre'] ?? ''),
            'position' => $member['position'] ?? ($member['cargo'] ?? ''),
            'description' => $member['description'] ?? ($member['descripcion'] ?? ''),
            'phone' => $member['phone'] ?? ($member['telefono'] ?? ''),
            'email' => $member['email'] ?? ($member['correo'] ?? ''),
            'photo' => $member['photo'] ?? ($member['foto'] ?? null),
            'whatsapp' => $member['whatsapp_url'] ?? ($member['whatsapp'] ?? null),
            'facebook' => $member['facebook_url'] ?? ($member['facebook'] ?? null),
            'instagram' => $member['instagram_url'] ?? ($member['instagram'] ?? null),
            'tiktok' => $member['tiktok_url'] ?? ($member['tiktok'] ?? null),
            'linkedin' => $member['linkedin_url'] ?? ($member['linkedin'] ?? null),
            'youtube' => $member['youtube_url'] ?? ($member['youtube'] ?? null),
            'active' => $member['active'] ?? ($member['activo'] ?? 1),
            'display_order' => $member['display_order'] ?? ($member['orden_mostrar'] ?? 0),
        ];
    }, $arr);
}

try {
    // 1) Intentar obtener desde el backend del CRM
    // Usar host.docker.internal para acceder a servicios en la máquina host desde Docker
    $crmApiBase = 'http://host.docker.internal:5000/api';
    $crmUrl = rtrim($crmApiBase, '/') . '/public/team';

    $ch = curl_init($crmUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    error_log("CRM API Response: HTTP $httpCode, URL: $crmUrl, Response: " . substr($response, 0, 200) . ", Error: $curlErr");

    if ($response && $httpCode >= 200 && $httpCode < 300) {
        $data = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($data) && is_array($data)) {
            $items = $data;
            if (isset($data['success']) && $data['success'] === true) {
                $items = $data['data'] ?? [];
            }
            echo json_encode(['success' => true, 'team' => formatTeamArray($items)]);
            exit;
        }
    }

    // 2) Fallback: intentar obtener datos de la BD local de PGDP (manejar esquemas antiguos)
    $conn = connectDB();
    $team = [];
    if (!$conn) {
        echo json_encode(['success' => true, 'team' => []]);
        exit;
    }
    try {
        // Intentar consulta con 'activo'
        $stmt = $conn->query("SELECT id, nombre, cargo, descripcion, correo, telefono, foto, facebook, instagram, tiktok, activo, orden_mostrar FROM miembros_equipo WHERE activo = 1 ORDER BY orden_mostrar ASC, nombre ASC");
        $team = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $t) {
        // Si falla (columna 'activo' inexistente), probar sin filtro 'activo'
        try {
            $stmt = $conn->query("SELECT id, nombre, cargo, descripcion, correo, telefono, foto, facebook, instagram, tiktok, orden_mostrar FROM miembros_equipo ORDER BY orden_mostrar ASC, nombre ASC");
            $team = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $t2) {
            $team = [];
        }
    }

    $teamFormatted = $team ? formatTeamArray($team) : [];
    echo json_encode(['success' => true, 'team' => $teamFormatted]);
} catch (Exception $e) {
    // Devolver error
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>