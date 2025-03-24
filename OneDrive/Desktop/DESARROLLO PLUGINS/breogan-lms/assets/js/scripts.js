/**
 * Scripts principales para Breogan LMS
 * 
 * Maneja todas las interacciones JavaScript del plugin, incluyendo pagos
 * y funcionalidades de cursos gratuitos.
 */

jQuery(document).ready(function($) {
    console.log("Script de Breogan LMS cargado correctamente");
    
    // ====================================
    // PROCESAMIENTO DE PAGOS
    // ====================================

    // Formulario de Stripe
    $('#breogan-pago-stripe').on('submit', function(e) {
        e.preventDefault();
        procesarPago($(this));
    });

    // Formulario de PayPal
    $('#breogan-pago-paypal').on('submit', function(e) {
        e.preventDefault();
        procesarPago($(this));
    });
    
    // Procesar acceso a curso gratuito
    $('#breogan-acceso-gratuito').on('submit', function(e) {
        e.preventDefault();
        procesarCursoGratuito($(this));
    });

    /**
     * Función para procesar formularios de pago
     */
    function procesarPago(form) {
        // Recoger datos del formulario
        let formData = new FormData(form[0]);
        
        // Deshabilitar botón para evitar doble clic
        form.find('button').prop('disabled', true).addClass('procesando');
        
        console.log("Procesando pago...");
        
        // Enviar solicitud AJAX
        $.ajax({
            url: breoganLMS.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log("Respuesta de pago:", response);
                
                // Re-habilitar botón
                form.find('button').prop('disabled', false).removeClass('procesando');
                
                if (response.success && response.data && response.data.redirect_url) {
                    console.log("Redirigiendo a pasarela de pago:", response.data.redirect_url);
                    // Redirigir a la pasarela de pago
                    window.location.href = response.data.redirect_url;
                } else {
                    // Mostrar error
                    const errorMsg = response.data && response.data.message 
                        ? response.data.message 
                        : "Error desconocido en el procesamiento del pago";
                    
                    mostrarMensaje('error', errorMsg);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX:", error);
                
                // Re-habilitar botón
                form.find('button').prop('disabled', false).removeClass('procesando');
                
                // Mostrar mensaje de error
                mostrarMensaje('error', "Error en la comunicación con el servidor. Por favor, intenta de nuevo más tarde.");
            }
        });
    }

    /**
     * Función para procesar acceso a cursos gratuitos
     */
    function procesarCursoGratuito(form) {
        // Recoger datos del formulario
        let formData = new FormData(form[0]);
        
        // Deshabilitar botón para evitar doble clic
        form.find('button').prop('disabled', true).addClass('procesando')
            .text(breoganLMS.text_processing || "Procesando...");
        
        console.log("Procesando acceso a curso gratuito...");
        
        // Enviar solicitud AJAX
        $.ajax({
            url: breoganLMS.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log("Respuesta de acceso gratuito:", response);
                
                if (response.success && response.data && response.data.redirect_url) {
                    console.log("Redirigiendo a:", response.data.redirect_url);
                    // Redirigir a la página indicada
                    window.location.href = response.data.redirect_url;
                } else {
                    // Re-habilitar botón
                    form.find('button').prop('disabled', false).removeClass('procesando')
                        .text(breoganLMS.text_free_access || "Acceder al Curso Gratuito");
                    
                    // Mostrar error
                    const errorMsg = response.data && response.data.message 
                        ? response.data.message 
                        : "Error desconocido al procesar el acceso";
                    
                    mostrarMensaje('error', errorMsg);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX:", error);
                
                // Re-habilitar botón
                form.find('button').prop('disabled', false).removeClass('procesando')
                    .text(breoganLMS.text_free_access || "Acceder al Curso Gratuito");
                
                // Mostrar mensaje de error
                mostrarMensaje('error', "Error en la comunicación con el servidor. Por favor, intenta de nuevo más tarde.");
            }
        });
    }
    
    // ====================================
    // LECCIONES
    // ====================================
    
    // Marcar lección como completada
    // En el archivo scripts.js
$('#marcar-completada').on('click', function() {
    const button = $(this);
    const leccion_id = button.data('leccion');
    const nonce = button.data('nonce'); // Obtiene el nonce del atributo data
    
    // Deshabilitar botón para evitar doble clic
    button.prop('disabled', true).addClass('procesando')
        .text(breoganLMS.text_saving || "Guardando...");
    
    // Enviar solicitud AJAX
    $.ajax({
        url: breoganLMS.ajaxurl,
        type: 'POST',
        data: {
            action: 'blms_mark_lesson_complete',
            leccion_id: leccion_id,
            nonce: nonce // Usa el nonce obtenido del botón
        },
        success: function(response) {
            console.log("Respuesta de lección completada:", response);
            
            if (response.success) {
                // Reemplazar botón con mensaje de éxito
                button.replaceWith(
                    '<div class="breogan-leccion-estado completada">' +
                    '<span class="dashicons dashicons-yes-alt"></span> ' +
                    (breoganLMS.text_lesson_completed || "Lección completada") +
                    '</div>'
                );
                
                // Recargar para actualizar todo el estado (opcional)
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                // Mostrar error y re-habilitar botón
                button.prop('disabled', false).removeClass('procesando')
                    .text(breoganLMS.text_mark_complete || "Marcar como completada");
                
                alert(response.data && response.data.message 
                    ? response.data.message 
                    : "Error al marcar la lección como completada");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error en la solicitud AJAX:", error);
            
            // Re-habilitar botón
            button.prop('disabled', false).removeClass('procesando')
                .text(breoganLMS.text_mark_complete || "Marcar como completada");
            
            alert("Error en la comunicación con el servidor. Por favor, intenta de nuevo más tarde.");
        }
    });
});
    
    // ====================================
    // UTILIDADES
    // ====================================
    
    /**
     * Función para mostrar mensajes al usuario
     */
    function mostrarMensaje(tipo, mensaje) {
        // Eliminar mensajes anteriores
        $('.breogan-mensaje-temporal').remove();
        
        // Crear elemento de mensaje
        const claseCSS = tipo === 'error' ? 'mensaje-error' : 'mensaje-exito';
        const mensajeHTML = $('<div class="breogan-mensaje-temporal ' + claseCSS + '">' + mensaje + '</div>');
        
        // Mostrar mensaje
        $('main.breogan-contenedor').prepend(mensajeHTML);
        
        // Efecto de aparición
        mensajeHTML.hide().fadeIn(300);
        
        // Eliminar después de un tiempo
        setTimeout(function() {
            mensajeHTML.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    // Animaciones y efectos visuales
    $('.breogan-lista-lecciones a').hover(
        function() { $(this).addClass('hover'); },
        function() { $(this).removeClass('hover'); }
    );
    
    // ====================================
    // ADMIN: METABOXES
    // ====================================
    
    // Checkbox para curso gratuito
    $('#blms_curso_gratuito').on('change', function() {
        if ($(this).is(':checked')) {
            $('.precio-container').slideUp(300);
        } else {
            $('.precio-container').slideDown(300);
        }
    });
});