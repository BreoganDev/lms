<?php
// Verificar que sea una petición de WordPress
if (!defined('ABSPATH')) exit;

/**
 * Función dedicada para enviar credenciales por correo
 * Esta función evita depender de otras partes del código
 */
function breogan_send_credentials_email($user_id, $curso_id, $password = '') {
    // Si no se proporciona contraseña, crear una nueva
    if (empty($password)) {
        $password = wp_generate_password(12, false);
        wp_set_password($password, $user_id);
    }
    
    // Obtener información del usuario
    $user = get_userdata($user_id);
    if (!$user) {
        error_log("❌ Error al enviar credenciales: Usuario $user_id no encontrado");
        return false;
    }
    
    // Información del curso
    $curso_title = get_the_title($curso_id);
    $site_name = get_bloginfo('name');
    
    // Preparar el correo
    $to = $user->user_email;
    $subject = "🔑 Tus credenciales para acceder a $site_name";
    
    $login_url = wp_login_url();
    $curso_url = get_permalink($curso_id);
    
    $message = "¡Hola!\n\n";
    $message .= "Gracias por comprar el curso \"$curso_title\" en $site_name.\n\n";
    $message .= "============= TUS CREDENCIALES DE ACCESO =============\n\n";
    $message .= "Usuario: " . $user->user_login . "\n";
    $message .= "Contraseña: " . $password . "\n\n";
    $message .= "Enlace para acceder: " . $login_url . "\n\n";
    $message .= "============= INFORMACIÓN IMPORTANTE =============\n\n";
    $message .= "1. Guarda estas credenciales en un lugar seguro.\n";
    $message .= "2. Después de iniciar sesión, ve a tu curso: " . $curso_url . "\n";
    $message .= "3. Por seguridad, te recomendamos cambiar tu contraseña después del primer inicio de sesión.\n\n";
    $message .= "Si necesitas ayuda, no dudes en contactarnos.\n\n";
    $message .= "¡Disfruta de tu curso!\n\n";
    $message .= "El equipo de " . $site_name;
    
    // Establecer cabeceras
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    
    // Enviar el correo
    $enviado = wp_mail($to, $subject, $message, $headers);
    
    // Registrar resultado
    if ($enviado) {
        error_log("✅ Correo con credenciales enviado exitosamente a " . $user->user_email);
    } else {
        error_log("❌ Error al enviar correo con credenciales a " . $user->user_email);
    }
    
    return $enviado;
}

/**
 * Función para enviar credenciales a todos los usuarios que han comprado un curso
 * Útil para enviar credenciales manualmente a usuarios existentes
 */
function breogan_send_credentials_to_course_buyers($curso_id) {
    global $wpdb;
    
    // Buscar todos los usuarios que han comprado este curso
    $meta_keys = array(
        'breogan_curso_' . $curso_id,
        'blms_curso_' . $curso_id
    );
    
    $users_sent = 0;
    
    foreach ($meta_keys as $meta_key) {
        $users = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT user_id FROM {$wpdb->usermeta} 
                 WHERE meta_key = %s AND meta_value = %s",
                $meta_key,
                'comprado'
            )
        );
        
        foreach ($users as $user_id) {
            // Generar nueva contraseña
            $password = wp_generate_password(12, false);
            wp_set_password($password, $user_id);
            
            // Enviar credenciales
            if (breogan_send_credentials_email($user_id, $curso_id, $password)) {
                $users_sent++;
            }
        }
    }
    
    return $users_sent;
}

// Agregar página de administración para enviar credenciales manualmente
function breogan_add_credentials_admin_page() {
    add_submenu_page(
        'breogan-lms',
        'Enviar Credenciales',
        'Enviar Credenciales',
        'manage_options',
        'breogan-send-credentials',
        'breogan_credentials_admin_page'
    );
}
add_action('admin_menu', 'breogan_add_credentials_admin_page');

// Página de administración para enviar credenciales
function breogan_credentials_admin_page() {
    // Verificar permisos
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Procesar formulario
    $mensaje = '';
    if (isset($_POST['enviar_credenciales']) && isset($_POST['curso_id'])) {
        $curso_id = intval($_POST['curso_id']);
        $users_sent = breogan_send_credentials_to_course_buyers($curso_id);
        $mensaje = "Se han enviado credenciales a $users_sent usuarios.";
    }
    
    // Obtener todos los cursos
    $cursos = get_posts(array(
        'post_type' => post_type_exists('blms_curso') ? 'blms_curso' : 'cursos',
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ));
    
    ?>
    <div class="wrap">
        <h1>Enviar Credenciales de Acceso</h1>
        
        <?php if ($mensaje): ?>
            <div class="notice notice-success">
                <p><?php echo $mensaje; ?></p>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h2>Enviar credenciales a compradores de un curso</h2>
            <p>Utiliza esta herramienta para enviar nuevas credenciales a todos los usuarios que han comprado un curso específico.</p>
            
            <form method="post">
                <table class="form-table">
                    <tr>
                        <th><label for="curso_id">Selecciona un curso:</label></th>
                        <td>
                            <select name="curso_id" id="curso_id" required>
                                <option value="">-- Seleccionar curso --</option>
                                <?php foreach ($cursos as $curso): ?>
                                    <option value="<?php echo $curso->ID; ?>"><?php echo $curso->post_title; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                
                <p class="description">
                    <strong>Nota:</strong> Esta acción generará nuevas contraseñas para todos los usuarios que han comprado el curso seleccionado.
                </p>
                
                <p class="submit">
                    <input type="submit" name="enviar_credenciales" class="button button-primary" value="Enviar Credenciales">
                </p>
            </form>
        </div>
        
        <div class="card">
            <h2>Información importante</h2>
            <p>Esta herramienta es útil cuando:</p>
            <ul style="list-style-type: disc; margin-left: 20px;">
                <li>Los usuarios no han recibido sus credenciales por correo electrónico.</li>
                <li>Los usuarios han olvidado sus credenciales.</li>
                <li>Necesitas restablecer las contraseñas por motivos de seguridad.</li>
            </ul>
        </div>
    </div>
    <?php
}