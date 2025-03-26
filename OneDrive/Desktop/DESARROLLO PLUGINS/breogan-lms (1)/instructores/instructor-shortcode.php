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
        'estilo' => 'moderno', // Nuevo atributo para diferentes estilos
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
    
    // Generar un ID único para este conjunto de instructores
    $unique_id = 'instructores-' . uniqid();
    
    // Comenzar a capturar la salida
    ob_start();
    ?>
    <div id="<?php echo esc_attr($unique_id); ?>" class="breogan-instructores-grid columnas-<?php echo esc_attr($columnas); ?>">
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
    /* Estilos personalizados para este grupo específico de instructores */
    #<?php echo $unique_id; ?> {
        --accent-color: <?php echo apply_filters('breogan_instructor_accent_color', '#6366F1'); ?>;
        --accent-hover: <?php echo apply_filters('breogan_instructor_accent_hover', '#4F46E5'); ?>;
    }
    </style>
<?php
    // Asegúrate de que los estilos globales estén encolados
    if (!function_exists('breogan_enqueue_instructor_styles')) {
        // Definir una función que encole los estilos solo una vez
        function breogan_enqueue_instructor_styles() {
            // Si los estilos ya están encolados, no hacer nada
            static $enqueued = false;
            if ($enqueued) {
                return;
            }
            
            $enqueued = true;
            
            // Aquí incluirías el CSS que te proporcioné antes
            echo '<style id="breogan-instructor-shortcode-styles">
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
    background: var(--card-bg, #ffffff);
    color: var(--card-text, #333333);
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    display: flex;
    flex-direction: column;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.instructor-shortcode-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
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
    border: 3px solid var(--accent-color, #6366F1);
}

.instructor-imagen-placeholder {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background-color: var(--accent-color, #6366F1);
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
    color: var(--heading-color, #1a202c);
    font-weight: 600; /* Añadimos negrita */
}

.instructor-nombre a {
    color: var(--heading-color, #1a202c);
    text-decoration: none;
    transition: color 0.3s ease;
}

.instructor-nombre a:hover {
    color: var(--accent-color, #6366F1);
}

.instructor-titulo {
    color: var(--secondary-text, #6b7280);
    margin-bottom: 15px;
    font-size: 0.9rem;
}

body .instructor-shortcode-card .instructor-extracto {
    color: var(--extract-text, #2d3748) !important;
}

body .instructor-shortcode-card .instructor-nombre a {
    color: var(--heading-color, #1a202c) !important;
}

.ver-instructor {
    display: inline-block;
    text-align: center;
    padding: 8px 15px;
    background-color: var(--accent-color, #6366F1);
    color: white !important;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s ease, transform 0.2s ease;
    margin-bottom: 15px;
    font-weight: 500;
}

.ver-instructor:hover {
    background-color: var(--accent-hover, #4F46E5);
    transform: scale(1.05);
}

.instructor-cursos-mini {
    border-top: 1px solid var(--border-color, #e5e7eb);
    padding-top: 15px;
}

.instructor-cursos-mini h4 {
    margin: 0 0 10px;
    color: var(--heading-color, #374151);
    font-size: 1rem;
}

.instructor-cursos-mini .cursos-lista {
    list-style: none;
    padding: 0;
    margin: 0;
}

.instructor-cursos-mini .cursos-lista li {
    margin-bottom: 5px;
    padding: 3px 0;
}

.instructor-cursos-mini .cursos-lista a {
    color: var(--accent-color, #6366F1);
    text-decoration: none;
    transition: color 0.3s ease;
}

.instructor-cursos-mini .cursos-lista a:hover {
    color: var(--accent-hover, #4F46E5);
    text-decoration: underline;
}

.instructor-cursos-mini .ver-mas a {
    color: var(--secondary-text, #6b7280);
    font-style: italic;
}

/* CSS Variables para modo claro/oscuro */
:root {
    --card-bg: #ffffff;
    --card-text: #333333;
    --heading-color: #1a202c; /* Color más oscuro para títulos */
    --secondary-text: #4a5568; /* Color más oscuro para texto secundario */
    --accent-color: #6366F1;
    --accent-hover: #4F46E5;
    --border-color: #e5e7eb;
    --extract-text: #2d3748; /* Color específico para el texto del extracto */
}

/* Aplicar estilos para modo oscuro */
@media (prefers-color-scheme: dark) {
    :root {
        --card-bg: #1e293b;
        --card-text: #e2e8f0;
        --heading-color: #f1f5f9;
        --secondary-text: #94a3b8;
        --border-color: #334155;
        --extract-text: #cbd5e0;
    }
    
    /* Añade esto para compatibilidad con la clase dark-mode */
body.dark-mode, .dark-mode {
    --card-bg: #1e293b;
    --card-text: #e2e8f0;
    --heading-color: #f1f5f9;
    --secondary-text: #94a3b8;
    --border-color: #334155;
    --extract-text: #cbd5e0;
}
    
    .instructor-shortcode-card {
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }
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

.page-content {
    background-color: #ffffff12;
    border-radius: 0px;
    padding: 40px;
    color: var(--text-light);
    width: 100vw;
    max-width: 100vw;
    margin-left: calc(-50vw + 50%);
    box-sizing: border-box;
}

/* Sobrescribir estilos para modo oscuro específico del tema */
body.dark-mode .instructor-shortcode-card .instructor-extracto p,
.dark-mode .instructor-shortcode-card .instructor-extracto p,
body.dark-mode .instructor-shortcode-card .instructor-extracto,
.dark-mode .instructor-shortcode-card .instructor-extracto {
    color: var(--extract-text, #cbd5e0) !important;
}

/* En modo claro, forzar colores oscuros para mejor legibilidad */
body:not(.dark-mode) .instructor-shortcode-card .instructor-extracto p,
:not(.dark-mode) .instructor-shortcode-card .instructor-extracto p,
body:not(.dark-mode) .instructor-shortcode-card .instructor-extracto,
:not(.dark-mode) .instructor-shortcode-card .instructor-extracto {
    color: var(--extract-text, #2d3748) !important;
}

/* Estilos para los nombres de instructores */
body.dark-mode .instructor-shortcode-card .instructor-nombre a,
.dark-mode .instructor-shortcode-card .instructor-nombre a {
    color: var(--heading-color, #f1f5f9) !important;
}

body:not(.dark-mode) .instructor-shortcode-card .instructor-nombre a,
:not(.dark-mode) .instructor-shortcode-card .instructor-nombre a {
    color: var(--heading-color, #1a202c) !important;
}

/* Estilos para el título del instructor */
body.dark-mode .instructor-shortcode-card .instructor-titulo,
.dark-mode .instructor-shortcode-card .instructor-titulo {
    color: var(--secondary-text, #94a3b8) !important;
}

body:not(.dark-mode) .instructor-shortcode-card .instructor-titulo,
:not(.dark-mode) .instructor-shortcode-card .instructor-titulo {
    color: var(--secondary-text, #4a5568) !important;
}

/* Otros textos en la tarjeta */
body.dark-mode .instructor-shortcode-card,
.dark-mode .instructor-shortcode-card {
    color: var(--card-text, #e2e8f0) !important;
    background: var(--card-bg, #1e293b) !important;
}

body:not(.dark-mode) .instructor-shortcode-card,
:not(.dark-mode) .instructor-shortcode-card {
    color: var(--card-text, #333333) !important;
    background: var(--card-bg, #ffffff) !important;
}
            </style>';
        }
        
        // Llamar a la función para encolar los estilos
        breogan_enqueue_instructor_styles();
    } else {
        // Si la función ya existe, llámala
        breogan_enqueue_instructor_styles();
    }
    
    return ob_get_clean();
}

// Asegúrate de registrar el shortcode
if (!shortcode_exists('breogan_lms_instructores')) {
    add_shortcode('breogan_lms_instructores', 'breogan_lms_instructores_shortcode');
}