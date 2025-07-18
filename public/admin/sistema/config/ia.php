<?php
require_once __DIR__ . '/../../includes/auth.php';

// La constante CONFIG_SITIO ya contiene todos nuestros parámetros de la BD.
$page_title = "Configuración del Motor de IA";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?> - Admin</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        .ia-config-form { max-width: 900px; }
        .form-section { background: #2c2c2d; border: 1px solid #444; padding: 20px; margin-bottom: 25px; border-radius: 8px; }
        .form-section h3 { margin-top: 0; border-bottom: 1px solid #555; padding-bottom: 10px; color: #0095ff; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 8px; color: #ccc; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%; padding: 10px; border: 1px solid #555; border-radius: 4px; background-color: #1e1e1e; color: #e0e0e0;
        }
        .form-group textarea { min-height: 150px; font-family: monospace; }
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
                <p>Gestiona todos los parámetros del Motor de IA en tiempo real.</p>
                
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></div>
                <?php endif; ?>

                <form class="ia-config-form" action="<?= BASE_URL ?>form-handler.php" method="POST">
                    <input type="hidden" name="action" value="actualizar_config_ia">

                    <div class="form-section">
                        <h3>🤖 Gemini</h3>
                        <div class="form-group">
                            <label for="ia_estado_gemini">Estado del Servicio (Circuit Breaker)</label>
                            <select id="ia_estado_gemini" name="config[ia_estado_gemini]">
                                <option value="online" <?= (CONFIG_SITIO['ia_estado_gemini'] == 'online') ? 'selected' : ''; ?>>Online</option>
                                <option value="offline" <?= (CONFIG_SITIO['ia_estado_gemini'] == 'offline') ? 'selected' : ''; ?>>Offline (Mantenimiento)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ia_timeout_gemini">Timeout (segundos)</label>
                            <input type="text" id="ia_timeout_gemini" name="config[ia_timeout_gemini]" value="<?= htmlspecialchars(CONFIG_SITIO['ia_timeout_gemini']) ?>">
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>🤗 Hugging Face</h3>
                         <div class="form-group">
                            <label for="ia_estado_huggingface">Estado del Servicio (Circuit Breaker)</label>
                            <select id="ia_estado_huggingface" name="config[ia_estado_huggingface]">
                                <option value="online" <?= (CONFIG_SITIO['ia_estado_huggingface'] == 'online') ? 'selected' : ''; ?>>Online</option>
                                <option value="offline" <?= (CONFIG_SITIO['ia_estado_huggingface'] == 'offline') ? 'selected' : ''; ?>>Offline (Mantenimiento)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ia_timeout_huggingface">Timeout (segundos)</label>
                            <input type="text" id="ia_timeout_huggingface" name="config[ia_timeout_huggingface]" value="<?= htmlspecialchars(CONFIG_SITIO['ia_timeout_huggingface']) ?>">
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>👩‍🎤 Personalidades (Prompts de Sistema)</h3>
                        <div class="form-group">
                            <label for="ia_prompt_lyra_guardiana">Lyra Guardiana (Seguridad)</label>
                            <textarea id="ia_prompt_lyra_guardiana" name="config[ia_prompt_lyra_guardiana]"><?= htmlspecialchars(CONFIG_SITIO['ia_prompt_lyra_guardiana']) ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="ia_prompt_lyra_creativa">Lyra Creativa (Mejora)</label>
                            <textarea id="ia_prompt_lyra_creativa" name="config[ia_prompt_lyra_creativa]"><?= htmlspecialchars(CONFIG_SITIO['ia_prompt_lyra_creativa']) ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>