<?php
// Evitar acceso directo
if (!defined('ABSPATH')) exit;

// 🔹 Forzar inicio de sesión en `admin-ajax.php`
if (!session_id()) {
    session_start();
    error_log("🔹 Sesión iniciada manualmente en functions.php");
}

// 🔹 Registrar Acciones para Procesar Pagos con AJAX
add_action('wp_ajax_breogan_procesar_pago_stripe_ajax', 'breogan_procesar_pago_stripe_ajax');
add_action('wp_ajax_nopriv_breogan_procesar_pago_stripe_ajax', 'breogan_procesar_pago_stripe_ajax');

add_action('wp_ajax_breogan_procesar_pago_paypal_ajax', 'breogan_procesar_pago_paypal_ajax');
add_action('wp_ajax_nopriv_breogan_procesar_pago_paypal_ajax', 'breogan_procesar_pago_paypal_ajax');

// 🔹 Acción de prueba para verificar sesión en `admin-ajax.php`
function breogan_verificar_sesion_ajax() {
    error_log("🔹 Verificando sesión en admin-ajax.php");

    // Mostrar todas las cookies disponibles
    error_log("🔹 Cookies Recibidas: " . print_r($_COOKIE, true));

    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        error_log("✅ Usuario autenticado: " . $user->user_login);
        wp_send_json_success(['usuario' => $user->user_login]);
    } else {
        error_log("❌ ERROR: Usuario no autenticado en AJAX.");
        wp_send_json_error(['error' => 'Usuario no autenticado.']);
    }
}
add_action('wp_ajax_breogan_verificar_sesion', 'breogan_verificar_sesion_ajax');
add_action('wp_ajax_nopriv_breogan_verificar_sesion', 'breogan_verificar_sesion_ajax');

// 🔹 Cargar Stripe Autoload
require_once plugin_dir_path(__FILE__) . '../vendor/autoload.php';

/** 🔹 Función para Procesar Pago con Stripe (AJAX) */
function breogan_procesar_pago_stripe_ajax() {
    error_log("🔹 Stripe AJAX ejecutado.");
    
    if (!isset($_POST['curso_id']) || !isset($_POST['precio'])) {
        error_log("❌ ERROR: Datos faltantes en AJAX Stripe.");
        wp_send_json_error(['error' => 'Datos faltantes para Stripe.']);
    }

    $curso_id = intval($_POST['curso_id']);
    $precio = floatval($_POST['precio']) * 100; // Convertir a centavos

    \Stripe\Stripe::setApiKey(get_option('breogan_stripe_secret_key'));
    error_log("🔹 Clave secreta de Stripe cargada.");

    try {
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => ['name' => get_the_title($curso_id)],
                    'unit_amount' => $precio,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => get_permalink($curso_id) . '?pago=exitoso',
            'cancel_url' => get_permalink($curso_id) . '?pago=fallido',
        ]);

        error_log("✅ Redirigiendo a Stripe: " . $session->url);
        wp_send_json_success(['redirect_url' => $session->url]);
    } catch (Exception $e) {
        error_log("❌ ERROR en Stripe: " . $e->getMessage());
        wp_send_json_error(['error' => 'Error en Stripe: ' . $e->getMessage()]);
    }
}

/** 🔹 Función para Procesar Pago con PayPal (AJAX) */
function breogan_procesar_pago_paypal_ajax() {
    error_log("🔹 PayPal AJAX ejecutado.");

    if (!isset($_POST['curso_id']) || !isset($_POST['precio'])) {
        error_log("❌ ERROR: Datos faltantes en AJAX PayPal.");
        wp_send_json_error(['error' => 'Datos faltantes en AJAX PayPal.']);
        exit;
    }

    $curso_id = intval($_POST['curso_id']);
    $precio = floatval($_POST['precio']);

    if ($curso_id <= 0) {
        error_log("❌ ERROR: curso_id inválido.");
        wp_send_json_error(['error' => 'Datos inválidos en PayPal.']);
    }

    $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr"; // Para pruebas
    $business_email = "pequeinados@gmail.com"; // Cambia esto a tu cuenta Business real

    $query_params = [
        'cmd'            => '_xclick',
        'business'       => $business_email,
        'item_name'      => get_the_title($curso_id),
        'amount'         => $precio,
        'currency_code'  => 'EUR',
        'return'         => home_url('/registro-usuario/?curso_id=' . $curso_id),
        'cancel_return'  => get_permalink($curso_id) . '?pago=fallido',
        'notify_url'     => home_url('/wp-json/breogan-lms/v1/paypal-ipn') // IPN para validación
    ];

    error_log("🔹 Parámetros enviados a PayPal: " . print_r($query_params, true));

    $query_string = http_build_query($query_params);
    $redirect_url = $paypal_url . '?' . $query_string;
    
    error_log("✅ Redirigiendo a PayPal: " . $redirect_url);
    
    wp_send_json_success(['redirect_url' => $redirect_url]);
    exit;
}





/** 🔹 Función AJAX para Simular Compra (Prueba) */
function breogan_procesar_pago_ajax() {
    error_log("🔹 Simulación de pago ejecutada.");

    if (!isset($_POST['curso_id']) || !isset($_POST['precio'])) {
        error_log("❌ ERROR: Datos faltantes en AJAX.");
        wp_send_json_error(['error' => 'Datos faltantes.']);
    }

    $curso_id = intval($_POST['curso_id']);
    $precio = floatval($_POST['precio']);

    if ($curso_id <= 0) {
        error_log("❌ ERROR: curso_id inválido.");
        wp_send_json_error(['error' => 'Curso no encontrado.']);
    }

    $redirect_url = get_permalink($curso_id) . '?pago=exitoso';
    error_log("✅ Redirigiendo a: " . $redirect_url);

    wp_send_json_success(['redirect_url' => $redirect_url]);
}

add_action('wp_ajax_breogan_procesar_pago_ajax', 'breogan_procesar_pago_ajax');
add_action('wp_ajax_nopriv_breogan_procesar_pago_ajax', 'breogan_procesar_pago_ajax');

function breogan_registrar_usuario_tras_pago($email, $nombre, $curso_id) {
    error_log("🔹 Registrando usuario con email: $email");

    // Verificar si el usuario ya existe
    if (email_exists($email)) {
        $user = get_user_by('email', $email);
        error_log("✅ Usuario ya registrado: " . $user->ID);
    } else {
        // Generar una contraseña aleatoria
        $password = wp_generate_password();
        
        // Crear el usuario en WordPress
        $user_id = wp_create_user($email, $password, $email);

        // Actualizar el perfil con el nombre
        wp_update_user([
            'ID'           => $user_id,
            'display_name' => $nombre,
            'role'         => 'subscriber'
        ]);

        // Enviar un email con los datos de acceso
        $subject = "Tu cuenta en Escuela de Madres";
        $message = "Hola $nombre,\n\nTu cuenta ha sido creada exitosamente.\n\nUsuario: $email\nContraseña: (la que elegiste)\n\nAccede aquí: " . wp_login_url();
        wp_mail($email, $subject, $message);

        error_log("✅ Usuario registrado: $user_id");
    }

    // Asignar acceso al curso
    update_user_meta($user_id, 'breogan_curso_' . $curso_id, 'comprado');
    error_log("✅ Acceso al curso $curso_id asignado.");

    // Obtener los Temas relacionados con el Curso
    $temas = get_posts([
        'post_type'   => 'temas',
        'meta_key'    => '_curso_relacionado',
        'meta_value'  => $curso_id,
        'numberposts' => -1
    ]);

    foreach ($temas as $tema) {
        update_user_meta($user_id, 'breogan_tema_' . $tema->ID, 'acceso');
        error_log("✅ Acceso al Tema {$tema->ID} asignado.");

        // Obtener las Lecciones relacionadas con el Tema
        $lecciones = get_posts([
            'post_type'   => 'lecciones',
            'meta_key'    => '_tema_relacionado',
            'meta_value'  => $tema->ID,
            'numberposts' => -1
        ]);

        foreach ($lecciones as $leccion) {
            update_user_meta($user_id, 'breogan_leccion_' . $leccion->ID, 'acceso');
            error_log("✅ Acceso a la Lección {$leccion->ID} asignado.");
        }
    }

    return $user_id;
}

