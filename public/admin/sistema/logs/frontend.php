<?php
// Sube dos niveles para encontrar la carpeta 'includes' en /admin/
require_once __DIR__ . '/../../includes/auth.php';

function get_frontend_logs($logName) {
    $logPath = ROOT_PATH . '/logs/' . $logName;
    $logs = [];
    if (file_exists($logPath)) {
        // Este log es un JSON completo, no línea por línea
        $json_data = file_get_contents($logPath);
        $decoded_data = json_decode($json_data, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded_data)) {
            // El log ya se guarda con los más nuevos primero, no es necesario revertir
            $logs = $decoded_data; 
        }
    }
    return $logs;
}

$frontend_logs = get_frontend_logs('frontend_debug_log.json');
$page_title = "Logs de Frontend";
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
                <p>Registros de errores de JavaScript capturados desde el navegador del usuario.</p>
                <div class="logs-container-scrollable">
                    <?php if (empty($frontend_logs)): ?>
                        <div class="log-card"><div class="log-card-body"><p>No hay entradas en el log de Frontend.</p></div></div>
                    <?php else: ?>
                        <?php foreach ($frontend_logs as $index => $log): ?>
                            <div class="log-card">
                                <div class="log-card-header">
                                    <span class="log-chip error"><?= strtoupper(htmlspecialchars($log['type'] ?? 'ERROR')) ?></span>
                                    <span class="log-timestamp"><?= htmlspecialchars($log['timestamp'] ?? '') ?></span>
                                    <button class="copy-btn" data-target="log-content-<?= $index ?>">Copiar</button>
                                </div>
                                <div class="log-card-body">
                                     <p><strong>Usuario ID:</strong> <?= htmlspecialchars($log['user_id'] ?? 'invitado') ?></p>
                                    <pre id="log-content-<?= $index ?>"><?= htmlspecialchars(json_encode($log['details'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
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