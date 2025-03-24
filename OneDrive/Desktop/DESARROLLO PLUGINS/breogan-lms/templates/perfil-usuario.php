<?php
/**
 * Template para la página de perfil de usuario
 * Diseño moderno y profesional para Breogan LMS
 */

// Evitar acceso si el usuario no está logueado
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

get_header();

// Obtener información del usuario actual
$user_id = get_current_user_id();
$user = get_userdata($user_id);
$display_name = $user->display_name;
$registered_date = date_i18n(get_option('date_format'), strtotime($user->user_registered));
$avatar = get_avatar($user_id, 96, '', $display_name, array('class' => 'perfil-avatar'));
?>

<main class="breogan-perfil-usuario">
    <div class="breogan-perfil-header">
        <h1><?php _e('Mi Perfil', 'breogan-lms'); ?></h1>
    </div>
    
    <!-- Sección de estadísticas -->
    <div class="perfil-estadisticas">
        <?php
        // Obtener estadísticas del usuario
        $cursos_comprados = 0;
        $cursos_completados = 0;
        $lecciones_completadas = 0;
        
        // Contar cursos comprados
        global $wpdb;
        $cursos_meta = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT meta_key FROM {$wpdb->usermeta} 
                 WHERE user_id = %d AND (meta_key LIKE %s OR meta_key LIKE %s) AND meta_value = 'comprado'",
                $user_id,
                'blms_curso_%',
                'breogan_curso_%'
            )
        );
        
        $cursos_comprados = count($cursos_meta);
        
        // Contar lecciones completadas (aproximación)
        $lecciones_meta = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT meta_key FROM {$wpdb->usermeta} 
                 WHERE user_id = %d AND meta_key LIKE %s",
                $user_id,
                'blms_leccion_completada_%'
            )
        );
        
        $lecciones_completadas = count($lecciones_meta);
        ?>
        
        <div class="estadistica-card">
            <div class="estadistica-valor"><?php echo $cursos_comprados; ?></div>
            <div class="estadistica-label"><?php _e('Cursos', 'breogan-lms'); ?></div>
        </div>
        
        <div class="estadistica-card">
            <div class="estadistica-valor"><?php echo $lecciones_completadas; ?></div>
            <div class="estadistica-label"><?php _e('Lecciones Completadas', 'breogan-lms'); ?></div>
        </div>
        
        <div class="estadistica-card">
            <div class="estadistica-valor"><?php echo human_time_diff(strtotime($user->user_registered), current_time('timestamp')); ?></div>
            <div class="estadistica-label"><?php _e('Miembro Desde', 'breogan-lms'); ?></div>
        </div>
    </div>
    
    <!-- Sección de información del usuario -->
    <div class="breogan-perfil-seccion">
        <h2><?php _e('Información Personal', 'breogan-lms'); ?></h2>
        
        <div class="perfil-info">
            <div class="perfil-info-card">
                <h3><?php _e('Datos de Usuario', 'breogan-lms'); ?></h3>
                <div class="info-item">
                    <strong><?php _e('Nombre:', 'breogan-lms'); ?></strong> 
                    <?php echo esc_html($display_name); ?>
                </div>
                <div class="info-item">
                    <strong><?php _e('Email:', 'breogan-lms'); ?></strong> 
                    <?php echo esc_html($user->user_email); ?>
                </div>
                <div class="info-item">
                    <strong><?php _e('Miembro desde:', 'breogan-lms'); ?></strong> 
                    <?php echo $registered_date; ?>
                </div>
            </div>
            
            <div class="perfil-info-card">
                <h3><?php _e('Enlaces Rápidos', 'breogan-lms'); ?></h3>
                <div class="info-item">
                    <a href="<?php echo wp_logout_url(home_url()); ?>" class="btn-acceder">
                        <?php _e('Cerrar Sesión', 'breogan-lms'); ?>
                    </a>
                </div>
                <div class="info-item">
                    <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" class="btn-acceder">
                        <?php _e('Mi Cuenta', 'breogan-lms'); ?>
                    </a>
                </div>
                <div class="info-item">
                    <a href="<?php echo wp_lostpassword_url(); ?>" class="btn-acceder">
                        <?php _e('Cambiar Contraseña', 'breogan-lms'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sección de cursos -->
    <div class="breogan-perfil-seccion">
        <h2><?php _e('Mis Cursos', 'breogan-lms'); ?></h2>
        
        <?php
        // Obtener cursos del usuario
        $cursos_en_progreso = array();
        
        // Obtener todos los cursos
        $cursos = get_posts(array(
            'post_type'   => 'cursos', // Ajustar según el post type correcto
            'numberposts' => -1,
            'orderby'     => 'title',
            'order'       => 'ASC'
        ));
        
        // Alternativamente, verifica también el post type 'blms_curso'
        $cursos_blms = get_posts(array(
            'post_type'   => 'blms_curso',
            'numberposts' => -1,
            'orderby'     => 'title',
            'order'       => 'ASC'
        ));
        
        // Combinar ambos arrays
        $cursos = array_merge($cursos, $cursos_blms);
        
        // Filtrar solo los cursos comprados
        foreach ($cursos as $curso) {
            $curso_id = $curso->ID;
            
            // Verificar ambos prefijos
            $ha_comprado_blms = get_user_meta($user_id, 'blms_curso_' . $curso_id, true);
            $ha_comprado_breogan = get_user_meta($user_id, 'breogan_curso_' . $curso_id, true);
            
            if ($ha_comprado_blms === 'comprado' || $ha_comprado_breogan === 'comprado') {
                // Calcular progreso (puedes ajustar esta lógica según tus necesidades)
                $temas = get_posts(array(
                    'post_type'   => 'blms_tema',
                    'meta_key'    => '_blms_curso_relacionado',
                    'meta_value'  => $curso_id,
                    'numberposts' => -1
                ));
                
                $total_lecciones = 0;
                $lecciones_completadas = 0;
                
                foreach ($temas as $tema) {
                    $lecciones = get_posts(array(
                        'post_type'   => 'blms_leccion',
                        'meta_key'    => '_blms_tema_relacionado',
                        'meta_value'  => $tema->ID,
                        'numberposts' => -1
                    ));
                    
                    foreach ($lecciones as $leccion) {
                        $total_lecciones++;
                        if (get_user_meta($user_id, 'blms_leccion_completada_' . $leccion->ID, true)) {
                            $lecciones_completadas++;
                        }
                    }
                }
                
                $porcentaje = ($total_lecciones > 0) ? round(($lecciones_completadas / $total_lecciones) * 100) : 0;
                
                $cursos_en_progreso[] = array(
                    'curso' => $curso,
                    'total_lecciones' => $total_lecciones,
                    'lecciones_completadas' => $lecciones_completadas,
                    'porcentaje' => $porcentaje
                );
            }
        }
        
        if (!empty($cursos_en_progreso)) {
        ?>
            <ul class="lista-cursos">
                <?php foreach ($cursos_en_progreso as $data) { 
                    $estado_clase = ($data['porcentaje'] >= 100) ? 'estado-completado' : (($data['porcentaje'] > 0) ? 'estado-activo' : 'estado-pendiente');
                    $estado_texto = ($data['porcentaje'] >= 100) ? __('Completado', 'breogan-lms') : (($data['porcentaje'] > 0) ? __('En progreso', 'breogan-lms') : __('Pendiente', 'breogan-lms'));
                ?>
                    <li>
                        <h3>
                            <a href="<?php echo get_permalink($data['curso']->ID); ?>"><?php echo get_the_title($data['curso']->ID); ?></a>
                        </h3>
                        <div class="curso-contenido">
                            <div class="estado-indicador <?php echo $estado_clase; ?>">
                                <?php echo $estado_texto; ?>
                            </div>
                            
                            <p><?php _e('Progreso:', 'breogan-lms'); ?> <strong><?php echo $data['porcentaje']; ?>%</strong></p>
                            
                            <div class="progreso-barra">
                                <div style="width: <?php echo $data['porcentaje']; ?>%"></div>
                            </div>
                            
                            <p class="lecciones-info">
                                <?php echo sprintf(__('%d de %d lecciones completadas', 'breogan-lms'), 
                                    $data['lecciones_completadas'], $data['total_lecciones']); ?>
                            </p>
                            
                            <div class="curso-acciones">
                                <a href="<?php echo get_permalink($data['curso']->ID); ?>" class="btn-acceder">
                                    <?php _e('Continuar', 'breogan-lms'); ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <div class="no-cursos">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <h3><?php _e('Aún no tienes cursos', 'breogan-lms'); ?></h3>
                <p><?php _e('Explora nuestro catálogo y empieza a aprender hoy mismo.', 'breogan-lms'); ?></p>
                <a href="<?php echo get_post_type_archive_link('cursos'); ?>" class="btn-explorar">
                    <?php _e('Explorar Cursos', 'breogan-lms'); ?>
                </a>
            </div>
        <?php } ?>
    </div>
</main>

<?php get_footer(); ?>