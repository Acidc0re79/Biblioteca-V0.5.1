<?php
// Archivo REFACTORIZADO: /public/admin/views/usuarios.php

$page_title = "Gestión de Usuarios";

// --- Lógica de la página (búsqueda, paginación, etc.) ---

// Usamos 'page_num' para la paginación para no confundirlo con el '?page=' del Front Controller.
$current_page_num = filter_input(INPUT_GET, 'page_num', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
$perPage = 20;
$offset = ($current_page_num - 1) * $perPage;
$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING) ?: '';

// La variable de control de acceso se mantiene.
$es_admin = ($_SESSION['rango'] === 'administrador');

// Construcción de la consulta
$sql = "SELECT id_usuario, nickname, email, rango, estado_cuenta, intentos_avatar, admin_theme FROM usuarios";
$countSql = "SELECT COUNT(id_usuario) FROM usuarios";
$params = [];

if ($search) {
    // La lógica de búsqueda respeta los roles.
    $sql .= " WHERE nickname LIKE :search";
    $countSql .= " WHERE nickname LIKE :search";
    if ($es_admin) {
        $sql .= " OR email LIKE :search";
        $countSql .= " OR email LIKE :search";
    }
    $params[':search'] = "%$search%";
}

$sql .= " ORDER BY id_usuario DESC LIMIT :limit OFFSET :offset";

// Total de usuarios para la paginación
$stmtCount = $pdo->prepare($countSql);
$stmtCount->execute($params);
$totalUsers = $stmtCount->fetchColumn();
$totalPages = ceil($totalUsers / $perPage);

// Usuarios para la página actual
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
if ($search) {
    $stmt->bindParam(':search', $params[':search']);
}
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="admin-content">
    <div class="content-header">
        <h2><i class="fas fa-users-cog"></i> <?= htmlspecialchars($page_title) ?></h2>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>

    <form method="get" class="search-form">
        <input type="hidden" name="page" value="usuarios">
        <input type="text" name="search" placeholder="Buscar por nickname <?= $es_admin ? 'o email' : '' ?>..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit"><i class="fas fa-search"></i> Buscar</button>
    </form>

    <div class="table-responsive">
        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nickname</th>
                    <?php if ($es_admin): // ✅ Se mantiene la lógica de rol para la columna Email ?>
                        <th>Email</th>
                    <?php endif; ?>
                    <th>Rango</th>
                    <th>Estado</th>
                    <th class="text-center">Intentos Avatar</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                    <?php $colspan = $es_admin ? 7 : 6; ?>
                    <tr><td colspan="<?= $colspan ?>">No se encontraron usuarios que coincidan con la búsqueda.</td></tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= $usuario['id_usuario'] ?></td>
                            <td><?= htmlspecialchars($usuario['nickname'] ?? 'N/A') ?></td>
                            <?php if ($es_admin): // ✅ Se mantiene la lógica de rol para la celda Email ?>
                                <td><?= htmlspecialchars($usuario['email'] ?? 'N/A') ?></td>
                            <?php endif; ?>
                            <td><span class="badge badge-<?= htmlspecialchars($usuario['rango'] ?? 'lector') ?>"><?= htmlspecialchars(ucfirst($usuario['rango'] ?? '')) ?></span></td>
                            <td><span class="badge status-<?= htmlspecialchars($usuario['estado_cuenta'] ?? 'pendiente') ?>"><?= htmlspecialchars(ucfirst($usuario['estado_cuenta'] ?? '')) ?></span></td>
                            <td class="text-center"><?= htmlspecialchars($usuario['intentos_avatar'] ?? '0') ?></td>
                            <td>
                                <a href="index.php?page=editar_usuario&id=<?= $usuario['id_usuario'] ?>" class="btn-action btn-edit" title="Editar Usuario"><i class="fas fa-pencil-alt"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="index.php?page=usuarios&page_num=<?= $i ?>&search=<?= urlencode($search) ?>" class="<?= $current_page_num == $i ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>