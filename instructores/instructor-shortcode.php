<?php
/**
 * Shortcode para mostrar instructores
 */
function breogan_lms_instructores_shortcode($atts) {
    // Definir atributos por defecto
    $atts = shortcode_atts(array(
        'cantidad' => 4,
        'orderby' => 'title',
        'order' => 'ASC',
        'mostrar_cursos' => 'no',
        'columnas' => 3,
    ), $atts);
    
    // Convertir a valores adecuados
    $cantidad = absint($atts['cantidad']);
    $mostrar_cursos = $atts['mostrar_cursos'] === 'si' || $atts['mostrar_cursos'] === 'yes';
    $columnas = absint($atts['columnas']);
    if ($columnas < 1 || $columnas > 4) {
        $columnas = 3;
    }
    
    // Consultar instructores
    $instructores = get_posts(array(
        'post_type' => 'blms_instructor',
        'posts_per_page' => $cantidad,
        'orderby' => $atts['orderby'],
        'order' => $atts['order'],
    ));
    
    if (empty($instructores)) {
        return '<p>' . __('No hay instructores disponibles.', 'breogan-lms') . '</p>';
    }
    
    // Comenzar a capturar la salida
    ob_start();
    ?>
    <div class="breogan-instructores-grid columnas-<?php echo esc_attr($columnas); ?>">
        <?php foreach ($instructores as $instructor) : 
            $instructor_id = $instructor->ID;
            $job_title = get_post_meta($instructor_id, '_instructor_job_title', true);
            
            // Obtener cursos impartidos si se necesitan
            $cursos_impartidos = array();
            if ($mostrar_cursos) {
                $cursos_impartidos = get_post_meta($instructor_id, '_instructor_courses', true);
                if (!is_array($cursos_impartidos)) {
                    $cursos_impartidos = array();
                }
            }
        ?>
            <div class="instructor-shortcode-card">
                <div class="instructor-header">
                    <a href="<?php echo get_permalink($instructor_id); ?>" class="instructor-imagen-link">
                        <?php if (has_post_thumbnail($instructor_id)) : ?>
                            <?php echo get_the_post_thumbnail($instructor_id, 'medium', array('class' => 'instructor-imagen')); ?>
                        <?php else : ?>
                            <div class="instructor-imagen-placeholder">
                                <span class="iniciales"><?php echo substr(get_the_title($instructor_id), 0, 1); ?></span>
                            </div>
                        <?php endif; ?>
                    </a>
                    
                    <h3 class="instructor-nombre">
                        <a href="<?php echo get_permalink($instructor_id); ?>"><?php echo get_the_title($instructor_id); ?></a>
                    </h3>
                    
                    <?php if (!empty($job_title)) : ?>
                        <p class="instructor-titulo"><?php echo esc_html($job_title); ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="instructor-extracto">
                    <?php 
                    $excerpt = get_the_excerpt($instructor_id);
                    if (empty($excerpt)) {
                        $excerpt = wp_trim_words(get_post_field('post_content', $instructor_id), 20, '...');
                    }
                    echo wpautop($excerpt);
                    ?>
                </div>
                
                <a href="<?php echo get_permalink($instructor_id); ?>" class="ver-instructor">
                    <?php _e('Ver perfil completo', 'breogan-lms'); ?>
                </a>
                
                <?php if ($mostrar_cursos && !empty($cursos_impartidos)) : ?>
                    <div class="instructor-cursos-mini">
                        <h4><?php _e('Cursos impartidos', 'breogan-lms'); ?></h4>
                        <ul class="cursos-lista">
                            <?php 
                            $i = 0;
                            foreach ($cursos_impartidos as $curso_id) : 
                                $curso = get_post($curso_id);
                                if ($curso && $curso->post_status == 'publish' && $i < 3) : // Limitar a 3 cursos por instructor
                                    $i++;
                            ?>
                                <li>
                                    <a href="<?php echo get_permalink($curso_id); ?>">
                                        <?php echo get_the_title($curso_id); ?>
                                    </a>
                                </li>
                            <?php endif; endforeach; ?>
                            
                            <?php if (count($cursos_impartidos) > 3) : ?>
                                <li class="ver-mas">
                                    <a href="<?php echo get_permalink($instructor_id); ?>">
                                        <?php _e('Ver más cursos...', 'breogan-lms'); ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    
    <style>
   .breogan-instructores-grid {
    display: grid;
    gap: 30px;
    margin-top: 20px;
    margin-bottom: 30px;
}

.breogan-instructores-grid.columnas-1 {
    grid-template-columns: 1fr;
}

.breogan-instructores-grid.columnas-2 {
    grid-template-columns: repeat(2, 1fr);
}

.breogan-instructores-grid.columnas-3 {
    grid-template-columns: repeat(3, 1fr);
}

.breogan-instructores-grid.columnas-4 {
    grid-template-columns: repeat(4, 1fr);
}

.instructor-shortcode-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    display: flex;
    flex-direction: column;
}

.instructor-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin-bottom: 15px;
}

.instructor-imagen-link {
    display: block;
    margin-bottom: 15px;
}

.instructor-imagen {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
}

.instructor-imagen-placeholder {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background-color: #6366F1;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: bold;
}

.instructor-nombre {
    margin: 10px 0 5px;
    font-size: 1.2rem;
    color: #333;
}

.instructor-titulo {
    color: #6b7280;
    margin-bottom: 15px;
    font-size: 0.9rem;
}

.instructor-extracto {
    flex-grow: 1;
    margin-bottom: 15px;
    color: #4b5563;
    line-height: 1.6;
}

.ver-instructor {
    display: inline-block;
    text-align: center;
    padding: 8px 15px;
    background-color: #6366F1;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s ease;
    margin-bottom: 15px;
}

.ver-instructor:hover {
    background-color: #4F46E5;
}

.instructor-cursos-mini {
    border-top: 1px solid #e5e7eb;
    padding-top: 15px;
}

.instructor-cursos-mini h4 {
    margin: 0 0 10px;
    color: #374151;
    font-size: 1rem;
}

.instructor-cursos-mini .cursos-lista {
    list-style: none;
    padding: 0;
    margin: 0;
}

.instructor-cursos-mini .cursos-lista li {
    margin-bottom: 5px;
}

.instructor-cursos-mini .cursos-lista a {
    color: #6366F1;
    text-decoration: none;
    transition: color 0.3s ease;
}

.instructor-cursos-mini .cursos-lista a:hover {
    color: #4F46E5;
    text-decoration: underline;
}

.instructor-cursos-mini .ver-mas a {
    color: #6b7280;
    font-style: italic;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .breogan-instructores-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 20px;
    }
}

@media (max-width: 480px) {
    .breogan-instructores-grid {
        grid-template-columns: 1fr !important;
        gap: 15px;
    }
}
<?php
    return ob_get_clean();
}