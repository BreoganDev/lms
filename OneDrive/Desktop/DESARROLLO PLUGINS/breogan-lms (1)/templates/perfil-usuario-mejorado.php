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
$cursos = get_posts(array(
    'post_type' => array('cursos', 'blms_curso'),
    'numberposts' => -1
));

$total_courses = count($cursos);
$completed_courses = 0;
$total_progress = 0;

foreach ($cursos as $curso) {
    // Verificar si el usuario ha comprado este curso
    $ha_comprado_breogan = get_user_meta($user_id, 'breogan_curso_' . $curso->ID, true);
    $ha_comprado_blms = get_user_meta($user_id, 'blms_curso_' . $curso->ID, true);
    
    if ($ha_comprado_breogan === 'comprado' || $ha_comprado_blms === 'comprado') {
        // Calcular el progreso para este curso
        $temas = get_posts(array(
            'post_type' => array('temas', 'blms_tema'),
            'meta_key' => '_curso_relacionado',
            'meta_value' => $curso->ID,
            'numberposts' => -1
        ));
        
        $total_lessons = 0;
        $completed_lessons = 0;
        
        foreach ($temas as $tema) {
            $lecciones = get_posts(array(
                'post_type' => array('lecciones', 'blms_leccion'),
                'meta_key' => '_tema_relacionado',
                'meta_value' => $tema->ID,
                'numberposts' => -1
            ));
            
            foreach ($lecciones as $leccion) {
                $total_lessons++;
                
                // Verificar si la lección está completada
                $completed_blms = get_user_meta($user_id, 'blms_leccion_completada_' . $leccion->ID, true);
                $completed_breogan = get_user_meta($user_id, 'breogan_leccion_' . $leccion->ID . '_completada', true);
                
                if (!empty($completed_blms) || !empty($completed_breogan)) {
                    $completed_lessons++;
                }
            }
        }
        
        // Calcular porcentaje
        $percentage = ($total_lessons > 0) ? round(($completed_lessons / $total_lessons) * 100) : 0;
        $total_progress += $percentage;
        
        if ($percentage >= 100) {
            $completed_courses++;
        }
    }
}

$avg_progress = ($total_courses > 0) ? round($total_progress / $total_courses) : 0;
?>

<!-- Mostrar el resumen de progreso -->
<div class="breogan-perfil-progreso">
    <h2><?php _e('Resumen de Progreso', 'breogan-lms'); ?></h2>
    
    <div class="perfil-estadisticas">
        <div class="estadistica-card">
            <div class="estadistica-valor"><?php echo $completed_courses; ?>/<?php echo $total_courses; ?></div>
            <div class="estadistica-label"><?php _e('Cursos Completados', 'breogan-lms'); ?></div>
        </div>
        
        <div class="estadistica-card">
            <div class="estadistica-valor"><?php echo $avg_progress; ?>%</div>
            <div class="estadistica-label"><?php _e('Progreso Promedio', 'breogan-lms'); ?></div>
        </div>
    </div>
</div>

<!-- Fin de la sección de Información Personal -->
</div> <!-- fin de .breogan-perfil-seccion -->

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


<main class="breogan-perfil-usuario">
    <div class="breogan-perfil-header">
        <h1><?php _e('Mi Perfil', 'breogan-lms'); ?></h1>
    </div>
    
    <?php do_action('breogan_profile_before_courses'); ?>
    
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
            'breogan_leccion_'
        );
        
        foreach ($lecciones_meta_prefijos as $prefijo) {
            $count = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->usermeta} 
                     WHERE user_id = %d AND meta_key LIKE %s AND meta_value IN ('completada', '1')",
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
    
    <!-- Sección de cursos -->
    <div class="breogan-perfil-seccion">
        <h2><?php _e('Mis Cursos', 'breogan-lms'); ?></h2>
        
        <?php
        // Obtener cursos comprados 
        // Primero, obtener todos los meta_key que tienen el prefijo breogan_curso_ o blms_curso_
        $curso_meta_keys = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT meta_key FROM {$wpdb->usermeta} 
                 WHERE user_id = %d 
                 AND (meta_key LIKE %s OR meta_key LIKE %s)
                 AND meta_value = %s",
                $user_id,
                'breogan_curso_%',
                'blms_curso_%',
                'comprado'
            )
        );
        
        $cursos_comprados = array();
        
        if (!empty($curso_meta_keys)) {
            // Para cada meta_key, extraer el ID del curso
            $curso_ids = array();
            foreach ($curso_meta_keys as $meta_key) {
                if (strpos($meta_key, 'breogan_curso_') === 0) {
                    $curso_id = intval(str_replace('breogan_curso_', '', $meta_key));
                    $curso_ids[] = $curso_id;
                } elseif (strpos($meta_key, 'blms_curso_') === 0) {
                    $curso_id = intval(str_replace('blms_curso_', '', $meta_key));
                    $curso_ids[] = $curso_id;
                }
            }
            
            // Eliminar duplicados
            $curso_ids = array_unique($curso_ids);
            
            // Si hay IDs de cursos, obtener los objetos de curso
            if (!empty($curso_ids)) {
                // Intentar ambos tipos de post
                $cursos_tipo_1 = get_posts(array(
                    'post_type' => 'blms_curso',
                    'include' => $curso_ids,
                    'numberposts' => -1
                ));
                
                $cursos_tipo_2 = get_posts(array(
                    'post_type' => 'cursos',
                    'include' => $curso_ids,
                    'numberposts' => -1
                ));
                
                $cursos_comprados = array_merge($cursos_tipo_1, $cursos_tipo_2);
            }
        }
        
        if (!empty($cursos_comprados)) :
        ?>
            <ul class="lista-cursos">
                <?php 
                foreach ($cursos_comprados as $curso) :
                    // Variables para almacenar los resultados
                    $total_lecciones = 0;
                    $lecciones_completadas = 0;
                    
                    // 1. Obtener los IDs de los temas relacionados con el curso
                    $temas_ids = array();
                    
                    // Buscar temas usando diferentes metakeys y post_types para compatibilidad
                    $meta_keys_temas = array('_blms_curso_relacionado', '_curso_relacionado');
                    $post_types_temas = array('blms_tema', 'temas');
                    
                    foreach ($post_types_temas as $post_type) {
                        foreach ($meta_keys_temas as $meta_key) {
                            $temas = get_posts(array(
                                'post_type' => $post_type,
                                'meta_key' => $meta_key,
                                'meta_value' => $curso->ID,
                                'numberposts' => -1,
                                'fields' => 'ids'
                            ));
                            
                            if (!empty($temas)) {
                                $temas_ids = array_merge($temas_ids, $temas);
                            }
                        }
                    }
                    
                    // Si no hay temas, continuar con el siguiente curso
                    if (empty($temas_ids)) {
                        continue;
                    }
                    
                    // 2. Obtener las lecciones relacionadas con los temas
                    $lecciones_ids = array();
                    
                    // Buscar lecciones usando diferentes metakeys y post_types para compatibilidad
                    $meta_keys_lecciones = array('_blms_tema_relacionado', '_tema_relacionado');
                    $post_types_lecciones = array('blms_leccion', 'lecciones');
                    
                    foreach ($post_types_lecciones as $post_type) {
                        foreach ($meta_keys_lecciones as $meta_key) {
                            foreach ($temas_ids as $tema_id) {
                                $lecciones = get_posts(array(
                                    'post_type' => $post_type,
                                    'meta_key' => $meta_key,
                                    'meta_value' => $tema_id,
                                    'numberposts' => -1,
                                    'fields' => 'ids'
                                ));
                                
                                if (!empty($lecciones)) {
                                    $lecciones_ids = array_merge($lecciones_ids, $lecciones);
                                }
                            }
                        }
                    }
                    
                    // 3. Contar lecciones completadas
                    $total_lecciones = count($lecciones_ids);
                    
                    foreach ($lecciones_ids as $leccion_id) {
                        // Comprobar diferentes formas de marcar lecciones completadas
                        $completada = false;
                        
                        if (get_user_meta($user_id, 'breogan_leccion_completada_' . $leccion_id, true)) {
                            $completada = true;
                        } elseif (get_user_meta($user_id, 'breogan_leccion_' . $leccion_id, true) == 'completada') {
                            $completada = true;
                        } elseif (get_user_meta($user_id, 'blms_leccion_completada_' . $leccion_id, true)) {
                            $completada = true;
                        }
                        
                        if ($completada) {
                            $lecciones_completadas++;
                        }
                    }
                    
                    // Calcular porcentaje de progreso
                    $porcentaje = ($total_lecciones > 0) ? round(($lecciones_completadas / $total_lecciones) * 100) : 0;
                    
                    // Determinar estado del curso
                    $estado_clase = ($porcentaje >= 100) ? 'estado-completado' : (($porcentaje > 0) ? 'estado-activo' : 'estado-pendiente');
                    $estado_texto = ($porcentaje >= 100) ? __('Completado', 'breogan-lms') : (($porcentaje > 0) ? __('En progreso', 'breogan-lms') : __('Pendiente', 'breogan-lms'));
                    ?>
                    <li>
                        <h3>
                            <a href="<?php echo get_permalink($curso->ID); ?>"><?php echo get_the_title($curso->ID); ?></a>
                        </h3>
                        <div class="curso-contenido">
                            <div class="estado-indicador <?php echo $estado_clase; ?>">
                                <?php echo $estado_texto; ?>
                            </div>
                            
                            <?php if (has_post_thumbnail($curso->ID)): ?>
                            <div class="curso-imagen">
                                <?php echo get_the_post_thumbnail($curso->ID, 'medium'); ?>
                            </div>
                            <?php endif; ?>
                            
                            <p><?php _e('Progreso:', 'breogan-lms'); ?> <strong><?php echo $porcentaje; ?>%</strong></p>
                            
                            <div class="progreso-barra">
                                <div style="width:<?php echo $porcentaje; ?>%"></div>
                            </div>
                            
                            <p class="lecciones-info">
                                <?php printf(
                                    __('%d de %d lecciones completadas', 'breogan-lms'),
                                    $lecciones_completadas,
                                    $total_lecciones
                                ); ?>
                            </p>
                            
                            <div class="curso-acciones">
                                <a href="<?php echo get_permalink($curso->ID); ?>" class="btn-acceder">
                                    <?php _e('Continuar', 'breogan-lms'); ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="no-cursos">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <h3><?php _e('Aún no tienes cursos', 'breogan-lms'); ?></h3>
                <p><?php _e('Explora nuestro catálogo y empieza a aprender hoy mismo.', 'breogan-lms'); ?></p>
                
                <?php
                // Intentar con diferentes tipos de post para el enlace de archivo
                $archive_link = '';
                $post_types_to_try = array('blms_curso', 'cursos');
                
                foreach ($post_types_to_try as $post_type) {
                    $link = get_post_type_archive_link($post_type);
                    if ($link) {
                        $archive_link = $link;
                        break;
                    }
                }
                
                // Si no se encontró ningún enlace, usar la página de inicio
                if (empty($archive_link)) {
                    $archive_link = home_url();
                }
                ?>
                
                <a href="<?php echo esc_url($archive_link); ?>" class="btn-explorar">
                    <?php _e('Explorar Cursos', 'breogan-lms'); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>