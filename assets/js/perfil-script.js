/**
 * JavaScript para gestionar interacciones en la página de perfil
 * 
 * - Maneja marcado de lecciones como completadas
 * - Actualiza barras de progreso
 * - Gestiona cambios visuales y transiciones
 */

(function($) {
    'use strict';
    
    // Variables globales
    const ANIMATION_DURATION = 500; // duración de animaciones en ms
    
    /**
     * Inicializar scripts cuando el DOM esté cargado
     */
    $(document).ready(function() {
        console.log('Breogan LMS: Perfil scripts inicializados');
        
        // Inicializar funcionalidades
        initProgressBarAnimations();
        setupCompleteLessonButtons();
        setupCourseFilters();
    });
    
    /**
     * Animar las barras de progreso al cargar la página
     */
    function initProgressBarAnimations() {
        // Seleccionar todas las barras de progreso
        const progressBars = $('.progreso-barra > div');
        
        // Asegurar que las barras empiezan en cero
        progressBars.css('width', '0%');
        
        // Animar barras después de un pequeño retraso para que se vea la transición
        setTimeout(function() {
            progressBars.each(function() {
                const targetWidth = $(this).data('progress') || $(this).parent().data('progress') || $(this).attr('style').match(/width: (\d+)%/)[1];
                
                $(this).animate({
                    width: targetWidth + '%'
                }, ANIMATION_DURATION);
            });
        }, 300);
    }
    
    /**
     * Configurar botones para marcar lecciones como completadas
     */
    function setupCompleteLessonButtons() {
        // Seleccionar botones para marcar lecciones
        const completeButtons = $('.marcar-completada-btn');
        
        completeButtons.on('click', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const leccionId = $button.data('leccion');
            const nonce = breoganLMS.nonce;
            
            // Deshabilitar botón durante la petición
            $button.prop('disabled', true).addClass('procesando');
            $button.html(breoganLMS.text_saving || 'Guardando...');
            
            // Enviar petición AJAX
            $.ajax({
                url: breoganLMS.ajaxurl,
                type: 'POST',
                data: {
                    action: 'breogan_marcar_leccion_completada',
                    leccion_id: leccionId,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Actualizar interfaz
                        $button.replaceWith(
                            '<div class="leccion-completada-badge">' +
                            '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
                            '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>' +
                            '<polyline points="22 4 12 14.01 9 11.01"></polyline>' +
                            '</svg>' +
                            '<span>' + (breoganLMS.text_lesson_completed || 'Lección completada') + '</span>' +
                            '</div>'
                        );
                        
                        // Buscar y actualizar la barra de progreso del curso relacionado
                        updateCourseProgress();
                        
                        // Opcional: Mostrar mensaje de éxito
                        showNotification('Lección marcada como completada', 'success');
                    } else {
                        // Restaurar botón
                        $button.prop('disabled', false).removeClass('procesando');
                        $button.html(breoganLMS.text_mark_complete || 'Marcar como completada');
                        
                        // Mostrar error
                        showNotification(response.data?.message || 'Error al marcar la lección', 'error');
                    }
                },
                error: function() {
                    // Restaurar botón
                    $button.prop('disabled', false).removeClass('procesando');
                    $button.html(breoganLMS.text_mark_complete || 'Marcar como completada');
                    
                    // Mostrar error
                    showNotification('Error de conexión. Inténtalo de nuevo.', 'error');
                }
            });
        });
    }
    
    /**
     * Actualizar el progreso del curso después de completar una lección
     */
    function updateCourseProgress() {
        // Opcional: Realizar petición AJAX para obtener el progreso actualizado
        // O simplemente aumentar el contador local y actualizar visualmente
        
        // Por ahora, simplemente incrementamos el contador de lecciones completadas
        // y actualizamos el porcentaje y la barra de progreso
        
        const $courseCard = $('.curso-actual');
        if ($courseCard.length) {
            const $progressInfo = $courseCard.find('.lecciones-info');
            const $progressBar = $courseCard.find('.progreso-barra > div');
            
            // Extraer información actual
            const infoText = $progressInfo.text();
            const matches = infoText.match(/(\d+) de (\d+)/);
            
            if (matches && matches.length === 3) {
                let completed = parseInt(matches[1], 10);
                const total = parseInt(matches[2], 10);
                
                // Incrementar completadas
                completed++;
                
                // Calcular nuevo porcentaje
                const newPercentage = Math.round((completed / total) * 100);
                
                // Actualizar textos
                $progressInfo.text(infoText.replace(/\d+ de \d+/, `${completed} de ${total}`));
                $courseCard.find('.progreso-porcentaje').text(`${newPercentage}%`);
                
                // Actualizar barra
                $progressBar.animate({
                    width: newPercentage + '%'
                }, ANIMATION_DURATION);
                
                // Actualizar estado del curso si es necesario
                if (newPercentage >= 100) {
                    const $statusBadge = $courseCard.find('.estado-indicador');
                    $statusBadge.removeClass('estado-activo').addClass('estado-completado');
                    $statusBadge.text('Completado');
                }
            }
        }
    }
    
    /**
     * Configurar filtros para los cursos en la página de perfil
     */
    function setupCourseFilters() {
        // Si hay filtros de cursos, configurar eventos
        const $filters = $('.curso-filtros .filtro-btn');
        
        if ($filters.length) {
            $filters.on('click', function() {
                const filter = $(this).data('filter');
                
                // Actualizar botón activo
                $filters.removeClass('active');
                $(this).addClass('active');
                
                // Filtrar cursos
                if (filter === 'todos') {
                    $('.lista-cursos li').slideDown();
                } else {
                    $('.lista-cursos li').each(function() {
                        const $item = $(this);
                        const $statusBadge = $item.find('.estado-indicador');
                        
                        if ($statusBadge.hasClass('estado-' + filter)) {
                            $item.slideDown();
                        } else {
                            $item.slideUp();
                        }
                    });
                }
            });
        }
    }
    
    /**
     * Mostrar notificación al usuario
     * 
     * @param {string} message Mensaje a mostrar
     * @param {string} type Tipo de notificación: 'success', 'error', 'info'
     */
    function showNotification(message, type = 'info') {
        // Eliminar notificaciones existentes
        $('.breogan-notification').remove();
        
        // Crear nueva notificación
        const $notification = $('<div class="breogan-notification ' + type + '">' + message + '</div>');
        
        // Añadir a la página
        $('body').append($notification);
        
        // Mostrar con animación
        $notification.fadeIn().css({bottom: '20px'});
        
        // Eliminar después de unos segundos
        setTimeout(function() {
            $notification.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }
    
})(jQuery);