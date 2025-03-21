<?php
// Evitar acceso si el usuario no está logueado
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

get_header();

$user_id = get_current_user_id();
?>

<main class="contenedor seccion">
    <h1 class="texto-center texto-primary">Tu Progreso</h1>

  

    <h2>Tus Cursos en Progreso</h2>
    <ul class="lista-cursos">
        <?php
        // Obtener todos los cursos en los que el usuario ha completado al menos una lección
        $cursos = get_posts(array(
            'post_type'   => 'cursos',
            'numberposts' => -1
        ));

        $cursos_en_progreso = [];

        foreach ($cursos as $curso) {
            $curso_id = $curso->ID;

            // Obtener todas las lecciones de los temas de este curso
            $temas = get_posts(array(
                'post_type'   => 'temas',
                'meta_key'    => '_curso_relacionado',
                'meta_value'  => $curso_id,
                'numberposts' => -1
            ));

            $total_lecciones = 0;
            $lecciones_completadas = 0;

            foreach ($temas as $tema) {
                $tema_id = $tema->ID;

                $lecciones = get_posts(array(
                    'post_type'   => 'lecciones',
                    'meta_key'    => '_tema_relacionado',
                    'meta_value'  => $tema_id,
                    'numberposts' => -1
                ));

                foreach ($lecciones as $leccion) {
                    $total_lecciones++;
                    if (get_user_meta($user_id, 'breogan_leccion_' . $leccion->ID, true)) {
                        $lecciones_completadas++;
                    }
                }
            }

            if ($total_lecciones > 0 && $lecciones_completadas > 0) {
                $porcentaje = round(($lecciones_completadas / $total_lecciones) * 100);
                $cursos_en_progreso[] = [
                    'curso' => $curso,
                    'porcentaje' => $porcentaje
                ];
            }
        }

        if (!empty($cursos_en_progreso)) {
            foreach ($cursos_en_progreso as $data) {
                echo '<li>';
                echo '<h3><a href="' . get_permalink($data['curso']->ID) . '">' . get_the_title($data['curso']->ID) . '</a></h3>';
                echo '<p>Progreso: ' . $data['porcentaje'] . '% completado</p>';
                echo '<div class="progreso-barra"><div style="width:' . $data['porcentaje'] . '%"></div></div>';
                echo '</li>';
            }
        } else {
            echo '<p>No tienes cursos en progreso.</p>';
        }
        ?>
    </ul>
</main>

<?php get_footer(); ?>
