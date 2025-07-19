<?php
// Archivo COMPLETO Y CORREGIDO (CON MAQUETACIÓN): /public/admin/sistema/ia/banco_pruebas_gemini.php

require_once __DIR__ . '/../../includes/auth.php';

// Asegurarnos de que solo los administradores puedan acceder a esta herramienta de desarrollo.
if ($_SESSION['rango'] !== 'administrador') {
    header('Location: ' . BASE_URL . 'admin/');
    exit;
}

$page_title = "Banco de Pruebas - Gemini API";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($page_title) ?> - Admin</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos específicos para esta página */
        .test-panel { max-width: 1200px; }
        .form-section { background: #2c2c2d; border: 1px solid #444; padding: 20px; margin-bottom: 25px; border-radius: 8px; }
        textarea { width: 100%; min-height: 150px; background-color: #1e1e1e; color: #e0e0e0; border: 1px solid #555; border-radius: 4px; padding: 10px; font-family: monospace; }
        .actions { display: flex; gap: 1rem; margin-top: 1rem; align-items: center; }
        .btn-test { background-color: #007bff; color: white; padding: 10px 20px; border-radius: 5px; cursor: pointer; border: none; font-size: 16px; }
        .btn-creative { background-color: #28a745; }
        .result-area { margin-top: 2rem; }
        .result-box { background: #1e1e1e; border: 1px solid #444; padding: 15px; border-radius: 8px; margin-top: 10px; white-space: pre-wrap; font-family: monospace; max-height: 400px; overflow-y: auto; }
        .result-box h4 { margin-top: 0; color: #0095ff; }
        .spinner { display: none; margin-left: 15px; color: #fff; font-size: 1.2em; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include __DIR__ . '/../../includes/nav.php'; // ✅ Se incluye la barra de navegación lateral ?>
        <main class="admin-main">
            <?php include __DIR__ . '/../../includes/header.php'; // ✅ Se incluye la cabecera del panel ?>
            
            <div class="admin-content">
                <h2><i class="fas fa-flask"></i> <?= htmlspecialchars($page_title) ?></h2>
                <p>Usa esta herramienta para enviar prompts directamente a la API de Gemini a través de las personalidades de Lyra y ver la respuesta cruda.</p>

                <div class="test-panel">
                    <div class="form-section">
                        <form id="gemini-test-form">
                            <label for="prompt"><h3>1. Escribe tu Prompt</h3></label>
                            <textarea id="prompt" name="prompt" required>Un hechicero de las estrellas con ojos de nebulosa, de pie en un observatorio antiguo.</textarea>

                            <div class="actions">
                                <button type="submit" class="btn-test" data-personality="guardiana">
                                    <i class="fas fa-shield-alt"></i> Ejecutar con Lyra Guardiana
                                </button>
                                <button type="submit" class="btn-test btn-creative" data-personality="creativa">
                                    <i class="fas fa-paint-brush"></i> Ejecutar con Lyra Creativa
                                </button>
                                <div class="spinner"><i class="fas fa-spinner fa-spin"></i> Procesando...</div>
                            </div>
                        </form>
                    </div>

                    <div class="result-area">
                        <h3>2. Resultados</h3>
                        <div id="api-response-box" class="result-box" style="display:none;">
                            <h4>Respuesta Cruda de la API</h4>
                            <pre id="api-response-content"></pre>
                        </div>
                         <div id="log-info-box" class="result-box" style="display:none; margin-top: 20px; background-color: #3a3a3a;">
                            <h4>Información del Log</h4>
                            <pre id="log-info-content"></pre>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

<script>
// El código JavaScript no necesita cambios, ya que usa rutas absolutas para el fetch.
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('gemini-test-form');
    const spinner = document.querySelector('.spinner');
    const buttons = form.querySelectorAll('.btn-test');
    let activePersonality = '';

    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            activePersonality = this.dataset.personality;
        });
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const prompt = document.getElementById('prompt').value;
        if (!prompt || !activePersonality) {
            alert('Por favor, escribe un prompt y selecciona una acción.');
            return;
        }

        spinner.style.display = 'inline-block';
        buttons.forEach(b => b.disabled = true);
        
        const responseBox = document.getElementById('api-response-box');
        const logBox = document.getElementById('log-info-box');
        responseBox.style.display = 'none';
        logBox.style.display = 'none';

        const formData = new FormData();
        formData.append('action', 'test_gemini_api');
        formData.append('prompt', prompt);
        formData.append('personality', activePersonality);

        fetch('<?= BASE_URL ?>ajax-handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                responseBox.style.display = 'block';
                document.getElementById('api-response-content').textContent = JSON.stringify(data.api_response, null, 2);
                
                logBox.style.display = 'block';
                document.getElementById('log-info-content').textContent = data.log_entry;
            } else {
                alert('Error: ' + data.message);
                responseBox.style.display = 'block';
                document.getElementById('api-response-content').textContent = data.error_details ? JSON.stringify(data.error_details, null, 2) : 'No hay detalles adicionales.';
            }
        })
        .catch(error => {
            console.error('Error en el fetch:', error);
            alert('Hubo un error de conexión al procesar la solicitud.');
        })
        .finally(() => {
            spinner.style.display = 'none';
            buttons.forEach(b => b.disabled = false);
        });
    });
});
</script>
</body>
</html>