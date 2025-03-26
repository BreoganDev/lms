/**
 * JavaScript for managing course progress updates via AJAX in Breogan LMS
 * 
 * Handles lecture completion status, progress bars, and progress summaries
 * Compatible with both the legacy and new post type prefixes
 * 
 * @package Breogan LMS
 */

(function($) {
    'use strict';
    
    // Main object for progress management
    var BreoganProgress = {
        /**
         * Initialize events and functionality
         */
        init: function() {
            // Mark lecture as completed
            $(document).on('click', '#marcar-completada', this.markLessonComplete);
            
            // Progress bar animation on page load
            this.animateProgressBars();
            
            // Update progress on course pages
            if ($('.curso-content-wrapper').length) {
                this.updateCourseProgress();
            }
            
            // Initialize tooltips if available
            if (typeof tippy !== 'undefined') {
                tippy('[data-tippy-content]');
            }
        },
        
        /**
         * Mark a lesson as completed
         */
        markLessonComplete: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var lessonId = $button.data('leccion');
            
            // Change button state
            $button.prop('disabled', true)
                .addClass('procesando')
                .text(breoganLMS.text_saving || 'Guardando...');
            
            // Send AJAX request
            $.ajax({
                url: breoganLMS.ajaxurl,
                type: 'POST',
                data: {
                    action: 'blms_mark_lesson_complete',
                    leccion_id: lessonId,
                    nonce: breoganLMS.nonce
                },
                success: function(response) {
                    console.log("Response from marking lesson complete:", response);
                    
                    if (response.success) {
                        // Replace button with success message
                        $button.replaceWith(
                            '<div class="breogan-leccion-estado completada">' +
                            '<span class="dashicons dashicons-yes-alt"></span> ' +
                            (breoganLMS.text_lesson_completed || "Lección completada") +
                            '</div>'
                        );
                        
                        // Add class to lesson in the list
                        $('.breogan-lista-lecciones .leccion-actual').addClass('leccion-completada');
                        
                        // Optionally reload to update all states
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        // Restore button on error
                        $button.prop('disabled', false)
                            .removeClass('procesando')
                            .text(breoganLMS.text_mark_complete || "Marcar como completada");
                        
                        alert(response.data && response.data.message 
                            ? response.data.message 
                            : "Error al marcar la lección como completada");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX request error:", error);
                    
                    // Restore button
                    $button.prop('disabled', false)
                        .removeClass('procesando')
                        .text(breoganLMS.text_mark_complete || "Marcar como completada");
                    
                    alert("Error en la comunicación con el servidor. Por favor, intenta de nuevo más tarde.");
                }
            });
        },
        
        /**
         * Update course progress information
         */
        updateCourseProgress: function() {
            // Get course ID from the URL or meta data
            var courseId = this.getCurrentCourseId();
            if (!courseId) return;
            
            // Only run if user is logged in
            if (!breoganLMS.user_logged_in) return;
            
            $.ajax({
                url: breoganLMS.ajaxurl,
                type: 'POST',
                data: {
                    action: 'breogan_get_course_progress',
                    course_id: courseId,
                    nonce: breoganLMS.nonce
                },
                success: function(response) {
                    if (response.success && response.data) {
                        BreoganProgress.updateProgressUI(response.data);
                    }
                }
            });
        },
        
        /**
         * Update UI elements with progress data
         */
        updateProgressUI: function(data) {
            // Update progress percentage
            $('.progreso-porcentaje').text(data.percentage + '%');
            
            // Update progress bar
            $('.progreso-barra > div').css('width', data.percentage + '%');
            
            // Update completed lessons count
            $('.lecciones-info').text(
                data.completed_lessons + ' de ' + data.total_lessons + ' lecciones completadas'
            );
            
            // Update status indicator
            $('.estado-indicador')
                .removeClass('estado-pendiente estado-activo estado-completado')
                .addClass(data.status_class)
                .text(data.status_text);
        },
        
        /**
         * Animate all progress bars on page load
         */
        animateProgressBars: function() {
            $('.progreso-barra > div').each(function() {
                var $bar = $(this);
                var width = $bar.data('progress') || $bar.parent().data('progress');
                if (!width && $bar.attr('style')) {
                    // Extract width from inline style if exists
                    var match = $bar.attr('style').match(/width\s*:\s*(\d+)%/);
                    if (match) width = match[1];
                }
                
                if (width) {
                    // Animate from 0 to the target width
                    $bar.css('width', '0%').animate({
                        width: width + '%'
                    }, 1000);
                }
            });
        },
        
        /**
         * Get current course ID from URL or meta
         */
        getCurrentCourseId: function() {
            // Try to get course ID from a data attribute
            var courseId = $('.breogan-curso').data('curso-id');
            if (courseId) return courseId;
            
            // Try from a hidden input
            courseId = $('input[name="curso_id"]').val();
            if (courseId) return courseId;
            
            // Try from URL
            var urlParams = new URLSearchParams(window.location.search);
            courseId = urlParams.get('curso_id');
            if (courseId) return courseId;
            
            // Try from page body class (WordPress adds post ID as a class)
            var bodyClass = $('body').attr('class');
            var match = bodyClass && bodyClass.match(/postid-(\d+)/);
            if (match) return match[1];
            
            return null;
        },
        
        /**
         * Check if all lessons in a theme are completed
         */
        checkThemeCompletion: function(themeId) {
            if (!themeId) return;
            
            $.ajax({
                url: breoganLMS.ajaxurl,
                type: 'POST',
                data: {
                    action: 'breogan_check_theme_completion',
                    theme_id: themeId,
                    nonce: breoganLMS.nonce
                },
                success: function(response) {
                    if (response.success && response.data.is_complete) {
                        // Visual indication that theme is complete
                        $('.tema-item[data-tema-id="' + themeId + '"]')
                            .addClass('tema-completado')
                            .find('.tema-status')
                            .html('<span class="dashicons dashicons-yes-alt"></span> Completado');
                    }
                }
            });
        }
    };
    
    // Initialize on document ready
    $(document).ready(function() {
        BreoganProgress.init();
    });
    
})(jQuery);