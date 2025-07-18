<?php
// Se asume que el form-handler ya ha verificado los permisos de admin/mod.

if (!isset($_POST['id_usuario']) || !($id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT))) {
    $_SESSION['error_message'] = "No se proporcionó un ID de usuario para aplicar la restricción.";
    header('Location: ' . BASE_URL . 'admin/gestion/usuarios.php');
    exit;
}

try {
    // Obtenemos el valor máximo de intentos desde la configuración general del sitio.
    // CONFIG_SITIO ya está disponible gracias a init.php.
    $max_intentos = (int)(CONFIG_SITIO['intentos_avatar_iniciales'] ?? 50);

    // Actualizamos los intentos del usuario a este valor máximo.
    $stmt = $pdo->prepare("UPDATE usuarios SET intentos_avatar = ? WHERE id_usuario = ?");
    
    if ($stmt->execute([$max_intentos, $id_usuario])) {
        log_system_event("Restricción de avatares aplicada a usuario.", [
            'admin_id' => $_SESSION['user_id'],
            'usuario_sancionado_id' => $id_usuario,
            'intentos_establecidos' => $max_intentos
        ]);
        $_SESSION['success_message'] = "Se ha restringido la generación de avatares para el usuario.";
    } else {
        $_SESSION['error_message'] = "No se pudo aplicar la restricción.";
    }

} catch (PDOException $e) {
    log_system_event("Excepción de BD al restringir avatares.", ['error_message' => $e->getMessage()]);
    $_SESSION['error_message'] = "Error de base de datos.";
}

// Redirigimos de vuelta a la página de edición del usuario.
header('Location: ' . BASE_URL . 'admin/gestion/editar_usuario.php?id=' . $id_usuario);
exit;