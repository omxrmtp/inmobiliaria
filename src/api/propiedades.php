<?php
/**
 * API Endpoint: Propiedades
 * CRUD completo de propiedades para el CRM
 */

require_once __DIR__ . '/../config/config_api.php';

$metodo = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

switch ($metodo) {
    case 'GET':
        if ($id) {
            obtenerPropiedad($id);
        } else {
            listarPropiedades();
        }
        break;
        
    case 'POST':
        crearPropiedad();
        break;
        
    case 'PUT':
        if (!$id) {
            enviarError('ID de propiedad requerido', 400);
        }
        actualizarPropiedad($id);
        break;
        
    case 'DELETE':
        if (!$id) {
            enviarError('ID de propiedad requerido', 400);
        }
        eliminarPropiedad($id);
        break;
        
    default:
        enviarError('Método no permitido', 405);
        break;
}

/**
 * Listar todas las propiedades
 */
function listarPropiedades() {
    global $conn;
    
    // Parámetros de consulta
    $estado = $_GET['estado'] ?? null;
    $tipo = $_GET['tipo'] ?? null;
    $ciudad = $_GET['ciudad'] ?? null;
    $destacada = $_GET['destacada'] ?? null;
    $limite = $_GET['limite'] ?? 50;
    $pagina = $_GET['pagina'] ?? 1;
    $offset = ($pagina - 1) * $limite;
    
    try {
        // Construir consulta
        $sql = "SELECT p.*, 
                (SELECT COUNT(*) FROM property_images WHERE property_id = p.id) as total_imagenes,
                (SELECT image_path FROM property_images WHERE property_id = p.id AND is_main = 1 LIMIT 1) as imagen_principal
                FROM properties p WHERE 1=1";
        
        $params = [];
        $types = "";
        
        if ($estado) {
            $sql .= " AND p.status = ?";
            $params[] = $estado;
            $types .= "s";
        }
        
        if ($tipo) {
            $sql .= " AND p.property_type = ?";
            $params[] = $tipo;
            $types .= "s";
        }
        
        if ($ciudad) {
            $sql .= " AND p.city LIKE ?";
            $params[] = "%$ciudad%";
            $types .= "s";
        }
        
        if ($destacada !== null) {
            $sql .= " AND p.is_featured = ?";
            $params[] = $destacada;
            $types .= "i";
        }
        
        // Contar total
        $sql_count = str_replace("SELECT p.*, (SELECT COUNT(*) FROM property_images WHERE property_id = p.id) as total_imagenes, (SELECT image_path FROM property_images WHERE property_id = p.id AND is_main = 1 LIMIT 1) as imagen_principal", "SELECT COUNT(*) as total", $sql);
        
        if (!empty($params)) {
            $stmt_count = $conn->prepare($sql_count);
            $stmt_count->bind_param($types, ...$params);
            $stmt_count->execute();
            $resultado_count = $stmt_count->get_result();
            $total_registros = $resultado_count->fetch_assoc()['total'];
        } else {
            $resultado_count = $conn->query($sql_count);
            $total_registros = $resultado_count->fetch_assoc()['total'];
        }
        
        // Obtener registros paginados
        $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limite;
        $params[] = $offset;
        $types .= "ii";
        
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $propiedades = [];
        while ($row = $resultado->fetch_assoc()) {
            $propiedades[] = $row;
        }
        
        enviarRespuesta(true, [
            'propiedades' => $propiedades,
            'paginacion' => [
                'total_registros' => $total_registros,
                'pagina_actual' => $pagina,
                'registros_por_pagina' => $limite,
                'total_paginas' => ceil($total_registros / $limite)
            ]
        ], 'Propiedades obtenidas exitosamente', 200);
        
    } catch (Exception $e) {
        enviarError('Error al obtener propiedades', 500, $e->getMessage());
    }
}

/**
 * Obtener una propiedad específica
 */
function obtenerPropiedad($id) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            SELECT p.*, 
            u.name as creado_por_nombre,
            u2.name as actualizado_por_nombre
            FROM properties p
            LEFT JOIN users u ON p.creado_por = u.id
            LEFT JOIN users u2 ON p.actualizado_por = u2.id
            WHERE p.id = ?
        ");
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows === 0) {
            enviarError('Propiedad no encontrada', 404);
        }
        
        $propiedad = $resultado->fetch_assoc();
        
        // Obtener imágenes
        $stmt_images = $conn->prepare("SELECT * FROM property_images WHERE property_id = ? ORDER BY is_main DESC");
        $stmt_images->bind_param("i", $id);
        $stmt_images->execute();
        $resultado_images = $stmt_images->get_result();
        
        $imagenes = [];
        while ($row = $resultado_images->fetch_assoc()) {
            $imagenes[] = $row;
        }
        
        $propiedad['imagenes'] = $imagenes;
        
        enviarRespuesta(true, $propiedad, 'Propiedad obtenida exitosamente', 200);
        
    } catch (Exception $e) {
        enviarError('Error al obtener propiedad', 500, $e->getMessage());
    }
}

/**
 * Crear nueva propiedad
 */
function crearPropiedad() {
    $payload = verificarAdmin();
    global $conn;
    
    $datos = obtenerCuerpoPeticion();
    
    // Campos requeridos
    validarCamposRequeridos($datos, [
        'title', 'description', 'price', 'address', 
        'property_type', 'area'
    ]);
    
    try {
        $conn->begin_transaction();
        
        $stmt = $conn->prepare("
            INSERT INTO properties 
            (title, description, price, address, city, bedrooms, bathrooms, 
            area, featured, status, property_type, map_url, video_url, tag, 
            creado_por, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $bedrooms = $datos['bedrooms'] ?? 0;
        $bathrooms = $datos['bathrooms'] ?? 0;
        $city = $datos['city'] ?? '';
        $featured = $datos['featured'] ?? 0;
        $status = $datos['status'] ?? 'available';
        $map_url = $datos['map_url'] ?? null;
        $video_url = $datos['video_url'] ?? null;
        $tag = $datos['tag'] ?? null;
        
        $stmt->bind_param(
            "ssdssiisisssssi",
            $datos['title'],
            $datos['description'],
            $datos['price'],
            $datos['address'],
            $city,
            $bedrooms,
            $bathrooms,
            $datos['area'],
            $featured,
            $status,
            $datos['property_type'],
            $map_url,
            $video_url,
            $tag,
            $payload['sub']
        );
        
        $stmt->execute();
        $propiedad_id = $conn->insert_id;
        
        // Insertar imágenes si existen
        if (isset($datos['imagenes']) && is_array($datos['imagenes'])) {
            $stmt_img = $conn->prepare("
                INSERT INTO property_images (property_id, image_path, is_main, created_at) 
                VALUES (?, ?, ?, NOW())
            ");
            
            foreach ($datos['imagenes'] as $index => $imagen) {
                $is_main = $index === 0 ? 1 : 0;
                $stmt_img->bind_param("isi", $propiedad_id, $imagen, $is_main);
                $stmt_img->execute();
            }
        }
        
        $conn->commit();
        
        enviarRespuesta(true, [
            'id' => $propiedad_id,
            'titulo' => $datos['title']
        ], 'Propiedad creada exitosamente', 201);
        
    } catch (Exception $e) {
        $conn->rollback();
        enviarError('Error al crear propiedad', 500, $e->getMessage());
    }
}

/**
 * Actualizar propiedad existente
 */
function actualizarPropiedad($id) {
    $payload = verificarAdmin();
    global $conn;
    
    $datos = obtenerCuerpoPeticion();
    
    try {
        $conn->begin_transaction();
        
        // Verificar que la propiedad existe
        $stmt_check = $conn->prepare("SELECT id FROM properties WHERE id = ?");
        $stmt_check->bind_param("i", $id);
        $stmt_check->execute();
        
        if ($stmt_check->get_result()->num_rows === 0) {
            enviarError('Propiedad no encontrada', 404);
        }
        
        // Construir actualización dinámica
        $campos_actualizar = [];
        $valores = [];
        $types = "";
        
        $campos_permitidos = [
            'title' => 's', 'description' => 's', 'price' => 'd', 'address' => 's',
            'city' => 's', 'bedrooms' => 'i', 'bathrooms' => 'i', 'area' => 'd',
            'featured' => 'i', 'status' => 's', 'property_type' => 's',
            'map_url' => 's', 'video_url' => 's', 'tag' => 's'
        ];
        
        foreach ($campos_permitidos as $campo => $tipo) {
            if (isset($datos[$campo])) {
                $campos_actualizar[] = "$campo = ?";
                $valores[] = $datos[$campo];
                $types .= $tipo;
            }
        }
        
        if (empty($campos_actualizar)) {
            enviarError('No hay campos para actualizar', 400);
        }
        
        // Agregar campos de auditoría
        $campos_actualizar[] = "actualizado_por = ?";
        $campos_actualizar[] = "updated_at = NOW()";
        $valores[] = $payload['sub'];
        $types .= "i";
        
        $sql = "UPDATE properties SET " . implode(", ", $campos_actualizar) . " WHERE id = ?";
        $valores[] = $id;
        $types .= "i";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$valores);
        $stmt->execute();
        
        // Actualizar imágenes si se proporcionan
        if (isset($datos['imagenes']) && is_array($datos['imagenes'])) {
            // Eliminar imágenes existentes
            $stmt_del = $conn->prepare("DELETE FROM property_images WHERE property_id = ?");
            $stmt_del->bind_param("i", $id);
            $stmt_del->execute();
            
            // Insertar nuevas imágenes
            $stmt_img = $conn->prepare("
                INSERT INTO property_images (property_id, image_path, is_main, created_at) 
                VALUES (?, ?, ?, NOW())
            ");
            
            foreach ($datos['imagenes'] as $index => $imagen) {
                $is_main = $index === 0 ? 1 : 0;
                $stmt_img->bind_param("isi", $id, $imagen, $is_main);
                $stmt_img->execute();
            }
        }
        
        $conn->commit();
        
        enviarRespuesta(true, ['id' => $id], 'Propiedad actualizada exitosamente', 200);
        
    } catch (Exception $e) {
        $conn->rollback();
        enviarError('Error al actualizar propiedad', 500, $e->getMessage());
    }
}

/**
 * Eliminar propiedad
 */
function eliminarPropiedad($id) {
    $payload = verificarAdmin();
    global $conn;
    
    try {
        $stmt = $conn->prepare("DELETE FROM properties WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            enviarError('Propiedad no encontrada', 404);
        }
        
        enviarRespuesta(true, null, 'Propiedad eliminada exitosamente', 200);
        
    } catch (Exception $e) {
        enviarError('Error al eliminar propiedad', 500, $e->getMessage());
    }
}
