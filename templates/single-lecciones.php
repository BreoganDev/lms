<?php
get_header();

// Verificar si el usuario está logueado
if (!is_user_logged_in()) {
    echo '<p style="color:red; font-weight:bold;">🔴 Usuario no logueado. Redirigiendo...</p>';
    echo '<script>window.location.href = "' . wp_login_url(get_permalink()) . '";</script>';
    exit();
}

// Obtener ID de la Lección y su Tema Relacionado
$leccion_id = get_the_ID();
$tema_id = get_post_meta($leccion_id, '_tema_relacionado', true);
$curso_id = get_post_meta($tema_id, '_curso_relacionado', true);

// Verificar si el usuario tiene acceso al curso o al tema
$user_id = get_current_user_id();
$ha_comprado_curso = get_user_meta($user_id, 'breogan_curso_' . $curso_id, true);
$ha_acceso_tema = get_user_meta($user_id, 'breogan_tema_' . $tema_id, true);

// Si el usuario no tiene acceso, mostrar mensaje de restricción
if (!$ha_comprado_curso && !$ha_acceso_tema) {
    echo '<main class="contenedor seccion">';
    echo '<h1 class="texto-center texto-primary">Acceso Restringido</h1>';
    echo '<p>Debes estar inscrito en este curso para acceder a esta lección.</p>';
    echo '<a href="' . get_permalink($curso_id) . '" class="btn">Volver al Curso</a>';
    echo '</main>';
    get_footer();
    exit();
}
?>

<main class="contenedor seccion">
    <h1 class="texto-center texto-primary"><?php the_title(); ?></h1>

    <div class="contenido-leccion">
        <h2>Contenido de la Lección</h2>
        <?php the_content(); ?>
    </div>

    <div class="lista-lecciones-container">
        <h2>Otras Lecciones de este Tema</h2>
        <ul class="lista-lecciones">
            <?php
            $lecciones = get_posts([
                'post_type'   => 'lecciones',
                'meta_key'    => '_tema_relacionado',
                'meta_value'  => $tema_id,
                'numberposts' => -1,
                'orderby'     => 'menu_order',
                'order'       => 'ASC'
            ]);

            if ($lecciones) {
                foreach ($lecciones as $leccion) {
                    echo '<li><a href="' . get_permalink($leccion->ID) . '">' . get_the_title($leccion->ID) . '</a></li>';
                }
            } else {
                echo '<p>No hay más lecciones en este tema.</p>';
            }
            ?>
        </ul>
    </div>

    <p class="texto-center">
        <a href="<?php echo get_permalink($tema_id); ?>" class="btn">← Volver al Tema</a>
    </p>
</main>

<?php get_footer(); ?>
