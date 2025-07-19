<?php
// Archivo REFACTORIZADO: /public/admin/views/editar_usuario.php

// 1. LÓGICA DE LA PÁGINA
// Obtenemos el ID del usuario desde la URL. El Front Controller ya nos lo pasa.
if (!isset($_GET['id']) || !($id_usuario = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT))) {
    // Si no hay ID, podríamos redirigir o mostrar un error.
    // Por ahora, mostramos un mensaje simple.
    $page_title = "Error";
    echo "<div class='admin-content'><p>Error: No se ha especificado un ID de usuario válido.</p></div>";
    return; // Detenemos la ejecución del resto del script.
}

// Obtenemos los datos del usuario a editar
try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $page_title = "Error de Base de Datos";
    echo "<div class='admin-content'><p>Error al obtener los datos del usuario: " . $e->getMessage() . "</p></div>";
    return;
}

// Si el usuario no existe, lo mismo.
if (!$usuario) {
    $page_title = "Error";
    echo "<div class='admin-content'><p>Error: Usuario no encontrado.</p></div>";
    return;
}

// Definimos el título de la página y la variable de control de rol.
$page_title = "Editando a " . htmlspecialchars($usuario['nickname'] ?? '');
$es_admin = $_SESSION['rango'] === 'administrador';

// 2. CONTENIDO HTML DE LA PÁGINA
?>
<div class="admin-content">
    <div class="content-header">
        <h2><i class="fas fa-user-edit"></i> <?= htmlspecialchars($page_title) ?></h2>
        <a href="index.php?page=usuarios" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver a la lista</a>
    </div>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>

    <div class="page-grid">
        <div>
            <form class="edit-form" action="<?= BASE_URL ?>form-handler.php" method="POST">
                <input type="hidden" name="action" value="actualizar_usuario_admin">
                <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
                
                <?php if ($es_admin): ?>
                <div class="form-section">
                    <h3><i class="fas fa-fingerprint"></i> Información Personal (Solo Admin)</h3>
                    <div class="form-grid">
                        <div class="form-group"><label for="nombre">Nombre</label><input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>"></div>
                        <div class="form-group"><label for="apellido">Apellido</label><input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($usuario['apellido'] ?? '') ?>"></div>
                        <div class="form-group"><label>Email</label><div class="readonly-field"><?= htmlspecialchars($usuario['email']) ?></div></div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="form-section">
                    <h3><i class="fas fa-user-shield"></i> Control de Cuenta</h3>
                    <div class="form-grid">
                        <div class="form-group"><label for="nickname">Nickname</label><input type="text" id="nickname" name="nickname" value="<?= htmlspecialchars($usuario['nickname'] ?? '') ?>"></div>
                        <div class="form-group"><label for="rango">Rango</label><select id="rango" name="rango"><option value="lector" <?= $usuario['rango'] == 'lector' ? 'selected' : '' ?>>Lector</option><option value="moderador" <?= $usuario['rango'] == 'moderador' ? 'selected' : '' ?>>Moderador</option><?php if ($es_admin): ?><option value="administrador" <?= $usuario['rango'] == 'administrador' ? 'selected' : '' ?>>Administrador</option><?php endif; ?></select></div>
                        <div class="form-group"><label for="estado_cuenta">Estado</label><select id="estado_cuenta" name="estado_cuenta"><option value="activo" <?= $usuario['estado_cuenta'] == 'activo' ? 'selected' : '' ?>>Activo</option><option value="pendiente" <?= $usuario['estado_cuenta'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option><option value="suspendido" <?= $usuario['estado_cuenta'] == 'suspendido' ? 'selected' : '' ?>>Suspendido</option><option value="baneado" <?= $usuario['estado_cuenta'] == 'baneado' ? 'selected' : '' ?>>Baneado</option></select></div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
                </div>
            </form>
        </div>

        <aside>
            <div class="actions-panel">
                <h3><i class="fas fa-cogs"></i> Panel de Control</h3>
                
                <div class="action-item">
                    </div>
                <div class="action-item">
                    </div>
                 <div class="action-item">
                     </div>
                <div class="action-item">
                    </div>
                <?php if ($es_admin && $_SESSION['user_id'] != $usuario['id_usuario']): ?>
                <div class="action-item">
                    </div>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>

<?php
// ✅ INCLUSIÓN DE MODALES
// Usamos la constante ROOT_PATH para asegurar que las rutas siempre sean correctas,
// sin importar desde dónde se incluya este archivo.
include_once ROOT_PATH . '/public/admin/includes/modals/modal_eliminar_usuario.php';
include_once ROOT_PATH . '/public/admin/includes/modals/modal_gestionar_insignias.php';
include_once ROOT_PATH . '/public/includes/modals/modal_galeria_avatares.php';
include_once ROOT_PATH . '/public/includes/modals/modal_viewer.php';
?>