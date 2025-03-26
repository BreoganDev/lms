<?php
/**
 * Plantilla para mostrar un curso individual
 *
 * @package Breogan LMS
 */

get_header();

// Obtener datos relevantes
$curso_id = get_the_ID();
$user_id = get_current_user_id();
$ha_comprado = get_user_meta($user_id, 'blms_curso_' . $curso_id, true);
$precio = get_post_meta($curso_id, '_blms_precio_curso', true);
$es_gratuito = get_post_meta($curso_id, '_blms_curso_gratuito', true);

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
            <h1 class="breogan-curso-titulo"><?php the_title(); ?></h1>
            
            <?php if ($mensaje) : ?>
                <div class="breogan-mensaje">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            
            <?php if (has_post_thumbnail()) : ?>
                <div class="breogan-curso-imagen">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>
        </header>

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
                    console.error("Error en el pago:", response.data ? response.data.message : "Error desconocido");
                    alert("Error en el pago: " + (response.data ? response.data.message : "Error desconocido"));
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX:", error);
                alert("Hubo un error en la solicitud. Por favor, intenta de nuevo.");
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
</script>

<?php get_footer(); ?>