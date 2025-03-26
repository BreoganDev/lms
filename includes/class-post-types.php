<?php
/**
 * Clase para gestionar los tipos de post personalizados
 */
class Breogan_LMS_Post_Types {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Registrar los tipos de post con prioridad alta (0)
        add_action('init', array($this, 'register_post_types'), 0);
    }
    
    /**
     * Registrar tipos de post personalizados
     */
    public function register_post_types() {
        // Usar prefijo 'blms_' para evitar conflictos con otros plugins o temas
        
        // Registrar Cursos
        register_post_type('blms_curso', array(
            'labels'        => array(
                'name'          => __('Cursos', 'breogan-lms'),
                'singular_name' => __('Curso', 'breogan-lms'),
                'menu_name'     => __('Cursos', 'breogan-lms'),
                'add_new'       => __('Añadir Curso', 'breogan-lms'),
                'add_new_item'  => __('Añadir Nuevo Curso', 'breogan-lms'),
                'edit_item'     => __('Editar Curso', 'breogan-lms'),
                'all_items'     => __('Todos los Cursos', 'breogan-lms'),
            ),
            'public'        => true,
            'menu_position' => null,
            'menu_icon'     => 'dashicons-welcome-learn-more',
            'supports'      => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'has_archive'   => true,
            'show_in_rest'  => true,
            'rewrite'       => array(
                'slug' => 'breogan-curso',
                'with_front' => false
            ),
            'show_in_menu'  => 'breogan-lms',
            'capability_type' => 'post',
            'map_meta_cap'  => true,
        ));
        
        // Registrar Temas
        register_post_type('blms_tema', array(
            'labels'        => array(
                'name'          => __('Temas', 'breogan-lms'),
                'singular_name' => __('Tema', 'breogan-lms'),
                'menu_name'     => __('Temas', 'breogan-lms'),
                'add_new'       => __('Añadir Tema', 'breogan-lms'),
                'add_new_item'  => __('Añadir Nuevo Tema', 'breogan-lms'),
                'edit_item'     => __('Editar Tema', 'breogan-lms'),
                'all_items'     => __('Todos los Temas', 'breogan-lms'),
            ),
            'public'        => true,
            'menu_position' => null,
            'menu_icon'     => 'dashicons-book-alt',
            'supports'      => array('title', 'editor', 'custom-fields'),
            'has_archive'   => false,
            'show_in_rest'  => true,
            'rewrite'       => array(
                'slug' => 'breogan-tema',
                'with_front' => false
            ),
            'show_in_menu'  => 'breogan-lms',
            'capability_type' => 'post',
            'map_meta_cap'  => true,
        ));
        
        // Registrar Lecciones
        register_post_type('blms_leccion', array(
            'labels'        => array(
                'name'          => __('Lecciones', 'breogan-lms'),
                'singular_name' => __('Lección', 'breogan-lms'),
                'menu_name'     => __('Lecciones', 'breogan-lms'),
                'add_new'       => __('Añadir Lección', 'breogan-lms'),
                'add_new_item'  => __('Añadir Nueva Lección', 'breogan-lms'),
                'edit_item'     => __('Editar Lección', 'breogan-lms'),
                'all_items'     => __('Todas las Lecciones', 'breogan-lms'),
            ),
            'public'        => true,
            'menu_position' => null,
            'menu_icon'     => 'dashicons-welcome-write-blog',
            'supports'      => array('title', 'editor', 'custom-fields'),
            'has_archive'   => false,
            'show_in_rest'  => true,
            'rewrite'       => array(
                'slug' => 'breogan-leccion',
                'with_front' => false
            ),
            'show_in_menu'  => 'breogan-lms',
            'capability_type' => 'post',
            'map_meta_cap'  => true,
        ));
    }
}