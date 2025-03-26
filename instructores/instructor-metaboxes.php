<?php
/**
 * Añadir metaboxes para la información del instructor
 */
function breogan_lms_add_instructor_metaboxes() {
    // Metabox para información de contacto
    add_meta_box(
        'breogan_instructor_contact',
        __('Información de Contacto', 'breogan-lms'),
        'breogan_instructor_contact_callback',
        'blms_instructor',
        'normal',
        'high'
    );

    // Metabox para redes sociales
    add_meta_box(
        'breogan_instructor_social',
        __('Redes Sociales', 'breogan-lms'),
        'breogan_instructor_social_callback',
        'blms_instructor',
        'normal',
        'default'
    );

    // Metabox para información profesional
    add_meta_box(
        'breogan_instructor_professional',
        __('Información Profesional', 'breogan-lms'),
        'breogan_instructor_professional_callback',
        'blms_instructor',
        'normal',
        'default'
    );

    // Metabox para cursos impartidos
    add_meta_box(
        'breogan_instructor_courses',
        __('Cursos Impartidos', 'breogan-lms'),
        'breogan_instructor_courses_callback',
        'blms_instructor',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'breogan_lms_add_instructor_metaboxes');

/**
 * Callback para información de contacto
 */
function breogan_instructor_contact_callback($post) {
    wp_nonce_field('breogan_instructor_contact_data', 'breogan_instructor_contact_nonce');
    
    // Obtener valores guardados
    $email = get_post_meta($post->ID, '_instructor_email', true);
    $phone = get_post_meta($post->ID, '_instructor_phone', true);
    $website = get_post_meta($post->ID, '_instructor_website', true);
    ?>
    <p>
        <label for="instructor_email"><?php _e('Email:', 'breogan-lms'); ?></label><br>
        <input type="email" id="instructor_email" name="instructor_email" value="<?php echo esc_attr($email); ?>" class="widefat">
    </p>
    <p>
        <label for="instructor_phone"><?php _e('Teléfono:', 'breogan-lms'); ?></label><br>
        <input type="text" id="instructor_phone" name="instructor_phone" value="<?php echo esc_attr($phone); ?>" class="widefat">
    </p>
    <p>
        <label for="instructor_website"><?php _e('Sitio Web:', 'breogan-lms'); ?></label><br>
        <input type="url" id="instructor_website" name="instructor_website" value="<?php echo esc_url($website); ?>" class="widefat">
    </p>
    <?php
}

/**
 * Callback para redes sociales
 */
function breogan_instructor_social_callback($post) {
    wp_nonce_field('breogan_instructor_social_data', 'breogan_instructor_social_nonce');
    
    // Obtener valores guardados
    $facebook = get_post_meta($post->ID, '_instructor_facebook', true);
    $twitter = get_post_meta($post->ID, '_instructor_twitter', true);
    $instagram = get_post_meta($post->ID, '_instructor_instagram', true);
    $linkedin = get_post_meta($post->ID, '_instructor_linkedin', true);
    $youtube = get_post_meta($post->ID, '_instructor_youtube', true);
    ?>
    <p>
        <label for="instructor_facebook"><?php _e('Facebook:', 'breogan-lms'); ?></label><br>
        <input type="url" id="instructor_facebook" name="instructor_facebook" value="<?php echo esc_url($facebook); ?>" class="widefat" placeholder="https://facebook.com/usuario">
    </p>
    <p>
        <label for="instructor_twitter"><?php _e('Twitter:', 'breogan-lms'); ?></label><br>
        <input type="url" id="instructor_twitter" name="instructor_twitter" value="<?php echo esc_url($twitter); ?>" class="widefat" placeholder="https://twitter.com/usuario">
    </p>
    <p>
        <label for="instructor_instagram"><?php _e('Instagram:', 'breogan-lms'); ?></label><br>
        <input type="url" id="instructor_instagram" name="instructor_instagram" value="<?php echo esc_url($instagram); ?>" class="widefat" placeholder="https://instagram.com/usuario">
    </p>
    <p>
        <label for="instructor_linkedin"><?php _e('LinkedIn:', 'breogan-lms'); ?></label><br>
        <input type="url" id="instructor_linkedin" name="instructor_linkedin" value="<?php echo esc_url($linkedin); ?>" class="widefat" placeholder="https://linkedin.com/in/usuario">
    </p>
    <p>
        <label for="instructor_youtube"><?php _e('YouTube:', 'breogan-lms'); ?></label><br>
        <input type="url" id="instructor_youtube" name="instructor_youtube" value="<?php echo esc_url($youtube); ?>" class="widefat" placeholder="https://youtube.com/channel/...">
    </p>
    <?php
}

/**
 * Callback para información profesional
 */
function breogan_instructor_professional_callback($post) {
    wp_nonce_field('breogan_instructor_professional_data', 'breogan_instructor_professional_nonce');
    
    // Obtener valores guardados
    $title = get_post_meta($post->ID, '_instructor_job_title', true);
    $experience = get_post_meta($post->ID, '_instructor_experience', true);
    $specialties = get_post_meta($post->ID, '_instructor_specialties', true);
    $education = get_post_meta($post->ID, '_instructor_education', true);
    ?>
    <p>
        <label for="instructor_job_title"><?php _e('Título Profesional:', 'breogan-lms'); ?></label><br>
        <input type="text" id="instructor_job_title" name="instructor_job_title" value="<?php echo esc_attr($title); ?>" class="widefat" placeholder="Ej: Psicólogo, Coach, Terapeuta...">
    </p>
    <p>
        <label for="instructor_experience"><?php _e('Años de Experiencia:', 'breogan-lms'); ?></label><br>
        <input type="number" id="instructor_experience" name="instructor_experience" value="<?php echo esc_attr($experience); ?>" min="0" class="small-text">
    </p>
    <p>
        <label for="instructor_specialties"><?php _e('Especialidades:', 'breogan-lms'); ?></label><br>
        <input type="text" id="instructor_specialties" name="instructor_specialties" value="<?php echo esc_attr($specialties); ?>" class="widefat" placeholder="Ej: Desarrollo personal, Nutrición, Mindfulness...">
        <span class="description"><?php _e('Separa las especialidades con comas', 'breogan-lms'); ?></span>
    </p>
    <p>
        <label for="instructor_education"><?php _e('Formación Académica:', 'breogan-lms'); ?></label><br>
        <textarea id="instructor_education" name="instructor_education" rows="3" class="widefat"><?php echo esc_textarea($education); ?></textarea>
        <span class="description"><?php _e('Incluye títulos académicos, certificaciones, etc.', 'breogan-lms'); ?></span>
    </p>
    <?php
}

/**
 * Callback para cursos impartidos
 */
function breogan_instructor_courses_callback($post) {
    wp_nonce_field('breogan_instructor_courses_data', 'breogan_instructor_courses_nonce');
    
    // Obtener todos los cursos disponibles
    $cursos = get_posts(array(
        'post_type' => 'blms_curso',
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ));
    
    // Si no hay cursos con el prefijo blms, intentar con el otro tipo
    if (empty($cursos)) {
        $cursos = get_posts(array(
            'post_type' => 'cursos',
            'numberposts' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));
    }
    
    // Obtener los cursos ya seleccionados
    $cursos_seleccionados = get_post_meta($post->ID, '_instructor_courses', true);
    if (!is_array($cursos_seleccionados)) {
        $cursos_seleccionados = array();
    }
    
    if (empty($cursos)) {
        echo '<p>' . __('No hay cursos disponibles. Crea algunos cursos primero.', 'breogan-lms') . '</p>';
    } else {
        echo '<div style="max-height: 200px; overflow-y: auto; margin-bottom: 10px; padding: 5px; border: 1px solid #ddd;">';
        foreach ($cursos as $curso) {
            $checked = in_array($curso->ID, $cursos_seleccionados) ? 'checked="checked"' : '';
            echo '<p>';
            echo '<label>';
            echo '<input type="checkbox" name="instructor_courses[]" value="' . $curso->ID . '" ' . $checked . '> ';
            echo esc_html($curso->post_title);
            echo '</label>';
            echo '</p>';
        }
        echo '</div>';
        echo '<p class="description">' . __('Selecciona los cursos que imparte este instructor.', 'breogan-lms') . '</p>';
    }
}

/**
 * Guardar datos de metaboxes
 */
function breogan_save_instructor_metaboxes($post_id) {
    // Verificar autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    
    // Verificar permisos
    if (!current_user_can('edit_post', $post_id)) return;
    
    // Verificar tipo de post
    if (get_post_type($post_id) != 'blms_instructor') return;
    
    // Guardar información de contacto
    if (isset($_POST['breogan_instructor_contact_nonce']) && wp_verify_nonce($_POST['breogan_instructor_contact_nonce'], 'breogan_instructor_contact_data')) {
        if (isset($_POST['instructor_email'])) {
            update_post_meta($post_id, '_instructor_email', sanitize_email($_POST['instructor_email']));
        }
        if (isset($_POST['instructor_phone'])) {
            update_post_meta($post_id, '_instructor_phone', sanitize_text_field($_POST['instructor_phone']));
        }
        if (isset($_POST['instructor_website'])) {
            update_post_meta($post_id, '_instructor_website', esc_url_raw($_POST['instructor_website']));
        }
    }
    
    // Guardar redes sociales
    if (isset($_POST['breogan_instructor_social_nonce']) && wp_verify_nonce($_POST['breogan_instructor_social_nonce'], 'breogan_instructor_social_data')) {
        if (isset($_POST['instructor_facebook'])) {
            update_post_meta($post_id, '_instructor_facebook', esc_url_raw($_POST['instructor_facebook']));
        }
        if (isset($_POST['instructor_twitter'])) {
            update_post_meta($post_id, '_instructor_twitter', esc_url_raw($_POST['instructor_twitter']));
        }
        if (isset($_POST['instructor_instagram'])) {
            update_post_meta($post_id, '_instructor_instagram', esc_url_raw($_POST['instructor_instagram']));
        }
        if (isset($_POST['instructor_linkedin'])) {
            update_post_meta($post_id, '_instructor_linkedin', esc_url_raw($_POST['instructor_linkedin']));
        }
        if (isset($_POST['instructor_youtube'])) {
            update_post_meta($post_id, '_instructor_youtube', esc_url_raw($_POST['instructor_youtube']));
        }
    }
    
    // Guardar información profesional
    if (isset($_POST['breogan_instructor_professional_nonce']) && wp_verify_nonce($_POST['breogan_instructor_professional_nonce'], 'breogan_instructor_professional_data')) {
        if (isset($_POST['instructor_job_title'])) {
            update_post_meta($post_id, '_instructor_job_title', sanitize_text_field($_POST['instructor_job_title']));
        }
        if (isset($_POST['instructor_experience'])) {
            update_post_meta($post_id, '_instructor_experience', absint($_POST['instructor_experience']));
        }
        if (isset($_POST['instructor_specialties'])) {
            update_post_meta($post_id, '_instructor_specialties', sanitize_text_field($_POST['instructor_specialties']));
        }
        if (isset($_POST['instructor_education'])) {
            update_post_meta($post_id, '_instructor_education', sanitize_textarea_field($_POST['instructor_education']));
        }
    }
    
    // Guardar cursos impartidos
    if (isset($_POST['breogan_instructor_courses_nonce']) && wp_verify_nonce($_POST['breogan_instructor_courses_nonce'], 'breogan_instructor_courses_data')) {
        if (isset($_POST['instructor_courses'])) {
            $cursos = array_map('absint', $_POST['instructor_courses']);
            update_post_meta($post_id, '_instructor_courses', $cursos);
            
            // También actualizamos los cursos para que sepan quién es su instructor
            foreach ($cursos as $curso_id) {
                update_post_meta($curso_id, '_blms_instructor_id', $post_id);
            }
        } else {
            // Si no se seleccionaron cursos, guardamos un array vacío
            update_post_meta($post_id, '_instructor_courses', array());
        }
    }
}
add_action('save_post', 'breogan_save_instructor_metaboxes');