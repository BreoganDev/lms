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
        
        // Argumentos para la consulta
        $args = array(
            'post_type' => 'blms_curso',
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
                ?>
                    <div class="curso-item">
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
                                <?php if ($precio) : ?>
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
        // Si el usuario no está logueado, mostrar mensaje y formulario de login
        if (!is_user_logged_in()) {
            return $this->login_form_for_profile();
        }
        
        // Iniciar buffer de salida
        ob_start();
        
        // Obtener ID del usuario actual
        $user_id = get_current_user_id();
        $user = get_userdata($user_id);
        
        ?>
        <div class="breogan-perfil-usuario">
            <h1><?php _e('Mi Perfil', 'breogan-lms'); ?></h1>
            
            <div class="perfil-info">
                <h2><?php _e('Información Personal', 'breogan-lms'); ?></h2>
                <p><strong><?php _e('Nombre:', 'breogan-lms'); ?></strong> <?php echo esc_html($user->display_name); ?></p>
                <p><strong><?php _e('Email:', 'breogan-lms'); ?></strong> <?php echo esc_html($user->user_email); ?></p>
            </div>
            
            <div class="perfil-cursos">
                <h2><?php _e('Mis Cursos', 'breogan-lms'); ?></h2>
                
                <?php
                // Obtener cursos comprados
                $cursos_comprados = $this->get_user_purchased_courses($user_id);
                
                if (!empty($cursos_comprados)) {
                    echo '<ul class="lista-cursos">';
                    
                    foreach ($cursos_comprados as $curso) {
                        // Obtener progreso
                        $user_handler = new Breogan_LMS_User();
                        $progreso = $user_handler->get_course_progress($user_id, $curso->ID);
                        
                        ?>
                        <li>
                            <h3><a href="<?php echo get_permalink($curso->ID); ?>"><?php echo get_the_title($curso->ID); ?></a></h3>
                            <p><?php _e('Progreso:', 'breogan-lms'); ?> <?php echo $progreso['porcentaje']; ?>% <?php _e('completado', 'breogan-lms'); ?></p>
                            <div class="progreso-barra">
                                <div style="width:<?php echo $progreso['porcentaje']; ?>%"></div>
                            </div>
                            <p class="lecciones-info">
                                <?php printf(
                                    __('%d de %d lecciones completadas', 'breogan-lms'),
                                    $progreso['lecciones_completadas'],
                                    $progreso['total_lecciones']
                                ); ?>
                            </p>
                        </li>
                        <?php
                    }
                    
                    echo '</ul>';
                } else {
                    echo '<p>' . __('No tienes cursos en progreso.', 'breogan-lms') . '</p>';
                    echo '<p><a href="' . get_post_type_archive_link('blms_curso') . '" class="btn-breogan">' . __('Ver catálogo de cursos', 'breogan-lms') . '</a></p>';
                }
                ?>
            </div>
        </div>
        <?php
        
        // Devolver contenido del buffer
        return ob_get_clean();
    }
    
    /**
     * Obtener cursos comprados por el usuario
     * 
     * @param int $user_id ID del usuario
     * @return array Arreglo de objetos post
     */
    private function get_user_purchased_courses($user_id) {
        // Obtener todas las claves de meta del usuario
        $user_meta_keys = get_user_meta($user_id);
        $curso_ids = array();
        
        // Filtrar las claves que corresponden a cursos comprados
        foreach ($user_meta_keys as $key => $value) {
            if (strpos($key, 'blms_curso_') === 0 && $value[0] === 'comprado') {
                $curso_id = intval(str_replace('blms_curso_', '', $key));
                $curso_ids[] = $curso_id;
            }
        }
        
        // Si no hay cursos, devolver arreglo vacío
        if (empty($curso_ids)) {
            return array();
        }
        
        // Obtener posts de los cursos
        $cursos = get_posts(array(
            'post_type' => 'blms_curso',
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
        ob_start();
        ?>
        <div class="breogan-login-form">
            <h2><?php _e('Acceso a Mi Perfil', 'breogan-lms'); ?></h2>
            <p><?php _e('Por favor, inicia sesión para acceder a tu perfil y ver tu progreso en los cursos.', 'breogan-lms'); ?></p>
            
            <?php
            // Mostrar formulario de login de WordPress
            echo wp_login_form(array(
                'echo' => false,
                'redirect' => get_permalink(),
                'form_id' => 'breogan-loginform',
                'label_username' => __('Usuario o Email', 'breogan-lms'),
                'label_password' => __('Contraseña', 'breogan-lms'),
                'label_remember' => __('Recordarme', 'breogan-lms'),
                'label_log_in' => __('Iniciar Sesión', 'breogan-lms'),
                'remember' => true
            ));
            ?>
            
            <p class="login-register-link">
                <?php _e('¿No tienes cuenta?', 'breogan-lms'); ?> 
                <a href="<?php echo wp_registration_url(); ?>"><?php _e('Regístrate', 'breogan-lms'); ?></a>
            </p>
            <p class="login-lostpassword-link">
                <a href="<?php echo wp_lostpassword_url(); ?>"><?php _e('¿Olvidaste tu contraseña?', 'breogan-lms'); ?></a>
            </p>
        </div>
        <?php
        
        return ob_get_clean();
    }
}