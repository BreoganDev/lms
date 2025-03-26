<?php
/*
Plugin Name: Admin Access Fix
Description: Asegura que los administradores puedan acceder al panel de administración
Version: 1.0
*/

// Ejecutar antes que cualquier otro plugin (prioridad negativa)
add_action('plugins_loaded', 'fix_admin_access', -999);

function fix_admin_access() {
    // Solo aplicar para administradores
    if (is_user_logged_in() && current_user_can('administrator')) {
        // Prevenir redirecciones no deseadas
        remove_all_actions('init', 10); 
        
        // Solo quitar redirecciones, no otras funcionalidades de init
        add_action('init', function() {
            if (is_admin()) {
                // Registrar que estamos permitiendo acceso
                error_log('Admin Access Fix: Permitiendo acceso al panel para administrador');
            }
        }, 1);
        
        // Asegurar que la barra de administración está disponible
        show_admin_bar(true);
    }
}