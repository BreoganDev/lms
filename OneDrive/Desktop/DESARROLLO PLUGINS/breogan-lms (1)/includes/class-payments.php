<?php
/**
 * Clase para gestionar pagos con Stripe y PayPal
 */
class Breogan_LMS_Payments {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Registrar página de opciones
        add_action('admin_menu', array($this, 'register_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        
        // Registrar endpoints AJAX para pagos
        add_action('wp_ajax_blms_process_stripe_payment', array($this, 'process_stripe_payment'));
        add_action('wp_ajax_nopriv_blms_process_stripe_payment', array($this, 'process_stripe_payment'));
        
        add_action('wp_ajax_blms_process_paypal_payment', array($this, 'process_paypal_payment'));
        add_action('wp_ajax_nopriv_blms_process_paypal_payment', array($this, 'process_paypal_payment'));
        
        // Endpoint para IPN de PayPal
        add_action('rest_api_init', array($this, 'register_paypal_ipn_endpoint'));
    }
    
    /**
     * Registrar página de configuración
     */
    public function register_settings_page() {
        add_submenu_page(
            'options-general.php',
            __('Configuración de Pagos - Breogan LMS', 'breogan-lms'),
            __('Breogan LMS - Pagos', 'breogan-lms'),
            'manage_options',
            'breogan-lms-pagos',
            array($this, 'settings_page_content')
        );
    }
    
    /**
     * Contenido de la página de configuración
     */
    public function settings_page_content() {
        ?>
        <div class="wrap">
            <h1><?php _e('Configuración de Pagos - Breogan LMS', 'breogan-lms'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('blms_payment_settings');
                do_settings_sections('breogan-lms-pagos');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Registrar ajustes
     */
    public function register_settings() {
        // Grupo de ajustes
        register_setting('blms_payment_settings', 'blms_stripe_public_key');
        register_setting('blms_payment_settings', 'blms_stripe_secret_key');
        register_setting('blms_payment_settings', 'blms_paypal_client_id');
        register_setting('blms_payment_settings', 'blms_paypal_secret');
        register_setting('blms_payment_settings', 'blms_paypal_sandbox');
        
        // Sección Stripe
        add_settings_section(
            'blms_stripe_section',
            __('Configuración de Stripe', 'breogan-lms'),
            array($this, 'stripe_section_callback'),
            'breogan-lms-pagos'
        );
        
        // Campos Stripe
        add_settings_field(
            'blms_stripe_public_key',
            __('Clave Pública de Stripe', 'breogan-lms'),
            array($this, 'stripe_public_key_callback'),
            'breogan-lms-pagos',
            'blms_stripe_section'
        );
        
        add_settings_field(
            'blms_stripe_secret_key',
            __('Clave Secreta de Stripe', 'breogan-lms'),
            array($this, 'stripe_secret_key_callback'),
            'breogan-lms-pagos',
            'blms_stripe_section'
        );
        
        // Sección PayPal
        add_settings_section(
            'blms_paypal_section',
            __('Configuración de PayPal', 'breogan-lms'),
            array($this, 'paypal_section_callback'),
            'breogan-lms-pagos'
        );
        
        // Campos PayPal
        add_settings_field(
            'blms_paypal_client_id',
            __('Client ID de PayPal', 'breogan-lms'),
            array($this, 'paypal_client_id_callback'),
            'breogan-lms-pagos',
            'blms_paypal_section'
        );
        
        add_settings_field(
            'blms_paypal_secret',
            __('Clave Secreta de PayPal', 'breogan-lms'),
            array($this, 'paypal_secret_callback'),
            'breogan-lms-pagos',
            'blms_paypal_section'
        );
        
        add_settings_field(
            'blms_paypal_sandbox',
            __('Modo Sandbox', 'breogan-lms'),
            array($this, 'paypal_sandbox_callback'),
            'breogan-lms-pagos',
            'blms_paypal_section'
        );
    }
    
    /**
     * Callback para sección Stripe
     */
    public function stripe_section_callback() {
        echo '<p>' . __('Configura tus claves API de Stripe para procesar pagos.', 'breogan-lms') . '</p>';
    }
    
    /**
     * Callback para clave pública de Stripe
     */
    public function stripe_public_key_callback() {
        $value = get_option('blms_stripe_public_key', '');
        echo '<input type="text" name="blms_stripe_public_key" value="' . esc_attr($value) . '" class="regular-text">';
    }
    
    /**
     * Callback para clave secreta de Stripe
     */
    public function stripe_secret_key_callback() {
        $value = get_option('blms_stripe_secret_key', '');
        echo '<input type="password" name="blms_stripe_secret_key" value="' . esc_attr($value) . '" class="regular-text">';
    }
    
    /**
     * Callback para sección PayPal
     */
    public function paypal_section_callback() {
        echo '<p>' . __('Configura tus credenciales de PayPal para procesar pagos.', 'breogan-lms') . '</p>';
    }
    
    /**
     * Callback para client ID de PayPal
     */
    public function paypal_client_id_callback() {
        $value = get_option('blms_paypal_client_id', '');
        echo '<input type="text" name="blms_paypal_client_id" value="' . esc_attr($value) . '" class="regular-text">';
    }
    
    /**
     * Callback para clave secreta de PayPal
     */
    public function paypal_secret_callback() {
        $value = get_option('blms_paypal_secret', '');
        echo '<input type="password" name="blms_paypal_secret" value="' . esc_attr($value) . '" class="regular-text">';
    }
    
    /**
     * Callback para modo sandbox de PayPal
     */
    public function paypal_sandbox_callback() {
        $value = get_option('blms_paypal_sandbox', 1);
        echo '<input type="checkbox" name="blms_paypal_sandbox" value="1" ' . checked(1, $value, false) . '>';
        echo '<span class="description">' . __('Habilitar modo sandbox para pruebas', 'breogan-lms') . '</span>';
    }
    
    /**
     * Procesar pago con Stripe
     */
    public function process_stripe_payment() {
        // Verificar nonce para seguridad
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'blms_payment_nonce')) {
            wp_send_json_error(array('message' => __('Error de seguridad', 'breogan-lms')));
        }
        
        // Verificar datos necesarios
        if (!isset($_POST['curso_id']) || !isset($_POST['precio'])) {
            wp_send_json_error(array('message' => __('Datos incompletos', 'breogan-lms')));
        }
        
        $curso_id = intval($_POST['curso_id']);
        $precio = floatval($_POST['precio']) * 100; // Convertir a centavos
        
        try {
            // Cargar SDK de Stripe
            require_once BREOGAN_LMS_PATH . 'vendor/autoload.php';
            
            // Configurar Stripe
            \Stripe\Stripe::setApiKey(get_option('blms_stripe_secret_key'));
            
            // Crear sesión de checkout
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => get_the_title($curso_id),
                        ],
                        'unit_amount' => $precio,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => add_query_arg(
                    array(
                        'blms_payment' => 'success',
                        'curso_id' => $curso_id,
                        'token' => wp_create_nonce('blms_payment_success')
                    ),
                    get_permalink($curso_id)
                ),
                'cancel_url' => add_query_arg('blms_payment', 'cancel', get_permalink($curso_id)),
            ]);
            
            // Devolver URL de redirección
            wp_send_json_success(array(
                'redirect_url' => $session->url
            ));
            
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => $e->getMessage()
            ));
        }
    }
    
    /**
     * Procesar pago con PayPal
     */
    public function process_paypal_payment() {
        // Verificar nonce para seguridad
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'blms_payment_nonce')) {
            wp_send_json_error(array('message' => __('Error de seguridad', 'breogan-lms')));
        }
        
        // Verificar datos necesarios
        if (!isset($_POST['curso_id']) || !isset($_POST['precio'])) {
            wp_send_json_error(array('message' => __('Datos incompletos', 'breogan-lms')));
        }
        
        $curso_id = intval($_POST['curso_id']);
        $precio = floatval($_POST['precio']);
        
        // Determinar URL de PayPal según el modo
        $is_sandbox = get_option('blms_paypal_sandbox', 1);
        $paypal_url = $is_sandbox 
            ? 'https://www.sandbox.paypal.com/cgi-bin/webscr'
            : 'https://www.paypal.com/cgi-bin/webscr';
        
        // Email de negocio (debería configurarse en las opciones)
        $business_email = get_option('blms_paypal_client_id', '');
        
        // Construir parámetros de la URL
        $query_params = array(
            'cmd' => '_xclick',
            'business' => $business_email,
            'item_name' => get_the_title($curso_id),
            'amount' => $precio,
            'currency_code' => 'EUR',
            'return' => add_query_arg(
                array(
                    'blms_payment' => 'success',
                    'curso_id' => $curso_id,
                    'token' => wp_create_nonce('blms_payment_success')
                ),
                get_permalink($curso_id)
            ),
            'cancel_return' => add_query_arg('blms_payment', 'cancel', get_permalink($curso_id)),
            'notify_url' => rest_url('breogan-lms/v1/paypal-ipn')
        );
        
        // Construir URL completa
        $redirect_url = add_query_arg($query_params, $paypal_url);
        
        // Devolver URL de redirección
        wp_send_json_success(array(
            'redirect_url' => $redirect_url
        ));
    }
    
    /**
     * Registrar endpoint para IPN de PayPal
     */
    public function register_paypal_ipn_endpoint() {
        register_rest_route('breogan-lms/v1', '/paypal-ipn', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_paypal_ipn'),
            'permission_callback' => '__return_true'
        ));
    }
    
    /**
     * Manejar notificación IPN de PayPal
     * 
     * @param WP_REST_Request $request Objeto de solicitud
     * @return WP_REST_Response Respuesta
     */
    public function handle_paypal_ipn($request) {
        // Implementación de verificación IPN
        $params = $request->get_params();
        
        // Validar la transacción con PayPal
        $is_sandbox = get_option('blms_paypal_sandbox', 1);
        $paypal_url = $is_sandbox 
            ? 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr'
            : 'https://ipnpb.paypal.com/cgi-bin/webscr';
        
        // Preparar solicitud de verificación
        $params['cmd'] = '_notify-validate';
        
        $response = wp_remote_post($paypal_url, array(
            'method' => 'POST',
            'body' => $params,
            'timeout' => 60,
            'httpversion' => '1.1'
        ));
        
        if (is_wp_error($response)) {
            error_log('Error en IPN de PayPal: ' . $response->get_error_message());
            return rest_ensure_response(array('status' => 'error'));
        }
        
        $body = wp_remote_retrieve_body($response);
        
        // Si la transacción es válida y está completada
        if ($body === 'VERIFIED' && isset($params['payment_status']) && $params['payment_status'] === 'Completed') {
            // Procesar el pago exitoso
            if (isset($params['item_number'])) {
                $curso_id = intval($params['item_number']);
                $email = sanitize_email($params['payer_email']);
                $user = get_user_by('email', $email);
                
                if ($user) {
                    // Marcar el curso como comprado para este usuario
                    $this->register_course_purchase($user->ID, $curso_id);
                }
            }
        }
        
        return rest_ensure_response(array('status' => 'ok'));
    }
    
    /**
     * Registrar compra de curso
     * 
     * @param int $user_id ID del usuario
     * @param int $curso_id ID del curso
     */
    public function register_course_purchase($user_id, $curso_id) {
    // Marcar el curso como comprado (ambos prefijos)
    update_user_meta($user_id, 'blms_curso_' . $curso_id, 'comprado');
    update_user_meta($user_id, 'breogan_curso_' . $curso_id, 'comprado');
    
    // Obtener temas relacionados con el curso (verificar ambos métodos)
    $temas = get_posts(array(
        'post_type' => 'blms_tema',
        'meta_key' => '_blms_curso_relacionado',
        'meta_value' => $curso_id,
        'numberposts' => -1
    ));
    
    // Si no hay temas con el nuevo tipo de post, intentar con el antiguo
    if (empty($temas)) {
        $temas = get_posts(array(
            'post_type' => 'temas',
            'meta_key' => '_curso_relacionado',
            'meta_value' => $curso_id,
            'numberposts' => -1
        ));
    }
    
    // Dar acceso a todos los temas
    foreach ($temas as $tema) {
        // Guardar con ambos prefijos
        update_user_meta($user_id, 'blms_tema_' . $tema->ID, 'acceso');
        update_user_meta($user_id, 'breogan_tema_' . $tema->ID, 'acceso');
        
        // Obtener lecciones relacionadas con el tema (verificar ambos métodos)
        $lecciones = get_posts(array(
            'post_type' => 'blms_leccion',
            'meta_key' => '_blms_tema_relacionado',
            'meta_value' => $tema->ID,
            'numberposts' => -1
        ));
        
        // Si no hay lecciones con el nuevo tipo de post, intentar con el antiguo
        if (empty($lecciones)) {
            $lecciones = get_posts(array(
                'post_type' => 'lecciones',
                'meta_key' => '_tema_relacionado',
                'meta_value' => $tema->ID,
                'numberposts' => -1
            ));
        }
        
        // Dar acceso a todas las lecciones
        foreach ($lecciones as $leccion) {
            // Guardar con ambos prefijos
            update_user_meta($user_id, 'blms_leccion_' . $leccion->ID, 'acceso');
            update_user_meta($user_id, 'breogan_leccion_' . $leccion->ID, 'acceso');
        }
    }
    return true;
}
}