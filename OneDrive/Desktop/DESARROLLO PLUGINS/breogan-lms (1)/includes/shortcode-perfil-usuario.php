<?php
/**
 * Shortcode para mostrar el perfil de usuario con progreso
 * 
 * Este shortcode muestra el perfil del usuario con:
 * - Información personal
 * - Estadísticas de uso
 * - Cursos comprados con barra de progreso y detalle de temas y lecciones
 * 
 * Uso: [breogan_perfil]
 */
function breogan_perfil_usuario_shortcode() {
    // Si el usuario no está logueado, mostrar formulario de login
    if (!is_user_logged_in()) {
        return breogan_login_form_for_profile();
    }
    
    // Cargar estilos específicos para el perfil
    wp_enqueue_style(
        'breogan-perfil-styles',
        BREOGAN_LMS_URL . 'assets/css/perfil-styles.css',
        array(),
        defined('BREOGAN_LMS_VERSION') ? BREOGAN_LMS_VERSION : '1.0'
    );
    
    // Iniciar buffer de salida
    ob_start();
    
    // Obtener información del usuario actual
    $user_id = get_current_user_id();
    $user = get_userdata($user_id);
    $display_name = $user->display_name;
    $registered_date = date_i18n(get_option('date_format'), strtotime($user->user_registered));
    ?>
    
    <main class="breogan-perfil-usuario">
        <div class="breogan-perfil-header">
            <h1><?php _e('Mi Perfil', 'breogan-lms'); ?></h1>
        </div>
        
        <!-- Sección de estadísticas -->
        <div class="perfil-estadisticas">
            <?php
            // Obtener estadísticas del usuario
            global $wpdb;
            
            // Contar cursos comprados
            $cursos_meta = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT meta_key FROM {$wpdb->usermeta} 
                     WHERE user_id = %d AND (meta_key LIKE %s OR meta_key LIKE %s) AND meta_value = %s",
                    $user_id,
                    'breogan_curso_%',
                    'blms_curso_%',
                    'comprado'
                )
            );
            $cursos_comprados = count($cursos_meta);
            
            // Contar lecciones completadas
            $lecciones_completadas = 0;
            $lecciones_meta_prefijos = array(
                'breogan_leccion_completada_',
                'blms_leccion_completada_',
                'breogan_leccion_%_completada'
            );
            
            foreach ($lecciones_meta_prefijos as $prefijo) {
                $count = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT COUNT(*) FROM {$wpdb->usermeta} 
                         WHERE user_id = %d AND meta_key LIKE %s",
                        $user_id,
                        $prefijo . '%'
                    )
                );
                $lecciones_completadas += intval($count);
            }
            
            // Tiempo como miembro
            $tiempo_miembro = human_time_diff(strtotime($user->user_registered), current_time('timestamp'));
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
                <div class="estadistica-valor"><?php echo $tiempo_miembro; ?></div>
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
                        <a href="<?php echo wp_lostpassword_url(); ?>" class="btn-acceder">
                            <?php _e('Cambiar Contraseña', 'breogan-lms'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- SECCIÓN NUEVA: Progreso Detallado de Cursos -->
        <div class="breogan-perfil-seccion">
            <?php
            echo '<div class="tabla-progreso-wrapper">';
            echo '<h2>' . __('Progreso Detallado de Cursos', 'breogan-lms') . '</h2>';
            echo '<table class="tabla-progreso">';
            echo '<thead><tr><th>Curso</th><th>Tema</th><th>Lección</th><th>Estado</th></tr></thead>';
            echo '<tbody>';
            
            $cursos_progress = get_posts(array(
                'post_type'   => array('cursos', 'blms_curso'),
                'numberposts' => -1
            ));
            
            foreach ($cursos_progress as $curso) {
                // Verificar si el curso fue comprado
                $curso_comprado = get_user_meta($user_id, 'breogan_curso_' . $curso->ID, true);
                $curso_comprado_blms = get_user_meta($user_id, 'blms_curso_' . $curso->ID, true);
                
                if ($curso_comprado !== 'comprado' && $curso_comprado_blms !== 'comprado') {
                    continue;
                }
                
                $temas = get_posts(array(
                    'post_type'   => array('temas', 'blms_tema'),
                    'meta_key'    => '_curso_relacionado',
                    'meta_value'  => $curso->ID,
                    'numberposts' => -1
                ));
                
                // Calcular el progreso del curso
                $total_lecciones = 0;
                $lecciones_completadas = 0;
                
                foreach ($temas as $tema) {
                    $lecciones = get_posts(array(
                        'post_type'   => array('lecciones', 'blms_leccion'),
                        'meta_key'    => '_tema_relacionado',
                        'meta_value'  => $tema->ID,
                        'numberposts' => -1
                    ));
                    
                    foreach ($lecciones as $leccion) {
                        $total_lecciones++;
                        
                        $completada_breogan = get_user_meta($user_id, 'breogan_leccion_' . $leccion->ID . '_completada', true);
                        $completada_blms = get_user_meta($user_id, 'blms_leccion_completada_' . $leccion->ID, true);
                        
                        if (!empty($completada_breogan) || !empty($completada_blms)) {
                            $lecciones_completadas++;
                        }
                    }
                }
                
                $porcentaje = ($total_lecciones > 0) ? round(($lecciones_completadas / $total_lecciones) * 100) : 0;
                
                // Determinar color de la barra según el porcentaje
                if ($porcentaje < 30) {
                    $barra_color = '#f44336'; // rojo
                } elseif ($porcentaje < 70) {
                    $barra_color = '#ffeb3b'; // amarillo
                } else {
                    $barra_color = '#4caf50'; // verde
                }
                
                // Mostrar fila resumen del curso con barra de progreso
                echo '<tr class="resumen-curso">';
                echo '<td colspan="4">';
                echo '<div class="curso-resumen">';
                echo '<strong>' . esc_html(get_the_title($curso)) . '</strong>: ' . $porcentaje . '% completado (' . $lecciones_completadas . '/' . $total_lecciones . ' lecciones)';
                echo '<div class="barra-progreso-externa">';
                echo '<div class="barra-progreso-interna" style="width:' . $porcentaje . '%; background-color:' . $barra_color . ';"></div>';
                echo '</div>';
                echo '</div>';
                echo '</td>';
                echo '</tr>';
                
                // Mostrar detalles por tema y lección
                foreach ($temas as $tema) {
                    $lecciones = get_posts(array(
                        'post_type'   => array('lecciones', 'blms_leccion'),
                        'meta_key'    => '_tema_relacionado',
                        'meta_value'  => $tema->ID,
                        'numberposts' => -1
                    ));
                    
                    foreach ($lecciones as $leccion) {
                        $completada_breogan = get_user_meta($user_id, 'breogan_leccion_' . $leccion->ID . '_completada', true);
                        $completada_blms = get_user_meta($user_id, 'blms_leccion_completada_' . $leccion->ID, true);
                        $completada = (!empty($completada_breogan) || !empty($completada_blms)) ? '✅' : '❌';
                        
                        echo '<tr>';
                        echo '<td>' . esc_html(get_the_title($curso)) . '</td>';
                        echo '<td>' . esc_html(get_the_title($tema)) . '</td>';
                        echo '<td>' . esc_html(get_the_title($leccion)) . '</td>';
                        echo '<td style="text-align:center;">' . $completada . '</td>';
                        echo '</tr>';
                    }
                }
            }
            
            echo '</tbody></table></div>';
            ?>
        </div>
        
        <!-- Sección de cursos -->
        <div class="breogan-perfil-seccion">
            <h2><?php _e('Mis Cursos', 'breogan-lms'); ?></h2>
            
            <?php
            // Obtener cursos comprados por el usuario
            // Identificar IDs de cursos a partir de los metadatos
            $curso_ids = array();
            
            // Buscar metadatos con prefijos 'breogan_curso_' o 'blms_curso_'
            foreach ($cursos_meta as $meta) {
                $meta_key = $meta->meta_key;
                if (strpos($meta_key, 'breogan_curso_') === 0) {
                    $curso_id = intval(str_replace('breogan_curso_', '', $meta_key));
                    $curso_ids[] = $curso_id;
                } elseif (strpos($meta_key, 'blms_curso_') === 0) {
                    $curso_id = intval(str_replace('blms_curso_', '', $meta_key));
                    $curso_ids[] = $curso_id;
                }
            }
            
            // Si no hay cursos, mostrar mensaje
            if (empty($curso_ids)) {
                ?>
                <div class="no-cursos">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    <h3><?php _e('Aún no tienes cursos', 'breogan-lms'); ?></h3>
                    <p><?php _e('Explora nuestro catálogo y empieza a aprender hoy mismo.', 'breogan-lms'); ?></p>
                    <?php 
                    // Determinar la URL correcta para el catálogo de cursos
                    $archive_link = '';
                    if (post_type_exists('blms_curso')) {
                        $archive_link = get_post_type_archive_link('blms_curso');
                    } elseif (post_type_exists('cursos')) {
                        $archive_link = get_post_type_archive_link('cursos');
                    }
                    
                    if (empty($archive_link)) {
                        $archive_link = home_url();
                    }
                    ?>
                    <a href="<?php echo esc_url($archive_link); ?>" class="btn-explorar">
                        <?php _e('Explorar Cursos', 'breogan-lms'); ?>
                    </a>
                </div>
                <?php
            } else {
                // Obtener los objetos post de los cursos
                $args = array(
                    'post_type' => array('blms_curso', 'cursos'),
                    'post__in' => $curso_ids,
                    'posts_per_page' => -1,
                    'orderby' => 'title',
                    'order' => 'ASC'
                );
                
                $cursos_query = new WP_Query($args);
                
                if ($cursos_query->have_posts()) {
                    ?>
                    <ul class="lista-cursos">
                        <?php while ($cursos_query->have_posts()) : $cursos_query->the_post(); 
                            $curso_id = get_the_ID();
                            
                            // Calculamos el progreso del curso
                            // 1. Identificar los temas del curso con ambos tipos de post
                            $temas = array();
                            
                            if (post_type_exists('blms_tema')) {
                                $temas_blms = get_posts(array(
                                    'post_type' => 'blms_tema',
                                    'meta_key' => '_blms_curso_relacionado',
                                    'meta_value' => $curso_id,
                                    'numberposts' => -1
                                ));
                                $temas = array_merge($temas, $temas_blms);
                            }
                            
                            if (post_type_exists('temas')) {
                                $temas_breogan = get_posts(array(
                                    'post_type' => 'temas',
                                    'meta_key' => '_curso_relacionado',
                                    'meta_value' => $curso_id,
                                    'numberposts' => -1
                                ));
                                $temas = array_merge($temas, $temas_breogan);
                            }
                            
                            // 2. Contar todas las lecciones y las completadas
                            $total_lecciones = 0;
                            $lecciones_completadas = 0;
                            
                            foreach ($temas as $tema) {
                                $tema_id = $tema->ID;
                                $lecciones = array();
                                
                                // Buscar lecciones con ambos tipos de post
                                if (post_type_exists('blms_leccion')) {
                                    $lecciones_blms = get_posts(array(
                                        'post_type' => 'blms_leccion',
                                        'meta_key' => '_blms_tema_relacionado',
                                        'meta_value' => $tema_id,
                                        'numberposts' => -1
                                    ));
                                    $lecciones = array_merge($lecciones, $lecciones_blms);
                                }
                                
                                if (post_type_exists('lecciones')) {
                                    $lecciones_breogan = get_posts(array(
                                        'post_type' => 'lecciones',
                                        'meta_key' => '_tema_relacionado',
                                        'meta_value' => $tema_id,
                                        'numberposts' => -1
                                    ));
                                    $lecciones = array_merge($lecciones, $lecciones_breogan);
                                }
                                
                                foreach ($lecciones as $leccion) {
                                    $leccion_id = $leccion->ID;
                                    $total_lecciones++;
                                    
                                    // Verificar si la lección está completada usando varios formatos de metadatos
                                    $completada_blms = get_user_meta($user_id, 'blms_leccion_completada_' . $leccion_id, true);
                                    $completada_breogan = get_user_meta($user_id, 'breogan_leccion_completada_' . $leccion_id, true);
                                    $completada_alt = get_user_meta($user_id, 'breogan_leccion_' . $leccion_id . '_completada', true);
                                    
                                    if (!empty($completada_blms) || !empty($completada_breogan) || !empty($completada_alt)) {
                                        $lecciones_completadas++;
                                    }
                                }
                            }
                            
                            // 3. Calcular porcentaje
                            $porcentaje = ($total_lecciones > 0) ? round(($lecciones_completadas / $total_lecciones) * 100) : 0;
                            
                            // 4. Determinar estado del curso
                            $estado_clase = ($porcentaje >= 100) ? 'estado-completado' : (($porcentaje > 0) ? 'estado-activo' : 'estado-pendiente');
                            $estado_texto = ($porcentaje >= 100) ? __('Completado', 'breogan-lms') : (($porcentaje > 0) ? __('En progreso', 'breogan-lms') : __('Pendiente', 'breogan-lms'));
                        ?>
                            <li>
                                <h3>
                                    <a href="<?php echo get_permalink($curso_id); ?>"><?php echo get_the_title($curso_id); ?></a>
                                </h3>
                                <div class="curso-contenido">
                                    <div class="estado-indicador <?php echo $estado_clase; ?>">
                                        <?php echo $estado_texto; ?>
                                    </div>
                                    
                                    <?php if (has_post_thumbnail()): ?>
                                    <div class="curso-imagen">
                                        <?php echo get_the_post_thumbnail($curso_id, 'medium'); ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <p><?php _e('Progreso:', 'breogan-lms'); ?> <strong><?php echo $porcentaje; ?>%</strong></p>
                                    
                                    <div class="progreso-barra">
                                        <div style="width: <?php echo $porcentaje; ?>%"></div>
                                    </div>
                                    
                                    <p class="lecciones-info">
                                        <?php echo sprintf(
                                            __('%d de %d lecciones completadas', 'breogan-lms'),
                                            $lecciones_completadas,
                                            $total_lecciones
                                        ); ?>
                                    </p>
                                    
                                    <div class="curso-acciones">
                                        <a href="<?php echo get_permalink($curso_id); ?>" class="btn-acceder">
                                            <?php _e('Continuar', 'breogan-lms'); ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                                <polyline points="12 5 19 12 12 19"></polyline>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        <?php endwhile; 
                        wp_reset_postdata();
                        ?>
                    </ul>
                    <?php
                } else {
                    ?>
                    <div class="no-cursos">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        <h3><?php _e('No encontramos tus cursos', 'breogan-lms'); ?></h3>
                        <p><?php _e('Parece que tus cursos no están disponibles o han sido eliminados.', 'breogan-lms'); ?></p>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </main>
    
    <?php
    return ob_get_clean();
}

/**
 * Función auxiliar para mostrar un formulario de login cuando el usuario no está autenticado
 * 
 * @return string Formulario HTML de login
 */
function breogan_login_form_for_profile() {
    // Cargar estilos específicos para el perfil
    wp_enqueue_style(
        'breogan-perfil-styles',
        BREOGAN_LMS_URL . 'assets/css/perfil-styles.css',
        array(),
        defined('BREOGAN_LMS_VERSION') ? BREOGAN_LMS_VERSION : '1.0'
    );
    
    ob_start();
    ?>
    <div class="breogan-login-container">
        <div class="breogan-login-form">
            <h2><?php _e('Acceso a Mi Perfil', 'breogan-lms'); ?></h2>
            <p><?php _e('Por favor, inicia sesión para acceder a tu perfil y ver tu progreso en los cursos.', 'breogan-lms'); ?></p>
            
            <form name="loginform" id="breogan-loginform" action="<?php echo esc_url(site_url('wp-login.php', 'login_post')); ?>" method="post">
                <div class="login-field">
                    <label for="user_login"><?php _e('Usuario o Email', 'breogan-lms'); ?></label>
                    <input type="text" name="log" id="user_login" class="input" value="" size="20" autocapitalize="off" autocomplete="username" required />
                </div>
                
                <div class="login-field">
                    <label for="user_pass"><?php _e('Contraseña', 'breogan-lms'); ?></label>
                    <input type="password" name="pwd" id="user_pass" class="input" value="" size="20" autocomplete="current-password" required />
                </div>
                
                <div class="login-remember">
                    <label>
                        <input name="rememberme" type="checkbox" id="rememberme" value="forever" />
                        <?php _e('Recordarme', 'breogan-lms'); ?>
                    </label>
                </div>
                
                <div class="login-submit">
                    <input type="submit" name="wp-submit" id="wp-submit" class="btn-acceder" value="<?php esc_attr_e('Iniciar Sesión', 'breogan-lms'); ?>" />
                    <input type="hidden" name="redirect_to" value="<?php echo esc_url(get_permalink()); ?>" />
                </div>
            </form>
            
            <div class="login-links">
                <p class="login-register-link">
                    <?php _e('¿No tienes cuenta?', 'breogan-lms'); ?> 
                    <a href="<?php echo wp_registration_url(); ?>"><?php _e('Regístrate', 'breogan-lms'); ?></a>
                </p>
                <p class="login-lostpassword-link">
                    <a href="<?php echo wp_lostpassword_url(); ?>"><?php _e('¿Olvidaste tu contraseña?', 'breogan-lms'); ?></a>
                </p>
            </div>
        </div>
    </div>
    <?php
    
    return ob_get_clean();
}
?>
