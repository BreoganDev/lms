<?php
/*
Plugin Name: Breogan LMS
Description: Un plugin LMS personalizado para Escuela de Madres en WordPress.
Version: 1.0
Author: BreoganDev (Diego)
Text Domain: breogan-lms
*/

if (!defined('ABSPATH')) {
    exit; // Evita el acceso directo
}

// Definir constantes
define('BREOGAN_LMS_PATH', plugin_dir_path(__FILE__));
define('BREOGAN_LMS_URL', plugin_dir_url(__FILE__));

// Cargar archivos necesarios
require_once BREOGAN_LMS_PATH . 'includes/post-types.php';
require_once BREOGAN_LMS_PATH . 'includes/shortcodes.php';
require_once BREOGAN_LMS_PATH . 'includes/functions.php';
require_once BREOGAN_LMS_PATH . 'includes/admin-menu.php';
require_once BREOGAN_LMS_PATH . 'includes/metaboxes.php';

function breogan_lms_override_templates($template) {
    if (is_singular('cursos')) {
        return BREOGAN_LMS_PATH . 'templates/single-cursos.php';
    }
    if (is_singular('temas')) {
        return BREOGAN_LMS_PATH . 'templates/single-temas.php';
    }
    if (is_singular('lecciones')) {
        return BREOGAN_LMS_PATH . 'templates/single-lecciones.php';
    }
    return $template;
}
add_filter('single_template', 'breogan_lms_override_templates');



// Encolar scripts y estilos
function breogan_lms_enqueue_scripts() {
    wp_enqueue_script('jquery'); // Asegurar que jQuery se carga correctamente
    wp_enqueue_style('breogan-lms-style', BREOGAN_LMS_URL . 'assets/css/style.css', array(), '1.0');
    wp_enqueue_script('breogan-lms-script', BREOGAN_LMS_URL . 'assets/js/script.js', array('jquery'), '1.0', true);
    
    // Localizar AJAX para que el script pueda acceder a admin-ajax.php
    wp_localize_script('breogan-lms-script', 'breoganLMS', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'breogan_lms_enqueue_scripts');

// Stripe y Paypal
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
    </div>
    <?php
}

function breogan_lms_registrar_configuracion() {
    register_setting('breogan_lms_pagos', 'breogan_stripe_public_key');
    register_setting('breogan_lms_pagos', 'breogan_stripe_secret_key');
    register_setting('breogan_lms_pagos', 'breogan_paypal_client_id');
    register_setting('breogan_lms_pagos', 'breogan_paypal_secret');

    add_settings_section('breogan_lms_stripe', 'Configuración de Stripe', null, 'breogan-lms-pagos');
    add_settings_field('breogan_stripe_public_key', 'Clave Pública de Stripe', 'breogan_stripe_public_key_callback', 'breogan-lms-pagos', 'breogan_lms_stripe');
    add_settings_field('breogan_stripe_secret_key', 'Clave Secreta de Stripe', 'breogan_stripe_secret_key_callback', 'breogan-lms-pagos', 'breogan_lms_stripe');

    add_settings_section('breogan_lms_paypal', 'Configuración de PayPal', null, 'breogan-lms-pagos');
    add_settings_field('breogan_paypal_client_id', 'Client ID de PayPal', 'breogan_paypal_client_id_callback', 'breogan-lms-pagos', 'breogan_lms_paypal');
    add_settings_field('breogan_paypal_secret', 'Clave Secreta de PayPal', 'breogan_paypal_secret_callback', 'breogan-lms-pagos', 'breogan_lms_paypal');
}
add_action('admin_init', 'breogan_lms_registrar_configuracion');

function breogan_stripe_public_key_callback() {
    $valor = get_option('breogan_stripe_public_key', '');
    echo "<input type='text' name='breogan_stripe_public_key' value='$valor' class='regular-text'>";
}

function breogan_stripe_secret_key_callback() {
    $valor = get_option('breogan_stripe_secret_key', '');
    echo "<input type='text' name='breogan_stripe_secret_key' value='$valor' class='regular-text'>";
}

function breogan_paypal_client_id_callback() {
    $valor = get_option('breogan_paypal_client_id', '');
    echo "<input type='text' name='breogan_paypal_client_id' value='$valor' class='regular-text'>";
}

function breogan_paypal_secret_callback() {
    $valor = get_option('breogan_paypal_secret', '');
    echo "<input type='text' name='breogan_paypal_secret' value='$valor' class='regular-text'>";
}

