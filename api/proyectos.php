<?php
/**
 * API para listar proyectos/propiedades
 * Consumida por la página web pública
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/cors.php';

try {
    // Obtener parámetros de query
    $estado = $_GET['estado'] ?? null;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $search = $_GET['search'] ?? null;

    // 1) Intentar obtener desde el backend del CRM
    $crmApiBase = getenv('CRM_API_BASE') ?: 'http://backend:5000/api';
    $crmUrl = rtrim($crmApiBase, '/') . '/public/projects';
    $q = [];
    if ($estado) $q['estado'] = $estado;
    if ($limit) $q['limit'] = $limit;
    if ($offset) $q['offset'] = $offset;
    if ($search) $q['search'] = $search;
    if (!empty($q)) {
        $crmUrl .= '?' . http_build_query($q);
    }

    $ch = curl_init($crmUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response && $httpCode >= 200 && $httpCode < 300) {
        $data = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($data['success']) && $data['success'] === true) {
            echo json_encode($data);
            exit;
        }
    }

    // 2) Fallback: consulta MySQL local actual
    $sql = "SELECT 
                p.id,
                p.nombre,
                p.descripcion,
                p.imagen,
                p.precio,
                p.ubicacion,
                p.estado,
                p.creado_en,
                GROUP_CONCAT(DISTINCT e.nombre) as etiquetas,
                GROUP_CONCAT(DISTINCT e.color) as colores_etiquetas
            FROM proyectos p
            LEFT JOIN etiquetas_proyectos ep ON p.id = ep.id_proyecto
            LEFT JOIN etiquetas e ON ep.id_etiqueta = e.id
            WHERE p.activo = 1 AND p.mostrar_en_web = 1";

    $params = [];
    if ($estado) { $sql .= " AND p.estado = :estado"; $params['estado'] = $estado; }
    if ($search) { $sql .= " AND (p.nombre LIKE :search OR p.descripcion LIKE :search OR p.ubicacion LIKE :search)"; $params['search'] = "%$search%"; }
    $sql .= " GROUP BY p.id ORDER BY p.creado_en DESC LIMIT :limit OFFSET :offset";

    $pdo = getDBConnection();
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) { $stmt->bindValue(":$key", $value); }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $proyectos = $stmt->fetchAll();

    foreach ($proyectos as &$proyecto) {
        $proyecto['etiquetas'] = $proyecto['etiquetas'] ? explode(',', $proyecto['etiquetas']) : [];
        $proyecto['colores_etiquetas'] = $proyecto['colores_etiquetas'] ? explode(',', $proyecto['colores_etiquetas']) : [];
    }

    $sqlCount = "SELECT COUNT(*) as total FROM proyectos WHERE activo = 1 AND mostrar_en_web = 1";
    $countParams = [];
    if ($estado) { $sqlCount .= " AND estado = :estado"; $countParams['estado'] = $estado; }
    if ($search) { $sqlCount .= " AND (nombre LIKE :search OR descripcion LIKE :search OR ubicacion LIKE :search)"; $countParams['search'] = "%$search%"; }
    $total = fetchOne($sqlCount, $countParams)['total'];

    echo json_encode([
        'success' => true,
        'data' => $proyectos,
        'meta' => [
            'total' => (int)$total,
            'limit' => $limit,
            'offset' => $offset,
            'count' => count($proyectos)
        ]
    ]);

} catch (Exception $e) {
    error_log("Error en API proyectos: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener proyectos'
    ]);
}
?>
