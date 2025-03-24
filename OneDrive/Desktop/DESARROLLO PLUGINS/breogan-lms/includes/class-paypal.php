<?php
/**
 * Clase para gestionar pagos con PayPal REST API
 * 
 * Esta clase proporciona una solución completa para integrar PayPal
 * en el plugin Breogan LMS, usando la API REST de PayPal.
 */
class Breogan_LMS_PayPal {
    
    /**
     * URL de la API de PayPal (Sandbox)
     */
    const SANDBOX_API_URL = 'https://api-m.sandbox.paypal.com';
    
    /**
     * URL de la API de PayPal (Producción)
     */
    const LIVE_API_URL = 'https://api-m.paypal.com';
    
    /**
     * Constructor
     */
    public function __construct() {
        // Registrar endpoints AJAX
        add_action('wp_ajax_breogan_procesar_pago_paypal_ajax', array($this, 'process_paypal_payment'));
        add_action('wp_ajax_nopriv_breogan_procesar_pago_paypal_ajax', array($this, 'process_paypal_payment'));
        
        // Opcionalmente, agregar un log para depuración
        if (defined('WP_DEBUG') && WP_DEBUG) {
            add_action('init', array($this, 'setup_debug_log'));
        }
    }
    
    /**
     * Configurar log de depuración
     */
    public function setup_debug_log() {
        if (!file_exists(WP_CONTENT_DIR . '/debug-paypal.log')) {
            @file_put_contents(WP_CONTENT_DIR . '/debug-paypal.log', "=== PayPal REST API Debug Log ===\n");
        }
    }
    
    /**
     * Escribir en el log de depuración
     * 
     * @param string $message Mensaje a loguear
     */
    private function log($message) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, WP_CONTENT_DIR . '/debug-paypal.log');
        }
    }
    
    /**
     * Obtener token de acceso para la API de PayPal
     * 
     * @return string|false Token de acceso o false en caso de error
     */
    private function get_access_token() {
        $is_sandbox = get_option('breogan_paypal_sandbox', '1') === '1';
        $api_url = $is_sandbox ? self::SANDBOX_API_URL : self::LIVE_API_URL;
        
        $client_id = get_option('breogan_paypal_client_id', '');
        $secret_key = get_option('breogan_paypal_secret', '');
        
        if (empty($client_id) || empty($secret_key)) {
            $this->log("❌ ERROR: Credenciales de PayPal no configuradas");
            return false;
        }
        
        $auth = base64_encode($client_id . ':' . $secret_key);
        
        $response = wp_remote_post($api_url . '/v1/oauth2/token', array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking' => true,
            'headers' => array(
                'Authorization' => "Basic {$auth}",
                'Content-Type' => 'application/x-www-form-urlencoded',
            ),
            'body' => 'grant_type=client_credentials'
        ));
        
        if (is_wp_error($response)) {
            $this->log("❌ ERROR: " . $response->get_error_message());
            return false;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['access_token'])) {
            return $body['access_token'];
        }
        
        $this->log("❌ ERROR: No se pudo obtener token de acceso: " . print_r($body, true));
        return false;
    }
    
    /**
     * Crear una orden de PayPal
     * 
     * @param string $access_token Token de acceso
     * @param int $curso_id ID del curso
     * @param float $precio Precio del curso
     * @return array|false Datos de la orden o false en caso de error
     */
    private function create_order($access_token, $curso_id, $precio) {
        $is_sandbox = get_option('breogan_paypal_sandbox', '1') === '1';
        $api_url = $is_sandbox ? self::SANDBOX_API_URL : self::LIVE_API_URL;
        
        // URLs de retorno
        $return_url = add_query_arg(
            array(
                'curso_id' => $curso_id,
                'paypal_action' => 'success'
            ),
            get_permalink($curso_id)
        );
        
        $cancel_url = add_query_arg(
            array(
                'curso_id' => $curso_id,
                'paypal_action' => 'cancel'
            ),
            get_permalink($curso_id)
        );
        
        // Datos de la orden
        $order_data = array(
            'intent' => 'CAPTURE',
            'purchase_units' => array(
                array(
                    'reference_id' => 'curso_' . $curso_id,
                    'description' => get_the_title($curso_id),
                    'amount' => array(
                        'currency_code' => 'EUR',
                        'value' => number_format($precio, 2, '.', '')
                    )
                )
            ),
            'application_context' => array(
                'brand_name' => get_bloginfo('name'),
                'return_url' => $return_url,
                'cancel_url' => $cancel_url,
                'user_action' => 'PAY_NOW',
                'shipping_preference' => 'NO_SHIPPING'
            )
        );
        
        $response = wp_remote_post($api_url . '/v2/checkout/orders', array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking' => true,
            'headers' => array(
                'Authorization' => "Bearer {$access_token}",
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode($order_data)
        ));
        
        if (is_wp_error($response)) {
            $this->log("❌ ERROR al crear orden: " . $response->get_error_message());
            return false;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (!isset($body['id'])) {
            $this->log("❌ ERROR: No se pudo crear la orden: " . print_r($body, true));
            return false;
        }
        
        // Guardar ID de orden para verificación posterior
        update_post_meta($curso_id, '_breogan_paypal_order_' . $body['id'], array(
            'user_id' => get_current_user_id(),
            'price' => $precio,
            'status' => 'created',
            'timestamp' => time()
        ));
        
        return $body;
    }
    
    /**
     * Procesar pago con PayPal
     */
    public function process_paypal_payment() {
        $this->log("🔹 Iniciando procesamiento de pago PayPal AJAX");
        
        // Verificar datos necesarios
        if (!isset($_POST['curso_id']) || !isset($_POST['precio'])) {
            $this->log("❌ ERROR: Datos incompletos en solicitud PayPal");
            wp_send_json_error(array('error' => 'Datos incompletos para PayPal.'));
            exit;
        }
        
        $curso_id = intval($_POST['curso_id']);
        $precio = floatval($_POST['precio']);
        
        if ($curso_id <= 0) {
            $this->log("❌ ERROR: ID de curso inválido: $curso_id");
            wp_send_json_error(array('error' => 'ID de curso inválido'));
            exit;
        }
        
        // Obtener token de acceso
        $access_token = $this->get_access_token();
        
        if (!$access_token) {
            wp_send_json_error(array('error' => 'No se pudo autenticar con PayPal. Verifica tus credenciales.'));
            exit;
        }
        
        // Crear orden
        $order = $this->create_order($access_token, $curso_id, $precio);
        
        if (!$order) {
            wp_send_json_error(array('error' => 'No se pudo crear la orden de PayPal.'));
            exit;
        }
        
        // Buscar URL de aprobación
        $approval_url = '';
        if (isset($order['links'])) {
            foreach ($order['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    $approval_url = $link['href'];
                    break;
                }
            }
        }
        
        if (empty($approval_url)) {
            $this->log("❌ ERROR: No se encontró URL de aprobación");
            wp_send_json_error(array('error' => 'No se pudo obtener la URL de aprobación de PayPal.'));
            exit;
        }
        
        $this->log("✅ Redirigiendo a PayPal: $approval_url");
        
        // Devolver URL para redirección
        wp_send_json_success(array('redirect_url' => $approval_url));
        exit;
    }
    
    /**
     * Capturar pago de orden
     * 
     * @param string $order_id ID de la orden
     * @return bool Resultado de la captura
     */
    public function capture_order($order_id) {
        // Obtener token de acceso
        $access_token = $this->get_access_token();
        
        if (!$access_token) {
            $this->log("❌ ERROR: No se pudo obtener token de acceso para capturar");
            return false;
        }
        
        $is_sandbox = get_option('breogan_paypal_sandbox', '1') === '1';
        $api_url = $is_sandbox ? self::SANDBOX_API_URL : self::LIVE_API_URL;
        
        $response = wp_remote_post($api_url . "/v2/checkout/orders/{$order_id}/capture", array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking' => true,
            'headers' => array(
                'Authorization' => "Bearer {$access_token}",
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation'
            ),
            'body' => '{}'
        ));
        
        if (is_wp_error($response)) {
            $this->log("❌ ERROR al capturar pago: " . $response->get_error_message());
            return false;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['status']) && $body['status'] === 'COMPLETED') {
            $this->log("✅ Pago capturado con éxito: " . $order_id);
            
            // Recuperar datos de la orden
            $order_data = get_post_meta(0, '_breogan_paypal_order_' . $order_id, true);
            
            if (!empty($order_data)) {
                $curso_id = 0;
                
                // Buscar curso_id basado en order_id
                global $wpdb;
                $meta_key = '_breogan_paypal_order_' . $order_id;
                $sql = $wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s LIMIT 1", $meta_key);
                $result = $wpdb->get_var($sql);
                
                if ($result) {
                    $curso_id = (int)$result;
                    $order_data = get_post_meta($curso_id, $meta_key, true);
                    
                    // Actualizar estado de la orden
                    $order_data['status'] = 'completed';
                    update_post_meta($curso_id, $meta_key, $order_data);
                    
                    // Registrar compra para el usuario
                    $user_id = $order_data['user_id'];
                    $this->register_course_purchase($user_id, $curso_id);
                    
                    return true;
                }
            }
            
            $this->log("⚠️ AVISO: No se encontraron datos para la orden: " . $order_id);
            return false;
        }
        
        $this->log("❌ ERROR: No se pudo capturar el pago. Estado: " . (isset($body['status']) ? $body['status'] : 'desconocido'));
        return false;
    }
    
    /**
     * Registrar compra de curso
     * 
     * @param int $user_id ID del usuario
     * @param int $curso_id ID del curso
     * @return bool Resultado
     */
    public function register_course_purchase($user_id, $curso_id) {
        if (empty($user_id) || empty($curso_id)) {
            return false;
        }
        
        // Marcar curso como comprado para el usuario
        update_user_meta($user_id, 'breogan_curso_' . $curso_id, 'comprado');
        
        // Obtener temas del curso
        $temas = get_posts(array(
            'post_type' => 'cursos', // o 'blms_curso' según tu implementación
            'meta_key' => '_curso_relacionado',
            'meta_value' => $curso_id,
            'numberposts' => -1
        ));
        
        // Dar acceso a todos los temas
        foreach ($temas as $tema) {
            update_user_meta($user_id, 'breogan_tema_' . $tema->ID, 'acceso');
            
            // Obtener lecciones del tema
            $lecciones = get_posts(array(
                'post_type' => 'lecciones', // o 'blms_leccion' según tu implementación
                'meta_key' => '_tema_relacionado',
                'meta_value' => $tema->ID,
                'numberposts' => -1
            ));
            
            // Dar acceso a todas las lecciones
            foreach ($lecciones as $leccion) {
                update_user_meta($user_id, 'breogan_leccion_' . $leccion->ID, 'acceso');
            }
        }
        
        return true;
    }
    
    /**
     * Verificar y capturar pago
     * 
     * @param string $order_id ID de la orden de PayPal
     * @return bool Resultado
     */
    public function verify_payment_return($order_id) {
        if (empty($order_id)) {
            return false;
        }
        
        return $this->capture_order($order_id);
    }
}