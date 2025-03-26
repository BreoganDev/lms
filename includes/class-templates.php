<?php
/**
 * Clase para gestionar plantillas
 */
class Breogan_LMS_Templates {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Filtro de alto nivel para incluir plantillas
        add_filter('template_include', array($this, 'override_templates'), 99);
    }
    
    /**
     * Sobreescribir plantillas
     * 
     * @param string $template Ruta a la plantilla actual
     * @return string Ruta a la plantilla (original o sustituida)
     */
    public function override_templates($template) {
        // Obtener el tipo de post actual
        $post_type = get_post_type();
        
        // Registrar en el log para depuración
        error_log("Breogan LMS - Post Type: " . $post_type);
        error_log("Breogan LMS - Template: " . $template);
        
        // Definir qué plantillas queremos sobreescribir
        $templates_map = array(
            'blms_curso' => 'single-blms-curso.php',
            'blms_tema' => 'single-blms-tema.php',
            'blms_leccion' => 'single-blms-leccion.php'
        );
        
        // Si estamos en una vista de post individual y el tipo coincide con uno de nuestros CPTs
        if (is_singular() && array_key_exists($post_type, $templates_map)) {
            $file = $templates_map[$post_type];
            $new_template = $this->locate_template($file);
            
            // Si se encontró la plantilla, usarla
            if ($new_template) {
                error_log("Breogan LMS - Usando plantilla: " . $new_template);
                return $new_template;
            }
        }
        
        // Plantillas de archivo
        if (is_post_type_archive('blms_curso')) {
            $new_template = $this->locate_template('archive-blms-curso.php');
            if ($new_template) {
                return $new_template;
            }
        }
        
        return $template;
    }
    
    /**
     * Localizar una plantilla
     * 
     * Busca primero en el tema actual, luego en el tema padre, y finalmente en el plugin
     * 
     * @param string $template_name Nombre del archivo de plantilla
     * @return string|boolean Ruta al archivo de plantilla o false si no se encuentra
     */
    private function locate_template($template_name) {
        // Buscar en el tema actual
        $theme_template = locate_template(array(
            'breogan-lms/' . $template_name,
            $template_name
        ));
        
        // Si se encontró en el tema, devolver esa ruta
        if ($theme_template) {
            return $theme_template;
        }
        
        // Si no, buscar en el plugin
        $plugin_template = BREOGAN_LMS_PATH . 'templates/' . $template_name;
        
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
        
        return false;
    }
    
    /**
     * Obtener plantilla
     * 
     * Función pública para incluir una plantilla desde cualquier parte del plugin
     * 
     * @param string $template_name Nombre del archivo de plantilla
     * @param array $args Variables a extraer para la plantilla
     */
    public static function get_template($template_name, $args = array()) {
        // Extraer variables para la plantilla
        if (!empty($args) && is_array($args)) {
            extract($args);
        }
        
        // Buscar la plantilla usando nuestra función de localización
        $instance = new self();
        $template_path = $instance->locate_template($template_name);
        
        if ($template_path) {
            include $template_path;
        } else {
            _e('Plantilla no encontrada: ' . $template_name, 'breogan-lms');
        }
    }
}