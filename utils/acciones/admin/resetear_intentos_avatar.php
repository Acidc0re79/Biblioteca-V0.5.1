<?php
// Se asume que el form-handler ya ha verificado que el usuario es admin/mod.

if (!isset($_POST['id_usuario']) || empty($_POST['id_usuario'])) {
    $_SESSION['error_message'] = "No se proporcionó un ID de usuario para resetear los intentos.";
    header('Location: ' . BASE_URL . 'admin/gestion/usuarios.php');
    exit;
}

$id_usuario_a_resetear = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);

try {
    // Ponemos el contador de intentos de avatar a 0 para el usuario especificado.
    $stmt = $pdo->prepare("UPDATE usuarios SET intentos_avatar = 0 WHERE id_usuario = ?");
    
    if ($stmt->execute([$id_usuario_a_resetear])) {
        log_system_event("Intentos de avatar reseteados desde el panel.", [
            'admin_id' => $_SESSION['user_id'],
            'usuario_afectado_id' => $id_usuario_a_resetear
        ]);
        $_SESSION['success_message'] = "Los intentos de generación de avatares para el usuario han sido reseteados a 0.";
    } else {
        $_SESSION['error_message'] = "No se pudieron resetear los intentos del usuario.";
    }

} catch (PDOException $e) {
    log_system_event("Excepción de BD al resetear intentos de avatar.", ['error_message' => $e->getMessage()]);
    $_SESSION['error_message'] = "Error de base de datos.";
}

// Redirigimos de vuelta a la lista de usuarios, manteniendo los filtros activos.
$redirect_url = BASE_URL . 'admin/gestion/usuarios.php?' . http_build_query($_GET);
header('Location: ' . $redirect_url);
exit;