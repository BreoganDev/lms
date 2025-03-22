<?php
// Evitar acceso directo
if (!defined('ABSPATH')) exit;

// 🔹 Forzar inicio de sesión en `admin-ajax.php`
if (session_status() == PHP_SESSION_NONE) {
    session_start();
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