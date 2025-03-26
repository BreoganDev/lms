<?php
/**
 * Plantilla para mostrar un tema individual
 *
 * @package Breogan LMS
 */

get_header();

// Verificar si el usuario está logueado
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit();
}

// Obtener datos relevantes
$tema_id = get_the_ID();
$user_id = get_current_user_id();
$curso_id = get_post_meta($tema_id, '_blms_curso_relacionado', true);

// Verificar si el usuario tiene acceso
$ha_comprado_curso = get_user_meta($user_id, 'blms_curso_' . $curso_id, true) === 'comprado';
$ha_acceso_tema = get_user_meta($user_id, 'blms_tema_' . $tema_id, true) === 'acceso';

// Verificar acceso
if (!$ha_comprado_curso && !$ha_acceso_tema) {
    ?>
    <main class="breogan-contenedor">
        <div class="mensaje-error">
            <h2><?php _e('Acceso Restringido', 'breogan-lms'); ?></h2>
            <p><?php _e('Debes estar inscrito en este curso para acceder a este tema.', 'breogan-lms'); ?></p>
<?php if ($curso_id) : ?>
<div class="breogan-navegacion">
    <a href="<?php echo get_permalink($curso_id); ?>" class="breogan-volver-curso">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
        </svg>
        <?php _e('Volver al curso: ', 'breogan-lms'); ?><?php echo get_the_title($curso_id); ?>
    </a>
</div>
<?php endif; ?>
        </div>
    </main>
    <?php
    get_footer();
    exit();
}
?>

<main class="breogan-contenedor">
    <article class="breogan-tema">
        <header class="breogan-tema-header">
            <h1 class="breogan-tema-titulo"><?php the_title(); ?></h1>
            
            <?php if ($curso_id) : ?>
                <p class="breogan-tema-curso">
                    <a href="<?php echo get_permalink($curso_id); ?>" class="breogan-btn-link">
                        <span class="dashicons dashicons-arrow-left-alt"></span> 
                        <?php _e('Volver al curso:', 'breogan-lms'); ?> 
                        <?php echo get_the_title($curso_id); ?>
                    </a>
                </p>
            <?php endif; ?>
        </header>

        <div class="breogan-tema-contenido">
            <?php 
            if (has_excerpt()) {
                echo '<div class="breogan-tema-descripcion">';
                the_excerpt();
                echo '</div>';
            }
            
            the_content();
            ?>
        </div>

       <div class="breogan-curso-acceso">
    <p class="mensaje-exito">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <?php _e('Ya tienes acceso a este curso. Explora los temas y lecciones:', 'breogan-lms'); ?>
    </p>

    <!-- Mostrar los Temas del Curso -->
    <h2><?php _e('Temas del Curso', 'breogan-lms'); ?></h2>
    <ul class="breogan-lista-temas">
        <?php
        // Obtener temas relacionados con este curso
        $temas = get_posts([
            'post_type'   => 'blms_tema',
            'meta_key'    => '_blms_curso_relacionado',
            'meta_value'  => $curso_id,
            'numberposts' => -1,
            'orderby'     => 'menu_order',
            'order'       => 'ASC'
        ]);

        if ($temas) :
            foreach ($temas as $index => $tema) : ?>
                <li>
                    <a href="<?php echo get_permalink($tema->ID); ?>">
                        <div class="tema-header">
                            <span class="tema-numero"><?php echo $index + 1; ?></span>
                            <span class="tema-titulo"><?php echo get_the_title($tema->ID); ?></span>
                        </div>
                        <?php 
                        // Obtener lecciones de este tema para mostrar contador
                        $lecciones_count = get_posts([
                            'post_type'   => 'blms_leccion',
                            'meta_key'    => '_blms_tema_relacionado',
                            'meta_value'  => $tema->ID,
                            'numberposts' => -1,
                            'fields'      => 'ids'
                        ]);
                        if (count($lecciones_count) > 0) : ?>
                            <span class="tema-contador"><?php echo sprintf(_n('%s lección', '%s lecciones', count($lecciones_count), 'breogan-lms'), count($lecciones_count)); ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <?php 
                    // Obtener lecciones de este tema
                    $lecciones = get_posts([
                        'post_type'   => 'blms_leccion',
                        'meta_key'    => '_blms_tema_relacionado',
                        'meta_value'  => $tema->ID,
                        'numberposts' => -1,
                        'orderby'     => 'menu_order',
                        'order'       => 'ASC'
                    ]);
                    
                    if ($lecciones) : ?>
                        <ul class="breogan-lista-lecciones">
                            <?php foreach ($lecciones as $key => $leccion) : 
                                // Verificar si la lección está completada
                                $user_handler = new Breogan_LMS_User();
                                $completada = $user_handler->is_lesson_completed($user_id, $leccion->ID);
                                
                                $clase_leccion = $completada ? 'leccion-completada' : '';
                                ?>
                                <li class="<?php echo $clase_leccion; ?>">
                                    <a href="<?php echo get_permalink($leccion->ID); ?>">
                                        <?php if ($completada) : ?>
                                            <span class="leccion-check">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                                </svg>
                                            </span>
                                        <?php else : ?>
                                            <span class="leccion-number"><?php echo $key + 1; ?></span>
                                        <?php endif; ?>
                                        <span class="leccion-titulo"><?php echo get_the_title($leccion->ID); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach;
        else : ?>
            <li class="no-temas">
                <p><?php _e('No hay temas disponibles en este curso.', 'breogan-lms'); ?></p>
            </li>
        <?php endif; ?>
    </ul>
</div>

        <?php if ($curso_id) : ?>
            <div class="breogan-navegacion">
                <a href="<?php echo get_permalink($curso_id); ?>" class="breogan-btn breogan-btn-volver">
                    <span class="dashicons dashicons-arrow-left-alt"></span> 
                    <?php _e('Volver al Curso', 'breogan-lms'); ?>
                </a>
            </div>
        <?php endif; ?>
    </article>
</main>

<?php get_footer(); ?>