<?php
// Evitar acceso directo
if (!defined('ABSPATH')) exit;

// Agregar metabox en "Temas" para seleccionar el curso
function breogan_lms_add_curso_metabox() {
    add_meta_box(
        'breogan_lms_curso_metabox',
        __('Curso al que pertenece', 'breogan-lms'),
        'breogan_lms_curso_metabox_callback',
        'temas',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'breogan_lms_add_curso_metabox');

function breogan_lms_curso_metabox_callback($post) {
    $curso_id = get_post_meta($post->ID, '_curso_relacionado', true);
    $cursos = get_posts(array('post_type' => 'cursos', 'numberposts' => -1));

    echo '<select name="breogan_lms_curso_relacionado">';
    echo '<option value="">Seleccionar Curso</option>';

    foreach ($cursos as $curso) {
        echo '<option value="' . $curso->ID . '" ' . selected($curso_id, $curso->ID, false) . '>' . $curso->post_title . '</option>';
    }

    echo '</select>';
}

// Guardar relación de Curso en el Tema
function breogan_lms_save_curso_relacionado($post_id) {
    if (isset($_POST['breogan_lms_curso_relacionado'])) {
        update_post_meta($post_id, '_curso_relacionado', sanitize_text_field($_POST['breogan_lms_curso_relacionado']));
    }
}
add_action('save_post', 'breogan_lms_save_curso_relacionado');

// Agregar metabox en "Lecciones" para seleccionar el Tema
function breogan_lms_add_tema_metabox() {
    add_meta_box(
        'breogan_lms_tema_metabox',
        __('Tema al que pertenece', 'breogan-lms'),
        'breogan_lms_tema_metabox_callback',
        'lecciones',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'breogan_lms_add_tema_metabox');

function breogan_lms_tema_metabox_callback($post) {
    $tema_id = get_post_meta($post->ID, '_tema_relacionado', true);
    $temas = get_posts(array('post_type' => 'temas', 'numberposts' => -1));

    echo '<select name="breogan_lms_tema_relacionado">';
    echo '<option value="">Seleccionar Tema</option>';

    foreach ($temas as $tema) {
        echo '<option value="' . $tema->ID . '" ' . selected($tema_id, $tema->ID, false) . '>' . $tema->post_title . '</option>';
    }

    echo '</select>';
}

// Guardar relación de Tema en la Lección
function breogan_lms_save_tema_relacionado($post_id) {
    if (isset($_POST['breogan_lms_tema_relacionado'])) {
        update_post_meta($post_id, '_tema_relacionado', sanitize_text_field($_POST['breogan_lms_tema_relacionado']));
    }
}
add_action('save_post', 'breogan_lms_save_tema_relacionado');

// Precios

function breogan_lms_agregar_metabox_precio() {
    add_meta_box(
        'breogan_precio_curso',
        'Precio del Curso (€)',
        'breogan_lms_precio_callback',
        'cursos',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'breogan_lms_agregar_metabox_precio');

function breogan_lms_precio_callback($post) {
    $precio = get_post_meta($post->ID, '_breogan_precio_curso', true);
    ?>
    <label for="breogan_precio_curso">Precio (€):</label>
    <input type="number" id="breogan_precio_curso" name="breogan_precio_curso" value="<?php echo esc_attr($precio); ?>" step="0.01" min="0">
    <?php
}

function breogan_lms_guardar_precio($post_id) {
    if (array_key_exists('breogan_precio_curso', $_POST)) {
        update_post_meta($post_id, '_breogan_precio_curso', sanitize_text_field($_POST['breogan_precio_curso']));
    }
}
add_action('save_post', 'breogan_lms_guardar_precio');
