<?php
/**
 * Plantilla para mostrar una lección individual
 *
 * @package Breogan LMS
 */

get_header();

<<<<<<< HEAD
// Verificar si el usuario está logueado (pero permitir administradores)
if (!is_user_logged_in() && !current_user_can('administrator')) {
    echo '<p style="color:red; font-weight:bold;">🔴 Usuario no logueado. Redirigiendo...</p>';
    echo '<script>window.location.href = "' . wp_login_url(get_permalink()) . '";</script>';
=======
// Verificar si el usuario está logueado
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
    exit();
}

// Obtener datos relevantes
$leccion_id = get_the_ID();
$user_id = get_current_user_id();
$tema_id = get_post_meta($leccion_id, '_blms_tema_relacionado', true);
$curso_id = $tema_id ? get_post_meta($tema_id, '_blms_curso_relacionado', true) : 0;

// Instanciar handler de usuario
$user_handler = new Breogan_LMS_User();

// Verificar si el usuario tiene acceso
$tiene_acceso = $user_handler->user_has_access_to_lesson($user_id, $leccion_id);
$leccion_completada = $user_handler->is_lesson_completed($user_id, $leccion_id);

// Si el usuario no tiene acceso, mostrar mensaje y salir
if (!$tiene_acceso) {
    ?>
    <main class="breogan-contenedor">
        <div class="mensaje-error">
            <h2><?php _e('Acceso Restringido', 'breogan-lms'); ?></h2>
            <p><?php _e('Debes estar inscrito en este curso para acceder a esta lección.', 'breogan-lms'); ?></p>
            <?php if ($curso_id) : ?>
                <p><a href="<?php echo get_permalink($curso_id); ?>" class="breogan-btn"><?php _e('Volver al Curso', 'breogan-lms'); ?></a></p>
            <?php endif; ?>
        </div>
    </main>
    <?php
    get_footer();
    exit();
}
?>

<main class="breogan-contenedor">
    <article class="breogan-leccion">
        <header class="breogan-leccion-header">
            <div class="breogan-ruta-navegacion">
                <?php if ($curso_id) : ?>
                    <a href="<?php echo get_permalink($curso_id); ?>" class="ruta-curso">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        <?php echo get_the_title($curso_id); ?>
                    </a>
                    <span class="ruta-separador">/</span>
                <?php endif; ?>
                
                <?php if ($tema_id) : ?>
                    <a href="<?php echo get_permalink($tema_id); ?>" class="ruta-tema">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        <?php echo get_the_title($tema_id); ?>
                    </a>
                <?php endif; ?>
            </div>

            <h1 class="breogan-leccion-titulo"><?php the_title(); ?></h1>
            
            <?php if ($leccion_completada) : ?>
                <div class="breogan-leccion-estado completada">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <?php _e('Lección completada', 'breogan-lms'); ?>
                </div>
            <?php endif; ?>
        </header>

        <div class="breogan-leccion-contenido">
            <?php the_content(); ?>
        </div>

<?php if (!$leccion_completada) : ?>
    <div class="breogan-leccion-acciones">
        <button id="marcar-completada" class="breogan-btn breogan-btn-success" data-leccion="<?php echo $leccion_id; ?>" data-nonce="<?php echo wp_create_nonce('blms_lesson_nonce'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <?php _e('Marcar como completada', 'breogan-lms'); ?>
        </button>
    </div>
<?php endif; ?>

        <div class="breogan-navegacion-lecciones">
            <?php
            // Obtener lecciones del mismo tema para navegación
            $lecciones = get_posts([
                'post_type'   => 'blms_leccion',
                'meta_key'    => '_blms_tema_relacionado',
                'meta_value'  => $tema_id,
                'numberposts' => -1,
                'orderby'     => 'menu_order',
                'order'       => 'ASC'
            ]);
            
            // Encontrar índice de la lección actual
            $leccion_actual_index = 0;
            foreach ($lecciones as $key => $leccion) {
                if ($leccion->ID == $leccion_id) {
                    $leccion_actual_index = $key;
                    break;
                }
            }
            
            // Obtener lección anterior y siguiente
            $leccion_anterior = ($leccion_actual_index > 0) ? $lecciones[$leccion_actual_index - 1] : null;
            $leccion_siguiente = ($leccion_actual_index < count($lecciones) - 1) ? $lecciones[$leccion_actual_index + 1] : null;
            ?>
            
            <?php if ($leccion_anterior) : ?>
                <a href="<?php echo get_permalink($leccion_anterior->ID); ?>" class="breogan-btn breogan-btn-anterior">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <?php _e('Lección anterior', 'breogan-lms'); ?>
                </a>
            <?php endif; ?>
            
            <?php if ($tema_id) : ?>
                <a href="<?php echo get_permalink($tema_id); ?>" class="breogan-btn breogan-btn-tema">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    <?php _e('Volver al tema', 'breogan-lms'); ?>
                </a>
            <?php endif; ?>
            
            <?php if ($leccion_siguiente) : ?>
                <a href="<?php echo get_permalink($leccion_siguiente->ID); ?>" class="breogan-btn breogan-btn-siguiente">
                    <?php _e('Lección siguiente', 'breogan-lms'); ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            <?php endif; ?>
        </div>

        <?php if (count($lecciones) > 1) : ?>
            <div class="breogan-otras-lecciones">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                    <?php _e('Otras lecciones de este tema', 'breogan-lms'); ?>
                </h3>
                
                <ul class="breogan-lista-lecciones">
                    <?php foreach ($lecciones as $key => $leccion) : 
                        $es_actual = ($leccion->ID == $leccion_id);
                        $completada = $user_handler->is_lesson_completed($user_id, $leccion->ID);
                        $clase = [];
                        
                        if ($es_actual) $clase[] = 'leccion-actual';
                        if ($completada) $clase[] = 'leccion-completada';
                        
                        $clase_str = implode(' ', $clase);
                    ?>
                        <li class="<?php echo $clase_str; ?>">
                            <a href="<?php echo get_permalink($leccion->ID); ?>">
                                <?php if ($completada) : ?>
                                    <span class="leccion-check">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                        </svg>
                                    </span>
                                <?php else : ?>
                                    <span class="leccion-number"><?php echo $key + 1; ?></span>
                                <?php endif; ?>
                                <span class="leccion-titulo"><?php echo get_the_title($leccion->ID); ?></span>
                                <?php if ($es_actual) : ?>
                                    <span class="leccion-actual-indicator"><?php _e('Actual', 'breogan-lms'); ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </article>
</main>
<script>
jQuery(document).ready(function($) {
    // Marcar lección como completada
    $('#marcar-completada').on('click', function() {
        var leccion_id = $(this).data('leccion');
        var button = $(this);
        
        button.prop('disabled', true).text('<?php _e('Guardando...', 'breogan-lms'); ?>');
        
        $.ajax({
            url: breoganLMS.ajaxurl,
            type: 'POST',
            data: {
                action: 'blms_mark_lesson_complete',
                leccion_id: leccion_id,
                nonce: breoganLMS.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Reemplazar botón con mensaje de éxito
                    button.replaceWith('<div class="breogan-leccion-estado completada"><span class="dashicons dashicons-yes-alt"></span> <?php _e('Lección completada', 'breogan-lms'); ?></div>');
                    
                    // Agregar clase a la lección en la lista
                    $('.breogan-lista-lecciones .leccion-actual').addClass('leccion-completada');
                    $('.breogan-lista-lecciones .leccion-actual a').append('<span class="leccion-estado">✅</span>');
                    
                    // Opcional: recargar para actualizar todo el estado
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    alert(response.data.message || '<?php _e('Error al marcar la lección como completada', 'breogan-lms'); ?>');
                    button.prop('disabled', false).text('<?php _e('Marcar como completada', 'breogan-lms'); ?>');
                }
            },
            error: function() {
                alert('<?php _e('Error de conexión al servidor', 'breogan-lms'); ?>');
                button.prop('disabled', false).text('<?php _e('Marcar como completada', 'breogan-lms'); ?>');
            }
        });
    });
});
</script>

<?php get_footer(); ?>