<?php
/**
 * Plugin Name: Breogan LMS
 * Description: Un plugin LMS personalizado para Escuela de Madres en WordPress.
 * Version: 2.0
 * Author: BreoganDev (Diego)
 * Text Domain: breogan-lms
 */

if (!defined('ABSPATH')) {
    exit; // Evita el acceso directo
}

// Definir constantes
define('BREOGAN_LMS_PATH', plugin_dir_path(__FILE__));
define('BREOGAN_LMS_URL', plugin_dir_url(__FILE__));
define('BREOGAN_LMS_VERSION', '2.0');

/**
 * Clase principal del plugin
 */
class Breogan_LMS {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Inicializar componentes en orden correcto
        $this->load_dependencies();
        $this->initialize();
        
        // Hooks de activación y desactivación
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Cargar dependencias
     */
    private function load_dependencies() {
        // Cargar autoloader de Composer
        require_once BREOGAN_LMS_PATH . 'vendor/autoload.php';
        
        // Cargar clases principales
        require_once BREOGAN_LMS_PATH . 'includes/class-post-types.php';
        require_once BREOGAN_LMS_PATH . 'includes/class-metaboxes.php';
        require_once BREOGAN_LMS_PATH . 'includes/class-templates.php';
        require_once BREOGAN_LMS_PATH . 'includes/class-shortcodes.php';
        require_once BREOGAN_LMS_PATH . 'includes/class-user.php';
        require_once BREOGAN_LMS_PATH . 'includes/class-admin.php';
        require_once BREOGAN_LMS_PATH . 'includes/class-payments.php';
        require_once BREOGAN_LMS_PATH . 'includes/functions.php';
        require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
require_once BREOGAN_LMS_PATH . 'vendor/paypal/lib/autoload.php';
    }
    
    /**
     * Inicializar componentes
     */
    private function initialize() {
        // Inicializar cada componente
        new Breogan_LMS_Post_Types();
        new Breogan_LMS_Metaboxes();
        new Breogan_LMS_Templates();
        new Breogan_LMS_Shortcodes();
        new Breogan_LMS_User();
        new Breogan_LMS_Admin();
        new Breogan_LMS_Payments();
        
        // Inicializar hooks generales
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
    }
    
    /**
     * Activación del plugin
     */
    public function activate() {
        // Registrar CPTs primero
        $post_types = new Breogan_LMS_Post_Types();
        $post_types->register_post_types();
        
        // Limpiar reglas de reescritura
        flush_rewrite_rules();
        
        // Crear páginas necesarias
        $this->create_pages();
    }
    
    /**
     * Desactivación del plugin
     */
    public function deactivate() {
        // Limpiar reglas de reescritura
        flush_rewrite_rules();
    }
    
    /**
     * Enqueue scripts y estilos frontend
     */
   public function enqueue_scripts() {
    // Estilos
    wp_enqueue_style(
        'breogan-lms-styles', 
        BREOGAN_LMS_URL . 'assets/css/styles.css', 
        array(), 
        BREOGAN_LMS_VERSION
    );
    
    // Iconos Dashicons para frontend
    wp_enqueue_style('dashicons');
    
    // Scripts
    wp_enqueue_script(
        'breogan-lms-scripts',
        BREOGAN_LMS_URL . 'assets/js/scripts.js',
        array('jquery'),
        BREOGAN_LMS_VERSION,
        true
    );
    
    // Localizar variables para JS
    wp_localize_script('breogan-lms-scripts', 'breoganLMS', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('breogan_lms_nonce'),
        'text_processing' => __('Procesando...', 'breogan-lms'),
        'text_free_access' => __('Acceder al Curso Gratuito', 'breogan-lms'),
        'text_saving' => __('Guardando...', 'breogan-lms'),
        'text_mark_complete' => __('Marcar como completada', 'breogan-lms'),
        'text_lesson_completed' => __('Lección completada', 'breogan-lms')
    ));
}
    
    /**
     * Enqueue scripts y estilos admin
     */
    public function admin_scripts() {
        wp_enqueue_style(
            'breogan-lms-admin-styles', 
            BREOGAN_LMS_URL . 'assets/css/admin.css', 
            array(), 
            BREOGAN_LMS_VERSION
        );
        
        wp_enqueue_script(
            'breogan-lms-admin-scripts',
            BREOGAN_LMS_URL . 'assets/js/admin.js',
            array('jquery'),
            BREOGAN_LMS_VERSION,
            true
        );
    }
    
    /**
     * Crear páginas necesarias
     */
    private function create_pages() {
        $pages = array(
            'breogan-perfil' => array(
                'title' => __('Mi Perfil', 'breogan-lms'),
                'content' => '[breogan_perfil]'
            ),
            'breogan-cursos' => array(
                'title' => __('Catálogo de Cursos', 'breogan-lms'),
                'content' => '[breogan_cursos]'
            )
        );
        
        foreach ($pages as $slug => $page_data) {
            // Comprobar si la página ya existe
            $page_exists = get_page_by_path($slug);
            
            if (!$page_exists) {
                wp_insert_post(array(
                    'post_title' => $page_data['title'],
                    'post_content' => $page_data['content'],
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_name' => $slug
                ));
            }
        }
    }
}

// Iniciar el plugin
function breogan_lms_init() {
    new Breogan_LMS();
}
add_action('plugins_loaded', 'breogan_lms_init');

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
    
    function breogan_stripe_public_key_callback() {
    $valor = get_option('breogan_stripe_public_key', '');
    echo "<input type='text' name='breogan_stripe_public_key' value='" . esc_attr($valor) . "' class='regular-text'>";
}

function breogan_stripe_secret_key_callback() {
    $valor = get_option('breogan_stripe_secret_key', '');
    echo "<input type='password' name='breogan_stripe_secret_key' value='" . esc_attr($valor) . "' class='regular-text'>";
}

function breogan_paypal_client_id_callback() {
    $valor = get_option('breogan_paypal_client_id', '');
    echo "<input type='text' name='breogan_paypal_client_id' value='" . esc_attr($valor) . "' class='regular-text'>";
}

function breogan_paypal_secret_callback() {
    $valor = get_option('breogan_paypal_secret', '');
    echo "<input type='password' name='breogan_paypal_secret' value='" . esc_attr($valor) . "' class='regular-text'>";
}
}
add_action('admin_init', 'breogan_lms_registrar_configuracion');