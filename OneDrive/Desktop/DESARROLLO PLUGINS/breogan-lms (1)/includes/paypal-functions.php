<?php
/**
 * Controlador AJAX simplificado para PayPal
 * 
 * Esta función procesa las solicitudes AJAX para pagos con PayPal
 * Compatible con la versión actual de tu plugin.
 */
    function breogan_procesar_pago_paypal_ajax() {
    // Habilitar registro de depuración
    error_log("🔹 PayPal AJAX ejecutado");
    
    // Verificar datos necesarios
    if (!isset($_POST['curso_id']) || !isset($_POST['precio'])) {
        error_log("❌ ERROR: Datos incompletos en la solicitud PayPal");
        wp_send_json_error(array('error' => 'Datos incompletos para PayPal.'));
        exit;
    }
    
    $curso_id = intval($_POST['curso_id']);
    $precio = floatval($_POST['precio']);
    
    if ($curso_id <= 0) {
        error_log("❌ ERROR: ID de curso inválido: $curso_id");
        wp_send_json_error(array('error' => 'ID de curso inválido'));
        exit;
    }
    
    error_log("🔹 Procesando pago para curso ID: $curso_id, Precio: $precio");
    
    // Determinar URL de PayPal según modo
    $is_sandbox = true; // Cambiar a false para producción
    $paypal_url = $is_sandbox 
        ? 'https://www.sandbox.paypal.com/cgi-bin/webscr'
        : 'https://www.paypal.com/cgi-bin/webscr';
    
    // Email de comerciante - ACTUALIZAR CON TU EMAIL
    $merchant_email = 'sb-vr4bb26708895@business.example.com';
    
    // Generar token único para esta transacción
    $custom_token = wp_generate_password(32, false);
    update_post_meta($curso_id, '_breogan_paypal_token_' . $custom_token, array(
        'user_id' => get_current_user_id(),
        'price' => $precio,
        'timestamp' => time()
    ));
    
    // Construir URLs de retorno
    $return_url = add_query_arg(
        array(
            'curso_id' => $curso_id,
            'token' => $custom_token,
            'pago' => 'exitoso'
        ),
        get_permalink($curso_id)
    );
    
    $cancel_url = add_query_arg('pago', 'fallido', get_permalink($curso_id));
    $notify_url = home_url('/?breogan_ipn=1');
    
    // Construir parámetros de PayPal
    $query_params = array(
        'cmd' => '_xclick',
        'business' => $merchant_email,
        'item_name' => get_the_title($curso_id),
        'item_number' => $curso_id,
        'amount' => $precio,
        'currency_code' => 'EUR',
        'custom' => $custom_token,
        'return' => $return_url,
        'cancel_return' => $cancel_url,
        'notify_url' => $notify_url,
        'no_shipping' => '1',
        'no_note' => '1'
    );
    
    // Construir URL completa
    $redirect_url = add_query_arg($query_params, $paypal_url);
    
    error_log("✅ URL de redirección PayPal: $redirect_url");
    
    // Enviar URL para redirección
    wp_send_json_success(array('redirect_url' => $redirect_url));
    exit;
}

// Registrar los hooks de AJAX
add_action('wp_ajax_breogan_procesar_pago_paypal_ajax', 'breogan_procesar_pago_paypal_ajax');
add_action('wp_ajax_nopriv_breogan_procesar_pago_paypal_ajax', 'breogan_procesar_pago_paypal_ajax');
