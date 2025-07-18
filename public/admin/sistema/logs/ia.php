<?php
require_once __DIR__ . '/../../includes/auth.php';

function get_ia_logs($logName) {
    $logPath = ROOT_PATH . '/logs/' . $logName;
    $logs = [];
    if (file_exists($logPath)) {
        $log_lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($log_lines as $line) {
            $decoded_line = json_decode($line, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $logs[] = $decoded_line;
            }
        }
        return array_reverse($logs);
    }
    return $logs;
}

$ia_logs = get_ia_logs('ia_debug_log.json');
$page_title = "Logs del Motor de IA";
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
                <p>Registros detallados de todas las operaciones realizadas por el Motor de IA.</p>
                <div class="logs-container-scrollable">
                    <?php if (empty($ia_logs)): ?>
                        <div class="log-card"><div class="log-card-body"><p>No hay entradas en el log de IA.</p></div></div>
                    <?php else: ?>
                        <?php foreach ($ia_logs as $index => $log): ?>
                            <div class="log-card">
                                <div class="log-card-header">
                                    <span class="log-timestamp"><?= htmlspecialchars($log['timestamp'] ?? '') ?></span>
                                    <button class="copy-btn" data-target="log-content-<?= $index ?>">Copiar</button>
                                </div>
                                <div class="log-card-body">
                                    <p><strong><?= htmlspecialchars($log['message'] ?? 'N/A') ?></strong></p>
                                    <?php if (!empty($log['context'])): ?>
                                    <pre id="log-content-<?= $index ?>"><?= htmlspecialchars(json_encode($log['context'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                                    <?php endif; ?>
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