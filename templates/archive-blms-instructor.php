<?php
/**
 * Plantilla para mostrar el archivo de instructores
 *
 * @package Breogan LMS
 */

get_header();
?>

<main class="breogan-contenedor instructores-archive">
    <header class="instructores-header">
        <h1 class="instructores-titulo"><?php _e('Nuestros Instructores', 'breogan-lms'); ?></h1>
        <div class="instructores-descripcion">
            <p><?php _e('Conoce a nuestros expertos e instructores que te guiarán en tu aprendizaje.', 'breogan-lms'); ?></p>
        </div>
    </header>

    <?php if (have_posts()) : ?>
        <div class="instructores-grid">
            <?php while (have_posts()) : the_post(); ?>
                <div class="instructor-card">
                    <a href="<?php the_permalink(); ?>" class="instructor-link">
                        <div class="instructor-imagen">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium'); ?>
                            <?php else : ?>
                                <div class="instructor-imagen-placeholder">
                                    <span class="iniciales"><?php echo substr(get_the_title(), 0, 1); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="instructor-info">
                            <h2 class="instructor-nombre"><?php the_title(); ?></h2>
                            
                            <?php 
                            // Mostrar título profesional si existe
                            $job_title = get_post_meta(get_the_ID(), '_instructor_job_title', true);
                            if (!empty($job_title)) : ?>
                                <p class="instructor-titulo"><?php echo esc_html($job_title); ?></p>
                            <?php endif; ?>
                            
                            <?php if (has_excerpt()) : ?>
                                <div class="instructor-extracto">
                                    <?php the_excerpt(); ?>
                                </div>
                            <?php endif; ?>
                            
                            <span class="ver-perfil"><?php _e('Ver perfil completo', 'breogan-lms'); ?></span>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
        
        <?php the_posts_pagination(array(
            'mid_size' => 2,
            'prev_text' => __('Anterior', 'breogan-lms'),
            'next_text' => __('Siguiente', 'breogan-lms'),
        )); ?>
        
    <?php else : ?>
        <div class="no-instructores">
            <p><?php _e('No hay instructores registrados actualmente.', 'breogan-lms'); ?></p>
        </div>
    <?php endif; ?>
</main>

<style>
/* Estilos para la página de instructores */
.instructores-archive {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.instructores-header {
    text-align: center;
    margin-bottom: 3rem;
}

.instructores-titulo {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: #333;
}

.instructores-descripcion {
    max-width: 800px;
    margin: 0 auto;
    font-size: 1.1rem;
    color: #555;
}

.instructores-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 2rem;
}

.instructor-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.instructor-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.instructor-link {
    display: block;
    text-decoration: none;
    color: inherit;
}

.instructor-imagen {
    height: 250px;
    overflow: hidden;
}

.instructor-imagen img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.instructor-card:hover .instructor-imagen img {
    transform: scale(1.05);
}

.instructor-imagen-placeholder {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f0f0f0;
}

.instructor-imagen-placeholder .iniciales {
    font-size: 5rem;
    color: #6366F1;
    font-weight: bold;
}

.instructor-info {
    padding: 1.5rem;
}

.instructor-nombre {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    color: #333;
}

.instructor-titulo {
    font-size: 1rem;
    color: #6366F1;
    margin-bottom: 1rem;
}

.instructor-extracto {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.ver-perfil {
    display: inline-block;
    color: #6366F1;
    font-weight: 600;
    font-size: 0.9rem;
    border-bottom: 2px solid #6366F1;
    padding-bottom: 2px;
    transition: color 0.3s ease, border-color 0.3s ease;
}

.instructor-card:hover .ver-perfil {
    color: #4F46E5;
    border-color: #4F46E5;
}

.no-instructores {
    text-align: center;
    padding: 3rem;
    background: #f9f9f9;
    border-radius: 10px;
}

@media (max-width: 768px) {
    .instructores-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
    
    .instructores-titulo {
        font-size: 2rem;
    }
}

@media (max-width: 480px) {
    .instructores-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php get_footer(); ?>