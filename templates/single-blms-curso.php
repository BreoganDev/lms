<?php
/**
<<<<<<< HEAD
 * Plantilla para mostrar un curso individual con diseño mejorado
=======
 * Plantilla para mostrar un curso individual
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
 *
 * @package Breogan LMS
 */

get_header();

// Obtener datos relevantes
$curso_id = get_the_ID();
$user_id = get_current_user_id();
<<<<<<< HEAD

// Verificar con ambos prefijos para compatibilidad
$ha_comprado_blms = get_user_meta($user_id, 'blms_curso_' . $curso_id, true);
$ha_comprado_breogan = get_user_meta($user_id, 'breogan_curso_' . $curso_id, true);
$ha_comprado = ($ha_comprado_blms === 'comprado' || $ha_comprado_breogan === 'comprado') ? 'comprado' : '';

// Obtener metadatos del curso
$precio = get_post_meta($curso_id, '_blms_precio_curso', true);
$es_gratuito = get_post_meta($curso_id, '_blms_curso_gratuito', true);

// Nuevos metadatos extendidos
$instructor = get_post_meta($curso_id, '_blms_instructor_curso', true);
$duration = get_post_meta($curso_id, '_blms_duracion_curso', true);
$level = get_post_meta($curso_id, '_blms_nivel_curso', true);
$instructor_image = get_post_meta($curso_id, '_blms_instructor_imagen', true);

// Textos para niveles
$level_texts = array(
    'beginner' => __('Principiante', 'breogan-lms'),
    'intermediate' => __('Intermedio', 'breogan-lms'),
    'advanced' => __('Avanzado', 'breogan-lms'),
    'all-levels' => __('Todos los niveles', 'breogan-lms'),
);

// Obtener categorías (usando la taxonomía correcta)
$categories = get_the_terms($curso_id, 'blms_categoria');

=======
$ha_comprado = get_user_meta($user_id, 'blms_curso_' . $curso_id, true);
$precio = get_post_meta($curso_id, '_blms_precio_curso', true);
$es_gratuito = get_post_meta($curso_id, '_blms_curso_gratuito', true);

>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
// Verificar mensajes de pago
$mensaje = '';
if (isset($_GET['blms_payment'])) {
    if ($_GET['blms_payment'] === 'success') {
        $mensaje = '<p class="mensaje-exito">' . __('¡Pago completado con éxito! Ahora tienes acceso a todo el contenido del curso.', 'breogan-lms') . '</p>';
    } elseif ($_GET['blms_payment'] === 'cancel') {
        $mensaje = '<p class="mensaje-error">' . __('El proceso de pago ha sido cancelado.', 'breogan-lms') . '</p>';
    }
}

// Mensaje de acceso concedido
if (isset($_GET['blms_access']) && $_GET['blms_access'] === 'granted') {
    $mensaje = '<p class="mensaje-exito">' . __('¡Acceso concedido! Ahora puedes explorar el contenido del curso.', 'breogan-lms') . '</p>';
}
?>

<main class="breogan-contenedor">
    <article id="post-<?php the_ID(); ?>" <?php post_class('breogan-curso'); ?>>
        <header class="breogan-curso-header">
<<<<<<< HEAD
            <?php if ($categories && !is_wp_error($categories)) : ?>
                <div class="curso-categorias">
                    <?php foreach ($categories as $category) : ?>
                        <span class="curso-categoria"><?php echo esc_html($category->name); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

=======
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
            <h1 class="breogan-curso-titulo"><?php the_title(); ?></h1>
            
            <?php if ($mensaje) : ?>
                <div class="breogan-mensaje">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
<<<<<<< HEAD

            <?php if ($instructor || $duration || $level) : ?>
                <div class="curso-meta">
                    <?php if ($instructor) : ?>
                        <div class="curso-instructor">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span><?php echo esc_html($instructor); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($duration) : ?>
                        <div class="curso-duracion">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <span><?php echo esc_html($duration); ?> <?php _e('horas', 'breogan-lms'); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($level && isset($level_texts[$level])) : ?>
                        <div class="curso-nivel">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                            </svg>
                            <span><?php echo esc_html($level_texts[$level]); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
=======
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
            
            <?php if (has_post_thumbnail()) : ?>
                <div class="breogan-curso-imagen">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>
        </header>

<<<<<<< HEAD
        <div class="curso-content-wrapper">
            <div class="curso-main-content">
                <div class="breogan-curso-contenido">
                    <?php the_content(); ?>
                </div>

                <?php if ($ha_comprado === 'comprado') : ?>
                    <div class="breogan-curso-acceso">
                        <p class="mensaje-exito">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <?php _e('Ya tienes acceso a este curso. Explora los temas y lecciones:', 'breogan-lms'); ?>
                        </p>

                        <h2><?php _e('Temas del Curso', 'breogan-lms'); ?></h2>
                        <ul class="breogan-lista-temas">
                            <?php
                            // Intentar ambos tipos de posts para compatibilidad
                            $temas = get_posts([
                                'post_type'   => 'blms_tema',
                                'meta_key'    => '_blms_curso_relacionado',
                                'meta_value'  => $curso_id,
                                'numberposts' => -1,
                                'orderby'     => 'menu_order',
                                'order'       => 'ASC'
                            ]);

                            // Si no hay resultados, probar con el otro prefijo
                            if (empty($temas)) {
                                $temas = get_posts([
                                    'post_type'   => 'temas',
                                    'meta_key'    => '_curso_relacionado',
                                    'meta_value'  => $curso_id,
                                    'numberposts' => -1,
                                    'orderby'     => 'menu_order',
                                    'order'       => 'ASC'
                                ]);
                            }

                            if ($temas) :
                                foreach ($temas as $index => $tema) : ?>
                                    <li>
                                        <a href="<?php echo get_permalink($tema->ID); ?>">
                                            <div class="tema-header">
                                                <span class="tema-numero"><?php echo $index + 1; ?></span>
                                                <span class="tema-titulo"><?php echo get_the_title($tema->ID); ?></span>
                                            </div>
                                            <?php 
                                            // Intentar con ambos tipos de post para lecciones
                                            $lecciones_count = get_posts([
                                                'post_type'   => 'blms_leccion',
                                                'meta_key'    => '_blms_tema_relacionado',
                                                'meta_value'  => $tema->ID,
                                                'numberposts' => -1,
                                                'fields'      => 'ids'
                                            ]);
                                            
                                            if (empty($lecciones_count)) {
                                                $lecciones_count = get_posts([
                                                    'post_type'   => 'lecciones',
                                                    'meta_key'    => '_tema_relacionado',
                                                    'meta_value'  => $tema->ID,
                                                    'numberposts' => -1,
                                                    'fields'      => 'ids'
                                                ]);
                                            }
                                            
                                            if (count($lecciones_count) > 0) : ?>
                                                <span class="tema-contador"><?php echo sprintf(_n('%s lección', '%s lecciones', count($lecciones_count), 'breogan-lms'), count($lecciones_count)); ?></span>
                                            <?php endif; ?>
                                        </a>
                                        
                                        <?php 
                                        // Obtener lecciones de este tema (intentar ambos tipos)
                                        $lecciones = get_posts([
                                            'post_type'   => 'blms_leccion',
                                            'meta_key'    => '_blms_tema_relacionado',
                                            'meta_value'  => $tema->ID,
                                            'numberposts' => -1,
                                            'orderby'     => 'menu_order',
                                            'order'       => 'ASC'
                                        ]);
                                        
                                        if (empty($lecciones)) {
                                            $lecciones = get_posts([
                                                'post_type'   => 'lecciones',
                                                'meta_key'    => '_tema_relacionado',
                                                'meta_value'  => $tema->ID,
                                                'numberposts' => -1,
                                                'orderby'     => 'menu_order',
                                                'order'       => 'ASC'
                                            ]);
                                        }
                                        
                                        if ($lecciones) : ?>
                                            <ul class="breogan-lista-lecciones">
                                                <?php foreach ($lecciones as $key => $leccion) : 
                                                    // Verificar si la lección está completada
                                                    $completada = false;
                                                    
                                                    if (class_exists('Breogan_LMS_User')) {
                                                        $user_handler = new Breogan_LMS_User();
                                                        $completada = $user_handler->is_lesson_completed($user_id, $leccion->ID);
                                                    } else {
                                                        // Comprobar manualmente con ambos prefijos
                                                        $completada_blms = get_user_meta($user_id, 'blms_leccion_completada_' . $leccion->ID, true);
                                                        $completada_breogan = get_user_meta($user_id, 'breogan_leccion_' . $leccion->ID, true);
                                                        $completada = !empty($completada_blms) || !empty($completada_breogan);
                                                    }
                                                    
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
                <?php endif; ?>
            </div>
            
            <div class="curso-sidebar">
                <?php if ($ha_comprado !== 'comprado') : ?>
                    <div class="curso-price-card">
                        <div class="curso-info-titulo">
                            <h3><?php _e('Inscripción al Curso', 'breogan-lms'); ?></h3>
                        </div>
                        
                        <div class="curso-precio-display">
                            <?php if ($es_gratuito == '1') : ?>
                                <span class="curso-precio-gratuito"><?php _e('Curso Gratuito', 'breogan-lms'); ?></span>
                            <?php elseif ($precio) : ?>
                                <span class="curso-precio-monto"><?php echo esc_html($precio); ?> €</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="curso-caracteristicas">
                            <ul>
                                <li>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                    <?php _e('Acceso completo al contenido', 'breogan-lms'); ?>
                                </li>
                                <li>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                    <?php _e('Estudia a tu propio ritmo', 'breogan-lms'); ?>
                                </li>
                                <li>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                    <?php _e('Recursos descargables', 'breogan-lms'); ?>
                                </li>
                                <li>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                    <?php _e('Acceso ilimitado', 'breogan-lms'); ?>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="breogan-botones-pago">
                            <?php if ($es_gratuito == '1') : ?>
                                <!-- Enlace directo para curso gratuito -->
                                <a href="<?php echo esc_url(add_query_arg(['blms_free_access' => 'true', 'curso_id' => $curso_id, 'nonce' => wp_create_nonce('blms_free_access_nonce')], get_permalink($curso_id))); ?>" class="breogan-btn-pago breogan-btn-gratuito">
                                    <?php _e('Acceder al Curso Gratuito', 'breogan-lms'); ?>
                                </a>
                            <?php else : ?>
                                <!-- Botón de Stripe -->
<form id="breogan-pago-stripe" method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>">
    <?php wp_nonce_field('blms_payment_nonce', 'nonce'); ?>
    <input type="hidden" name="action" value="blms_process_stripe_payment">
    <input type="hidden" name="curso_id" value="<?php echo esc_attr($curso_id); ?>">
    <input type="hidden" name="precio" value="<?php echo esc_attr($precio); ?>">
    <button type="submit" class="breogan-btn-pago breogan-stripe-btn">
        <?php _e('Pagar con Stripe', 'breogan-lms'); ?>
    </button>
</form>

<!-- Botón de PayPal -->
<form id="breogan-pago-paypal" method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>">
    <input type="hidden" name="action" value="breogan_procesar_pago_paypal_ajax">
    <input type="hidden" name="curso_id" value="<?php echo esc_attr($curso_id); ?>">
    <input type="hidden" name="precio" value="<?php echo esc_attr($precio); ?>">
    <button type="submit" class="breogan-btn-pago breogan-paypal-btn">
        <?php if (file_exists(BREOGAN_LMS_PATH . 'assets/images/paypal-logo.png')) : ?>
            <img src="<?php echo BREOGAN_LMS_URL; ?>assets/images/paypal-logo.png" alt="PayPal" class="paypal-logo">
        <?php endif; ?>
        <span><?php _e('Pagar con PayPal', 'breogan-lms'); ?></span>
    </button>
</form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($instructor) : ?>
                    <div class="curso-instructor-card">
                        <div class="instructor-info-titulo">
                            <h3><?php _e('Instructor', 'breogan-lms'); ?></h3>
                        </div>
                        
                        <div class="instructor-details">
                            <?php if ($instructor_image) : ?>
                                <img src="<?php echo esc_url($instructor_image); ?>" alt="<?php echo esc_attr($instructor); ?>" class="instructor-image">
                            <?php else : ?>
                                <div class="instructor-initials">
                                    <?php echo esc_html(substr($instructor, 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                            
                            <h4 class="instructor-name"><?php echo esc_html($instructor); ?></h4>
                            
                            <?php
                            // Buscar página del instructor (tipo de post instructor si existe)
                            $instructor_page = null;
                            if (post_type_exists('instructor')) {
                                $instructor_query = new WP_Query(array(
                                    'post_type' => 'instructor',
                                    'title' => $instructor,
                                    'posts_per_page' => 1
                                ));
                                
                                if ($instructor_query->have_posts()) {
                                    $instructor_query->the_post();
                                    $instructor_page = get_permalink();
                                    wp_reset_postdata();
                                }
                            }
                            
                            if ($instructor_page) : ?>
                                <a href="<?php echo esc_url($instructor_page); ?>" class="instructor-profile-link">
                                    <?php _e('Ver perfil del instructor', 'breogan-lms'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </article>
</main>

<style>
/* Estilos adicionales para el nuevo layout */
:root {
    /* Colores para modo claro (valores por defecto) */
    --blms-bg-color: #ffffff;
    --blms-text-color: #333333;
    --blms-card-bg: #ffffff;
    --blms-card-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    --blms-border-color: #f0f0f0;
    --blms-primary-color: #6366F1;
    --blms-primary-hover: #4F46E5;
    --blms-success-color: #28a745;
    --blms-success-bg: #d4edda;
    --blms-success-text: #155724;
    --blms-cat-bg: #e9ecef;
    --blms-cat-text: #495057;
    --blms-meta-text: #6c757d;
    --blms-card-border: #e5e7eb;
}

/* Detección automática de modo oscuro basado en prefers-color-scheme */
@media (prefers-color-scheme: dark) {
    :root {
        --blms-bg-color: #1a1a1a;
        --blms-text-color: #e0e0e0;
        --blms-card-bg: #2d2d2d;
        --blms-card-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        --blms-border-color: #3d3d3d;
        --blms-primary-color: #818cf8;
        --blms-primary-hover: #a5b4fc;
        --blms-success-color: #34c759;
        --blms-success-bg: #103320;
        --blms-success-text: #4ade80;
        --blms-cat-bg: #2d3748;
        --blms-cat-text: #cbd5e0;
        --blms-meta-text: #a0aec0;
        --blms-card-border: #4a5568;
    }
}

/* Compatibilidad con tema de WordPress */
.breogan-contenedor {
    color: var(--blms-text-color);
    background-color: var(--blms-bg-color);
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

/* Si el tema ya tiene un fondo oscuro, mantener consistencia */
.wp-block-cover + .breogan-contenedor,
.has-background + .breogan-contenedor,
.has-background-dim + .breogan-contenedor {
    background: transparent;
}

.curso-content-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    margin-top: 2rem;
}

.curso-main-content {
    flex: 1;
    min-width: 300px;
}

.curso-sidebar {
    width: 350px;
}

/* Tarjeta de precio */
.curso-price-card, 
.curso-instructor-card {
    background-color: var(--blms-card-bg);
    color: var(--blms-text-color);
    border-radius: 10px;
    box-shadow: var(--blms-card-shadow);
    border: 1px solid var(--blms-card-border);
    padding: 20px;
    margin-bottom: 20px;
}

.curso-info-titulo h3,
.instructor-info-titulo h3 {
    margin-top: 0;
    margin-bottom: 15px;
    border-bottom: 2px solid var(--blms-border-color);
    padding-bottom: 10px;
    font-size: 1.25rem;
    color: var(--blms-text-color);
}

.curso-precio-display {
    text-align: center;
    margin: 20px 0;
}

.curso-precio-monto {
    font-size: 1.8rem;
    font-weight: bold;
    color: var(--blms-text-color);
}

.curso-precio-gratuito {
    background-color: var(--blms-success-bg);
    color: var(--blms-success-text);
    padding: 5px 15px;
    border-radius: 20px;
    font-weight: bold;
}

.curso-caracteristicas ul {
    list-style: none;
    padding: 0;
    margin: 15px 0;
}

.curso-caracteristicas li {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    gap: 10px;
    color: var(--blms-text-color);
}

.curso-caracteristicas li svg {
    color: var(--blms-success-color);
    flex-shrink: 0;
}

/* Estilos para el instructor */
.curso-instructor-card {
    text-align: center;
}

.instructor-image {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    margin: 0 auto 15px;
    display: block;
}

.instructor-initials {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: var(--blms-primary-color);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    margin: 0 auto 15px;
}

.instructor-name {
    margin: 10px 0;
    font-size: 1.2rem;
    color: var(--blms-text-color);
}

.instructor-profile-link {
    display: inline-block;
    margin-top: 10px;
    color: var(--blms-primary-color);
    text-decoration: none;
}

.instructor-profile-link:hover {
    text-decoration: underline;
    color: var(--blms-primary-hover);
}

/* Estilos para categorías */
.curso-categorias {
    margin-bottom: 15px;
}

.curso-categoria {
    display: inline-block;
    background-color: var(--blms-cat-bg);
    color: var(--blms-cat-text);
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.85rem;
    margin-right: 8px;
    margin-bottom: 8px;
}

/* Estilos para metadatos */
.curso-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 20px;
    color: var(--blms-meta-text);
}

.curso-instructor, 
.curso-duracion, 
.curso-nivel {
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Botones de pago mejorados */
.breogan-btn-pago {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 12px 15px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}

.breogan-stripe-btn {
    background-color: #6772e5;
    color: white;
}

.breogan-stripe-btn:hover {
    background-color: #5469d4;
}

.breogan-paypal-btn {
    background-color: #0070BA;
    color: white;
}

.breogan-paypal-btn:hover {
    background-color: #005ea6;
}

.breogan-btn-gratuito {
    background-color: var(--blms-success-color);
    color: white;
}

.breogan-btn-gratuito:hover {
    background-color: #218838;
}

.paypal-logo {
    height: 20px;
    margin-right: 10px;
}

/* Estilos adicionales para la lista de temas y lecciones */
.breogan-lista-temas {
    list-style: none;
    padding: 0;
    margin: 0;
}

.breogan-lista-temas > li {
    margin-bottom: 15px;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid var(--blms-border-color);
    background-color: var(--blms-card-bg);
}

.breogan-lista-temas > li > a {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px;
    text-decoration: none;
    color: var(--blms-text-color);
    background-color: var(--blms-card-bg);
    border-left: 4px solid var(--blms-primary-color);
}

.breogan-lista-temas > li > a:hover {
    background-color: rgba(99, 102, 241, 0.1);
}

.breogan-lista-lecciones {
    list-style: none;
    padding: 0;
    margin: 0;
}

.breogan-lista-lecciones li {
    margin: 5px 0;
}

.breogan-lista-lecciones a {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    text-decoration: none;
    color: #000 !important;
    background-color: rgba(0, 0, 0, 0.03);
    border-radius: 5px;
}

.dark .breogan-lista-lecciones a {
    background-color: rgba(255, 255, 255, 0.05);
}

.leccion-completada a {
    background-color: #f6000099 !important;
    color: var(--breogan-success-dark);
}
.breogan-lista-lecciones a:hover {
    background-color: rgba(99, 102, 241, 0.1);
}

.breogan-curso-titulo,
.breogan-tema-titulo,
.breogan-leccion-titulo {
    color: var(--blms-text-color);
}

.breogan-curso p {
    color: black !important;
}
.mensaje-exito, 
.mensaje-error {
    color: var(--blms-text-color);
    padding: 10px 15px;
    border-radius: 5px;
    margin: 15px 0;
}

.mensaje-exito {
    background-color: #e5e7eb;
    border-left: 4px solid var(--blms-success-color);
}

h2 {
    color: black ;
}

.mensaje-error {
    background-color: rgba(220, 38, 38, 0.1);
    border-left: 4px solid #dc2626;
}

/* Responsive */
@media (max-width: 768px) {
    .curso-content-wrapper {
        flex-direction: column;
    }
    
    .curso-sidebar {
        width: 100%;
        order: -1; /* Mostrar sidebar primero en móviles */
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Funciones para procesar pagos con Stripe y PayPal
    function procesarPago(formId) {
        let form = $('#' + formId);
        let formData = new FormData(form[0]);
        let button = form.find('button');
        let originalText = button.html();
        
        // Deshabilitar botón para evitar doble clic
        button.prop('disabled', true).html('<?php _e('Procesando...', 'breogan-lms'); ?>');
        console.log("Enviando datos de pago...");
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
=======
        <div class="breogan-curso-contenido">
            <?php the_content(); ?>
        </div>

        <?php if ($ha_comprado === 'comprado') : ?>
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
        <?php else : ?>
            <div class="breogan-curso-pago">
                <div class="breogan-curso-info-pago">
                    <h2><?php _e('Información del Curso', 'breogan-lms'); ?></h2>
                    
                    <?php if ($es_gratuito == '1') : ?>
                        <p class="breogan-curso-precio curso-gratuito">
                            <?php _e('Curso Gratuito', 'breogan-lms'); ?>
                        </p>
                        <p class="breogan-curso-acceso-info">
                            <?php _e('Puedes acceder a este curso gratuitamente.', 'breogan-lms'); ?>
                        </p>
                    <?php else : ?>
                        <?php if ($precio) : ?>
                            <p class="breogan-curso-precio">
                                <?php _e('Precio:', 'breogan-lms'); ?> <strong><?php echo esc_html($precio); ?> €</strong>
                            </p>
                        <?php endif; ?>
                        <p class="breogan-curso-acceso-info">
                            <?php _e('Para acceder al contenido de este curso, debes comprarlo.', 'breogan-lms'); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="breogan-botones-pago">
                    <?php if ($es_gratuito == '1') : ?>
                        <!-- Enlace directo para curso gratuito -->
                        <a href="<?php echo esc_url(add_query_arg(['blms_free_access' => 'true', 'curso_id' => $curso_id, 'nonce' => wp_create_nonce('blms_free_access_nonce')], get_permalink($curso_id))); ?>" class="breogan-btn-pago breogan-btn-gratuito">
                            <?php _e('Acceder al Curso Gratuito', 'breogan-lms'); ?>
                        </a>
                    <?php else : ?>
                        <!-- Botón de Stripe -->
                        <form id="breogan-pago-stripe" method="POST">
                            <?php wp_nonce_field('blms_payment_nonce', 'nonce'); ?>
                            <input type="hidden" name="action" value="blms_process_stripe_payment">
                            <input type="hidden" name="curso_id" value="<?php echo esc_attr($curso_id); ?>">
                            <input type="hidden" name="precio" value="<?php echo esc_attr($precio); ?>">
                            <button type="submit" class="breogan-btn-pago breogan-stripe-btn">
                                <?php _e('Pagar con Stripe', 'breogan-lms'); ?>
                            </button>
                        </form>

                        <!-- Botón de PayPal -->
                        <form id="breogan-pago-paypal" method="POST">
                            <?php wp_nonce_field('blms_payment_nonce', 'nonce'); ?>
                            <input type="hidden" name="action" value="blms_process_paypal_payment">
                            <input type="hidden" name="curso_id" value="<?php echo esc_attr($curso_id); ?>">
                            <input type="hidden" name="precio" value="<?php echo esc_attr($precio); ?>">
                            <button type="submit" class="breogan-btn-pago breogan-paypal-btn">
                                <?php _e('Pagar con PayPal', 'breogan-lms'); ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </article>
</main>

<script>
jQuery(document).ready(function($) {
    // Funciones para procesar pagos
    function procesarPago(formId) {
        let form = $('#' + formId);
        let formData = new FormData(form[0]);

        console.log("Enviando datos de pago...");

        $.ajax({
            url: breoganLMS.ajaxurl,
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log("Respuesta del servidor:", response);
                
                if (response.success && response.data && response.data.redirect_url) {
                    console.log("Redirigiendo a:", response.data.redirect_url);
                    window.location.href = response.data.redirect_url;
                } else {
<<<<<<< HEAD
                    // Restaurar botón
                    button.prop('disabled', false).html(originalText);
                    
                    // Mostrar error
                    console.error("Error en el pago:", response.data ? response.data.message || "Error desconocido" : "Error desconocido");
                    alert("Error en el pago: " + (response.data && response.data.message ? response.data.message : "Error desconocido"));
                }
            },
            error: function(xhr, status, error) {
                // Restaurar botón
                button.prop('disabled', false).html(originalText);
                
                // Mostrar error
                console.error("Error en la solicitud AJAX:", error, xhr.responseText);
                alert("Error de comunicación. Por favor, inténtalo de nuevo más tarde.");
=======
                    console.error("Error en el pago:", response.data ? response.data.message : "Error desconocido");
                    alert("Error en el pago: " + (response.data ? response.data.message : "Error desconocido"));
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX:", error);
                alert("Hubo un error en la solicitud. Por favor, intenta de nuevo.");
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
            }
        });
    }

    // Event listeners para los botones de pago
    $('#breogan-pago-stripe').on('submit', function(e) {
        e.preventDefault();
        procesarPago('breogan-pago-stripe');
    });

    $('#breogan-pago-paypal').on('submit', function(e) {
        e.preventDefault();
        procesarPago('breogan-pago-paypal');
    });
});
<<<<<<< HEAD
</script>
=======
</script>

<?php get_footer(); ?>
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
