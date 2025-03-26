<?php
/**
 * AJAX Handlers for Progress Tracking in Breogan LMS
 * 
 * Provides endpoints for:
 * - Marking lessons as complete
 * - Getting course progress information
 * - Checking theme completion
 * 
 * @package Breogan LMS
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Class to handle AJAX requests for course progress tracking
 */
class Breogan_LMS_Progress_AJAX {
    
    /**
     * Constructor - register AJAX hooks
     */
    public function __construct() {
        // Get course progress
        add_action('wp_ajax_breogan_get_course_progress', array($this, 'get_course_progress'));
        add_action('wp_ajax_nopriv_breogan_get_course_progress', array($this, 'get_course_progress'));
        
        // Mark lesson as complete
        add_action('wp_ajax_blms_mark_lesson_complete', array($this, 'mark_lesson_complete'));
        
        // Check theme completion
        add_action('wp_ajax_breogan_check_theme_completion', array($this, 'check_theme_completion'));
        
        // Ensure scripts are loaded
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    /**
     * Enqueue necessary scripts
     */
    public function enqueue_scripts() {
        // Only enqueue on relevant pages
        if (!is_singular(array('blms_curso', 'blms_tema', 'blms_leccion', 'cursos', 'temas', 'lecciones'))) {
            return;
        }
        
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
     * Get course progress information
     */
    public function get_course_progress() {
        // Check if course ID is set
        if (!isset($_POST['course_id'])) {
            wp_send_json_error(array('message' => __('ID de curso no especificado', 'breogan-lms')));
            return;
        }
        
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'breogan_progress_nonce')) {
            wp_send_json_error(array('message' => __('Error de seguridad', 'breogan-lms')));
            return;
        }
        
        $course_id = intval($_POST['course_id']);
        $user_id = get_current_user_id();
        
        // If not logged in, return empty progress
        if (!$user_id) {
            wp_send_json_success(array(
                'percentage' => 0,
                'completed_lessons' => 0,
                'total_lessons' => 0,
                'status_class' => 'estado-pendiente',
                'status_text' => __('Pendiente', 'breogan-lms')
            ));
            return;
        }
        
        // Get course progress data
        $progress_data = $this->calculate_course_progress($user_id, $course_id);
        
        wp_send_json_success($progress_data);
    }
    
    /**
     * Check if all lessons in a theme are completed
     */
    public function check_theme_completion() {
        // Check if theme ID is set
        if (!isset($_POST['theme_id'])) {
            wp_send_json_error(array('message' => __('ID de tema no especificado', 'breogan-lms')));
            return;
        }
        
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'breogan_progress_nonce')) {
            wp_send_json_error(array('message' => __('Error de seguridad', 'breogan-lms')));
            return;
        }
        
        $theme_id = intval($_POST['theme_id']);
        $user_id = get_current_user_id();
        
        // If not logged in, return incomplete
        if (!$user_id) {
            wp_send_json_success(array('is_complete' => false));
            return;
        }
        
        // Get all lessons for this theme
        $lessons = $this->get_theme_lessons($theme_id);
        
        if (empty($lessons)) {
            wp_send_json_success(array('is_complete' => false));
            return;
        }
        
        // Check if all lessons are completed
        $all_completed = true;
        
        foreach ($lessons as $lesson) {
            if (!$this->is_lesson_completed($user_id, $lesson->ID)) {
                $all_completed = false;
                break;
            }
        }
        
        wp_send_json_success(array(
            'is_complete' => $all_completed,
            'total_lessons' => count($lessons)
        ));
    }
    
    /**
     * Mark a lesson as complete
     */
    public function mark_lesson_complete() {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('Debes iniciar sesión para marcar lecciones como completadas', 'breogan-lms')));
            return;
        }
        
        // Check if lesson ID is set
        if (!isset($_POST['leccion_id'])) {
            wp_send_json_error(array('message' => __('ID de lección no especificado', 'breogan-lms')));
            return;
        }
        
        // Verify nonce is set and valid
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'breogan_progress_nonce')) {
            wp_send_json_error(array('message' => __('Error de seguridad', 'breogan-lms')));
            return;
        }
        
        $lesson_id = intval($_POST['leccion_id']);
        $user_id = get_current_user_id();
        
        // Get theme related to this lesson
        $theme_id = $this->get_lesson_theme($lesson_id);
        
        // Mark lesson as completed - store timestamp
        $result = update_user_meta($user_id, 'blms_leccion_completada_' . $lesson_id, current_time('mysql'));
        
        // Also update with legacy prefix for compatibility
        update_user_meta($user_id, 'breogan_leccion_' . $lesson_id . '_completada', current_time('mysql'));
        
        if ($result) {
            // Get updated course progress
            $progress_data = array();
            
            if ($theme_id) {
                $course_id = $this->get_theme_course($theme_id);
                if ($course_id) {
                    $progress_data = $this->calculate_course_progress($user_id, $course_id);
                }
            }
            
            wp_send_json_success(array(
                'message' => __('Lección marcada como completada', 'breogan-lms'),
                'progress' => $progress_data
            ));
        } else {
            wp_send_json_error(array('message' => __('No se pudo marcar la lección como completada', 'breogan-lms')));
        }
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
        
        // Get all themes from this course
        $themes = $this->get_course_themes($course_id);
        
        foreach ($themes as $theme) {
            // Get all lessons from this theme
            $lessons = $this->get_theme_lessons($theme->ID);
            
            foreach ($lessons as $lesson) {
                $total_lessons++;
                
                // Check if lesson is completed
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
     * Get themes from a course
     *
     * @param int $course_id Course ID
     * @return array Array of theme objects
     */
    private function get_course_themes($course_id) {
        // Try with new post type first
        $themes = get_posts([
            'post_type'   => 'blms_tema',
            'meta_key'    => '_blms_curso_relacionado',
            'meta_value'  => $course_id,
            'numberposts' => -1,
            'orderby'     => 'menu_order',
            'order'       => 'ASC'
        ]);
        
        // If no results, try with legacy post type
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
        
        return $themes;
    }
    
    /**
     * Get lessons from a theme
     *
     * @param int $theme_id Theme ID
     * @return array Array of lesson objects
     */
    private function get_theme_lessons($theme_id) {
        // Try with new post type first
        $lessons = get_posts([
            'post_type'   => 'blms_leccion',
            'meta_key'    => '_blms_tema_relacionado',
            'meta_value'  => $theme_id,
            'numberposts' => -1,
            'orderby'     => 'menu_order',
            'order'       => 'ASC'
        ]);
        
        // If no results, try with legacy post type
        if (empty($lessons)) {
            $lessons = get_posts([
                'post_type'   => 'lecciones',
                'meta_key'    => '_tema_relacionado',
                'meta_value'  => $theme_id,
                'numberposts' => -1,
                'orderby'     => 'menu_order',
                'order'       => 'ASC'
            ]);
        }
        
        return $lessons;
    }
    
    /**
     * Get theme ID for a lesson
     *
     * @param int $lesson_id Lesson ID
     * @return int|false Theme ID or false if not found
     */
    private function get_lesson_theme($lesson_id) {
        // Try with new post type meta first
        $theme_id = get_post_meta($lesson_id, '_blms_tema_relacionado', true);
        
        // If not found, try with legacy meta
        if (empty($theme_id)) {
            $theme_id = get_post_meta($lesson_id, '_tema_relacionado', true);
        }
        
        return !empty($theme_id) ? $theme_id : false;
    }
    
    /**
     * Get course ID for a theme
     *
     * @param int $theme_id Theme ID
     * @return int|false Course ID or false if not found
     */
    private function get_theme_course($theme_id) {
        // Try with new post type meta first
        $course_id = get_post_meta($theme_id, '_blms_curso_relacionado', true);
        
        // If not found, try with legacy meta
        if (empty($course_id)) {
            $course_id = get_post_meta($theme_id, '_curso_relacionado', true);
        }
        
        return !empty($course_id) ? $course_id : false;
    }
}

// Initialize class
new Breogan_LMS_Progress_AJAX();