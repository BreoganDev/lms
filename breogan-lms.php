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
        // Añadir después de otros includes
require_once BREOGAN_LMS_PATH . 'includes/progreso-cursos.php';
require_once BREOGAN_LMS_PATH . 'includes/progreso/progreso-init.php';

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
        new Breogan_LMS_PayPal(); // Inicializar el componente de PayPal
        
        // Inicializar hooks generales
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        
        // Verificar pagos completados
        add_action('template_redirect', array($this, 'check_payment_return'));
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
        
        // Configurar opciones por defecto si no existen
        if (get_option('breogan_paypal_sandbox') === false) {
            update_option('breogan_paypal_sandbox', '1'); // Modo sandbox por defecto
        }
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

/**
 * Función para redirigir intentos de acceso a wp-login.php
 */
function breogan_login_redirect($redirect_to, $request, $user) {
    // Verificar si el usuario no es un administrador
    if (isset($user->roles) && is_array($user->roles)) {
        if (in_array('administrator', $user->roles)) {
            // Si es administrador, mantener el comportamiento predeterminado
            return $redirect_to;
        } else {
            // Para otros roles, redirigir a una página específica
            return home_url('/mi-perfil/'); // Cambia '/mi-perfil' por la ruta que quieras
        }
    }
    
    return $redirect_to;
}
add_filter('login_redirect', 'breogan_login_redirect', 10, 3);

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
   
    