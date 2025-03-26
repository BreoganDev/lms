<?php
/**
 * Funciones para calcular el progreso de los cursos
 * 
 * @package Breogan LMS
 */

// Evitar acceso directo
if (!defined('ABSPATH')) exit;

/**
 * Calcular el progreso de un usuario en un curso específico
 * 
 * @param int $user_id ID del usuario
 * @param int $curso_id ID del curso
 * @return array Array con datos del progreso (total_lecciones, lecciones_completadas, porcentaje)
 */
function breogan_calcular_progreso_curso($user_id, $curso_id) {
    // Valores predeterminados
    $resultado = array(
        'total_lecciones' => 0,
        'lecciones_completadas' => 0,
        'porcentaje' => 0,
        'estado_clase' => 'estado-pendiente',
        'estado_texto' => __('Pendiente', 'breogan-lms')
    );
    
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
                'meta_value' => $curso_id,
                'numberposts' => -1,
                'fields' => 'ids'
            ));
            
            if (!empty($temas)) {
                $temas_ids = array_merge($temas_ids, $temas);
            }
        }
    }
    
    // Si no hay temas, devolver los valores predeterminados
    if (empty($temas_ids)) {
        return $resultado;
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
    $lecciones_completadas = 0;
    
    foreach ($lecciones_ids as $leccion_id) {
        // Comprobar diferentes formas de marcar lecciones completadas
        $completada = false;
        
        // Comprobar con diferentes prefijos para compatibilidad
        $meta_keys_completado = array(
            'breogan_leccion_completada_' . $leccion_id,
            'blms_leccion_completada_' . $leccion_id,
            'breogan_leccion_' . $leccion_id
        );
        
        foreach ($meta_keys_completado as $meta_key) {
            $valor = get_user_meta($user_id, $meta_key, true);
            if (!empty($valor) && ($valor === 'completada' || $valor === '1' || $valor === true)) {
                $completada = true;
                break;
            }
        }
        
        if ($completada) {
            $lecciones_completadas++;
        }
    }
    
    // 4. Calcular porcentaje de progreso
    $porcentaje = ($total_lecciones > 0) ? round(($lecciones_completadas / $total_lecciones) * 100) : 0;
    
    // 5. Determinar estado del curso
    $estado_clase = ($porcentaje >= 100) ? 'estado-completado' : (($porcentaje > 0) ? 'estado-activo' : 'estado-pendiente');
    $estado_texto = ($porcentaje >= 100) ? __('Completado', 'breogan-lms') : (($porcentaje > 0) ? __('En progreso', 'breogan-lms') : __('Pendiente', 'breogan-lms'));
    
    // Devolver resultados
    return array(
        'total_lecciones' => $total_lecciones,
        'lecciones_completadas' => $lecciones_completadas,
        'porcentaje' => $porcentaje,
        'estado_clase' => $estado_clase,
        'estado_texto' => $estado_texto
    );
}

/**
 * Obtener todos los cursos comprados por un usuario
 * 
 * @param int $user_id ID del usuario
 * @return array Array de objetos WP_Post
 */
function breogan_obtener_cursos_usuario($user_id) {
    global $wpdb;
    
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
    
    return $cursos_comprados;
}

/**
 * Obtener estadísticas generales del usuario
 * 
 * @param int $user_id ID del usuario
 * @return array Array con las estadísticas
 */
function breogan_obtener_estadisticas_usuario($user_id) {
    global $wpdb;
    
    // Inicializar estadísticas
    $estadisticas = array(
        'cursos_comprados' => 0,
        'lecciones_completadas' => 0,
        'tiempo_miembro' => '',
    );
    
    // 1. Contar cursos comprados
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
    $estadisticas['cursos_comprados'] = count($cursos_meta);
    
    // 2. Contar lecciones completadas
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
    $estadisticas['lecciones_completadas'] = $lecciones_completadas;
    
    // 3. Calcular tiempo como miembro
    $user = get_userdata($user_id);
    if ($user) {
        $estadisticas['tiempo_miembro'] = human_time_diff(strtotime($user->user_registered), current_time('timestamp'));
    }
    
    return $estadisticas;
}

/**
 * Mostrar el progreso de un curso para un usuario
 * 
 * @param int $user_id ID del usuario
 * @param int $curso_id ID del curso
 * @return string HTML con la barra de progreso
 */
function breogan_mostrar_progreso_curso($user_id, $curso_id) {
    // Obtener datos de progreso
    $progreso = breogan_calcular_progreso_curso($user_id, $curso_id);
    
    // Formatear HTML
    $html = '<div class="breogan-progreso-curso">';
    $html .= '<div class="estado-indicador ' . esc_attr($progreso['estado_clase']) . '">' . esc_html($progreso['estado_texto']) . '</div>';
    $html .= '<p>' . __('Progreso:', 'breogan-lms') . ' <strong>' . esc_html($progreso['porcentaje']) . '%</strong></p>';
    $html .= '<div class="progreso-barra"><div style="width:' . esc_attr($progreso['porcentaje']) . '%"></div></div>';
    $html .= '<p class="lecciones-info">' . sprintf(
        __('%d de %d lecciones completadas', 'breogan-lms'),
        $progreso['lecciones_completadas'],
        $progreso['total_lecciones']
    ) . '</p>';
    $html .= '</div>';
    
    return $html;
}

/**
 * Verificar si el usuario ha completado un curso
 * 
 * @param int $user_id ID del usuario
 * @param int $curso_id ID del curso
 * @return bool True si el curso está completado, false si no
 */
function breogan_curso_completado($user_id, $curso_id) {
    $progreso = breogan_calcular_progreso_curso($user_id, $curso_id);
    return ($progreso['porcentaje'] >= 100);
}

/**
 * Marcar o desmarcar una lección como completada
 * 
 * @param int $user_id ID del usuario
 * @param int $leccion_id ID de la lección
 * @param bool $completada True para marcar como completada, false para desmarcar
 * @return bool Resultado de la operación
 */
function breogan_marcar_leccion_completada($user_id, $leccion_id, $completada = true) {
    if ($completada) {
        // Marcar con los diferentes formatos para garantizar compatibilidad
        update_user_meta($user_id, 'breogan_leccion_completada_' . $leccion_id, current_time('mysql'));
        update_user_meta($user_id, 'blms_leccion_completada_' . $leccion_id, current_time('mysql'));
        update_user_meta($user_id, 'breogan_leccion_' . $leccion_id, 'completada');
        return true;
    } else {
        // Desmarcar en todos los formatos
        delete_user_meta($user_id, 'breogan_leccion_completada_' . $leccion_id);
        delete_user_meta($user_id, 'blms_leccion_completada_' . $leccion_id);
        delete_user_meta($user_id, 'breogan_leccion_' . $leccion_id);
        return true;
    }
}