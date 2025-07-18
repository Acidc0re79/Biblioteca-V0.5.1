<?php
require_once __DIR__ . '/../../includes/auth.php';

$page_title = "Configuración General del Sitio";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?> - Admin</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        /* Reutilizamos los mismos estilos del panel de IA para mantener la consistencia */
        .config-form { max-width: 900px; }
        .form-section { background: #2c2c2d; border: 1px solid #444; padding: 20px; margin-bottom: 25px; border-radius: 8px; }
        .form-section h3 { margin-top: 0; border-bottom: 1px solid #555; padding-bottom: 10px; color: #0095ff; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 8px; color: #ccc; }
        .form-group input, .form-group select {
            width: 100%; padding: 10px; border: 1px solid #555; border-radius: 4px; background-color: #1e1e1e; color: #e0e0e0;
        }
        .form-actions button { background-color: #007bff; color: white; padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; color: #fff; }
        .alert-success { background-color: #28a745; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include __DIR__ . '/../../includes/nav.php'; ?>
        <main class="admin-main">
            <?php include __DIR__ . '/../../includes/header.php'; ?>
            <div class="admin-content">
                <h2><?php echo $page_title; ?></h2>
                <p>Ajusta los parámetros de funcionamiento de la Biblioteca Digital.</p>
                
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></div>
                <?php endif; ?>

                <form class="config-form" action="<?= BASE_URL ?>form-handler.php" method="POST">
                    <input type="hidden" name="action" value="actualizar_config_general">

                    <div class="form-section">
                        <h3>🔧 Depuración y Mantenimiento</h3>
                        <div class="form-group">
                            <label for="modo_depuracion">Modo Depuración</label>
                            <select id="modo_depuracion" name="config[modo_depuracion]">
                                <option value="1" <?= (CONFIG_SITIO['modo_depuracion'] == '1') ? 'selected' : ''; ?>>Activado (Muestra logs y errores detallados)</option>
                                <option value="0" <?= (CONFIG_SITIO['modo_depuracion'] == '0') ? 'selected' : ''; ?>>Desactivado (Modo Producción)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>🎮 Gamificación</h3>
                         <div class="form-group">
                            <label for="intentos_avatar_iniciales">Intentos de Avatar por Reseteo</label>
                            <input type="text" id="intentos_avatar_iniciales" name="config[intentos_avatar_iniciales]" value="<?= htmlspecialchars(CONFIG_SITIO['intentos_avatar_iniciales']) ?>">
                        </div>
                        <div class="form-group">
                            <label for="dias_reseteo_intentos">Días para Resetear Intentos de Avatar</label>
                            <input type="text" id="dias_reseteo_intentos" name="config[dias_reseteo_intentos]" value="<?= htmlspecialchars(CONFIG_SITIO['dias_reseteo_intentos']) ?>">
                        </div>
                        <div class="form-group">
                            <label for="puntos_por_login">Puntos Otorgados por Login Diario</label>
                            <input type="text" id="puntos_por_login" name="config[puntos_por_login]" value="<?= htmlspecialchars(CONFIG_SITIO['puntos_por_login']) ?>">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit">Guardar Configuración General</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>