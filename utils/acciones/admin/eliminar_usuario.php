<?php
// Se asume que el form-handler ha verificado los permisos de administrador.

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $_SESSION['rango'] !== 'administrador') {
    header('Location: ' . BASE_URL);
    exit;
}

$id_usuario_a_eliminar = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);

if (!$id_usuario_a_eliminar) {
    $_SESSION['error_message'] = "ID de usuario no válido.";
    header('Location: ' . BASE_URL . 'admin/gestion/usuarios.php');
    exit;
}

if ($id_usuario_a_eliminar === (int)$_SESSION['user_id']) {
    $_SESSION['error_message'] = "No puedes eliminar tu propia cuenta desde el panel.";
    header('Location: ' . BASE_URL . 'admin/gestion/usuarios.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Encontrar y eliminar archivos de avatares generados
    $stmt_avatares = $pdo->prepare("SELECT nombre_archivo FROM usuarios_avatares WHERE id_usuario = ?");
    $stmt_avatares->execute([$id_usuario_a_eliminar]);
    $avatares = $stmt_avatares->fetchAll(PDO::FETCH_ASSOC);

    foreach ($avatares as $avatar) {
        $ruta_completa = ROOT_PATH . '/public/assets/img/avatars/users/' . $avatar['nombre_archivo'];
        $ruta_thumbnail = ROOT_PATH . '/public/assets/img/avatars/thumbs/users/' . $avatar['nombre_archivo'];
        if (file_exists($ruta_completa)) unlink($ruta_completa);
        if (file_exists($ruta_thumbnail)) unlink($ruta_thumbnail);
    }

    // 2. Eliminar registros de la tabla usuarios_avatares
    $stmt_del_avatares = $pdo->prepare("DELETE FROM usuarios_avatares WHERE id_usuario = ?");
    $stmt_del_avatares->execute([$id_usuario_a_eliminar]);

    // 3. Eliminar registros de la tabla usuarios_insignias
    $stmt_del_insignias = $pdo->prepare("DELETE FROM usuarios_insignias WHERE id_usuario = ?");
    $stmt_del_insignias->execute([$id_usuario_a_eliminar]);

    // 4. Finalmente, eliminar el usuario de la tabla principal
    $stmt_del_usuario = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt_del_usuario->execute([$id_usuario_a_eliminar]);

    $pdo->commit();
    
    log_system_event("Usuario eliminado permanentemente desde el panel (con todos sus datos).", [
        'admin_id' => $_SESSION['user_id'],
        'usuario_eliminado_id' => $id_usuario_a_eliminar
    ]);
    $_SESSION['success_message'] = "Usuario y todos sus datos asociados han sido eliminados correctamente.";

} catch (PDOException $e) {
    $pdo->rollBack();
    log_system_event("Excepción de BD al realizar eliminación completa de usuario.", ['error_message' => $e->getMessage()]);
    $_SESSION['error_message'] = "Error de base de datos durante la eliminación.";
}

header('Location: ' . BASE_URL . 'admin/gestion/usuarios.php');
exit;