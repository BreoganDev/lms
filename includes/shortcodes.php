<?php
// Evitar acceso directo
if (!defined('ABSPATH')) exit;

// Shortcode para mostrar los cursos
function breogan_lms_courses_shortcode($atts) {
    ob_start(); // Iniciar buffer de salida

    // Obtener cursos
    $cursos = get_posts(array(
        'post_type'   => 'cursos',
        'numberposts' => -1, // Obtener todos los cursos
        'orderby'     => 'date',
        'order'       => 'DESC'
    ));

    if ($cursos) {
        echo '<div class="breogan-cursos">';
        foreach ($cursos as $curso) {
            $curso_id = $curso->ID;
            $titulo = get_the_title($curso_id);
            $link = get_permalink($curso_id);
            $imagen = get_the_post_thumbnail($curso_id, 'medium');

            echo '<div class="breogan-curso">';
            if ($imagen) {
                echo '<a href="' . esc_url($link) . '">' . $imagen . '</a>';
            }
            echo '<h3><a href="' . esc_url($link) . '">' . esc_html($titulo) . '</a></h3>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>No hay cursos disponibles.</p>';
    }

    return ob_get_clean(); // Retornar el contenido generado
}

// Registrar el shortcode [breogan_cursos]
add_shortcode('breogan_cursos', 'breogan_lms_courses_shortcode');


// **MOVER AQUÍ** - Shortcode para mostrar el perfil del usuario
function breogan_perfil_usuario_shortcode() {
    ob_start();
    include BREOGAN_LMS_PATH . 'templates/perfil-usuario.php';
    return ob_get_clean();
}

// Registrar el shortcode [breogan_perfil]
add_shortcode('breogan_perfil', 'breogan_perfil_usuario_shortcode');
