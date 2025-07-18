<?php
// Sube dos niveles para encontrar la carpeta 'includes' en /admin/
require_once __DIR__ . '/../../includes/auth.php';

function get_handler_logs($logName) {
    $logPath = ROOT_PATH . '/logs/' . $logName;
    $logs = [];
    if (file_exists($logPath)) {
        // Este log es un JSON completo, no línea por línea
        $json_data = file_get_contents($logPath);
        $decoded_data = json_decode($json_data, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded_data)) {
            $logs = $decoded_data; // No es necesario revertir, ya vienen ordenados
        }
    }
    return $logs;
}

$handler_logs = get_handler_logs('handler_debug_log.json');
$page_title = "Logs de Handlers";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?> - Admin</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include __DIR__ . '/../../includes/nav.php'; ?>
        <main class="admin-main">
            <?php include __DIR__ . '/../../includes/header.php'; ?>
            <div class="admin-content">
                <h2><?php echo $page_title; ?></h2>
                <p>Registros de las peticiones recibidas por los controladores principales (form-handler, ajax-handler).</p>
                <div class="logs-container-scrollable">
                    <?php if (empty($handler_logs)): ?>
                        <div class="log-card"><div class="log-card-body"><p>No hay entradas en el log de Handlers.</p></div></div>
                    <?php else: ?>
                        <?php foreach ($handler_logs as $index => $log): ?>
                            <div class="log-card">
                                <div class="log-card-header">
                                    <span class="log-chip"><?= strtoupper(htmlspecialchars($log['handler_type'] ?? 'HANDLER')) ?></span>
                                    <span class="log-timestamp"><?= htmlspecialchars($log['timestamp'] ?? '') ?></span>
                                    <button class="copy-btn" data-target="log-content-<?= $index ?>">Copiar</button>
                                </div>
                                <div class="log-card-body">
                                    <p><strong>Usuario ID:</strong> <?= htmlspecialchars($log['user_id'] ?? 'invitado') ?></p>
                                    <pre id="log-content-<?= $index ?>"><?= htmlspecialchars(json_encode($log, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <script src="../../assets/js/logs_viewer.js"></script>
</body>
</html>