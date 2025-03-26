<?php
/**
 * Integration file for Progress Tracking functionality
 * 
 * This file loads and initializes all progress tracking components
 * 
 * @package Breogan LMS
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Core class for initializing Progress Tracking
 */
class Breogan_LMS_Progress_Manager {
    
    /**
     * Initialize the Progress Tracking functionality
     */
    public static function init() {
        // Define the directory
        $progress_dir = BREOGAN_LMS_PATH . 'includes/progreso/';
        
        // Include required files
        require_once $progress_dir . 'progreso-class.php';
        require_once $progress_dir . 'progreso-ajax.php';
        
        // Add hooks
        add_action('wp_enqueue_scripts', array('Breogan_LMS_Progress_Manager', 'enqueue_assets'));
        add_action('admin_enqueue_scripts', array('Breogan_LMS_Progress_Manager', 'admin_enqueue_assets'));
        
        // Add shortcode for progress widget
        add_shortcode('breogan_progreso', array('Breogan_LMS_Progress_Manager', 'progress_shortcode'));
    }
    
    /**
     * Enqueue front-end assets
     */
    public static function enqueue_assets() {
        // Only load on relevant pages
        if (!is_singular(array('blms_curso', 'blms_tema', 'blms_leccion', 'cursos', 'temas', 'lecciones')) && 
            !is_page('mi-perfil')) {
            return;
        }
        
        // Enqueue styles
        wp_enqueue_style(
            'breogan-progress-styles',
            BREOGAN_LMS_URL . 'assets/css/progress-styles.css',
            array(),
            BREOGAN_LMS_VERSION
        );
        
        // Enqueue scripts
        wp_enqueue_script(
            'breogan-progress-tracker',
            BREOGAN_LMS_URL . 'assets/js/progress-tracker.js',
            array('jquery'),
            BREOGAN_LMS_VERSION,
            true
        );
        
        // Pass data to script
        wp_localize_script('breogan-progress-tracker', 'breoganLMS', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('breogan_progress_nonce'),
            'user_logged_in' => is_user_logged_in(),
            'text_saving' => __('Guardando...', 'breogan-lms'),
            'text_mark_complete' => __('Marcar como completada', 'breogan-lms'),
            'text_lesson_completed' => __('Lección completada', 'breogan-lms')
        ));
    }
    
    /**
     * Enqueue admin assets
     */
    public static function admin_enqueue_assets($hook) {
        // Only load on edit screens for our custom post types
        $screens = array('edit-blms_curso', 'edit-cursos');
        if (!in_array(get_current_screen()->id, $screens)) {
            return;
        }
        
        // Enqueue admin styles
        wp_enqueue_style(
            'breogan-progress-admin',
            BREOGAN_LMS_URL . 'includes/progress/css/progress-admin.css',
            array(),
            BREOGAN_LMS_VERSION
        );
    }
    
    /**
     * Shortcode for displaying progress widget
     */
    public static function progress_shortcode($atts) {
        // Parse attributes
        $atts = shortcode_atts(array(
            'curso_id' => 0,
            'mostrar_titulo' => 'si',
            'mostrar_boton' => 'si'
        ), $atts);
        
        // If not logged in, return login message
        if (!is_user_logged_in()) {
            return '<div class="breogan-progress-widget-login">' . 
                __('Inicia sesión para ver tu progreso.', 'breogan-lms') . 
                '</div>';
        }
        
        $user_id = get_current_user_id();
        $course_id = intval($atts['curso_id']);
        
        // If no course ID specified, show summary of all courses
        if ($course_id === 0) {
            return self::render_progress_summary($user_id);
        }
        
        // Check if user has access to the course
        $has_access_blms = get_user_meta($user_id, 'blms_curso_' . $course_id, true);
        $has_access_breogan = get_user_meta($user_id, 'breogan_curso_' . $course_id, true);
        
        if ($has_access_blms !== 'comprado' && $has_access_breogan !== 'comprado') {
            return '<div class="breogan-progress-widget-access">' . 
                __('No tienes acceso a este curso.', 'breogan-lms') . 
                '</div>';
        }
        
        // Get course progress
        $progress = self::calculate_course_progress($user_id, $course_id);
        
        // Start output buffer
        ob_start();
        
        ?>
        <div class="breogan-widget-progreso" data-curso-id="<?php echo $course_id; ?>">
            <?php if ($atts['mostrar_titulo'] === 'si') : ?>
                <h3><?php echo get_the_title($course_id); ?></h3>
            <?php endif; ?>
            
            <div class="progreso-info">
                <span class="estado-indicador <?php echo $progress['status_class']; ?>"><?php echo $progress['status_text']; ?></span>
                <span class="progreso-porcentaje"><?php echo $progress['percentage']; ?>%</span>
            </div>
            
            <div class="progreso-barra">
                <div style="width: <?php echo $progress['percentage']; ?>%"></div>
            </div>
            
            <p class="lecciones-info">
                <?php 
                printf(
                    __('%d de %d lecciones completadas', 'breogan-lms'),
                    $progress['completed_lessons'],
                    $progress['total_lessons']
                ); 
                ?>
            </p>
            
            <?php if ($atts['mostrar_boton'] === 'si') : ?>
                <a href="<?php echo get_permalink($course_id); ?>" class="breogan-btn-continuar">
                    <?php _e('Continuar curso', 'breogan-lms'); ?>
                </a>
            <?php endif; ?>
        </div>
        <?php
        
        // Return the output buffer content
        return ob_get_clean();
    }
    
    /**
     * Render progress summary for all user courses
     */
    private static function render_progress_summary($user_id) {
        // Get all courses the user has access to
        $courses = self::get_user_courses($user_id);
        
        if (empty($courses)) {
            return '<div class="breogan-progress-widget-empty">' . 
                __('No tienes cursos activos.', 'breogan-lms') . 
                '</div>';
        }
        
        // Calculate overall stats
        $total_courses = count($courses);
        $completed_courses = 0;
        $total_progress = 0;
        $total_lessons = 0;
        $completed_lessons = 0;
        
        foreach ($courses as $course) {
            $progress = self::calculate_course_progress($user_id, $course->ID);
            $total_progress += $progress['percentage'];
            $total_lessons += $progress['total_lessons'];
            $completed_lessons += $progress['completed_lessons'];
            
            if ($progress['percentage'] >= 100) {
                $completed_courses++;
            }
        }
        
        $avg_progress = $total_courses > 0 ? round($total_progress / $total_courses) : 0;
        
        // Start output buffer
        ob_start();
        
        ?>
        <div class="breogan-widget-progreso">
            <h3><?php _e('Mi Progreso', 'breogan-lms'); ?></h3>
            
            <div class="resumen-estadisticas">
                <div class="estadistica-item">
                    <div class="estadistica-valor"><?php echo $completed_courses; ?>/<?php echo $total_courses; ?></div>
                    <div class="estadistica-label"><?php _e('Cursos Completados', 'breogan-lms'); ?></div>
                </div>
                
                <div class="estadistica-item">
                    <div class="estadistica-valor"><?php echo $avg_progress; ?>%</div>
                    <div class="estadistica-label"><?php _e('Progreso Promedio', 'breogan-lms'); ?></div>
                </div>
                
                <div class="estadistica-item">
                    <div class="estadistica-valor"><?php echo $completed_lessons; ?></div>
                    <div class="estadistica-label"><?php _e('Lecciones Completadas', 'breogan-lms'); ?></div>
                </div>
            </div>
            
            <div class="progreso-barra">
                <div style="width: <?php echo $avg_progress; ?>%"></div>
            </div>
            
            <?php if (count($courses) > 0) : ?>
                <h4><?php _e('Cursos en progreso', 'breogan-lms'); ?></h4>
                <ul class="widget-cursos-lista">
                    <?php 
                    // Sort courses by progress (highest to lowest)
                    $cursos_ordenados = array();
                    foreach ($courses as $course) {
                        $progress = self::calculate_course_progress($user_id, $course->ID);
                        $cursos_ordenados[$course->ID] = $progress['percentage'];
                    }
                    
                    arsort($cursos_ordenados);
                    
                    // Display top 3 courses
                    $i = 0;
                    foreach ($cursos_ordenados as $course_id => $percentage) {
                        if ($i >= 3) break; // Only show top 3
                        
                        $course = get_post($course_id);
                        if (!$course) continue;
                        
                        $i++;
                        ?>
                        <li class="widget-curso-item">
                            <a href="<?php echo get_permalink($course_id); ?>" class="curso-titulo">
                                <?php echo get_the_title($course_id); ?>
                            </a>
                            <div class="curso-mini-progress">
                                <span class="mini-progress-text"><?php echo $percentage; ?>%</span>
                                <div class="mini-progress-bar">
                                    <div style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                            </div>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
                
                <?php if (count($courses) > 3) : ?>
                    <a href="<?php echo get_permalink(get_page_by_path('mi-perfil')->ID); ?>" class="ver-todos-cursos">
                        <?php _e('Ver todos mis cursos', 'breogan-lms'); ?>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php
        
        // Return the output buffer content
        return ob_get_clean();
    }
    
    /**
     * Get all courses a user has access to
     */
    private static function get_user_courses($user_id) {
        global $wpdb;
        
        // Get all user meta keys for courses
        $meta_keys = $wpdb->get_col($wpdb->prepare(
            "SELECT meta_key FROM {$wpdb->usermeta} 
             WHERE user_id = %d AND (meta_key LIKE %s OR meta_key LIKE %s) 
             AND meta_value = %s",
            $user_id,
            'blms_curso_%',
            'breogan_curso_%',
            'comprado'
        ));
        
        if (empty($meta_keys)) {
            return array();
        }
        
        // Extract course IDs from meta keys
        $course_ids = array();
        
        foreach ($meta_keys as $key) {
            if (strpos($key, 'blms_curso_') === 0) {
                $course_ids[] = intval(str_replace('blms_curso_', '', $key));
            } elseif (strpos($key, 'breogan_curso_') === 0) {
                $course_ids[] = intval(str_replace('breogan_curso_', '', $key));
            }
        }
        
        if (empty($course_ids)) {
            return array();
        }
        
        // Try with new post type first
        $courses = get_posts(array(
            'post_type' => 'blms_curso',
            'post__in' => $course_ids,
            'numberposts' => -1
        ));
        
        // If empty, try with legacy post type
        if (empty($courses)) {
            $courses = get_posts(array(
                'post_type' => 'cursos',
                'post__in' => $course_ids,
                'numberposts' => -1
            ));
        }
        
        return $courses;
    }
    
    /**
     * Calculate course progress for a user
     */
    private static function calculate_course_progress($user_id, $course_id) {
        // Initialize variables
        $total_lessons = 0;
        $completed_lessons = 0;
        
        // Try to get themes with new post type
        $themes = get_posts([
            'post_type'   => 'blms_tema',
            'meta_key'    => '_blms_curso_relacionado',
            'meta_value'  => $course_id,
            'numberposts' => -1,
            'orderby'     => 'menu_order',
            'order'       => 'ASC'
        ]);
        
        // If empty, try with legacy post type
        if (empty($themes)) {
            $themes = get_posts([
                'post_type'   => 'temas',
                'meta_key'    => '_curso_relacionado',
                'meta_value'  => $course_id,
                'numberposts' => -1,
                'orderby'     => 'menu_order',
                'order'       => 'ASC'
            ]);
        }
        
        // Loop through themes and lessons
        foreach ($themes as $theme) {
            // Try to get lessons with new post type
            $lessons = get_posts([
                'post_type'   => 'blms_leccion',
                'meta_key'    => '_blms_tema_relacionado',
                'meta_value'  => $theme->ID,
                'numberposts' => -1,
                'orderby'     => 'menu_order',
                'order'       => 'ASC'
            ]);
            
            // If empty, try with legacy post type
            if (empty($lessons)) {
                $lessons = get_posts([
                    'post_type'   => 'lecciones',
                    'meta_key'    => '_tema_relacionado',
                    'meta_value'  => $theme->ID,
                    'numberposts' => -1,
                    'orderby'     => 'menu_order',
                    'order'       => 'ASC'
                ]);
            }
            
            foreach ($lessons as $lesson) {
                $total_lessons++;
                
                // Check if lesson is completed with both prefixes
                $completed_blms = get_user_meta($user_id, 'blms_leccion_completada_' . $lesson->ID, true);
                $completed_breogan = get_user_meta($user_id, 'breogan_leccion_' . $lesson->ID . '_completada', true);
                
                if (!empty($completed_blms) || !empty($completed_breogan)) {
                    $completed_lessons++;
                }
            }
        }
        
        // Calculate percentage
        $percentage = ($total_lessons > 0) ? round(($completed_lessons / $total_lessons) * 100) : 0;
        
        // Determine status
        $status_class = 'estado-pendiente';
        $status_text = __('Pendiente', 'breogan-lms');
        
        if ($percentage >= 100) {
            $status_class = 'estado-completado';
            $status_text = __('Completado', 'breogan-lms');
        } elseif ($percentage > 0) {
            $status_class = 'estado-activo';
            $status_text = __('En progreso', 'breogan-lms');
        }
        
        return array(
            'percentage' => $percentage,
            'completed_lessons' => $completed_lessons,
            'total_lessons' => $total_lessons,
            'status_class' => $status_class,
            'status_text' => $status_text
        );
    }
}

// Initialize progress tracking
Breogan_LMS_Progress_Manager::init();