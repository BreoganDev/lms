<?php
/**
 * Plantilla para mostrar el archivo de cursos
 *
 * @package Breogan LMS
 */

get_header();
?>

<main class="breogan-contenedor">
    <header class="breogan-archive-header">
        <h1 class="breogan-archive-titulo"><?php _e('Catálogo de Cursos', 'breogan-lms'); ?></h1>
        
        <div class="breogan-archive-descripcion">
            <p><?php _e('Explora nuestro catálogo completo de cursos disponibles. Encuentra el curso que mejor se adapte a tus necesidades e intereses.', 'breogan-lms'); ?></p>
        </div>
    </header>

    <?php if (have_posts()) : ?>
        <div class="listado-cursos-grid">
           <?php while (have_posts()) : the_post(); 
    $curso_id = get_the_ID();
    $precio = get_post_meta($curso_id, '_blms_precio_curso', true);
    $es_gratuito = get_post_meta($curso_id, '_blms_curso_gratuito', true);
?>
    <div class="curso-item">
        <?php if ($es_gratuito == '1') : ?>
            <span class="curso-gratuito-label"><?php _e('Gratis', 'breogan-lms'); ?></span>
        <?php endif; ?>
        <a href="<?php the_permalink(); ?>">
            <div class="curso-imagen">
                <?php 
                if (has_post_thumbnail()) {
                    the_post_thumbnail('medium');
                } else {
                    echo '<img src="' . BREOGAN_LMS_URL . 'assets/images/curso-default.jpg" alt="' . get_the_title() . '">';
                }
                ?>
            </div>
            <div class="curso-contenido">
                <h3><?php the_title(); ?></h3>
                <?php if ($es_gratuito == '1') : ?>
                    <p class="curso-precio curso-gratuito"><?php _e('Curso Gratuito', 'breogan-lms'); ?></p>
                <?php elseif ($precio) : ?>
                    <p class="curso-precio"><?php echo esc_html($precio); ?> €</p>
                <?php endif; ?>
                <div class="btn-mas"><?php _e('Ver más', 'breogan-lms'); ?></div>
            </div>
        </a>
    </div>
<?php endwhile; ?>
        

        <?php 
        // Paginación
        the_posts_pagination(array(
            'mid_size' => 2,
            'prev_text' => __('Anterior', 'breogan-lms'),
            'next_text' => __('Siguiente', 'breogan-lms'),
            'screen_reader_text' => __('Navegación de cursos', 'breogan-lms')
        ));
        ?>

    <?php else : ?>
        <div class="mensaje-info">
            <p><?php _e('No hay cursos disponibles en este momento.', 'breogan-lms'); ?></p>
        </div>
    <?php endif; ?>
</main>

<<<<<<< HEAD


=======
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
<?php get_footer(); ?>