<?php
// Archivo REFACTORIZADO: /public/admin/includes/nav.php

// La variable $page ya está definida en nuestro index.php principal.
// Esto nos permite marcar el enlace activo correctamente.
?>
<nav class="admin-nav">
    <div class="nav-header">
        <a href="<?= BASE_URL ?>admin/" class="nav-brand">
            <i class="fas fa-book-reader"></i>
            <span>BiblioSYS</span>
        </a>
    </div>
    <ul class="nav-menu">
        <li class="<?= ($page == 'dashboard') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>admin/index.php?page=dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        </li>
        
        <?php // Solo mostramos Gestión de Usuarios a Admins y Mods ?>
        <?php if (in_array($_SESSION['rango'], ['administrador', 'moderador'])): ?>
            <li class="<?= in_array($page, ['usuarios', 'editar_usuario']) ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>admin/index.php?page=usuarios"><i class="fas fa-users-cog"></i> Gestión de Usuarios</a>
            </li>
        <?php endif; ?>

        <?php // Solo los Administradores pueden ver las opciones de Sistema ?>
        <?php if ($_SESSION['rango'] === 'administrador'): ?>
            <li class="has-submenu <?= (strpos($page, 'config_') === 0 || strpos($page, 'logs_') === 0 || $page == 'banco_pruebas_gemini') ? 'open' : '' ?>">
                <a href="#"><i class="fas fa-cogs"></i> Sistema</a>
                <ul class="submenu">
                    <li class="<?= (strpos($page, 'config_') === 0) ? 'active' : '' ?>">
                        <a href="<?= BASE_URL ?>admin/index.php?page=config_general"><i class="fas fa-wrench"></i> Configuración</a>
                    </li>
                    <li class="<?= (strpos($page, 'logs_') === 0) ? 'active' : '' ?>">
                        <a href="<?= BASE_URL ?>admin/index.php?page=logs_system"><i class="fas fa-clipboard-list"></i> Visor de Logs</a>
                    </li>
                    <li class="<?= ($page == 'banco_pruebas_gemini') ? 'active' : '' ?>">
                         <a href="<?= BASE_URL ?>admin/index.php?page=banco_pruebas_gemini"><i class="fas fa-flask"></i> Banco de Pruebas IA</a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>

        <li>
            <a href="<?= BASE_URL ?>form-handler.php?action=logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </li>
    </ul>
</nav>

<script>
// Script para manejar los submenús desplegables
document.addEventListener('DOMContentLoaded', function () {
    const submenuItems = document.querySelectorAll('.has-submenu > a');
    submenuItems.forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            const parent = this.parentElement;
            parent.classList.toggle('open');
        });
    });

    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const adminNav = document.querySelector('.admin-nav');
    if (mobileMenuToggle && adminNav) {
        mobileMenuToggle.addEventListener('click', function() {
            adminNav.classList.toggle('mobile-open');
        });
    }
});
</script>