<?php
// Archivo COMPLETO Y REFACTORIZADO: /public/admin/gestion/editar_usuario.php

require_once __DIR__ . '/../includes/auth.php';

if (!isset($_GET['id']) || !($id_usuario = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT))) {
    header('Location: usuarios.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los datos del usuario: " . $e->getMessage());
}

if (!$usuario) {
    header('Location: usuarios.php');
    exit;
}

$page_title = "Editando a " . htmlspecialchars($usuario['nickname'] ?? '');
$es_admin = $_SESSION['rango'] === 'administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?> - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>const BASE_URL = '<?= BASE_URL ?>';</script>

    <style>
        .page-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        @media (min-width: 1200px) {
            .page-grid {
                grid-template-columns: 2fr 1fr;
            }
        }
        
        .form-section { background: #2c2c2d; border: 1px solid #444; padding: 20px; margin-bottom: 25px; border-radius: 8px; }
        .form-section h3 { margin-top: 0; border-bottom: 1px solid #555; padding-bottom: 10px; color: #0095ff; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 8px; color: #ccc; }
        .form-group input, .form-group select, .form-group .readonly-field {
            width: 100%; box-sizing: border-box; padding: 10px; border: 1px solid #555; border-radius: 4px; background-color: #1e1e1e; color: #e0e0e0;
        }
        .form-group .readonly-field { background-color: #3a3a3a; }
        .form-actions { text-align: right; margin-top: 1.5rem; }
        .btn-save { background-color: #007bff; color: white; padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        
        /* Estilos para el nuevo Panel de Acciones */
        .actions-panel { background: #2c2c2d; border: 1px solid #444; padding: 20px; border-radius: 8px; }
        .actions-panel h3 { color: #ffc107; margin-top: 0; border-bottom: 1px solid #555; padding-bottom: 10px; }
        .action-item { display: flex; flex-direction: column; align-items: flex-start; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #3a3a3a; }
        .action-item:last-child { border-bottom: none; margin-bottom: 0; }
        .action-item-header { display: flex; gap: 15px; width: 100%; }
        .action-item-header button, .action-item-header form { flex-shrink: 0; }
        .action-item-details { display: flex; flex-direction: column; }
        .action-item-details strong { color: #e0e0e0; font-size: 1.1em; }
        .action-item-details p { font-size: 0.9em; color: #aaa; margin: 5px 0 0 0; line-height: 1.4; }
        .action-button { padding: 10px 15px; border-radius: 5px; border: none; cursor: pointer; color: white; text-align: left; }
        .btn-gestionar { background-color: #17a2b8; } .btn-sancion { background-color: #ffc107; color: #000; }
        .btn-delete { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include __DIR__ . '/../includes/nav.php'; ?>
        <main class="admin-main">
            <?php include __DIR__ . '/../includes/header.php'; ?>
            <div class="admin-content">
                <h2><?php echo $page_title; ?></h2>

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
                                    <div class="form-group"><label for="nombre">Nombre</label><input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>"></div>
                                    <div class="form-group"><label for="apellido">Apellido</label><input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>"></div>
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

                            <div class="form-section">
                                <h3><i class="fas fa-gamepad"></i> Gamificación</h3>
                                <div class="form-grid">
                                    <div class="form-group"><label for="puntos">Puntos</label><input type="number" id="puntos" name="puntos" value="<?= $usuario['puntos'] ?>"></div>
                                    <div class="form-group"><label for="intentos_avatar">Intentos Avatar</label><input type="number" id="intentos_avatar" name="intentos_avatar" value="<?= $usuario['intentos_avatar'] ?>"></div>
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <h3><i class="fas fa-key"></i> Seguridad</h3>
                                <div class="form-grid">
                                    <div class="form-group"><label for="password">Nueva Contraseña</label><input type="password" id="password" name="password" autocomplete="new-password" placeholder="Dejar en blanco para no cambiar"></div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn-save"><i class="fas fa-save"></i> Guardar Cambios</button>
                            </div>
                        </form>
                    </div>

                    <aside>
                        <div class="actions-panel">
                            <h3><i class="fas fa-cogs"></i> Panel de Control</h3>
                            
                            <div class="action-item">
                                <div class="action-item-header">
                                    <button type="button" class="action-button btn-gestionar" onclick="abrirModalGaleria(<?= $usuario['id_usuario'] ?>, 'admin')"><i class="fas fa-user-astronaut"></i></button>
                                    <div class="action-item-details">
                                        <strong>Revisar Avatares</strong>
                                        <p>Accede a la galería de avatares generados por el usuario para aprobar o eliminarlos.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="action-item">
                                <div class="action-item-header">
                                    <button type="button" class="action-button btn-gestionar" onclick="abrirModalInsignias(<?= $usuario['id_usuario'] ?>)"><i class="fas fa-medal"></i></button>
                                    <div class="action-item-details">
                                        <strong>Gestionar Insignias</strong>
                                        <p>Asigna o revoca insignias manualmente para recompensar o corregir al usuario.</p>
                                    </div>
                                </div>
                            </div>

							<div class="action-item">
								<div class="action-item-header">
									<form action="<?= BASE_URL ?>form-handler.php" method="POST" onsubmit="return confirm('¿Resetear intentos de avatar a CERO?')">
										<input type="hidden" name="action" value="resetear_intentos_avatar">
										<input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
										<input type="hidden" name="return_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
										<button type="submit" class="action-button btn-sancion" title="Establece los intentos de avatar a 0."><i class="fas fa-sync-alt"></i></button>
									</form>
									<div class="action-item-details">
										<strong>Resetear Intentos</strong>
										<p>Reinicia el contador de intentos de generación de avatares del usuario a cero.</p>
									</div>
								</div>
							</div>

							<div class="action-item">
								<div class="action-item-header">
									<form action="<?= BASE_URL ?>form-handler.php" method="POST" onsubmit="return confirm('¿Restringir permanentemente la generación de avatares?')">
										<input type="hidden" name="action" value="restringir_avatares">
										<input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
										<input type="hidden" name="return_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
										<button type="submit" class="action-button btn-sancion" title="Impide que el usuario genere más avatares."><i class="fas fa-gavel"></i></button>
									</form>
								<div class="action-item-details">
									<strong>Restringir Avatares</strong>
									<p>Impide que el usuario genere más avatares hasta que la restricción sea levantada.</p>
								</div>
							</div>
						</div>

                            <?php if ($es_admin && $_SESSION['user_id'] != $usuario['id_usuario']): ?>
                            <div class="action-item">
                                <div class="action-item-header">
                                    <button type="button" class="action-button btn-delete" onclick="abrirModalEliminar(<?= $usuario['id_usuario'] ?>, '<?= htmlspecialchars($usuario['nickname'] ?? '') ?>')"><i class="fas fa-trash-alt"></i></button>
                                    <div class="action-item-details">
                                        <strong>Eliminar Usuario</strong>
                                        <p>Borra permanentemente al usuario y todo su contenido asociado del sistema.</p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </aside>
                </div>
            </div>
        </main>
    </div>

    <?php
    include __DIR__ . '/../includes/modals/modal_eliminar_usuario.php';
    include __DIR__ . '/../includes/modals/modal_gestionar_insignias.php';
    include_once ROOT_PATH . '/public/includes/modals/modal_galeria_avatares.php';
    include_once ROOT_PATH . '/public/includes/modals/modal_viewer.php';
    ?>
</body>
</html>