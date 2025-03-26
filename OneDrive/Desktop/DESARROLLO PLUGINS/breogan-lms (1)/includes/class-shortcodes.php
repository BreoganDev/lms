<?php
/**
 * Clase para gestionar shortcodes
 */
class Breogan_LMS_Shortcodes {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Registrar shortcodes
        add_shortcode('breogan_cursos', array($this, 'cursos_shortcode'));
        add_shortcode('breogan_perfil', array($this, 'perfil_shortcode'));
    }
    
    /**
     * Shortcode para mostrar listado de cursos
     * 
     * @param array $atts Atributos del shortcode
     * @return string Contenido HTML
     */
   public function cursos_shortcode($atts) {
    // Parsear atributos
    $atts = shortcode_atts(array(
        'cantidad' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
        'categoria' => '',
    ), $atts);
    
    // Iniciar buffer de salida
    ob_start();
    
    // Argumentos para la consulta - Usar cursos o blms_curso dependiendo de la estructura actual
    $post_types = array('blms_curso', 'cursos');
    $args = array(
        'post_type' => $post_types,
        'posts_per_page' => $atts['cantidad'],
        'orderby' => $atts['orderby'],
        'order' => $atts['order']
    );
    
    // Si se especificó una categoría
    if (!empty($atts['categoria'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'blms_categoria',
                'field' => 'slug',
                'terms' => $atts['categoria']
            )
        );
    }
    
    // Ejecutar consulta
    $cursos = new WP_Query($args);
    
    // Si hay cursos
    if ($cursos->have_posts()) {
        ?>
        <div class="listado-cursos-grid">
            <?php while ($cursos->have_posts()) : $cursos->the_post(); 
                $curso_id = get_the_ID();
                $precio = get_post_meta($curso_id, '_blms_precio_curso', true);
                if (empty($precio)) {
                    $precio = get_post_meta($curso_id, '_breogan_precio_curso', true);
                }
                $es_gratuito = get_post_meta($curso_id, '_blms_curso_gratuito', true);
                if (empty($es_gratuito)) {
                    $es_gratuito = get_post_meta($curso_id, '_breogan_curso_gratuito', true);
                }
            ?>
                <div class="curso-item">
                    <?php if ($es_gratuito == '1') : ?>
                        <span class="curso-gratuito-label"><?php _e('Gratis', 'breogan-lms'); ?></span>
                    <?php endif; ?>
                    <a href="<?php the_permalink(); ?>">
                        <div class="curso-imagen">
                            <?php 
                            if (has_post_thumbnail()) {
                                the_post_thumbnail('medium');
                            } else {
                                echo '<img src="' . BREOGAN_LMS_URL . 'assets/images/curso-default.jpg" alt="' . get_the_title() . '">';
                            }
                            ?>
                        </div>
                        <div class="curso-contenido">
                            <h3><?php the_title(); ?></h3>
                            <?php if ($es_gratuito == '1') : ?>
                                <p class="curso-precio curso-gratuito"><?php _e('Curso Gratuito', 'breogan-lms'); ?></p>
                            <?php elseif ($precio) : ?>
                                <p class="curso-precio"><?php echo esc_html($precio); ?> €</p>
                            <?php endif; ?>
                            <div class="btn-mas"><?php _e('Ver más', 'breogan-lms'); ?></div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
        <?php
        // Restaurar datos originales
        wp_reset_postdata();
    } else {
        echo '<p>' . __('No hay cursos disponibles.', 'breogan-lms') . '</p>';
    }
    
    // Devolver contenido del buffer
    return ob_get_clean();
}
    
    /**
     * Shortcode para mostrar perfil de usuario
     * 
     * @param array $atts Atributos del shortcode
     * @return string Contenido HTML
     */
    public function perfil_shortcode($atts) {
    // Cargar estilos específicos para el perfil
    wp_enqueue_style(
        'breogan-perfil-styles',
        BREOGAN_LMS_URL . 'assets/css/perfil-styles.css',
        array(),
        BREOGAN_LMS_VERSION
    );
    
    // Si el usuario no está logueado, mostrar mensaje y formulario de login
    if (!is_user_logged_in()) {
        return $this->login_form_for_profile();
    }
    
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
        
        <!-- Sección de información personal -->
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
            $cursos_comprados = $this->get_user_purchased_courses($user_id);
            
            if (!empty($cursos_comprados)) :
            ?>
                <ul class="lista-cursos">
                    <?php foreach ($cursos_comprados as $curso) :
                        // Calcular progreso igual que en la función original
                        $total_lecciones = 0;
                        $lecciones_completadas = 0;
                        
                        // Determinar tipo de post
                        $post_type_temas = post_type_exists('blms_tema') ? 'blms_tema' : 'temas';
                        $post_type_lecciones = post_type_exists('blms_leccion') ? 'blms_leccion' : 'lecciones';
                        
                        // Obtener temas del curso
                        $temas = get_posts([
                            'post_type'   => $post_type_temas,
                            'meta_key'    => '_curso_relacionado',
                            'meta_value'  => $curso->ID,
                            'numberposts' => -1
                        ]);
                        
                        foreach ($temas as $tema) {
                            // Obtener lecciones del tema
                            $lecciones = get_posts([
                                'post_type'   => $post_type_lecciones,
                                'meta_key'    => '_tema_relacionado',
                                'meta_value'  => $tema->ID,
                                'numberposts' => -1
                            ]);
                            
                            foreach ($lecciones as $leccion) {
                                $total_lecciones++;
                                
                                // Comprobar diferentes formas de marcar lecciones completadas
                                $completada = false;
                                if (get_user_meta($user_id, 'breogan_leccion_completada_' . $leccion->ID, true)) {
                                    $completada = true;
                                } elseif (get_user_meta($user_id, 'breogan_leccion_' . $leccion->ID, true) == 'completada') {
                                    $completada = true;
                                } elseif (get_user_meta($user_id, 'blms_leccion_completada_' . $leccion->ID, true)) {
                                    $completada = true;
                                }
                                
                                if ($completada) {
                                    $lecciones_completadas++;
                                }
                            }
                        }
                        
                        // Calcular porcentaje
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
                    // Determine correct post type archive link
                    $post_type = post_type_exists('blms_curso') ? 'blms_curso' : 'cursos';
                    $archive_link = get_post_type_archive_link($post_type);
                    
                    if (!$archive_link) {
                        $archive_link = home_url(); // Fallback to home if no archive exists
                    }
                    ?>
                    
                    <a href="<?php echo $archive_link; ?>" class="btn-explorar">
                        <?php _e('Explorar Cursos', 'breogan-lms'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php
    return ob_get_clean();
}
    
    /**
     * Obtener cursos comprados por el usuario - Versión actualizada
     * 
     * @param int $user_id ID del usuario
     * @return array Arreglo de objetos post
     */
    private function get_user_purchased_courses($user_id) {
        global $wpdb;
        $curso_ids = array();
        
        // Buscar metadatos con prefijo 'breogan_curso_' o 'blms_curso_'
        $breogan_meta = $wpdb->get_results(
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
        
        // Extraer IDs de cursos de las claves de metadatos
        foreach ($breogan_meta as $meta) {
            if (strpos($meta->meta_key, 'breogan_curso_') === 0) {
                $curso_id = intval(str_replace('breogan_curso_', '', $meta->meta_key));
                $curso_ids[] = $curso_id;
            } elseif (strpos($meta->meta_key, 'blms_curso_') === 0) {
                $curso_id = intval(str_replace('blms_curso_', '', $meta->meta_key));
                $curso_ids[] = $curso_id;
            }
        }
        
        // Si no hay cursos, devolver arreglo vacío
        if (empty($curso_ids)) {
            return array();
        }
        
        // Determine el tipo de post correcto
        $post_type = post_type_exists('blms_curso') ? 'blms_curso' : 'cursos';
        
        // Obtener posts de los cursos
        $cursos = get_posts(array(
            'post_type' => $post_type,
            'post__in' => $curso_ids,
            'numberposts' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));
        
        return $cursos;
    }
    
    /**
     * Mostrar formulario de login para acceder al perfil
     * 
     * @return string Formulario HTML
     */
    private function login_form_for_profile() {
    // Cargar estilos específicos para el perfil
    wp_enqueue_style(
        'breogan-perfil-styles',
        BREOGAN_LMS_URL . 'assets/css/perfil-styles.css',
        array(),
        BREOGAN_LMS_VERSION
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
}
// Añadir al principio del archivo o después de los includes existentes
require_once BREOGAN_LMS_PATH . 'includes/shortcode-perfil-usuario.php';
