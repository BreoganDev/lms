<?php
/**
 * Clase para gestionar metaboxes y campos personalizados
 */
class Breogan_LMS_Metaboxes {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Añadir metaboxes
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        
        // Guardar datos de metaboxes
        add_action('save_post', array($this, 'save_meta_boxes'), 10, 2);
    }
    
    /**
     * Añadir todos los metaboxes
     */
    public function add_meta_boxes() {
        // Metabox para precios en cursos
        add_meta_box(
            'blms_precio_curso',
            __('Precio del Curso (€)', 'breogan-lms'),
            array($this, 'precio_curso_callback'),
            'blms_curso',
            'side',
            'high'
        );
        
        // Metabox para relacionar temas con cursos
        add_meta_box(
            'blms_curso_relacionado',
            __('Curso al que pertenece', 'breogan-lms'),
            array($this, 'curso_relacionado_callback'),
            'blms_tema',
            'side',
            'default'
        );
        
        // Metabox para relacionar lecciones con temas
        add_meta_box(
            'blms_tema_relacionado',
            __('Tema al que pertenece', 'breogan-lms'),
            array($this, 'tema_relacionado_callback'),
            'blms_leccion',
            'side',
            'default'
        );
    }
    
    /**
     * Callback para el metabox de precio del curso
     * 
     * @param WP_Post $post Objeto post actual
     */
    public function precio_curso_callback($post) {
    // Añadir nonce para seguridad
    wp_nonce_field('blms_precio_curso_nonce', 'blms_precio_curso_nonce');
    
    // Obtener valores guardados
    $precio = get_post_meta($post->ID, '_blms_precio_curso', true);
    $es_gratuito = get_post_meta($post->ID, '_blms_curso_gratuito', true);
    
    ?>
    <div class="blms-precio-wrapper">
        <p>
            <label for="blms_curso_gratuito">
                <input type="checkbox" id="blms_curso_gratuito" name="blms_curso_gratuito" value="1" <?php checked($es_gratuito, '1'); ?>>
                <?php _e('Este es un curso gratuito', 'breogan-lms'); ?>
            </label>
        </p>
        <p class="precio-container" <?php echo $es_gratuito ? 'style="display:none;"' : ''; ?>>
            <label for="blms_precio_curso"><?php _e('Precio (€):', 'breogan-lms'); ?></label>
            <input type="number" id="blms_precio_curso" name="blms_precio_curso" 
                  value="<?php echo esc_attr($precio); ?>" step="0.01" min="0" class="widefat">
        </p>
        <p class="description"><?php _e('Si el curso es gratuito, el campo de precio será ignorado.', 'breogan-lms'); ?></p>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('#blms_curso_gratuito').on('change', function() {
            if ($(this).is(':checked')) {
                $('.precio-container').hide();
            } else {
                $('.precio-container').show();
            }
        });
    });
    </script>
    <?php
}
    
    /**
     * Callback para el metabox de curso relacionado
     * 
     * @param WP_Post $post Objeto post actual
     */
    public function curso_relacionado_callback($post) {
        // Añadir nonce para seguridad
        wp_nonce_field('blms_curso_relacionado_nonce', 'blms_curso_relacionado_nonce');
        
        // Obtener valor guardado
        $curso_id = get_post_meta($post->ID, '_blms_curso_relacionado', true);
        
        // Obtener todos los cursos
        $cursos = get_posts(array(
            'post_type' => 'blms_curso',
            'numberposts' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));
        
        ?>
        <select name="blms_curso_relacionado" id="blms_curso_relacionado" class="widefat">
            <option value=""><?php _e('Seleccionar Curso', 'breogan-lms'); ?></option>
            <?php foreach ($cursos as $curso) : ?>
                <option value="<?php echo $curso->ID; ?>" <?php selected($curso_id, $curso->ID); ?>>
                    <?php echo esc_html($curso->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php _e('Selecciona el curso al que pertenece este tema.', 'breogan-lms'); ?></p>
        <?php
    }
    
    /**
     * Callback para el metabox de tema relacionado
     * 
     * @param WP_Post $post Objeto post actual
     */
    public function tema_relacionado_callback($post) {
        // Añadir nonce para seguridad
        wp_nonce_field('blms_tema_relacionado_nonce', 'blms_tema_relacionado_nonce');
        
        // Obtener valor guardado
        $tema_id = get_post_meta($post->ID, '_blms_tema_relacionado', true);
        
        // Obtener todos los temas
        $temas = get_posts(array(
            'post_type' => 'blms_tema',
            'numberposts' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));
        
        ?>
        <select name="blms_tema_relacionado" id="blms_tema_relacionado" class="widefat">
            <option value=""><?php _e('Seleccionar Tema', 'breogan-lms'); ?></option>
            <?php foreach ($temas as $tema) : ?>
                <option value="<?php echo $tema->ID; ?>" <?php selected($tema_id, $tema->ID); ?>>
                    <?php echo esc_html($tema->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php _e('Selecciona el tema al que pertenece esta lección.', 'breogan-lms'); ?></p>
        <?php
    }
    
    /**
     * Guardar datos de metaboxes
     * 
     * @param int $post_id ID del post
     * @param WP_Post $post Objeto post
     */
    public function save_meta_boxes($post_id, $post) {
        // Verificar si es un autoguardado
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Guardar precio del curso
        if (isset($_POST['blms_precio_curso_nonce']) && 
        wp_verify_nonce($_POST['blms_precio_curso_nonce'], 'blms_precio_curso_nonce')) {
        
        // Guardar si es gratuito
        $es_gratuito = isset($_POST['blms_curso_gratuito']) ? '1' : '';
        update_post_meta($post_id, '_blms_curso_gratuito', $es_gratuito);
        
        // Guardar precio
        if (isset($_POST['blms_precio_curso'])) {
            $precio = sanitize_text_field($_POST['blms_precio_curso']);
            update_post_meta($post_id, '_blms_precio_curso', $precio);
        }
    }
        
        // Guardar curso relacionado
        if (isset($_POST['blms_curso_relacionado_nonce']) && 
            wp_verify_nonce($_POST['blms_curso_relacionado_nonce'], 'blms_curso_relacionado_nonce')) {
            
            if (isset($_POST['blms_curso_relacionado'])) {
                $curso_id = sanitize_text_field($_POST['blms_curso_relacionado']);
                update_post_meta($post_id, '_blms_curso_relacionado', $curso_id);
            }
        }
        
        // Guardar tema relacionado
        if (isset($_POST['blms_tema_relacionado_nonce']) && 
            wp_verify_nonce($_POST['blms_tema_relacionado_nonce'], 'blms_tema_relacionado_nonce')) {
            
            if (isset($_POST['blms_tema_relacionado'])) {
                $tema_id = sanitize_text_field($_POST['blms_tema_relacionado']);
                update_post_meta($post_id, '_blms_tema_relacionado', $tema_id);
            }
        }
    }
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
}

function breogan_lms_add_curso_metadatos() {
    add_meta_box(
        'breogan_lms_curso_metadatos',
        __('Información Adicional del Curso', 'breogan-lms'),
        'breogan_lms_curso_metadatos_callback',
        'blms_curso',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'breogan_lms_add_curso_metadatos');

function breogan_lms_curso_metadatos_callback($post) {
    // Instructor
    $instructor = get_post_meta($post->ID, '_blms_instructor_curso', true);
    echo '<label>Instructor:</label>';
    echo '<input type="text" name="blms_instructor_curso" value="' . esc_attr($instructor) . '" class="widefat">';

    // Duración
    $duration = get_post_meta($post->ID, '_blms_duracion_curso', true);
    echo '<label>Duración (horas):</label>';
    echo '<input type="number" name="blms_duracion_curso" value="' . esc_attr($duration) . '" class="widefat">';

    // Nivel
    $level = get_post_meta($post->ID, '_blms_nivel_curso', true);
    echo '<label>Nivel:</label>';
    echo '<select name="blms_nivel_curso" class="widefat">';
    echo '<option value="beginner" ' . selected($level, 'beginner', false) . '>Principiante</option>';
    echo '<option value="intermediate" ' . selected($level, 'intermediate', false) . '>Intermedio</option>';
    echo '<option value="advanced" ' . selected($level, 'advanced', false) . '>Avanzado</option>';
    echo '<option value="all-levels" ' . selected($level, 'all-levels', false) . '>Todos los niveles</option>';
    echo '</select>';
}

function breogan_lms_save_curso_metadatos($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (isset($_POST['blms_instructor_curso'])) {
        update_post_meta($post_id, '_blms_instructor_curso', sanitize_text_field($_POST['blms_instructor_curso']));
    }

    if (isset($_POST['blms_duracion_curso'])) {
        update_post_meta($post_id, '_blms_duracion_curso', sanitize_text_field($_POST['blms_duracion_curso']));
    }

    if (isset($_POST['blms_nivel_curso'])) {
        update_post_meta($post_id, '_blms_nivel_curso', sanitize_text_field($_POST['blms_nivel_curso']));
    }
}
<<<<<<< HEAD
add_action('save_post', 'breogan_lms_save_curso_metadatos');
=======
add_action('save_post', 'breogan_lms_save_curso_metadatos');
=======
}
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
