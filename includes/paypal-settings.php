<?php
/**
 * Funciones de configuración de PayPal para Breogan LMS
 */

// Registrar ajustes para PayPal
function breogan_lms_registrar_configuracion_paypal() {
    // Registrar opciones
    register_setting('breogan_lms_pagos', 'breogan_paypal_client_id');
    register_setting('breogan_lms_pagos', 'breogan_paypal_secret');
    register_setting('breogan_lms_pagos', 'breogan_paypal_email');
    register_setting('breogan_lms_pagos', 'breogan_paypal_sandbox');
    
    // Sección de PayPal
    add_settings_section(
        'breogan_lms_paypal',
        'Configuración de PayPal',
        'breogan_lms_paypal_section_callback',
        'breogan-lms-pagos'
    );
    
    // Campos
    add_settings_field(
        'breogan_paypal_client_id',
        'Client ID de PayPal',
        'breogan_paypal_client_id_callback',
        'breogan-lms-pagos',
        'breogan_lms_paypal'
    );
    
    add_settings_field(
        'breogan_paypal_secret',
        'Secret Key de PayPal',
        'breogan_paypal_secret_callback',
        'breogan-lms-pagos',
        'breogan_lms_paypal'
    );
    
    add_settings_field(
        'breogan_paypal_email',
        'Email de PayPal Business',
        'breogan_paypal_email_callback',
        'breogan-lms-pagos',
        'breogan_lms_paypal'
    );
    
    add_settings_field(
        'breogan_paypal_sandbox',
        'Modo Sandbox',
        'breogan_paypal_sandbox_callback',
        'breogan-lms-pagos',
        'breogan_lms_paypal'
    );
}
add_action('admin_init', 'breogan_lms_registrar_configuracion_paypal');

/**
 * Callback para sección de PayPal
 */
function breogan_lms_paypal_section_callback() {
    echo '<p>Configura tus credenciales de PayPal para procesar pagos. Puedes usar tanto la API REST (Client ID y Secret) como el método estándar (Email).</p>';
}

/**
 * Callback para Client ID
 */
function breogan_paypal_client_id_callback() {
    $valor = get_option('breogan_paypal_client_id', '');
    echo '<input type="text" name="breogan_paypal_client_id" value="' . esc_attr($valor) . '" class="regular-text">';
    echo '<p class="description">Para la API REST de PayPal. Obtenido en tu dashboard de desarrollador de PayPal.</p>';
}

/**
 * Callback para Secret Key
 */
function breogan_paypal_secret_callback() {
    $valor = get_option('breogan_paypal_secret', '');
    echo '<input type="password" name="breogan_paypal_secret" value="' . esc_attr($valor) . '" class="regular-text">';
    echo '<p class="description">Para la API REST de PayPal. Obtenido en tu dashboard de desarrollador de PayPal.</p>';
}

/**
 * Callback para Email
 */
function breogan_paypal_email_callback() {
    $valor = get_option('breogan_paypal_email', '');
    echo '<input type="email" name="breogan_paypal_email" value="' . esc_attr($valor) . '" class="regular-text">';
    echo '<p class="description">Email de tu cuenta de PayPal Business. Necesario para el método estándar.</p>';
}

/**
 * Callback para modo Sandbox
 */
function breogan_paypal_sandbox_callback() {
    $valor = get_option('breogan_paypal_sandbox', '1');
    echo '<input type="checkbox" name="breogan_paypal_sandbox" value="1" ' . checked('1', $valor, false) . '>';
    echo '<span class="description">Activar modo de pruebas (Sandbox). Desactivar para pagos reales.</span>';
}

/**
 * Añadir página de configuración
 */
function breogan_lms_configuracion_menu() {
    add_options_page(
        'Breogan LMS - Pagos',
        'Breogan LMS - Pagos',
        'manage_options',
        'breogan-lms-pagos',
        'breogan_lms_pagos_configuracion'
    );
}
add_action('admin_menu', 'breogan_lms_configuracion_menu');

/**
 * Contenido de la página de configuración
 */
function breogan_lms_pagos_configuracion() {
    ?>
    <div class="wrap">
        <h1>Configuración de Pagos - Breogan LMS</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('breogan_lms_pagos');
            do_settings_sections('breogan-lms-pagos');
            submit_button();
            ?>
        </form>
        
        <div class="breogan-lms-debug">
            <h2>Ayuda y diagnóstico</h2>
            
            <div class="notice notice-info">
                <p><strong>Información importante sobre PayPal:</strong></p>
                <ol>
                    <li>Si estás en modo Sandbox, es necesario crear cuentas específicas de prueba en <a href="https://developer.paypal.com" target="_blank">developer.paypal.com</a>.</li>
                    <li>Para la integración REST API, necesitas tener una aplicación creada en el Portal de Desarrolladores de PayPal.</li>
                    <li>Para la integración estándar, necesitas una cuenta Business verificada.</li>
                    <li>Si encuentras errores, activa WP_DEBUG en tu wp-config.php para generar logs detallados.</li>
                </ol>
            </div>
            
            <?php if (defined('WP_DEBUG') && WP_DEBUG && file_exists(WP_CONTENT_DIR . '/debug-paypal.log')): ?>
                <h3>Log de depuración de PayPal</h3>
                <div class="breogan-log">
                    <pre><?php echo esc_html(file_get_contents(WP_CONTENT_DIR . '/debug-paypal.log')); ?></pre>
                </div>
                <p>
                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=breogan-lms-pagos&clear_log=1'), 'clear_paypal_log'); ?>" class="button">Limpiar log</a>
                </p>
            <?php endif; ?>
        </div>
        
        <style>
            .breogan-log {
                background: #f5f5f5;
                padding: 10px;
                border: 1px solid #ddd;
                overflow: auto;
                max-height: 400px;
                font-family: monospace;
                font-size: 12px;
                line-height: 1.4;
            }
        </style>
    </div>
    <?php
    
    // Manejar limpieza de log
    if (isset($_GET['clear_log']) && check_admin_referer('clear_paypal_log')) {
        @file_put_contents(WP_CONTENT_DIR . '/debug-paypal.log', "=== PayPal Debug Log ===\nLog limpiado el " . date('Y-m-d H:i:s') . "\n");
        echo '<div class="notice notice-success"><p>Log de depuración limpiado correctamente.</p></div>';
        echo '<script>window.location.href = "' . admin_url('admin.php?page=breogan-lms-pagos') . '";</script>';
    }
}