<?php
function breogan_lms_admin_menu() {
    add_menu_page(
        'Breogan LMS',           // Título de la página
        'Breogan LMS',           // Nombre del menú
        'manage_options',        // Permiso requerido
        'breogan-lms',           // Slug
        'breogan_lms_dashboard_page', // Función que carga el contenido
        'dashicons-welcome-learn-more', // Icono
        25 // Posición en el menú
    );
}
add_action('admin_menu', 'breogan_lms_admin_menu');

// ✅ Crear una función para el Dashboard de Breogan LMS
function breogan_lms_dashboard_page() {
    ?>
    <div class="wrap">
        <h1>Bienvenido a Breogan LMS</h1>
        <p>Desde aquí puedes gestionar tus cursos, temas y lecciones.</p>
        <ul>
            <li><a href="edit.php?post_type=cursos">📚 Ver Cursos</a></li>
            <li><a href="edit.php?post_type=temas">📖 Ver Temas</a></li>
            <li><a href="edit.php?post_type=lecciones">🎓 Ver Lecciones</a></li>
        </ul>
    </div>
    <?php
}
