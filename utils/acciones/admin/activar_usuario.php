<?php
// Se asume que el form-handler ya ha verificado que el usuario es admin/mod.

if (!isset($_POST['id_usuario']) || empty($_POST['id_usuario'])) {
    $_SESSION['error_message'] = "No se proporcionó un ID de usuario para activar.";
    header('Location: ' . BASE_URL . 'admin/gestion/usuarios.php');
    exit;
}

$id_usuario_a_activar = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);

try {
    $stmt = $pdo->prepare("UPDATE usuarios SET estado_cuenta = 'activo' WHERE id_usuario = ? AND estado_cuenta = 'pendiente'");
    
    if ($stmt->execute([$id_usuario_a_activar])) {
        log_system_event("Cuenta de usuario activada desde el panel.", [
            'admin_id' => $_SESSION['user_id'],
            'usuario_activado_id' => $id_usuario_a_activar
        ]);
        $_SESSION['success_message'] = "Usuario activado correctamente.";
    } else {
        $_SESSION['error_message'] = "No se pudo activar el usuario.";
    }

} catch (PDOException $e) {
    log_system_event("Excepción de BD al activar usuario.", ['error_message' => $e->getMessage()]);
    $_SESSION['error_message'] = "Error de base de datos.";
}

header('Location: ' . BASE_URL . 'admin/gestion/usuarios.php');
exit;