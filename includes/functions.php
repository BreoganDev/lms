<?php
// Evitar acceso directo
if (!defined('ABSPATH')) exit;

// 🔹 Forzar inicio de sesión en `admin-ajax.php`
<<<<<<< HEAD
if (!session_id()) {
    session_start();
    error_log("🔹 Sesión iniciada manualmente en functions.php");
=======
<<<<<<< HEAD
if (session_status() == PHP_SESSION_NONE) {
    session_start();
=======
if (!session_id()) {
    session_start();
    error_log("🔹 Sesión iniciada manualmente en functions.php");
>>>>>>> 12ee31a27decda5eba9c768c4e10372ecba265b3
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
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

<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
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



>>>>>>> 12ee31a27decda5eba9c768c4e10372ecba265b3


>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
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

<<<<<<< HEAD
// Funciones para registrar usuario y curso tras pago exitoso
=======
<<<<<<< HEAD
function breogan_paypal_ipn_handler() {
    // Registrar los datos recibidos
    error_log("🔹 Recibiendo IPN de PayPal");
    error_log("🔹 Datos recibidos: " . print_r($_POST, true));

    // Validar la transacción con PayPal
    $raw_post_data = file_get_contents('php://input');
    $raw_post_array = explode('&', $raw_post_data);
    $myPost = array();
    foreach ($raw_post_array as $keyval) {
        $keyval = explode('=', $keyval);
        if (count($keyval) == 2)
            $myPost[$keyval[0]] = urldecode($keyval[1]);
    }

    // Añadir comando para validación
    $req = 'cmd=_notify-validate';
    foreach ($myPost as $key => $value) {
        $value = urlencode($value);
        $req .= "&$key=$value";
    }

    // Enviar solicitud de validación a PayPal
    $ch = curl_init('https://ipnpb.paypal.com/cgi-bin/webscr');
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

    $res = curl_exec($ch);

    if (strcmp($res, "VERIFIED") == 0) {
        // Verificar detalles de la transacción
        $curso_id = isset($_POST['item_number']) ? intval($_POST['item_number']) : 0;
        $payment_status = isset($_POST['payment_status']) ? $_POST['payment_status'] : '';
        $received_amount = isset($_POST['mc_gross']) ? floatval($_POST['mc_gross']) : 0;
        $receiver_email = isset($_POST['receiver_email']) ? $_POST['receiver_email'] : '';

        error_log("✅ Pago verificado para curso: $curso_id");
        error_log("✅ Estado del pago: $payment_status");
        error_log("✅ Monto recibido: $received_amount");

        // Aquí puedes añadir lógica para registrar el acceso al curso
        if ($payment_status == 'Completed') {
            // Registrar acceso al curso para el usuario
            breogan_registrar_usuario_tras_pago(
                $_POST['payer_email'], 
                $_POST['first_name'] . ' ' . $_POST['last_name'], 
                $curso_id
            );
        }
    } else {
        error_log("❌ ERROR: IPN no verificado");
    }

    // Responder a PayPal
    http_response_code(200);
    exit;
}
add_action('rest_api_init', function () {
    register_rest_route('breogan-lms/v1', '/paypal-ipn', array(
        'methods' => 'POST',
        'callback' => 'breogan_paypal_ipn_handler',
        'permission_callback' => '__return_true'
    ));
});

=======
>>>>>>> 12ee31a27decda5eba9c768c4e10372ecba265b3
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
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
<<<<<<< HEAD
        $subject = "Tu cuenta en Rocio de Mayo";
$message = "Hola $nombre,\n\nTu cuenta ha sido creada exitosamente.\n\nUsuario: $email\nContraseña: $password\n\nAccede aquí: " . wp_login_url();
wp_mail($email, $subject, $message);
=======
        $subject = "Tu cuenta en Escuela de Madres";
        $message = "Hola $nombre,\n\nTu cuenta ha sido creada exitosamente.\n\nUsuario: $email\nContraseña: (la que elegiste)\n\nAccede aquí: " . wp_login_url();
        wp_mail($email, $subject, $message);
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858

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

<<<<<<< HEAD
// Manejador de IPN de PayPal
function breogan_paypal_ipn_handler() {
    // Verificar si es una solicitud IPN
    if (isset($_GET['breogan_ipn'])) {
        error_log("🔹 Recibiendo IPN de PayPal");
        
        // Obtener los datos POST sin procesar
        $raw_post_data = file_get_contents('php://input');
        error_log("🔹 Datos IPN recibidos: " . $raw_post_data);
        
        // Verificar con PayPal
        $args = array(
            'body' => "cmd=_notify-validate&" . $raw_post_data,
            'timeout' => 30,
            'httpversion' => '1.1',
            'user-agent' => 'BreoganLMS/1.0'
        );
        
        $is_sandbox = get_option('breogan_paypal_sandbox', '1') === '1';
        $verify_url = $is_sandbox 
            ? 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr'
            : 'https://ipnpb.paypal.com/cgi-bin/webscr';
        
        $response = wp_remote_post($verify_url, $args);
        
        if (is_wp_error($response)) {
            error_log("❌ ERROR en verificación IPN: " . $response->get_error_message());
            status_header(500);
            exit;
        }
        
        $body = wp_remote_retrieve_body($response);
        
        if (strcmp($body, "VERIFIED") == 0) {
            error_log("✅ IPN verificado");

        update_user_meta($user_id, 'breogan_curso_' . $curso_id, 'comprado');
error_log("✅ Acceso al curso $curso_id asignado.");

// Añade esta línea para enviar las credenciales
breogan_enviar_credenciales_email($user_id, $curso_id);

            // Procesar los datos del pago
            parse_str($raw_post_data, $ipn_data);
            
           // Verificar datos esenciales
            if (isset($ipn_data['payment_status']) && 
                $ipn_data['payment_status'] == 'Completed' && 
                isset($ipn_data['custom']) && 
                isset($ipn_data['item_number']) &&
                isset($ipn_data['payer_email'])) {
                
                $custom = sanitize_text_field($ipn_data['custom']);
                $curso_id = intval($ipn_data['item_number']);
                $payer_email = sanitize_email($ipn_data['payer_email']);
                
                // Usar nuestra función central para registrar la compra
                breogan_registrar_compra(
                    $payer_email,
                    $curso_id,
                    'paypal',
                    array(
                        'transaction_id' => isset($ipn_data['txn_id']) ? $ipn_data['txn_id'] : '',
                        'nombre' => isset($ipn_data['first_name']) ? 
                            $ipn_data['first_name'] . ' ' . (isset($ipn_data['last_name']) ? $ipn_data['last_name'] : '') : '',
                        'monto' => isset($ipn_data['mc_gross']) ? $ipn_data['mc_gross'] : '',
                        'moneda' => isset($ipn_data['mc_currency']) ? $ipn_data['mc_currency'] : ''
                    )
                );
                
                // Limpiar token usado
                delete_post_meta($curso_id, '_breogan_paypal_token_' . $custom);
                
                error_log("✅ Compra registrada correctamente para email: $payer_email, curso: $curso_id");
            } else {
                error_log("❌ ERROR: Datos incompletos en IPN");
            }
        } else {
            error_log("❌ ERROR: IPN no verificado");
        }
        
        status_header(200);
        exit;
    }
}
add_action('template_redirect', 'breogan_paypal_ipn_handler');

// Funciones para las páginas de configuración
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

function breogan_paypal_email_callback() {
    $valor = get_option('breogan_paypal_email', '');
    echo "<input type='email' name='breogan_paypal_email' value='" . esc_attr($valor) . "' class='regular-text'>";
    echo '<p class="description">Email de tu cuenta de PayPal Business</p>';
}

function breogan_paypal_sandbox_callback() {
    $valor = get_option('breogan_paypal_sandbox', '1');
    echo "<input type='checkbox' name='breogan_paypal_sandbox' value='1' " . checked('1', $valor, false) . ">";
    echo '<span class="description">Habilitar modo sandbox para pruebas</span>';
}

// Manejador para verificar pagos después de redirección de PayPal
function breogan_check_payment_return() {
    if (!is_singular('cursos')) {
        return;
    }
    
    $curso_id = get_the_ID();
    
    // Verificar pago exitoso
    if (isset($_GET['pago']) && $_GET['pago'] == 'exitoso' && isset($_GET['token'])) {
        $token = sanitize_text_field($_GET['token']);
        
        // Si el usuario no está logueado, redirigir a login
        if (!is_user_logged_in()) {
            if (!session_id()) {
                session_start();
            }
            
            $_SESSION['breogan_pending_payment'] = array(
                'curso_id' => $curso_id,
                'token' => $token
            );
            
            wp_redirect(wp_login_url(get_permalink($curso_id)));
            exit;
        }
        
        // Verificar token y registrar compra
        $token_data = get_post_meta($curso_id, '_breogan_paypal_token_' . $token, true);
        
        if (!empty($token_data)) {
            $user_id = get_current_user_id();
            
            // Registrar compra para usuario autenticado
            update_user_meta($user_id, 'breogan_curso_' . $curso_id, 'comprado');
            // Añade esta línea
breogan_enviar_credenciales_email($user_id, $curso_id);

// Redirigir para limpiar URL
wp_redirect(add_query_arg('compra', 'completada', get_permalink($curso_id)));
exit;

            // Asignar acceso al curso completo
            breogan_asignar_acceso_curso($user_id, $curso_id);
            
            // Enviar email de confirmación
            breogan_enviar_email_compra($user_id, $curso_id);
            
            // Limpiar token usado
            delete_post_meta($curso_id, '_breogan_paypal_token_' . $token);
            
            // Redirigir para limpiar URL
            wp_redirect(add_query_arg('compra', 'completada', get_permalink($curso_id)));
            exit;
        }
    }
    
    // Verificar compra pendiente después de login
    if (is_user_logged_in() && isset($_SESSION['breogan_pending_payment'])) {
        $pending = $_SESSION['breogan_pending_payment'];
        $curso_id = $pending['curso_id'];
        $token = $pending['token'];
        
        // Verificar token y registrar compra
        $token_data = get_post_meta($curso_id, '_breogan_paypal_token_' . $token, true);
        
        if (!empty($token_data)) {
            $user_id = get_current_user_id();
            
            // Registrar compra
            update_user_meta($user_id, 'breogan_curso_' . $curso_id, 'comprado');
            error_log("✅ Acceso al curso $curso_id asignado.");

// Añade esta línea para enviar las credenciales
breogan_enviar_credenciales_email($user_id, $curso_id);
            
            // Asignar acceso
            breogan_asignar_acceso_curso($user_id, $curso_id);
            
            // Limpiar token usado
            delete_post_meta($curso_id, '_breogan_paypal_token_' . $token);
        }
        
        // Limpiar sesión
        unset($_SESSION['breogan_pending_payment']);
        
        // Redirigir para limpiar URL
        wp_redirect(add_query_arg('compra', 'completada', get_permalink($curso_id)));
        exit;
    }
    
    // Mostrar mensaje después de compra completada
    if (isset($_GET['compra']) && $_GET['compra'] == 'completada') {
        add_action('breogan_before_content', function() {
            echo '<div class="mensaje-exito">';
            echo '<p>¡Compra completada con éxito! Ya tienes acceso a todo el contenido del curso.</p>';
            echo '</div>';
        });
    }
}
add_action('template_redirect', 'breogan_check_payment_return');

// Hook para mostrar mensajes
function breogan_display_before_content() {
    do_action('breogan_before_content');
}

// Registrar menú de configuración
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

// Contenido de la página de configuración
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

// Registrar configuración
function breogan_lms_registrar_configuracion() {
    register_setting('breogan_lms_pagos', 'breogan_stripe_public_key');
    register_setting('breogan_lms_pagos', 'breogan_stripe_secret_key');
    register_setting('breogan_lms_pagos', 'breogan_paypal_email');
    register_setting('breogan_lms_pagos', 'breogan_paypal_sandbox');

    add_settings_section('breogan_lms_stripe', 'Configuración de Stripe', null, 'breogan-lms-pagos');
    add_settings_field('breogan_stripe_public_key', 'Clave Pública de Stripe', 'breogan_stripe_public_key_callback', 'breogan-lms-pagos', 'breogan_lms_stripe');
    add_settings_field('breogan_stripe_secret_key', 'Clave Secreta de Stripe', 'breogan_stripe_secret_key_callback', 'breogan-lms-pagos', 'breogan_lms_stripe');

    add_settings_section('breogan_lms_paypal', 'Configuración de PayPal', null, 'breogan-lms-pagos');
    add_settings_field('breogan_paypal_email', 'Email de PayPal Business', 'breogan_paypal_email_callback', 'breogan-lms-pagos', 'breogan_lms_paypal');
    add_settings_field('breogan_paypal_sandbox', 'Modo Sandbox', 'breogan_paypal_sandbox_callback', 'breogan-lms-pagos', 'breogan_lms_paypal');
}
add_action('admin_init', 'breogan_lms_registrar_configuracion');

/**
 * Compatibilidad entre diferentes nomenclaturas de metadatos
 * Esta función asegura que independientemente de qué prefijo se use,
 * se pueda acceder a los datos de compra de curso
 */
function breogan_check_course_purchase($user_id, $curso_id) {
    // Comprobar con prefijo breogan_
    $comprado_breogan = get_user_meta($user_id, 'breogan_curso_' . $curso_id, true);
    if ($comprado_breogan == 'comprado') {
        return true;
    }
    
    // Comprobar con prefijo blms_
    $comprado_blms = get_user_meta($user_id, 'blms_curso_' . $curso_id, true);
    if ($comprado_blms == 'comprado') {
        return true;
    }
    
    return false;
}

/**
 * Enviar correo con credenciales después de compra
 */
function breogan_enviar_correo_credenciales($user_id, $curso_id) {
    $user = get_userdata($user_id);
    if (!$user) return false;
    
    // Generar contraseña nueva
    $password = wp_generate_password(12, false);
    wp_set_password($password, $user_id);
    
    $to = $user->user_email;
    $site_name = get_bloginfo('name');
    $site_url = site_url();
    $curso_title = get_the_title($curso_id);
    
    $subject = "Acceso a tu curso en $site_name";
    
    $message = "¡Hola!\n\n";
    $message .= "Gracias por tu compra del curso \"$curso_title\" en $site_name.\n\n";
    $message .= "====== TUS CREDENCIALES DE ACCESO ======\n\n";
    $message .= "Usuario: " . $user->user_login . "\n";
    $message .= "Contraseña: " . $password . "\n\n";
    $message .= "URL de acceso: " . wp_login_url() . "\n\n";
    $message .= "Para ver tus cursos, visita tu perfil aquí:\n";
    $message .= site_url('/mi-perfil/') . "\n\n";
    $message .= "Saludos,\n";
    $message .= "$site_name";
    
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    
    // Enviar email
    $sent = wp_mail($to, $subject, $message, $headers);
    
    // Registrar en el log
    if ($sent) {
        error_log("✅ Email con credenciales enviado a $to");
    } else {
        error_log("❌ Error al enviar email a $to");
    }
    
    return $sent;
}

/**
 * Enviar correo con credenciales (versión mejorada)
 */
function breogan_enviar_credenciales_email($user_id, $curso_id) {
    // Registrar inicio de función en el log
    error_log("🔹 Iniciando breogan_enviar_credenciales_email para usuario ID: $user_id, curso ID: $curso_id");
    
    // Obtener datos del usuario
    $user = get_userdata($user_id);
    if (!$user) {
        error_log("❌ No se encontró el usuario con ID: $user_id");
        return false;
    }
    
    // Generar una nueva contraseña
    $password = wp_generate_password(12, false);
    wp_set_password($password, $user_id);
    error_log("✅ Contraseña generada para usuario $user_id");
    
    // Datos para el correo
    $to = $user->user_email;
    $site_name = get_bloginfo('name');
    $curso_title = get_the_title($curso_id);
    $login_url = wp_login_url();
    
    $subject = "🔑 Acceso a tu curso en $site_name";
    
    $message = "¡Hola " . $user->display_name . "!\n\n";
    $message .= "Gracias por tu compra del curso \"$curso_title\" en $site_name.\n\n";
    $message .= "======================================\n";
    $message .= "CREDENCIALES DE ACCESO\n";
    $message .= "======================================\n\n";
    $message .= "Usuario: " . $user->user_login . "\n";
    $message .= "Contraseña: " . $password . "\n\n";
    $message .= "URL de acceso: " . $login_url . "\n\n";
    $message .= "Te recomendamos cambiar tu contraseña después de iniciar sesión.\n\n";
    $message .= "Saludos,\n";
    $message .= "El equipo de $site_name";
    
    // Cabeceras del correo
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . $site_name . ' <' . get_option('admin_email') . '>'
    );
    
    // Guardar una copia del correo para debug
    $log_dir = WP_CONTENT_DIR . '/breogan-logs';
    if (!file_exists($log_dir)) {
        wp_mkdir_p($log_dir);
    }
    $log_file = $log_dir . '/email-' . time() . '-' . $user_id . '.txt';
    file_put_contents($log_file, "To: $to\nSubject: $subject\n\n$message");
    error_log("✅ Copia del correo guardada en: $log_file");
    
    // Enviar el correo
    $sent = wp_mail($to, $subject, $message, $headers);
    
    // Registrar resultado
    if ($sent) {
        error_log("✅ Correo con credenciales enviado a: $to");
    } else {
        error_log("❌ Error al enviar correo a: $to");
    }
    
    return $sent;
}

/**
 * Versión mejorada para asignar acceso a un curso
 * 
 * @param int $user_id ID del usuario
 * @param int $curso_id ID del curso
 * @return bool Resultado de la operación
 */
function breogan_asignar_acceso_mejorado($user_id, $curso_id) {
    if (!$user_id || !$curso_id) {
        error_log("❌ Error: User ID o Curso ID inválidos en breogan_asignar_acceso_mejorado");
        return false;
    }
    
    error_log("🔹 Asignando acceso al usuario $user_id para el curso $curso_id");
    
    // Asignar acceso al curso con ambos prefijos para compatibilidad
    update_user_meta($user_id, 'breogan_curso_' . $curso_id, 'comprado');
    update_user_meta($user_id, 'blms_curso_' . $curso_id, 'comprado');
    
    // Obtener temas del curso
    $temas = get_posts([
        'post_type'   => 'temas',
        'meta_key'    => '_curso_relacionado',
        'meta_value'  => $curso_id,
        'numberposts' => -1
    ]);
    
    if (empty($temas)) {
        error_log("⚠️ No se encontraron temas para el curso $curso_id");
    }
    
    // Asignar acceso a temas y lecciones
    foreach ($temas as $tema) {
        // Asignar acceso al tema
        update_user_meta($user_id, 'breogan_tema_' . $tema->ID, 'acceso');
        update_user_meta($user_id, 'blms_tema_' . $tema->ID, 'acceso');
        
        // Obtener lecciones del tema
        $lecciones = get_posts([
            'post_type'   => 'lecciones',
            'meta_key'    => '_tema_relacionado',
            'meta_value'  => $tema->ID,
            'numberposts' => -1
        ]);
        
        if (empty($lecciones)) {
            error_log("⚠️ No se encontraron lecciones para el tema {$tema->ID}");
        }
        
        foreach ($lecciones as $leccion) {
            // Asignar acceso a la lección
            update_user_meta($user_id, 'breogan_leccion_' . $leccion->ID, 'acceso');
            update_user_meta($user_id, 'blms_leccion_' . $leccion->ID, 'acceso');
        }
    }
    
    error_log("✅ Acceso completo asignado al usuario $user_id para el curso $curso_id");
    return true;
}
=======
<<<<<<< HEAD
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

function breogan_configurar_paypal() {
    // Configuración del contexto de API
    $apiContext = new ApiContext(
        new OAuthTokenCredential(
            'AeWoGVm0EZb7DRWQbX4HtGmhlwMlrjQm-0PR0jmxJ_KfewlGovWyfdurDibxh5y5L3Gv1aHBu4ZZWt2q', // Client ID
            'EO0FYVdT6A-VXD-lOY3LW-MpYbr7tftGXiaPwzVDXBMmNhMgKYC66K1ijtZl3YYRECk4er-2WfESLgFi'  // Secret
        )
    );

    // Configurar modo Sandbox
    $apiContext->setConfig([
        'mode' => 'sandbox'
    ]);

    return $apiContext;
}

function breogan_procesar_pago_paypal($curso_id, $precio) {
    try {
        $apiContext = breogan_configurar_paypal();

        // Crear un payer
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        // Crear un item
        $item = new Item();
        $item->setName(get_the_title($curso_id))
             ->setCurrency('EUR')
             ->setQuantity(1)
             ->setPrice($precio);

        // Lista de items
        $itemList = new ItemList();
        $itemList->setItems([$item]);

        // Detalles del pago
        $details = new Details();
        $details->setSubtotal($precio);

        // Monto total
        $amount = new Amount();
        $amount->setCurrency('EUR')
               ->setTotal($precio)
               ->setDetails($details);

        // Transacción
        $transaction = new Transaction();
        $transaction->setAmount($amount)
                    ->setItemList($itemList)
                    ->setDescription('Compra de curso: ' . get_the_title($curso_id));

        // URLs de redirección
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(home_url('/registro-usuario/?curso_id=' . $curso_id))
                     ->setCancelUrl(get_permalink($curso_id) . '?pago=fallido');

        // Crear pago
        $payment = new Payment();
        $payment->setIntent('sale')
                ->setPayer($payer)
                ->setRedirectUrls($redirectUrls)
                ->setTransactions([$transaction]);

        // Crear pago en PayPal
        $payment->create($apiContext);

        // Obtener URL de aprobación
        $approvalUrl = $payment->getApprovalLink();

        // Guardar detalles del pago para verificación posterior
        update_post_meta($curso_id, '_ultimo_pago_paypal', $payment->getId());

        return $approvalUrl;

    } catch (Exception $ex) {
        error_log('Error en pago de PayPal: ' . $ex->getMessage());
        return false;
    }
}

// Modificar la función AJAX para usar la nueva implementación
function breogan_procesar_pago_paypal_ajax() {
    if (!isset($_POST['curso_id']) || !isset($_POST['precio'])) {
        wp_send_json_error(['error' => 'Datos incompletos']);
        exit;
    }

    $curso_id = intval($_POST['curso_id']);
    $precio = floatval($_POST['precio']);

    $redirect_url = breogan_procesar_pago_paypal($curso_id, $precio);

    if ($redirect_url) {
        wp_send_json_success(['redirect_url' => $redirect_url]);
    } else {
        wp_send_json_error(['error' => 'No se pudo procesar el pago']);
    }
}
add_action('wp_ajax_breogan_procesar_pago_paypal_ajax', 'breogan_procesar_pago_paypal_ajax');
add_action('wp_ajax_nopriv_breogan_procesar_pago_paypal_ajax', 'breogan_procesar_pago_paypal_ajax');
=======
>>>>>>> 12ee31a27decda5eba9c768c4e10372ecba265b3
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
