<?php
/**
 * Funciones para gestionar compras y usuarios
 * 
 * @package Breogan LMS
 */

// Evitar acceso directo
if (!defined('ABSPATH')) exit;

/**
 * Función central para registrar una compra y crear/actualizar usuario
 * 
 * Esta función maneja todo el proceso de:
 * - Verificar si el usuario existe
 * - Crear usuario si es necesario
 * - Asignar acceso al curso
 * - Enviar email de confirmación
 * 
 * @param string $email Email del comprador
 * @param int $curso_id ID del curso comprado
 * @param string $pasarela Nombre de la pasarela (paypal, stripe, etc.)
 * @param array $datos_extra Datos adicionales de la compra
 * @return int|bool ID del usuario o false en caso de error
 */
function breogan_registrar_compra($email, $curso_id, $pasarela = '', $datos_extra = []) {
    if (empty($email) || empty($curso_id)) {
        error_log("❌ Error en breogan_registrar_compra: Datos incompletos");
        return false;
    }
    
    error_log("🔹 Registrando compra para email: $email, curso: $curso_id, pasarela: $pasarela");
    
    // Normalizar email
    $email = sanitize_email($email);
    
    // Verificar si el usuario ya existe
    $user = get_user_by('email', $email);
    $es_nuevo = false;
    
    if ($user) {
        // Usuario existente
        $user_id = $user->ID;
        error_log("✅ Usuario existente encontrado: $user_id");
    } else {
        // Crear nuevo usuario
        $es_nuevo = true;
        $random_password = wp_generate_password(12, false);
        $username = sanitize_user(current(explode('@', $email)), true);
        
        // Asegurar nombre de usuario único
        $count = 1;
        $new_username = $username;
        while (username_exists($new_username)) {
            $new_username = $username . $count;
            $count++;
        }
        
        $userdata = array(
            'user_login'  => $new_username,
            'user_email'  => $email,
            'user_pass'   => $random_password,
            'role'        => 'subscriber'
        );
        
        // Añadir nombre si está disponible
        if (!empty($datos_extra['nombre'])) {
            $userdata['display_name'] = sanitize_text_field($datos_extra['nombre']);
            $userdata['first_name'] = sanitize_text_field($datos_extra['nombre']);
        }
        
        $user_id = wp_insert_user($userdata);
        
        if (is_wp_error($user_id)) {
            error_log("❌ Error al crear usuario: " . $user_id->get_error_message());
            return false;
        }
        
        error_log("✅ Nuevo usuario creado: $user_id");
        
        // Guardar credenciales temporalmente para uso en emails
        update_user_meta($user_id, '_breogan_temp_password', $random_password);
    }
    
    // Registrar la compra en metadatos de usuario
    update_user_meta($user_id, 'breogan_curso_' . $curso_id, 'comprado');
    
    // Registrar datos adicionales de la compra
    update_user_meta(
        $user_id, 
        'breogan_compra_' . $curso_id, 
        array(
            'fecha' => current_time('mysql'),
            'pasarela' => $pasarela,
            'datos' => $datos_extra
        )
    );
    
    // Asignar acceso a todos los temas y lecciones del curso
    breogan_asignar_acceso_curso($user_id, $curso_id);
    
    // Enviar email de confirmación si es usuario nuevo
    if ($es_nuevo) {
        breogan_enviar_email_bienvenida($user_id, $curso_id);
    } else {
        breogan_enviar_email_compra($user_id, $curso_id);
    }
    
    return $user_id;
}

/**
 * Asignar acceso a todos los temas y lecciones de un curso
 * 
 * @param int $user_id ID del usuario
 * @param int $curso_id ID del curso
 */
function breogan_asignar_acceso_curso($user_id, $curso_id) {
    // Obtener todos los temas del curso
    $temas = get_posts([
        'post_type'   => 'temas', // o 'blms_tema' según tu configuración
        'meta_key'    => '_curso_relacionado',
        'meta_value'  => $curso_id,
        'numberposts' => -1
    ]);
    
    foreach ($temas as $tema) {
        // Asignar acceso al tema
        update_user_meta($user_id, 'breogan_tema_' . $tema->ID, 'acceso');
        
        // Obtener lecciones del tema
        $lecciones = get_posts([
            'post_type'   => 'lecciones', // o 'blms_leccion' según tu configuración
            'meta_key'    => '_tema_relacionado',
            'meta_value'  => $tema->ID,
            'numberposts' => -1
        ]);
        
        foreach ($lecciones as $leccion) {
            // Asignar acceso a la lección
            update_user_meta($user_id, 'breogan_leccion_' . $leccion->ID, 'acceso');
        }
    }
    
    error_log("✅ Acceso completo al curso $curso_id asignado al usuario $user_id");
}

/**
 * Enviar email de bienvenida con credenciales a un nuevo usuario
 * 
 * @param int $user_id ID del usuario
 * @param int $curso_id ID del curso comprado
 */
function breogan_enviar_email_bienvenida($user_id, $curso_id) {
    // Obtener datos del usuario
    $user = get_userdata($user_id);
    if (!$user) {
        error_log("❌ Error: No se pudo obtener datos del usuario $user_id");
        return false;
    }
    
    // Siempre generar una contraseña nueva
    $new_password = wp_generate_password(12, false);
    
    // Actualizar la contraseña del usuario
    wp_set_password($new_password, $user_id);
    error_log("✅ Nueva contraseña establecida para usuario $user_id: $new_password");
    
    // Datos del curso
    $curso_title = get_the_title($curso_id);
    $site_name = get_bloginfo('name');
    
    // URLs importantes
    $login_url = wp_login_url();
    $curso_url = get_permalink($curso_id);
    
    // IMPORTANTE: Usar URL correcta para el perfil
    $perfil_url = home_url('/mi-perfil/');
    
    // Asunto del correo
    $subject = "¡Bienvenido a tu nuevo curso en $site_name!";
    
    // Mensaje con formato claro y visible
    $message = "¡Hola {$user->display_name}!\n\n";
    $message .= "¡Gracias por comprar el curso \"$curso_title\" en $site_name!\n\n";
    $message .= "----------------------------------------------------------\n";
    $message .= "TUS CREDENCIALES DE ACCESO\n";
    $message .= "----------------------------------------------------------\n\n";
    $message .= "Usuario: {$user->user_login}\n";
    $message .= "Contraseña: $new_password\n\n";
    $message .= "URL de acceso: $login_url\n\n";
    $message .= "----------------------------------------------------------\n";
    $message .= "INFORMACIÓN IMPORTANTE\n";
    $message .= "----------------------------------------------------------\n\n";
    $message .= "Para acceder a tu curso:\n";
    $message .= "1. Inicia sesión con las credenciales anteriores\n";
    $message .= "2. Ve a la página del curso: $curso_url\n\n";
    $message .= "Para ver todos tus cursos, visita tu perfil en:\n";
    $message .= "$perfil_url\n\n";
    $message .= "Te recomendamos cambiar tu contraseña después de iniciar sesión.\n\n";
    $message .= "¡Gracias por tu compra y disfruta de tu curso!\n\n";
    $message .= "El equipo de $site_name";
    
    // Cabeceras del correo
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    
    // Enviar correo y registrar resultado
    $enviado = wp_mail($user->user_email, $subject, $message, $headers);
    
    if ($enviado) {
        error_log("✅ Email de bienvenida con credenciales enviado a {$user->user_email}");
    } else {
        error_log("❌ Error al enviar email a {$user->user_email}");
    }
    
    return $enviado;
}

/**
 * Enviar email de confirmación de compra a un usuario existente
 * 
 * @param int $user_id ID del usuario
 * @param int $curso_id ID del curso comprado
 */
function breogan_enviar_email_compra($user_id, $curso_id) {
    $user = get_userdata($user_id);
    if (!$user) return false;
    
    // Generar nueva contraseña para el usuario
    $password = wp_generate_password(12, false);
    wp_set_password($password, $user_id);
    error_log("✅ Nueva contraseña generada para usuario $user_id en compra de curso");
    
    $curso_title = get_the_title($curso_id);
    $site_name = get_bloginfo('name');
    $curso_url = get_permalink($curso_id);
    $perfil_url = home_url('/mi-perfil/'); // Ajusta según tu configuración
    $login_url = wp_login_url();
    
    $subject = "Confirmación de compra en $site_name";
    
    $message = "Hola {$user->display_name},\n\n";
    $message .= "¡Gracias por comprar el curso \"$curso_title\"!\n\n";
    $message .= "Tu compra ha sido procesada correctamente y ya tienes acceso al contenido del curso.\n\n";
    $message .= "=== TUS CREDENCIALES DE ACCESO ===\n\n";
    $message .= "Usuario: {$user->user_login}\n";
    $message .= "Contraseña: $password\n\n";
    
    
    $message .= "También puedes ver todos tus cursos en tu perfil: $perfil_url\n\n";
    $message .= "¡Disfruta de tu curso!\n\n";
    $message .= "$site_name";
    
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    
    $sent = wp_mail($user->user_email, $subject, $message, $headers);
    
    if ($sent) {
        error_log("✅ Email de confirmación con credenciales enviado a {$user->user_email}");
    } else {
        error_log("❌ Error al enviar email a {$user->user_email}");
    }
    
    return $sent;
}