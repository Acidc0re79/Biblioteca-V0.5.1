<?php
// Archivo COMPLETO Y ACTUALIZADO: /public/ajax-handler.php

// Carga la configuración y el entorno de la aplicación.
// init.php se encarga de iniciar la sesión y la conexión a la BD.
require_once __DIR__ . '/../config/init.php';

// Establece la cabecera para asegurar que la respuesta sea siempre JSON.
header('Content-Type: application/json');

// Obtiene la acción solicitada. Usamos un filtro para más seguridad.
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING) ?: filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

if (!$action) {
    echo json_encode(['success' => false, 'message' => 'Acción no especificada.']);
    exit;
}

// El switch central que dirige cada acción a su lógica correspondiente.
switch ($action) {

    // --- ACCIONES DE GESTIÓN DE USUARIOS (ADMIN) ---

    case 'get_user_badges_admin':
        // Aquí iría la lógica para obtener las insignias de un usuario.
        // Por ahora, es un placeholder.
        echo json_encode(['success' => true, 'message' => 'Función get_user_badges_admin pendiente de implementación.']);
        break;

    case 'gestionar_avatar':
        // Lógica para las acciones de la galería de avatares (revisar, aprobar, etc.)
        // Placeholder.
        echo json_encode(['success' => true, 'message' => 'Función gestionar_avatar pendiente de implementación.']);
        break;

    // --- ACCIONES DE HERRAMIENTAS DE IA (BANCO DE PRUEBAS) ---

    case 'test_gemini_api':
        // Seguridad: solo administradores pueden usar el banco de pruebas.
        if (!isset($_SESSION['user_id']) || $_SESSION['rango'] !== 'administrador') {
            echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
            exit;
        }

        try {
            // Incluimos la clase Lyra que contiene toda la lógica de la IA.
            require_once ROOT_PATH . '/utils/Lyra.php';
            
            $prompt = $_POST['prompt'] ?? '';
            $personality_key = $_POST['personality'] ?? '';
            
            if (empty($prompt) || empty($personality_key)) {
                throw new Exception("Faltan parámetros: se requiere prompt y personalidad.");
            }

            $lyra = new Lyra($pdo);
            $response = null;
            $logContext = ['admin_id' => $_SESSION['user_id'], 'personality_key' => $personality_key, 'prompt' => $prompt];

            if ($personality_key === 'guardiana') {
                $response = $lyra->analizarConGuardiana($prompt);
            } elseif ($personality_key === 'creativa') {
                $response = $lyra->mejorarConCreativa($prompt);
            } else {
                throw new Exception("Personalidad de Lyra no válida.");
            }
            
            // Simulamos la entrada de log para mostrarla en la UI del banco de pruebas.
            $logEntry = [
                'timestamp' => date('Y-m-d H:i:s'),
                'level'     => 'INFO',
                'message'   => "Prueba de API ejecutada desde Banco de Pruebas.",
                'context'   => array_merge($logContext, ['api_response_text' => $response])
            ];

            // Enviamos la respuesta exitosa al frontend.
            echo json_encode([
                'success'       => true,
                'api_response'  => $response, // La respuesta de texto de la IA
                'log_entry'     => json_encode($logEntry, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            ]);

        } catch (Exception $e) {
            // En caso de error, también se loguea y se devuelve un mensaje claro.
            log_system_event("Error en Banco de Pruebas", ['error' => $e->getMessage()], 'api');
            echo json_encode(['success' => false, 'message' => $e->getMessage(), 'error_details' => $e->getTraceAsString()]);
        }
        break;
        
    // --- ACCIONES DE PERSONALIZACIÓN DEL PANEL DE ADMIN ---
        
    case 'update_admin_theme':
        // Seguridad: solo usuarios logueados pueden cambiar su tema.
        if (isset($_SESSION['user_id'], $_POST['theme'])) {
            $theme_key = $_POST['theme'];

            // Valida que el tema enviado exista y esté activo en la base de datos.
            $stmt_validate = $pdo->prepare("SELECT COUNT(*) FROM admin_themes WHERE theme_key = ? AND status = 'activo'");
            $stmt_validate->execute([$theme_key]);
            
            if ($stmt_validate->fetchColumn() > 0) {
                // Si el tema es válido, actualiza la preferencia en la tabla de usuarios.
                $stmt_update = $pdo->prepare("UPDATE usuarios SET admin_theme = ? WHERE id_usuario = ?");
                if ($stmt_update->execute([$theme_key, $_SESSION['user_id']])) {
                    // Actualiza también la sesión para que el cambio sea inmediato.
                    $_SESSION['admin_theme'] = $theme_key;
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al actualizar la base de datos.']);
                }
            } else {
                // Si el tema no es válido, devuelve un error.
                echo json_encode(['success' => false, 'message' => 'El tema seleccionado no es válido o no está activo.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Faltan parámetros o no ha iniciado sesión.']);
        }
        break;

    // --- ACCIÓN POR DEFECTO ---

    default:
        // Si se llama con una acción que no existe, se devuelve un error.
        echo json_encode(['success' => false, 'message' => 'La acción solicitada no es válida.']);
        break;
}