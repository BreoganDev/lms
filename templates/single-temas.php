<?php
get_header();

// Verificar si el usuario está logueado
if (!is_user_logged_in()) {
    echo '<p style="color:red; font-weight:bold;">🔴 Usuario no logueado. Redirigiendo...</p>';
    echo '<script>window.location.href = "' . wp_login_url(get_permalink()) . '";</script>';
    exit();
}

// Obtener el ID del usuario y del tema actual
$user_id = get_current_user_id();
$tema_id = get_the_ID();
$curso_id = get_post_meta($tema_id, '_curso_relacionado', true);
$ha_comprado_curso = get_user_meta($user_id, 'breogan_curso_' . $curso_id, true);
$ha_acceso_tema = get_user_meta($user_id, 'breogan_tema_' . $tema_id, true);

// ✅ Solución: Permitir acceso si el usuario ha comprado el Curso O el Tema
if (!$ha_comprado_curso && !$ha_acceso_tema) {
    echo '<main class="contenedor seccion">';
    echo '<h1 class="texto-center texto-primary">Acceso Restringido</h1>';
    echo '<p>Debes estar inscrito en este curso para acceder a este tema.</p>';
    if ($curso_id) {
        echo '<a href="' . get_permalink($curso_id) . '" class="btn">Volver al Curso</a>';
    }
    echo '</main>';
    get_footer();
    exit();
}
?>

<main class="contenedor seccion">
    <h1 class="texto-center texto-primary"><?php the_title(); ?></h1>

    <p><a href="<?php echo get_permalink($curso_id); ?>">← Volver al Curso</a></p>

    <div class="contenido-tema">
        <h2>Contenido del Tema</h2>
        <?php 
        if (has_excerpt()) {
            the_excerpt(); // Muestra el extracto si existe
        } else {
            the_content(); // Muestra el contenido completo
        }
        ?>
    </div>

    <h2>Lecciones del Tema</h2>
    <ul class="lista-lecciones">
        <?php
        $lecciones = get_posts([
            'post_type'   => 'lecciones',
            'meta_key'    => '_tema_relacionado',
            'meta_value'  => get_the_ID(),
            'numberposts' => -1,
            'orderby'     => 'menu_order',
            'order'       => 'ASC'
        ]);

        if ($lecciones) {
            foreach ($lecciones as $leccion) {
                echo '<li><a href="' . get_permalink($leccion->ID) . '">' . get_the_title($leccion->ID) . '</a></li>';
            }
        } else {
            echo '<p>No hay lecciones en este tema.</p>';
        }
        ?>
    </ul>
</main>

<?php get_footer(); ?>
