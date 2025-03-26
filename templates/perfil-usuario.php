<?php
<<<<<<< HEAD
// Evitar acceso si el usuario no está logueado
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
=======
<<<<<<< HEAD
/**
 * Template para la página de perfil de usuario
 * Diseño moderno y profesional para Breogan LMS
 */

// Evitar acceso si el usuario no está logueado
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
=======
// Evitar acceso si el usuario no está logueado
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
    exit;
}

get_header();

<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
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
<<<<<<< HEAD
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
            'blms_leccion_completada_'
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
=======
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
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
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
<<<<<<< HEAD
            <div class="estadistica-valor"><?php echo $tiempo_miembro; ?></div>
=======
            <div class="estadistica-valor"><?php echo human_time_diff(strtotime($user->user_registered), current_time('timestamp')); ?></div>
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
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
<<<<<<< HEAD
=======
                    <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" class="btn-acceder">
                        <?php _e('Mi Cuenta', 'breogan-lms'); ?>
                    </a>
                </div>
                <div class="info-item">
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
                    <a href="<?php echo wp_lostpassword_url(); ?>" class="btn-acceder">
                        <?php _e('Cambiar Contraseña', 'breogan-lms'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
<<<<<<< HEAD
    <!-- NUEVA SECCIÓN: Progreso Detallado de Cursos -->
<?php
// Código para mostrar la tabla de progreso
$user_id = get_current_user_id();

$cursos = get_posts(array(
    'post_type' => array('cursos', 'blms_curso'),
    'numberposts' => -1
));

echo '<div class="tabla-progreso-wrapper">';
echo '<h2>' . __('Progreso Detallado de Cursos', 'breogan-lms') . '</h2>';
echo '<table class="tabla-progreso">';
echo '<thead><tr><th>Curso</th><th>Tema</th><th>Lección</th><th>Estado</th></tr></thead>';
echo '<tbody>';

foreach ($cursos as $curso) {
    $curso_comprado = get_user_meta($user_id, 'breogan_curso_' . $curso->ID, true);
    $curso_comprado_blms = get_user_meta($user_id, 'blms_curso_' . $curso->ID, true);

    if ($curso_comprado !== 'comprado' && $curso_comprado_blms !== 'comprado') continue;

    $temas = get_posts(array(
        'post_type' => array('temas', 'blms_tema'),
        'meta_key' => '_curso_relacionado',
        'meta_value' => $curso->ID,
        'numberposts' => -1
    ));

    // Calcular progreso del curso
    $total_lecciones = 0;
    $lecciones_completadas = 0;

    foreach ($temas as $tema) {
        $lecciones = get_posts(array(
            'post_type' => array('lecciones', 'blms_leccion'),
            'meta_key' => '_tema_relacionado',
            'meta_value' => $tema->ID,
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

    // Mostrar fila con resumen del curso y la barra de progreso
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
            'post_type' => array('lecciones', 'blms_leccion'),
            'meta_key' => '_tema_relacionado',
            'meta_value' => $tema->ID,
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
    
=======
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
    <!-- Sección de cursos -->
    <div class="breogan-perfil-seccion">
        <h2><?php _e('Mis Cursos', 'breogan-lms'); ?></h2>
        
        <?php
<<<<<<< HEAD
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
=======
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
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
            <div class="no-cursos">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <h3><?php _e('Aún no tienes cursos', 'breogan-lms'); ?></h3>
                <p><?php _e('Explora nuestro catálogo y empieza a aprender hoy mismo.', 'breogan-lms'); ?></p>
<<<<<<< HEAD
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
                                
                                // Verificar si la lección está completada usando ambos prefijos
                                $completada_blms = get_user_meta($user_id, 'blms_leccion_completada_' . $leccion_id, true);
                                $completada_breogan = get_user_meta($user_id, 'breogan_leccion_completada_' . $leccion_id, true);
                                
                                // También verificar con prefijo alternativo
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

<?php get_footer(); ?>
=======
                <a href="<?php echo get_post_type_archive_link('cursos'); ?>" class="btn-explorar">
                    <?php _e('Explorar Cursos', 'breogan-lms'); ?>
                </a>
            </div>
        <?php } ?>
    </div>
</main>

<?php get_footer(); ?>
=======
$user_id = get_current_user_id();
?>

<main class="contenedor seccion">
    <h1 class="texto-center texto-primary">Tu Progreso</h1>

  

    <h2>Tus Cursos en Progreso</h2>
    <ul class="lista-cursos">
        <?php
        // Obtener todos los cursos en los que el usuario ha completado al menos una lección
        $cursos = get_posts(array(
<<<<<<< HEAD
            'post_type'   => 'breogan_cursos',
=======
            'post_type'   => 'cursos',
>>>>>>> 12ee31a27decda5eba9c768c4e10372ecba265b3
            'numberposts' => -1
        ));

        $cursos_en_progreso = [];

        foreach ($cursos as $curso) {
            $curso_id = $curso->ID;

            // Obtener todas las lecciones de los temas de este curso
            $temas = get_posts(array(
<<<<<<< HEAD
                'post_type'   => 'breogan_temas',
=======
                'post_type'   => 'temas',
>>>>>>> 12ee31a27decda5eba9c768c4e10372ecba265b3
                'meta_key'    => '_curso_relacionado',
                'meta_value'  => $curso_id,
                'numberposts' => -1
            ));

            $total_lecciones = 0;
            $lecciones_completadas = 0;

            foreach ($temas as $tema) {
                $tema_id = $tema->ID;

                $lecciones = get_posts(array(
<<<<<<< HEAD
                    'post_type'   => 'breogan_lecciones',
=======
                    'post_type'   => 'lecciones',
>>>>>>> 12ee31a27decda5eba9c768c4e10372ecba265b3
                    'meta_key'    => '_tema_relacionado',
                    'meta_value'  => $tema_id,
                    'numberposts' => -1
                ));

                foreach ($lecciones as $leccion) {
                    $total_lecciones++;
                    if (get_user_meta($user_id, 'breogan_leccion_' . $leccion->ID, true)) {
                        $lecciones_completadas++;
                    }
                }
            }

            if ($total_lecciones > 0 && $lecciones_completadas > 0) {
                $porcentaje = round(($lecciones_completadas / $total_lecciones) * 100);
                $cursos_en_progreso[] = [
                    'curso' => $curso,
                    'porcentaje' => $porcentaje
                ];
            }
        }

        if (!empty($cursos_en_progreso)) {
            foreach ($cursos_en_progreso as $data) {
                echo '<li>';
                echo '<h3><a href="' . get_permalink($data['curso']->ID) . '">' . get_the_title($data['curso']->ID) . '</a></h3>';
                echo '<p>Progreso: ' . $data['porcentaje'] . '% completado</p>';
                echo '<div class="progreso-barra"><div style="width:' . $data['porcentaje'] . '%"></div></div>';
                echo '</li>';
            }
        } else {
            echo '<p>No tienes cursos en progreso.</p>';
        }
        ?>
    </ul>
</main>

<?php get_footer(); ?>
>>>>>>> 49d2a8a4a15c13644e33921ea14a3171b7b0e858
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
