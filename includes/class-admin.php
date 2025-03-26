<?php
/**
 * Clase para gestionar funciones de administración
 */
class Breogan_LMS_Admin {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Agregar menú de administración
        add_action('admin_menu', array($this, 'register_admin_menu'));
        
        // Agregar columnas personalizadas
        add_filter('manage_blms_curso_posts_columns', array($this, 'add_curso_columns'));
        add_action('manage_blms_curso_posts_custom_column', array($this, 'display_curso_columns'), 10, 2);
        
        add_filter('manage_blms_tema_posts_columns', array($this, 'add_tema_columns'));
        add_action('manage_blms_tema_posts_custom_column', array($this, 'display_tema_columns'), 10, 2);
        
        add_filter('manage_blms_leccion_posts_columns', array($this, 'add_leccion_columns'));
        add_action('manage_blms_leccion_posts_custom_column', array($this, 'display_leccion_columns'), 10, 2);
    }
    
    /**
     * Registrar menú de administración
     */
    public function register_admin_menu() {
        // Menú principal
        add_menu_page(
            __('Breogan LMS', 'breogan-lms'),
            __('Breogan LMS', 'breogan-lms'),
            'manage_options',
            'breogan-lms',
            array($this, 'admin_dashboard_page'),
            'dashicons-welcome-learn-more',
            25
        );
        
        // Submenús
        add_submenu_page(
            'breogan-lms',
            __('Panel de Control', 'breogan-lms'),
            __('Panel de Control', 'breogan-lms'),
            'manage_options',
            'breogan-lms',
            array($this, 'admin_dashboard_page')
        );
        
        add_submenu_page(
            'breogan-lms',
            __('Cursos', 'breogan-lms'),
            __('Cursos', 'breogan-lms'),
            'manage_options',
            'edit.php?post_type=blms_curso'
        );
        
        add_submenu_page(
            'breogan-lms',
            __('Temas', 'breogan-lms'),
            __('Temas', 'breogan-lms'),
            'manage_options',
            'edit.php?post_type=blms_tema'
        );
        
        add_submenu_page(
            'breogan-lms',
            __('Lecciones', 'breogan-lms'),
            __('Lecciones', 'breogan-lms'),
            'manage_options',
            'edit.php?post_type=blms_leccion'
        );
        
        add_submenu_page(
            'breogan-lms',
            __('Configuración', 'breogan-lms'),
            __('Configuración', 'breogan-lms'),
            'manage_options',
            'breogan-lms-settings',
            array($this, 'admin_settings_page')
        );
    }
    
    /**
     * Página de panel de control
     */
    public function admin_dashboard_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Breogan LMS - Panel de Control', 'breogan-lms'); ?></h1>
            
            <div class="breogan-dashboard-content">
                <div class="breogan-dashboard-stats">
                    <h2><?php _e('Estadísticas', 'breogan-lms'); ?></h2>
                    
                    <?php
                    // Obtener estadísticas
                    $curso_count = wp_count_posts('blms_curso');
                    $tema_count = wp_count_posts('blms_tema');
                    $leccion_count = wp_count_posts('blms_leccion');
                    
                    // Contar usuarios que han comprado al menos un curso
                    global $wpdb;
                    $users_with_courses = $wpdb->get_var(
                        "SELECT COUNT(DISTINCT user_id) 
                         FROM {$wpdb->usermeta} 
                         WHERE meta_key LIKE 'blms_curso_%' 
                         AND meta_value = 'comprado'"
                    );
                    ?>
                    
                    <div class="breogan-stats-grid">
                        <div class="breogan-stat-box">
                            <h3><?php _e('Cursos', 'breogan-lms'); ?></h3>
                            <p class="stat-number"><?php echo $curso_count->publish; ?></p>
                        </div>
                        
                        <div class="breogan-stat-box">
                            <h3><?php _e('Temas', 'breogan-lms'); ?></h3>
                            <p class="stat-number"><?php echo $tema_count->publish; ?></p>
                        </div>
                        
                        <div class="breogan-stat-box">
                            <h3><?php _e('Lecciones', 'breogan-lms'); ?></h3>
                            <p class="stat-number"><?php echo $leccion_count->publish; ?></p>
                        </div>
                        
                        <div class="breogan-stat-box">
                            <h3><?php _e('Estudiantes', 'breogan-lms'); ?></h3>
                            <p class="stat-number"><?php echo $users_with_courses; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="breogan-dashboard-actions">
                    <h2><?php _e('Acciones Rápidas', 'breogan-lms'); ?></h2>
                    
                    <div class="breogan-actions-grid">
                        <a href="<?php echo admin_url('post-new.php?post_type=blms_curso'); ?>" class="breogan-action-link">
                            <span class="dashicons dashicons-plus"></span>
                            <?php _e('Nuevo Curso', 'breogan-lms'); ?>
                        </a>
                        
                        <a href="<?php echo admin_url('post-new.php?post_type=blms_tema'); ?>" class="breogan-action-link">
                            <span class="dashicons dashicons-plus"></span>
                            <?php _e('Nuevo Tema', 'breogan-lms'); ?>
                        </a>
                        
                        <a href="<?php echo admin_url('post-new.php?post_type=blms_leccion'); ?>" class="breogan-action-link">
                            <span class="dashicons dashicons-plus"></span>
                            <?php _e('Nueva Lección', 'breogan-lms'); ?>
                        </a>
                        
                        <a href="<?php echo admin_url('admin.php?page=breogan-lms-settings'); ?>" class="breogan-action-link">
                            <span class="dashicons dashicons-admin-generic"></span>
                            <?php _e('Configuración', 'breogan-lms'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Página de configuración
     */
    public function admin_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Breogan LMS - Configuración', 'breogan-lms'); ?></h1>
            
            <h2 class="nav-tab-wrapper">
                <a href="?page=breogan-lms-settings&tab=general" class="nav-tab <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'general') ? 'nav-tab-active' : ''; ?>">
                    <?php _e('General', 'breogan-lms'); ?>
                </a>
                <a href="?page=breogan-lms-settings&tab=pagos" class="nav-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'pagos') ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Pagos', 'breogan-lms'); ?>
                </a>
                <a href="?page=breogan-lms-settings&tab=avanzado" class="nav-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'avanzado') ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Avanzado', 'breogan-lms'); ?>
                </a>
            </h2>
            
            <div class="breogan-settings-content">
                <?php
                $tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
                
                switch ($tab) {
                    case 'general':
                        $this->display_general_settings();
                        break;
                    case 'pagos':
                        $this->display_payment_settings();
                        break;
                    case 'avanzado':
                        $this->display_advanced_settings();
                        break;
                    default:
                        $this->display_general_settings();
                }
                ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Mostrar configuración general
     */
    private function display_general_settings() {
        ?>
        <form method="post" action="options.php">
            <?php
            settings_fields('blms_general_settings');
            do_settings_sections('breogan-lms-general');
            submit_button();
            ?>
        </form>
        <?php
    }
    
    /**
     * Mostrar configuración de pagos
     */
    private function display_payment_settings() {
        ?>
        <form method="post" action="options.php">
            <?php
            settings_fields('blms_payment_settings');
            do_settings_sections('breogan-lms-pagos');
            submit_button();
            ?>
        </form>
        <?php
    }
    
    /**
     * Mostrar configuración avanzada
     */
    private function display_advanced_settings() {
        ?>
        <form method="post" action="options.php">
            <?php
            settings_fields('blms_advanced_settings');
            do_settings_sections('breogan-lms-advanced');
            submit_button();
            ?>
        </form>
        <?php
    }
    
    /**
     * Agregar columnas personalizadas a la lista de cursos
     * 
     * @param array $columns Columnas actuales
     * @return array Columnas modificadas
     */
    public function add_curso_columns($columns) {
        $new_columns = array();
        
        // Insertar columnas después de 'title'
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            
            if ($key === 'title') {
                $new_columns['precio'] = __('Precio', 'breogan-lms');
                $new_columns['estudiantes'] = __('Estudiantes', 'breogan-lms');
                $new_columns['temas'] = __('Temas', 'breogan-lms');
            }
        }
        
        return $new_columns;
    }
    
    /**
     * Mostrar contenido de columnas personalizadas en cursos
     * 
     * @param string $column_name Nombre de la columna
     * @param int $post_id ID del post
     */
    public function display_curso_columns($column_name, $post_id) {
        switch ($column_name) {
            case 'precio':
                $precio = get_post_meta($post_id, '_blms_precio_curso', true);
                echo !empty($precio) ? esc_html($precio) . ' €' : '-';
                break;
                
            case 'estudiantes':
                global $wpdb;
                $estudiantes = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = 'comprado'",
                    'blms_curso_' . $post_id
                ));
                echo $estudiantes ? $estudiantes : '0';
                break;
                
            case 'temas':
                $temas = get_posts(array(
                    'post_type' => 'blms_tema',
                    'meta_key' => '_blms_curso_relacionado',
                    'meta_value' => $post_id,
                    'numberposts' => -1
                ));
                echo count($temas);
                break;
        }
    }
    
    /**
     * Agregar columnas personalizadas a la lista de temas
     * 
     * @param array $columns Columnas actuales
     * @return array Columnas modificadas
     */
    public function add_tema_columns($columns) {
        $new_columns = array();
        
        // Insertar columnas después de 'title'
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            
            if ($key === 'title') {
                $new_columns['curso'] = __('Curso', 'breogan-lms');
                $new_columns['lecciones'] = __('Lecciones', 'breogan-lms');
            }
        }
        
        return $new_columns;
    }
    
    /**
     * Mostrar contenido de columnas personalizadas en temas
     * 
     * @param string $column_name Nombre de la columna
     * @param int $post_id ID del post
     */
    public function display_tema_columns($column_name, $post_id) {
        switch ($column_name) {
            case 'curso':
                $curso_id = get_post_meta($post_id, '_blms_curso_relacionado', true);
                if ($curso_id) {
                    echo '<a href="' . get_edit_post_link($curso_id) . '">' . get_the_title($curso_id) . '</a>';
                } else {
                    echo '-';
                }
                break;
                
            case 'lecciones':
                $lecciones = get_posts(array(
                    'post_type' => 'blms_leccion',
                    'meta_key' => '_blms_tema_relacionado',
                    'meta_value' => $post_id,
                    'numberposts' => -1
                ));
                echo count($lecciones);
                break;
        }
    }
    
    /**
     * Agregar columnas personalizadas a la lista de lecciones
     * 
     * @param array $columns Columnas actuales
     * @return array Columnas modificadas
     */
    public function add_leccion_columns($columns) {
        $new_columns = array();
        
        // Insertar columnas después de 'title'
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            
            if ($key === 'title') {
                $new_columns['tema'] = __('Tema', 'breogan-lms');
                $new_columns['curso'] = __('Curso', 'breogan-lms');
            }
        }
        
        return $new_columns;
    }
    
    /**
     * Mostrar contenido de columnas personalizadas en lecciones
     * 
     * @param string $column_name Nombre de la columna
     * @param int $post_id ID del post
     */
    public function display_leccion_columns($column_name, $post_id) {
        switch ($column_name) {
            case 'tema':
                $tema_id = get_post_meta($post_id, '_blms_tema_relacionado', true);
                if ($tema_id) {
                    echo '<a href="' . get_edit_post_link($tema_id) . '">' . get_the_title($tema_id) . '</a>';
                } else {
                    echo '-';
                }
                break;
                
            case 'curso':
                $tema_id = get_post_meta($post_id, '_blms_tema_relacionado', true);
                $curso_id = $tema_id ? get_post_meta($tema_id, '_blms_curso_relacionado', true) : '';
                
                if ($curso_id) {
                    echo '<a href="' . get_edit_post_link($curso_id) . '">' . get_the_title($curso_id) . '</a>';
                } else {
                    echo '-';
                }
                break;
        }
    }
}