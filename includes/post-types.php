<?php
// Evitar acceso directo
if (!defined('ABSPATH')) exit;

// Registrar Custom Post Types
function breogan_lms_register_post_types() {
    // ✅ Cursos dentro de Breogan LMS
    register_post_type('cursos', array(
        'labels'        => array(
            'name'          => __('Cursos', 'breogan-lms'),
            'singular_name' => __('Curso', 'breogan-lms'),
            'menu_name'     => __('Cursos', 'breogan-lms'),
            'add_new'       => __('Añadir Curso', 'breogan-lms'),
        ),
        'public'        => true,
        'menu_position' => null,
        'menu_icon'     => 'dashicons-welcome-learn-more',
        'supports'      => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'has_archive'   => true,
        'show_in_rest'  => true,
        'rewrite'       => array('slug' => 'cursos'),
        'show_in_menu'  => 'breogan-lms', // ✅ Asegurar que esté dentro del menú Breogan LMS
    ));

    // ✅ Temas dentro de Breogan LMS
    register_post_type('temas', array(
        'labels'        => array(
            'name'          => __('Temas', 'breogan-lms'),
            'singular_name' => __('Tema', 'breogan-lms'),
            'menu_name'     => __('Temas', 'breogan-lms'),
            'add_new'       => __('Añadir Tema', 'breogan-lms'),
        ),
        'public'        => true,
        'menu_position' => null,
        'menu_icon'     => 'dashicons-book-alt',
        'supports'      => array('title', 'editor', 'custom-fields'),
        'has_archive'   => false,
        'show_in_rest'  => true,
        'show_in_menu'  => 'breogan-lms', // ✅ Asegurar que esté dentro del menú Breogan LMS
    ));

    // ✅ Lecciones dentro de Breogan LMS
    register_post_type('lecciones', array(
        'labels'        => array(
            'name'          => __('Lecciones', 'breogan-lms'),
            'singular_name' => __('Lección', 'breogan-lms'),
            'menu_name'     => __('Lecciones', 'breogan-lms'),
            'add_new'       => __('Añadir Lección', 'breogan-lms'),
        ),
        'public'        => true,
        'menu_position' => null,
        'menu_icon'     => 'dashicons-welcome-write-blog',
        'supports'      => array('title', 'editor', 'custom-fields'),
        'has_archive'   => false,
        'show_in_rest'  => true,
        'show_in_menu'  => 'breogan-lms', // ✅ Asegurar que esté dentro del menú Breogan LMS
    ));
}
add_action('init', 'breogan_lms_register_post_types');
