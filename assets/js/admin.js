/**
 * Scripts de administración para Breogan LMS
 */
jQuery(document).ready(function($) {
    // Script para el checkbox de curso gratuito
    $('#blms_curso_gratuito').on('change', function() {
        if ($(this).is(':checked')) {
            $('.precio-container').slideUp(300);
        } else {
            $('.precio-container').slideDown(300);
        }
    });
    
    // Inicializar estado del checkbox
    if ($('#blms_curso_gratuito').is(':checked')) {
        $('.precio-container').hide();
    }
});