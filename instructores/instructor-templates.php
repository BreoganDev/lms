<?php
/**
 * Registrar plantillas personalizadas para instructores
 */
function breogan_lms_instructor_templates($template) {
    if (is_singular('blms_instructor')) {
        $new_template = locate_template(array('single-blms-instructor.php'));
        if ('' != $new_template) {
            return $new_template;
        }
        return plugin_dir_path(__FILE__) . 'templates/single-blms-instructor.php';
    }
    
    if (is_post_type_archive('blms_instructor')) {
        $new_template = locate_template(array('archive-blms-instructor.php'));
        if ('' != $new_template) {
            return $new_template;
        }
        return plugin_dir_path(__FILE__) . 'templates/archive-blms-instructor.php';
    }
    
    return $template;
}
add_filter('template_include', 'breogan_lms_instructor_templates');