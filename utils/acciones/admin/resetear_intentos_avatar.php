<?php
// Se asume que form-handler.php ya cargó init.php

// --- Seguridad y Permisos ---
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['rango'], ['administrador', 'moderador'])) {
    header('Location: ' . BASE_URL);
    exit;
}

// --- Validación de Entradas ---
$id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
$return_url = filter_input(INPUT_POST, 'return_url', FILTER_SANITIZE_URL);

if (!$id_usuario) {
    $_SESSION['error_message'] = "ID de usuario inválido.";
    header('Location: ' . BASE_URL . 'admin/gestion/usuarios.php');
    exit;
}

// --- Lógica de la Base de Datos ---
try {
    $stmt = $pdo->prepare("UPDATE usuarios SET intentos_avatar = 0 WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $_SESSION['success_message'] = "Intentos de avatar reseteados correctamente.";
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Error al resetear los intentos.";
    log_system_event("Error reseteando intentos de avatar", ['error' => $e->getMessage()]);
}

// --- Redirección Inteligente ---
$redirect_location = BASE_URL . 'admin/gestion/usuarios.php'; // Destino por defecto

// Si se proveyó una URL de retorno y es una URL local segura...
if ($return_url && strpos($return_url, '/') === 0) {
    $redirect_location = $return_url; // ...la usamos.
}

header('Location: ' . $redirect_location);
exit;