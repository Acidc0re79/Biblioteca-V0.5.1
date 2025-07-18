<?php

// Sube un nivel para encontrar la carpeta 'includes'.
require_once __DIR__ . '/../includes/auth.php';

// --- Lógica de Paginación y Filtros ---
$pagina_actual = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT, ['options' => ['default' => 1]]);

// Usamos htmlspecialchars para limpiar los inputs de texto en lugar de la constante obsoleta.
$busqueda_raw = $_GET['busqueda'] ?? '';
$rango_raw = $_GET['rango'] ?? '';
$estado_raw = $_GET['estado'] ?? '';

$filtros = [
    'busqueda' => htmlspecialchars($busqueda_raw, ENT_QUOTES, 'UTF-8'),
    'rango'    => htmlspecialchars($rango_raw, ENT_QUOTES, 'UTF-8'),
    'estado'   => htmlspecialchars($estado_raw, ENT_QUOTES, 'UTF-8'),
];
$filtros = array_filter($filtros);

// Asumimos que la función get_usuarios_paginados está en /utils/helpers.php
$datos_usuarios = get_usuarios_paginados($pdo, $pagina_actual, 15, $filtros);
$usuarios = $datos_usuarios['usuarios'];
$total_paginas = $datos_usuarios['total_paginas'];

$page_title = "Gestión de Usuarios";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?> - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .filter-bar { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem; padding: 1rem; background: #2c2c2d; border-radius: 8px; }
        .filter-bar input, .filter-bar select, .filter-bar button { padding: 8px; border-radius: 4px; border: 1px solid #555; background-color: #1e1e1e; color: #e0e0e0; }
        .user-table { width: 100%; border-collapse: collapse; }
        .user-table th, .user-table td { border: 1px solid #444; padding: 12px; text-align: left; }
        .actions-cell { text-align: center; }
        .actions-cell form { display: inline-block; }
        .actions-cell button, .actions-cell a { background: none; border: none; color: #ccc; cursor: pointer; padding: 0; font-size: 1.1em; margin: 0 5px; }
        .pagination { display: flex; justify-content: center; gap: 0.5rem; margin-top: 1.5rem; }
        .pagination a, .pagination span { padding: 8px 12px; background: #2c2c2d; color: #fff; text-decoration: none; border-radius: 4px; }
        .pagination .active { background: #007bff; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include __DIR__ . '/../includes/nav.php'; ?>
        <main class="admin-main">
            <?php include __DIR__ . '/../includes/header.php'; ?>
            <div class="admin-content">
                <h2><?php echo $page_title; ?></h2>

                <form class="filter-bar" method="GET">
                    <input type="text" name="busqueda" placeholder="Buscar..." value="<?= htmlspecialchars($filtros['busqueda'] ?? '') ?>">
                    <select name="rango">
                        <option value="">Todos los Rangos</option>
                        <option value="lector" <?= ($filtros['rango'] ?? '') == 'lector' ? 'selected' : '' ?>>Lector</option>
                        <option value="moderador" <?= ($filtros['rango'] ?? '') == 'moderador' ? 'selected' : '' ?>>Moderador</option>
                        <option value="administrador" <?= ($filtros['rango'] ?? '') == 'administrador' ? 'selected' : '' ?>>Administrador</option>
                    </select>
                    <select name="estado">
                        <option value="">Todos los Estados</option>
                        <option value="activo" <?= ($filtros['estado'] ?? '') == 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="pendiente" <?= ($filtros['estado'] ?? '') == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="suspendido" <?= ($filtros['estado'] ?? '') == 'suspendido' ? 'selected' : '' ?>>Suspendido</option>
                    </select>
                    <button type="submit">Filtrar</button>
                </form>

                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nickname</th>
                            <?php if ($_SESSION['rango'] === 'administrador'): ?><th>Email</th><?php endif; ?>
                            <th>Rango</th>
                            <th>Estado</th>
                            <th>Registrado</th>
                            <th class="actions-cell">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?= $usuario['id_usuario'] ?></td>
                                <td><?= htmlspecialchars($usuario['nickname'] ?? '') ?></td>
                                <?php if ($_SESSION['rango'] === 'administrador'): ?><td><?= htmlspecialchars($usuario['email']) ?></td><?php endif; ?>
                                <td><?= ucfirst(htmlspecialchars($usuario['rango'])) ?></td>
                                <td><?= ucfirst(htmlspecialchars($usuario['estado_cuenta'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></td>
                                <td class="actions-cell">
                                    <?php if ($usuario['estado_cuenta'] === 'pendiente'): ?>
                                        <form action="<?= BASE_URL ?>form-handler.php" method="POST" title="Activar Cuenta">
                                            <input type="hidden" name="action" value="activar_usuario_admin">
                                            <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
                                            <button type="submit"><i class="fas fa-check-circle"></i></button>
                                        </form>
                                    <?php endif; ?>
                                    <form action="<?= BASE_URL ?>form-handler.php?<?= http_build_query($filtros) ?>" method="POST" title="Resetear Intentos de Avatar">
        <input type="hidden" name="action" value="resetear_intentos_avatar">
        <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
        <button type="submit"><i class="fas fa-sync-alt"></i></button>
    </form><a href="editar_usuario.php?id=<?= $usuario['id_usuario'] ?>" title="Editar Usuario"><i class="fas fa-edit"></i></a>
                                    <?php if ($_SESSION['rango'] === 'administrador' && $_SESSION['user_id'] != $usuario['id_usuario']): ?>
                                        <button onclick="abrirModalEliminar(<?= $usuario['id_usuario'] ?>, '<?= htmlspecialchars($usuario['nickname'] ?? '') ?>')" title="Eliminar Usuario"><i class="fas fa-trash-alt"></i></button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <a href="?pagina=<?= $i ?>&<?= http_build_query($filtros) ?>" class="<?= ($i == $pagina_actual) ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            </div>
        </main>
    </div>

    <?php
    // Incluimos el modal reutilizable para la eliminación de usuarios.
    include __DIR__ . '/../includes/modals/modal_eliminar_usuario.php';
    ?>
    
</body>
</html>