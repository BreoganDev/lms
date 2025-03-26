<?php
/**
 * Class for managing user progress tracking in courses
 * 
 * @package Breogan LMS
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Class to handle course progress tracking
 */
class Breogan_LMS_Progress {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Add profile progress section
        add_action('breogan_profile_before_courses', array($this, 'add_progress_summary'));
        
        // Add course progress widget
        add_action('breogan_before_course_content', array($this, 'add_course_progress_widget'));
        add_action('breogan_after_lesson_content', array($this, 'add_mark_complete_button'));
        
        // Add course progress to admin columns
        add_filter('manage_edit-blms_curso_columns', array($this, 'add_progress_column'));
        add_action('manage_blms_curso_posts_custom_column', array($this, 'display_progress_column'), 10, 2);
        
        // Style and scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }
    
    /**
     * Enqueue assets
     */
    public function enqueue_assets() {
        // Only load on relevant pages
        if (!is_singular(array('blms_curso', 'blms_tema', 'blms_leccion', 'cursos', 'temas', 'lecciones')) && 
            !is_page('mi-perfil')) {
            return;
        }
        
        wp_enqueue_style(
            'breogan-progress-styles',
            BREOGAN_LMS_URL . 'assets/css/progress-styles.css',
            array(),
            BREOGAN_LMS_VERSION
        );
    }
    
    /**
     * Add progress summary to user profile
     */
    public function add_progress_summary() {
        if (!is_user_logged_in()) return;
        
        $user_id = get_current_user_id();
        $courses = $this->get_user_courses($user_id);
        
        if (empty($courses)) return;
        
        // Calculate overall stats
        $total_courses = count($courses);
        $completed_courses = 0;
        $total_progress = 0;
        $total_lessons = 0;
        $completed_lessons = 0;
        
        foreach ($courses as $course) {
            $progress = $this->calculate_course_progress($user_id, $course->ID);
            $total_progress += $progress['percentage'];
            $total_lessons += $progress['total_lessons'];
            $completed_lessons += $progress['completed_lessons'];
            
            if ($progress['percentage'] >= 100) {
                $completed_courses++;
            }
        }
        
        $avg_progress = $total_courses > 0 ? round($total_progress / $total_courses) : 0;
        
        // Display summary
        ?>
        <div class="breogan-perfil-progreso">
            <h2><?php _e('Resumen de Progreso', 'breogan-lms'); ?></h2>
            
            <div class="perfil-estadisticas">
                <div class="estadistica-card">
                    <div class="estadistica-valor"><?php echo $completed_courses; ?>/<?php echo $total_courses; ?></div>
                    <div class="estadistica-label"><?php _e('Cursos Completados', 'breogan-lms'); ?></div>
                </div>
                
                <div class="estadistica-card">
                    <div class="estadistica-valor"><?php echo $avg_progress; ?>%</div>
                    <div class="estadistica-label"><?php _e('Progreso Promedio', 'breogan-lms'); ?></div>
                </div>
                
                <div class="estadistica-card">
                    <div class="estadistica-valor"><?php echo $completed_lessons; ?></div>
                    <div class="estadistica-label"><?php _e('Lecciones Completadas', 'breogan-lms'); ?></div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Add course progress widget to course page
     */
    public function add_course_progress_widget() {
        if (!is_user_logged_in() || !is_singular(array('blms_curso', 'cursos'))) return;
        
        $user_id = get_current_user_id();
        $course_id = get_the_ID();
        
        // Check if user has access to this course
        if (!$this->user_has_course_access($user_id, $course_id)) return;
        
        // Get course progress
        $progress = $this->calculate_course_progress($user_id, $course_id);
        
        // Display progress widget
        ?>
        <div class="breogan-curso-progreso">
            <h3><?php _e('Tu Progreso', 'breogan-lms'); ?></h3>
            
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
        </div>
        <?php
    }
    
    /**
     * Add mark complete button to lesson page
     */
    public function add_mark_complete_button() {
        if (!is_user_logged_in() || !is_singular(array('blms_leccion', 'lecciones'))) return;
        
        $user_id = get_current_user_id();
        $lesson_id = get_the_ID();
        
        // Check if user has access to this lesson
        if (!$this->user_has_lesson_access($user_id, $lesson_id)) return;
        
        // Check if lesson is already completed
        $is_completed = $this->is_lesson_completed($user_id, $lesson_id);
        
        if ($is_completed) {
            // Display completed status
            ?>
            <div class="breogan-leccion-estado completada">
                <span class="dashicons dashicons-yes-alt"></span>
                <?php _e('Lección completada', 'breogan-lms'); ?>
            </div>
            <?php
        } else {
            // Display mark complete button
            ?>
            <div class="breogan-leccion-acciones">
                <button id="marcar-completada" class="breogan-btn breogan-btn-success" data-leccion="<?php echo $lesson_id; ?>">
                    <span class="dashicons dashicons-yes-alt"></span>
                    <?php _e('Marcar como completada', 'breogan-lms'); ?>
                </button>
            </div>
            <?php
        }
    }
    
    /**
     * Add progress column to courses admin list
     */
    public function add_progress_column($columns) {
        $new_columns = array();
        
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            
            if ($key === 'title') {
                $new_columns['progress'] = __('Progreso de Estudiantes', 'breogan-lms');
            }
        }
        
        return $new_columns;
    }
    
    /**
     * Display progress column content
     */
    public function display_progress_column($column, $post_id) {
        if ($column !== 'progress') return;
        
        // Get all users who have access to this course
        $user_ids = $this->get_course_users($post_id);
        
        if (empty($user_ids)) {
            echo '-';
            return;
        }
        
        $total_users = count($user_ids);
        $completed_users = 0;
        $total_progress = 0;
        
        foreach ($user_ids as $user_id) {
            $progress = $this->calculate_course_progress($user_id, $post_id);
            $total_progress += $progress['percentage'];
            
            if ($progress['percentage'] >= 100) {
                $completed_users++;
            }
        }
        
        $avg_progress = $total_users > 0 ? round($total_progress / $total_users) : 0;
        
        echo '<div class="course-progress-stats">';
        echo '<span class="progress-stat"><strong>' . $total_users . '</strong> ' . __('estudiantes', 'breogan-lms') . '</span>';
        echo '<span class="progress-stat"><strong>' . $completed_users . '</strong> ' . __('completados', 'breogan-lms') . '</span>';
        echo '<span class="progress-stat"><strong>' . $avg_progress . '%</strong> ' . __('promedio', 'breogan-lms') . '</span>';
        echo '</div>';
        
        // Simple progress bar
        echo '<div class="admin-progress-bar"><div style="width:' . $avg_progress . '%"></div></div>';
    }
    
    /**
     * Get users who have access to a course
     */
    private function get_course_users($course_id) {
        global $wpdb;
        
        // Query for both prefixes
        $blms_users = $wpdb->get_col($wpdb->prepare(
            "SELECT user_id FROM {$wpdb->usermeta} 
             WHERE meta_key = %s AND meta_value = %s",
            'blms_curso_' . $course_id,
            'comprado'
        ));
        
        $breogan_users = $wpdb->get_col($wpdb->prepare(
            "SELECT user_id FROM {$wpdb->usermeta} 
             WHERE meta_key = %s AND meta_value = %s",
            'breogan_curso_' . $course_id,
            'comprado'
        ));
        
        // Combine and make unique
        return array_unique(array_merge($blms_users, $breogan_users));
    }
    
    /**
     * Get all courses a user has access to
     * 
     * @param int $user_id User ID
     * @return array Array of course objects
     */
    private function get_user_courses($user_id) {
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
     * Check if a user has access to a course
     * 
     * @param int $user_id User ID
     * @param int $course_id Course ID
     * @return boolean Whether the user has access
     */
    private function user_has_course_access($user_id, $course_id) {
        // Check both prefixes for compatibility
        $has_access_blms = get_user_meta($user_id, 'blms_curso_' . $course_id, true);
        $has_access_breogan = get_user_meta($user_id, 'breogan_curso_' . $course_id, true);
        
        return ($has_access_blms === 'comprado' || $has_access_breogan === 'comprado');
    }
    
    /**
     * Check if a user has access to a lesson
     * 
     * @param int $user_id User ID
     * @param int $lesson_id Lesson ID
     * @return boolean Whether the user has access
     */
    private function user_has_lesson_access($user_id, $lesson_id) {
        // Get theme ID
        $theme_id = get_post_meta($lesson_id, '_blms_tema_relacionado', true);
        if (empty($theme_id)) {
            $theme_id = get_post_meta($lesson_id, '_tema_relacionado', true);
        }
        
        if (empty($theme_id)) {
            return false;
        }
        
        // Get course ID
        $course_id = get_post_meta($theme_id, '_blms_curso_relacionado', true);
        if (empty($course_id)) {
            $course_id = get_post_meta($theme_id, '_curso_relacionado', true);
        }
        
        if (empty($course_id)) {
            return false;
        }
        
        // Check if user has access to the course
        return $this->user_has_course_access($user_id, $course_id);
    }
    
    /**
     * Check if a lesson is completed by a user
     * 
     * @param int $user_id User ID
     * @param int $lesson_id Lesson ID
     * @return boolean Whether the lesson is completed
     */
    private function is_lesson_completed($user_id, $lesson_id) {
        // Check both prefixes for compatibility
        $completed_blms = get_user_meta($user_id, 'blms_leccion_completada_' . $lesson_id, true);
        $completed_breogan = get_user_meta($user_id, 'breogan_leccion_' . $lesson_id . '_completada', true);
        
        return !empty($completed_blms) || !empty($completed_breogan);
    }
    
    /**
     * Calculate course progress for a user
     * 
     * @param int $user_id User ID
     * @param int $course_id Course ID
     * @return array Progress data
     */
    private function calculate_course_progress($user_id, $course_id) {
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
                
                if ($this->is_lesson_completed($user_id, $lesson->ID)) {
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

// Initialize the class
new Breogan_LMS_Progress();