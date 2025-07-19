<?php
// Archivo REFACTORIZADO: /public/admin/includes/header.php

// 1. CARGAMOS LOS TEMAS DISPONIBLES
// Obtenemos todos los temas que están marcados como 'activos' en nuestra nueva tabla.
$stmt_themes = $pdo->prepare("SELECT theme_key, theme_name FROM admin_themes WHERE status = 'activo' ORDER BY theme_name ASC");
$stmt_themes->execute();
$available_themes = $stmt_themes->fetchAll(PDO::FETCH_ASSOC);

// 2. DETERMINAMOS EL TEMA DEL USUARIO
// Revisamos la sesión. Si no está, la cargamos desde la BD para no hacer consultas en cada página.
if (empty($_SESSION['admin_theme'])) {
    // Asumimos que la info del usuario ya está en la sesión desde el login.
    $_SESSION['admin_theme'] = $_SESSION['user_data']['admin_theme'] ?? 'neon_dark';
}
$user_theme = $_SESSION['admin_theme'];

// Construimos la ruta al archivo CSS del tema del usuario.
$theme_file_path = BASE_URL . 'admin/assets/themes/' . htmlspecialchars($user_theme) . '/theme.css';
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' - ' : '' ?>Admin BiblioSYS</title>
    
    <link id="admin-theme-stylesheet" rel="stylesheet" href="<?= $theme_file_path ?>">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <?= $page_specific_styles ?? '' ?>
</head>

<header class="admin-header">
    <div class="header-left">
        <button id="mobile-menu-toggle" class="mobile-menu-button"><i class="fas fa-bars"></i></button>
        <span class="header-title"><?= htmlspecialchars($page_title ?? 'Panel de Administración') ?></span>
    </div>
    <div class="header-right">
        <div class="theme-selector">
            <i class="fas fa-palette"></i>
            <select id="theme-switcher" title="Seleccionar tema visual">
                <?php foreach ($available_themes as $theme): ?>
                    <option value="<?= htmlspecialchars($theme['theme_key']) ?>" <?= ($user_theme == $theme['theme_key']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($theme['theme_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <span>Hola, <?= htmlspecialchars($_SESSION['nickname'] ?? 'Usuario') ?></span>
        </div>
    </div>
</header>