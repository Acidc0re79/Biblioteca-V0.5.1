<?php
require_once __DIR__ . '/../../includes/auth.php';

function get_api_logs($logName) {
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

$api_logs = get_api_logs('api_debug_log.json');
$page_title = "Logs de API";
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
                <p>Interacciones con las APIs externas (Gemini, Hugging Face, etc.)</p>
                <div class="logs-container-scrollable">
                    <?php if (empty($api_logs)): ?>
                        <div class="log-card"><div class="log-card-body"><p>No hay entradas en el log de API.</p></div></div>
                    <?php else: ?>
                        <?php foreach ($api_logs as $index => $log): ?>
                            <div class="log-card">
                                <div class="log-card-header">
                                    <span class="log-chip"><?= strtoupper(htmlspecialchars($log['type'] ?? 'EVENT')) ?></span>
                                    <span class="log-timestamp"><?= htmlspecialchars($log['timestamp'] ?? '') ?></span>
                                    <button class="copy-btn" data-target="log-content-<?= $index ?>">Copiar</button>
                                </div>
                                <div class="log-card-body">
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