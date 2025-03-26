<?php
/**
 * Clase para gestionar usuarios y progreso
 */
class Breogan_LMS_User {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Registrar endpoint AJAX para marcar lecciones como completadas
        add_action('wp_ajax_blms_mark_lesson_complete', array($this, 'mark_lesson_complete'));
        
        // Verificar compra completada
        add_action('template_redirect', array($this, 'check_payment_success'));
        
        // Añadir handler para cursos gratuitos
        add_action('wp_ajax_blms_process_free_access', array($this, 'process_free_access'));
        add_action('wp_ajax_nopriv_blms_process_free_access', array($this, 'process_free_access'));
    }

    /**
     * Procesar acceso a curso gratuito
     */
    public function process_free_access() {
        // Verificar nonce para seguridad
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'blms_free_access_nonce')) {
            wp_send_json_error(array('message' => __('Error de seguridad', 'breogan-lms')));
            return;
        }
        
        // Verificar datos necesarios
        if (!isset($_POST['curso_id'])) {
            wp_send_json_error(array('message' => __('Datos incompletos', 'breogan-lms')));
            return;
        }
        
        $curso_id = intval($_POST['curso_id']);
        
        // Verificar que el curso existe y es gratuito
        $es_gratuito = get_post_meta($curso_id, '_blms_curso_gratuito', true);
        
        if (!$es_gratuito) {
            wp_send_json_error(array('message' => __('Este curso no es gratuito', 'breogan-lms')));
            return;
        }
        
        // Si el usuario no está logueado, redirigir a login
        if (!is_user_logged_in()) {
            // Guardar curso_id en sesión para procesarlo después del login
            if (!session_id() && !headers_sent()) {
                session_start();
            }
            $_SESSION['blms_pending_free_course'] = $curso_id;
            
            wp_send_json_success(array(
                'redirect_url' => wp_login_url(get_permalink($curso_id))
            ));
            return;
        }
        
        // Dar acceso al usuario al curso gratuito
        $user_id = get_current_user_id();
        $payment_handler = new Breogan_LMS_Payments();
        $payment_handler->register_course_purchase($user_id, $curso_id);
        
        // Redirigir a la página del curso
        wp_send_json_success(array(
            'redirect_url' => get_permalink($curso_id) . '?blms_access=granted'
        ));
    }
    
    /**
     * Marcar lección como completada
     */
    public function mark_lesson_complete() {
        // Verificar nonce para seguridad
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'blms_lesson_nonce')) {
            wp_send_json_error(array('message' => __('Error de seguridad', 'breogan-lms')));
        }
        
        // Verificar si el usuario está logueado
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('Debes iniciar sesión', 'breogan-lms')));
        }
        
        // Verificar datos necesarios
        if (!isset($_POST['leccion_id'])) {
            wp_send_json_error(array('message' => __('Datos incompletos', 'breogan-lms')));
        }
        
        $user_id = get_current_user_id();
        $leccion_id = intval($_POST['leccion_id']);
        
        // Verificar si el usuario tiene acceso a esta lección
        if (!$this->user_has_access_to_lesson($user_id, $leccion_id)) {
            wp_send_json_error(array('message' => __('No tienes acceso a esta lección', 'breogan-lms')));
        }
        
        // Marcar lección como completada
        update_user_meta($user_id, 'blms_leccion_completada_' . $leccion_id, current_time('mysql'));
        
        // Devolver éxito
        wp_send_json_success(array(
            'message' => __('Lección marcada como completada', 'breogan-lms')
        ));
    }
    
    /**
     * Verificar si el usuario tiene acceso a una lección
     * 
     * @param int $user_id ID del usuario
     * @param int $leccion_id ID de la lección
     * @return boolean True si tiene acceso, false si no
     */
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
   public function user_has_access_to_lesson($user_id, $leccion_id) {
    // Obtener tema relacionado
    $tema_id = get_post_meta($leccion_id, '_blms_tema_relacionado', true);
    if (!$tema_id) {
        // Probar con el prefijo antiguo
        $tema_id = get_post_meta($leccion_id, '_tema_relacionado', true);
        if (!$tema_id) {
            return false;
        }
    }
    
    // Obtener curso relacionado
    $curso_id = get_post_meta($tema_id, '_blms_curso_relacionado', true);
    if (!$curso_id) {
        // Probar con el prefijo antiguo
        $curso_id = get_post_meta($tema_id, '_curso_relacionado', true);
        if (!$curso_id) {
            return false;
        }
    }
    
    // Verificar si el usuario ha comprado el curso (ambos prefijos)
    $ha_comprado_blms = get_user_meta($user_id, 'blms_curso_' . $curso_id, true);
    $ha_comprado_breogan = get_user_meta($user_id, 'breogan_curso_' . $curso_id, true);
    if ($ha_comprado_blms === 'comprado' || $ha_comprado_breogan) {
        return true;
    }
    
    // Verificar si el usuario tiene acceso al tema (ambos prefijos)
    $ha_acceso_tema_blms = get_user_meta($user_id, 'blms_tema_' . $tema_id, true);
    $ha_acceso_tema_breogan = get_user_meta($user_id, 'breogan_tema_' . $tema_id, true);
    if ($ha_acceso_tema_blms === 'acceso' || $ha_acceso_tema_breogan) {
        return true;
    }
    
    // Verificar si el usuario tiene acceso directo a la lección (ambos prefijos)
    $ha_acceso_leccion_blms = get_user_meta($user_id, 'blms_leccion_' . $leccion_id, true);
    $ha_acceso_leccion_breogan = get_user_meta($user_id, 'breogan_leccion_' . $leccion_id, true);
    if ($ha_acceso_leccion_blms === 'acceso' || $ha_acceso_leccion_breogan) {
        return true;
    }
    
    return false;
}
    
<<<<<<< HEAD
=======
=======
    public function user_has_access_to_lesson($user_id, $leccion_id) {
        // Obtener tema relacionado
        $tema_id = get_post_meta($leccion_id, '_blms_tema_relacionado', true);
        if (!$tema_id) {
            return false;
        }
        
        // Obtener curso relacionado
        $curso_id = get_post_meta($tema_id, '_blms_curso_relacionado', true);
        if (!$curso_id) {
            return false;
        }
        
        // Verificar si el usuario ha comprado el curso
        $ha_comprado_curso = get_user_meta($user_id, 'blms_curso_' . $curso_id, true);
        if ($ha_comprado_curso) {
            return true;
        }
        
        // Verificar si el usuario tiene acceso al tema
        $ha_acceso_tema = get_user_meta($user_id, 'blms_tema_' . $tema_id, true);
        if ($ha_acceso_tema) {
            return true;
        }
        
        // Verificar si el usuario tiene acceso directo a la lección
        $ha_acceso_leccion = get_user_meta($user_id, 'blms_leccion_' . $leccion_id, true);
        if ($ha_acceso_leccion) {
            return true;
        }
        
        return false;
    }
    
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
    /**
     * Verificar si la lección está completada
     * 
     * @param int $user_id ID del usuario
     * @param int $leccion_id ID de la lección
     * @return boolean True si está completada, false si no
     */
    public function is_lesson_completed($user_id, $leccion_id) {
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
    $completed_blms = get_user_meta($user_id, 'blms_leccion_completada_' . $leccion_id, true);
    $completed_breogan = get_user_meta($user_id, 'breogan_leccion_' . $leccion_id . '_completada', true);
    return !empty($completed_blms) || !empty($completed_breogan);
}
<<<<<<< HEAD
=======
=======
        $completed = get_user_meta($user_id, 'blms_leccion_completada_' . $leccion_id, true);
        return !empty($completed);
    }
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
    
    /**
     * Obtener progreso del curso
     * 
     * @param int $user_id ID del usuario
     * @param int $curso_id ID del curso
     * @return array Datos de progreso
     */
    public function get_course_progress($user_id, $curso_id) {
        // Obtener todos los temas del curso
        $temas = get_posts(array(
            'post_type' => 'blms_tema',
            'meta_key' => '_blms_curso_relacionado',
            'meta_value' => $curso_id,
            'numberposts' => -1
        ));
        
        $total_lecciones = 0;
        $lecciones_completadas = 0;
        
        foreach ($temas as $tema) {
            // Obtener lecciones de cada tema
            $lecciones = get_posts(array(
                'post_type' => 'blms_leccion',
                'meta_key' => '_blms_tema_relacionado',
                'meta_value' => $tema->ID,
                'numberposts' => -1
            ));
            
            foreach ($lecciones as $leccion) {
                $total_lecciones++;
                if ($this->is_lesson_completed($user_id, $leccion->ID)) {
                    $lecciones_completadas++;
                }
            }
        }
        
        // Calcular porcentaje
        $porcentaje = ($total_lecciones > 0) ? round(($lecciones_completadas / $total_lecciones) * 100) : 0;
        
        return array(
            'total_lecciones' => $total_lecciones,
            'lecciones_completadas' => $lecciones_completadas,
            'porcentaje' => $porcentaje
        );
    }
    
    /**
     * Verificar pago exitoso y procesar diferentes tipos de acceso pendiente
     */
public function check_payment_success() {
    // Verificar solicitud de acceso gratuito mediante enlace directo
    if (is_singular('blms_curso') && 
        isset($_GET['blms_free_access']) && 
        $_GET['blms_free_access'] === 'true' &&
        isset($_GET['curso_id']) &&
        isset($_GET['nonce'])) {
        
        // Verificar nonce
        if (!wp_verify_nonce($_GET['nonce'], 'blms_free_access_nonce')) {
            return;
        }
        
        $curso_id = intval($_GET['curso_id']);
        
        // Verificar que el curso es gratuito
        $es_gratuito = get_post_meta($curso_id, '_blms_curso_gratuito', true);
        if (!$es_gratuito) {
            return; // No es un curso gratuito
        }
        
        // Si el usuario no está logueado, redirigir a login
        if (!is_user_logged_in()) {
            // Guardar curso_id en sesión y redirigir a login
            if (!session_id() && !headers_sent()) {
                session_start();
            }
            $_SESSION['blms_pending_free_course'] = $curso_id;
            wp_redirect(wp_login_url(get_permalink($curso_id)));
            exit;
        }
        
        // Dar acceso al usuario al curso gratuito
        $user_id = get_current_user_id();
        $payment_handler = new Breogan_LMS_Payments();
        $payment_handler->register_course_purchase($user_id, $curso_id);
        
        // Redirigir a la página del curso con acceso concedido
        wp_redirect(get_permalink($curso_id) . '?blms_access=granted');
        exit;
    }

    // Verificar si estamos en una página de curso y hay un parámetro de pago exitoso
    if (is_singular('blms_curso') && 
        isset($_GET['blms_payment']) && 
        $_GET['blms_payment'] === 'success' &&
        isset($_GET['curso_id']) &&
        isset($_GET['token'])) {
        
        // Verificar nonce
        if (!wp_verify_nonce($_GET['token'], 'blms_payment_success')) {
            return;
        }
        
        // Verificar si el usuario está logueado
        if (!is_user_logged_in()) {
            // Guardar curso_id en sesión y redirigir a login
            if (!session_id() && !headers_sent()) {
                session_start();
            }
            $_SESSION['blms_pending_purchase'] = intval($_GET['curso_id']);
            wp_redirect(wp_login_url(get_permalink(intval($_GET['curso_id']))));
            exit;
        }
        
        $user_id = get_current_user_id();
        $curso_id = intval($_GET['curso_id']);
        
        // Registrar la compra
        $payment_handler = new Breogan_LMS_Payments();
        $payment_handler->register_course_purchase($user_id, $curso_id);
        
        // Redirigir a la página del curso sin parámetros
        wp_redirect(get_permalink($curso_id));
        exit;
    }
    
    // Verificar si hay una compra pendiente después de login
    if (is_user_logged_in() && isset($_SESSION['blms_pending_purchase'])) {
        $curso_id = intval($_SESSION['blms_pending_purchase']);
        $user_id = get_current_user_id();
        
        // Registrar la compra
        $payment_handler = new Breogan_LMS_Payments();
        $payment_handler->register_course_purchase($user_id, $curso_id);
        
        // Limpiar sesión
        unset($_SESSION['blms_pending_purchase']);
        
        // Redirigir a la página del curso
        wp_redirect(get_permalink($curso_id));
        exit;
    }
    
    // Verificar si hay un curso gratuito pendiente después del login
    if (is_user_logged_in() && isset($_SESSION['blms_pending_free_course'])) {
        $curso_id = intval($_SESSION['blms_pending_free_course']);
        
        if ($curso_id > 0) {  // Asegurarse de que tenemos un ID válido
            $user_id = get_current_user_id();
            
            // Verificar que el curso es gratuito
            $es_gratuito = get_post_meta($curso_id, '_blms_curso_gratuito', true);
            if ($es_gratuito) {
                // Registrar acceso al curso
                $payment_handler = new Breogan_LMS_Payments();
                $payment_handler->register_course_purchase($user_id, $curso_id);
            }
            
            // Limpiar sesión
            unset($_SESSION['blms_pending_free_course']);
            
            // Redirigir a la página del curso
            wp_redirect(get_permalink($curso_id) . '?blms_access=granted');
            exit;
        }
    }
    
    // Verificar si se acaba de conceder acceso a un curso
    if (is_singular('blms_curso') && isset($_GET['blms_access']) && $_GET['blms_access'] === 'granted') {
        // Podríamos añadir un mensaje aquí si queremos
        // La próxima vez que cargue la página ya mostrará el contenido del curso
    }
}
}