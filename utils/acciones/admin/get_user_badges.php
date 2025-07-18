<?php
// Archivo COMPLETO Y CORREGIDO: /utils/acciones/admin/get_user_badges.php

// Se asume que ajax-handler.php ha verificado los permisos de admin/mod.

if (!isset($_GET['id_usuario']) || !($id_usuario = filter_input(INPUT_GET, 'id_usuario', FILTER_VALIDATE_INT))) {
    echo json_encode(['success' => false, 'message' => 'ID de usuario no proporcionado.']);
    exit;
}

try {
    // 1. Obtenemos TODAS las insignias, AÑADIENDO LA COLUMNA `imagen`
    $stmt_todas = $pdo->query("SELECT id_insignia, nombre, imagen FROM insignias ORDER BY nombre ASC");
    $todas_las_insignias = $stmt_todas->fetchAll(PDO::FETCH_ASSOC);

    // 2. Obtenemos los IDs de las insignias que el usuario YA TIENE
    $stmt_usuario = $pdo->prepare("SELECT id_insignia FROM usuarios_insignias WHERE id_usuario = ?");
    $stmt_usuario->execute([$id_usuario]);
    $insignias_del_usuario = $stmt_usuario->fetchAll(PDO::FETCH_COLUMN);

    // 3. Combinamos los datos, construyendo la URL de la imagen
    $resultado = [];
    foreach ($todas_las_insignias as $insignia) {
        $resultado[] = [
            'id_insignia'    => $insignia['id_insignia'],
            'nombre'         => $insignia['nombre'],
            'imagen_thumb'   => BASE_URL . 'assets/img/insignias/thumbs/' . $insignia['imagen'], // <-- RUTA COMPLETA
            'tiene_insignia' => in_array($insignia['id_insignia'], $insignias_del_usuario)
        ];
    }

    echo json_encode(['success' => true, 'insignias' => $resultado]);

} catch (PDOException $e) {
    log_system_event("Error de BD al obtener insignias de usuario para admin.", ['error' => $e->getMessage()]);
    echo json_encode(['success' => false, 'message' => 'Error de base de datos.']);
}