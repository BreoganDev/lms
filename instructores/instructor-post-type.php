<?php
/**
 * Registrar Custom Post Type para Instructores
 */
function breogan_lms_register_instructor_post_type() {
    $labels = array(
        'name'               => __('Instructores', 'breogan-lms'),
        'singular_name'      => __('Instructor', 'breogan-lms'),
        'menu_name'          => __('Instructores', 'breogan-lms'),
        'add_new'            => __('Añadir Nuevo', 'breogan-lms'),
        'add_new_item'       => __('Añadir Nuevo Instructor', 'breogan-lms'),
        'edit_item'          => __('Editar Instructor', 'breogan-lms'),
        'new_item'           => __('Nuevo Instructor', 'breogan-lms'),
        'view_item'          => __('Ver Instructor', 'breogan-lms'),
        'search_items'       => __('Buscar Instructores', 'breogan-lms'),
        'not_found'          => __('No se encontraron instructores', 'breogan-lms'),
        'not_found_in_trash' => __('No hay instructores en la papelera', 'breogan-lms')
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => 'breogan-lms', // Colocar dentro del menú de Breogan LMS
        'query_var'           => true,
        'rewrite'             => array('slug' => 'instructor'),
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'menu_position'       => null,
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'        => true, // Para el editor Gutenberg
    );

    register_post_type('blms_instructor', $args);
}
add_action('init', 'breogan_lms_register_instructor_post_type');