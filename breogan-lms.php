<?php
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
/**
 * Plugin Name: Breogan LMS
 * Description: Un plugin LMS personalizado para Escuela de Madres en WordPress.
 * Version: 2.0
 * Author: BreoganDev (Diego)
 * Text Domain: breogan-lms
 */
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
=======
/*
Plugin Name: Breogan LMS
Description: Un plugin LMS personalizado para Escuela de Madres en WordPress.
Version: 1.0
Author: BreoganDev (Diego)
Text Domain: breogan-lms
*/
>>>>>>> 12ee31a27decda5eba9c768c4e10372ecba265b3
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1

if (!defined('ABSPATH')) {
    exit; // Evita el acceso directo
}

// Definir constantes
define('BREOGAN_LMS_PATH', plugin_dir_path(__FILE__));
define('BREOGAN_LMS_URL', plugin_dir_url(__FILE__));
<<<<<<< HEAD
define('BREOGAN_LMS_VERSION', '2.0');

// Garantizar acceso al panel para administradores - AÑADIR AQUÍ
function breogan_ensure_admin_access() {
    if (is_user_logged_in() && current_user_can('administrator') && is_admin()) {
        // Habilitar la barra de administración
        show_admin_bar(true);
        
        // Prevenir redirecciones fuera del admin para administradores
        add_action('admin_init', function() {
            // No hacer nada, solo asegurar que llegamos aquí
        }, 1);
    }
}
add_action('init', 'breogan_ensure_admin_access', 1); // Prioridad 1 para ejecutarse primero
=======
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
define('BREOGAN_LMS_VERSION', '2.0');

>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
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
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
        require_once BREOGAN_LMS_PATH . 'includes/class-paypal.php'; // Nuevo archivo para PayPal
        require_once BREOGAN_LMS_PATH . 'includes/functions.php';
        require_once BREOGAN_LMS_PATH . 'includes/paypal-functions.php';
        require_once BREOGAN_LMS_PATH . 'includes/purchase-functions.php';
        require_once BREOGAN_LMS_PATH . 'send-credentials.php';
        require_once BREOGAN_LMS_PATH . 'includes/class-login.php';
        // Incluir archivos de instructores
        require_once BREOGAN_LMS_PATH . 'instructores/instructor-post-type.php';
        require_once BREOGAN_LMS_PATH . 'instructores/instructor-metaboxes.php';
        require_once BREOGAN_LMS_PATH . 'instructores/instructor-templates.php';
        require_once BREOGAN_LMS_PATH . 'instructores/instructor-shortcode.php';
<<<<<<< HEAD
        // Añadir después de otros includes
require_once BREOGAN_LMS_PATH . 'includes/progreso-cursos.php';
require_once BREOGAN_LMS_PATH . 'includes/progreso/progreso-init.php';

=======

=======
        require_once BREOGAN_LMS_PATH . 'includes/functions.php';
        require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
require_once BREOGAN_LMS_PATH . 'vendor/paypal/lib/autoload.php';
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
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
<<<<<<< HEAD
        new Breogan_LMS_PayPal(); // Inicializar el componente de PayPal
=======
<<<<<<< HEAD
        new Breogan_LMS_PayPal(); // Inicializar el componente de PayPal
=======
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
        
        // Inicializar hooks generales
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
<<<<<<< HEAD
        
        // Verificar pagos completados
        add_action('template_redirect', array($this, 'check_payment_return'));
=======
<<<<<<< HEAD
        
        // Verificar pagos completados
        add_action('template_redirect', array($this, 'check_payment_return'));
=======
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
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
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
        
        // Configurar opciones por defecto si no existen
        if (get_option('breogan_paypal_sandbox') === false) {
            update_option('breogan_paypal_sandbox', '1'); // Modo sandbox por defecto
        }
<<<<<<< HEAD
=======
=======
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
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
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
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
<<<<<<< HEAD
=======
=======
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
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
    
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
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
     * Verificar retorno de pago
     */
    public function check_payment_return() {
        // Verificar si estamos en una página de curso y hay un parámetro de pago
        if (is_singular('cursos') && isset($_GET['pago']) && $_GET['pago'] === 'paypal') {
            // Verificar si hay token y curso_id
            if (isset($_GET['token']) && isset($_GET['curso_id'])) {
                $token = sanitize_text_field($_GET['token']);
                $curso_id = intval($_GET['curso_id']);
                
                // Verificar si el usuario está logueado
                if (!is_user_logged_in()) {
                    // Guardar en sesión y redirigir a login
                    if (!session_id() && !headers_sent()) {
                        session_start();
                    }
                    $_SESSION['breogan_pending_paypal'] = array(
                        'curso_id' => $curso_id,
                        'token' => $token
                    );
                    wp_redirect(wp_login_url(get_permalink($curso_id)));
                    exit;
                }
                
                // Verificar el pago
                $paypal = new Breogan_LMS_PayPal();
                $verified = $paypal->verify_payment_return($curso_id, $token);
                
                if ($verified) {
                    // Redirigir con mensaje de éxito
                    wp_redirect(add_query_arg('pago', 'exitoso', get_permalink($curso_id)));
                    exit;
                }
            }
        }
        
        // Verificar si hay un pago pendiente después del login
        if (is_user_logged_in() && !empty($_SESSION['breogan_pending_paypal'])) {
            $pending = $_SESSION['breogan_pending_paypal'];
            $curso_id = $pending['curso_id'];
            $token = $pending['token'];
            
            // Verificar el pago
            $paypal = new Breogan_LMS_PayPal();
            $paypal->verify_payment_return($curso_id, $token);
            
            // Limpiar sesión
            unset($_SESSION['breogan_pending_paypal']);
            
            // Redirigir con mensaje de éxito
            wp_redirect(add_query_arg('pago', 'exitoso', get_permalink($curso_id)));
            exit;
        }
    }
    
    /**
<<<<<<< HEAD
=======
=======
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
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
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1

/**
 * Cargar estilos para la página de perfil
 */
function breogan_cargar_estilos_perfil() {
    // Solo cargar en la página de perfil
    if (is_page('mi-perfil')) {
        wp_enqueue_style(
            'breogan-perfil-styles',
            BREOGAN_LMS_URL . 'assets/css/perfil-styles.css',
            array(),
            BREOGAN_LMS_VERSION
        );
    }
}
add_action('wp_enqueue_scripts', 'breogan_cargar_estilos_perfil');

<<<<<<< HEAD
/**
 * Función para redirigir intentos de acceso a wp-login.php
 */
=======
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
function breogan_login_redirect($redirect_to, $request, $user) {
    // Verificar si el usuario no es un administrador
    if (isset($user->roles) && is_array($user->roles)) {
        if (in_array('administrator', $user->roles)) {
            // Si es administrador, mantener el comportamiento predeterminado
            return $redirect_to;
        } else {
            // Para otros roles, redirigir a una página específica
<<<<<<< HEAD
            return home_url('/mi-perfil/'); // Cambia '/mi-perfil' por la ruta que quieras
=======
            return home_url('/mi-perfil'); // Cambia '/mi-perfil' por la ruta que quieras
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
        }
    }
    
    return $redirect_to;
}
add_filter('login_redirect', 'breogan_login_redirect', 10, 3);

<<<<<<< HEAD
/**
 * Función para redirigir después del login
 */
function breogan_login_redirect_after($redirect_to, $request, $user) {
    // Solo redireccionar si el usuario existe y está autenticado
    if (isset($user->ID) && $user->ID) {
        // Verificar si el usuario no es un administrador
        if (isset($user->roles) && is_array($user->roles) && !in_array('administrator', $user->roles)) {
            // Solo redireccionar usuarios no administradores
            return home_url('/mi-perfil/');
        }
    }
    
    // Para administradores o usuarios no autenticados, mantener el comportamiento predeterminado
    return $redirect_to;
}
//add_filter('login_redirect', 'breogan_login_redirect_after', 10, 3);

/**
 * Función para ocultar la barra de administración para no-administradores
 */
function quitar_admin_bar() {
    // Asegúrate de que el usuario está autenticado antes de verificar roles
    if (is_user_logged_in() && !current_user_can('administrator')) {
=======
function quitar_admin_bar() {
    if (!current_user_can('administrator')) {
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'quitar_admin_bar');

function breogan_enqueue_curso_styles() {
    // Verifica si estás en la página de archivo de cursos
    if (is_post_type_archive('blms_curso')) {
        wp_enqueue_style(
            'breogan-curso-styles', 
            BREOGAN_LMS_URL . 'assets/css/styles.css', 
            array(), 
            '1.0', 
        'all'
        );
    }
}
add_action('wp_enqueue_scripts', 'breogan_enqueue_curso_styles');

<<<<<<< HEAD
function breogan_enqueue_theme_switcher_script() {
    wp_enqueue_script(
        'breogan-theme-switcher',
        BREOGAN_LMS_URL . 'assets/js/theme-switcher.js',
        array(), 
        '1.0.1', 
        true
    );

    // Opcional: añadir datos localizados
    wp_localize_script('breogan-theme-switcher', 'breoganThemeData', array(
        'initialTheme' => 'dark'
    ));
}
add_action('wp_enqueue_scripts', 'breogan_enqueue_theme_switcher_script');
   
add_shortcode('breogan_lms_instructores', 'breogan_lms_instructores_shortcode');  
   
    
=======
function breogan_lms_enqueue_theme_script() {
    wp_enqueue_script(
        'breogan-theme-switcher',
        BREOGAN_LMS_URL . 'assets/js/theme-switcher.js',
        array(),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'breogan_lms_enqueue_theme_script');
=======
=======

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
>>>>>>> 12ee31a27decda5eba9c768c4e10372ecba265b3

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
<<<<<<< HEAD
    
    function breogan_stripe_public_key_callback() {
    $valor = get_option('breogan_stripe_public_key', '');
    echo "<input type='text' name='breogan_stripe_public_key' value='" . esc_attr($valor) . "' class='regular-text'>";
=======
}
add_action('admin_init', 'breogan_lms_registrar_configuracion');

function breogan_stripe_public_key_callback() {
    $valor = get_option('breogan_stripe_public_key', '');
    echo "<input type='text' name='breogan_stripe_public_key' value='$valor' class='regular-text'>";
>>>>>>> 12ee31a27decda5eba9c768c4e10372ecba265b3
}

function breogan_stripe_secret_key_callback() {
    $valor = get_option('breogan_stripe_secret_key', '');
<<<<<<< HEAD
    echo "<input type='password' name='breogan_stripe_secret_key' value='" . esc_attr($valor) . "' class='regular-text'>";
=======
    echo "<input type='text' name='breogan_stripe_secret_key' value='$valor' class='regular-text'>";
>>>>>>> 12ee31a27decda5eba9c768c4e10372ecba265b3
}

function breogan_paypal_client_id_callback() {
    $valor = get_option('breogan_paypal_client_id', '');
<<<<<<< HEAD
    echo "<input type='text' name='breogan_paypal_client_id' value='" . esc_attr($valor) . "' class='regular-text'>";
=======
    echo "<input type='text' name='breogan_paypal_client_id' value='$valor' class='regular-text'>";
>>>>>>> 12ee31a27decda5eba9c768c4e10372ecba265b3
}

function breogan_paypal_secret_callback() {
    $valor = get_option('breogan_paypal_secret', '');
<<<<<<< HEAD
    echo "<input type='password' name='breogan_paypal_secret' value='" . esc_attr($valor) . "' class='regular-text'>";
}
}
add_action('admin_init', 'breogan_lms_registrar_configuracion');
=======
    echo "<input type='text' name='breogan_paypal_secret' value='$valor' class='regular-text'>";
}

>>>>>>> 12ee31a27decda5eba9c768c4e10372ecba265b3
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
