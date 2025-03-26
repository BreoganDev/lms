<?php
/**
 * Funciones para manejar el checkout de PayPal
 * 
 * Este archivo contiene funciones para manejar el retorno de PayPal
 * y verificar el estado de pago.
 */

if (!defined('ABSPATH')) {
    exit; // Salida si se accede directamente
}

/**
 * Verificar el estado de pago de PayPal al volver al sitio
 */
function breogan_check_paypal_return() {
    // Si no estamos en una página de curso, salir
    if (!is_singular('cursos')) {
        return;
    }
    
    // Verificar si hay parámetros de PayPal
    if (isset($_GET['paypal_action'])) {
        $curso_id = get_the_ID();
        $action = sanitize_text_field($_GET['paypal_action']);
        
        // Caso 1: Pago cancelado
        if ($action === 'cancel') {
            // Mostrar mensaje de cancelación
            add_action('breogan_before_content', function() {
                echo '<div class="mensaje-error">';
                echo '<p>El proceso de pago ha sido cancelado. Puedes intentarlo de nuevo cuando desees.</p>';
                echo '</div>';
            });
            return;
        }
        
        // Caso 2: Pago exitoso
        if ($action === 'success') {
            // Verificar si tenemos un token de PayPal
            if (isset($_GET['token'])) {
                $token = sanitize_text_field($_GET['token']);
                
                // Si el usuario no está logueado, guardamos en sesión y redirigimos
                if (!is_user_logged_in()) {
                    if (!session_id() && !headers_sent()) {
                        session_start();
                    }
                    $_SESSION['breogan_pending_paypal'] = array(
                        'token' => $token,
                        'curso_id' => $curso_id
                    );
                    wp_redirect(wp_login_url(get_permalink($curso_id)));
                    exit;
                }
                
                // Verificar y capturar el pago
                $paypal = new Breogan_LMS_PayPal();
                $success = $paypal->verify_payment_return($token);
                
                if ($success) {
                    // Dar acceso al curso
                    update_user_meta(get_current_user_id(), 'breogan_curso_' . $curso_id, 'comprado');
                    
                    // Mostrar mensaje de éxito
                    add_action('breogan_before_content', function() {
                        echo '<div class="mensaje-exito">';
                        echo '<p>¡Pago completado con éxito! Ya tienes acceso al curso.</p>';
                        echo '</div>';
                    });
                    
                    // Redirigir para limpiar URL
                    wp_redirect(get_permalink($curso_id));
                    exit;
                } else {
                    // Mostrar mensaje de error
                    add_action('breogan_before_content', function() {
                        echo '<div class="mensaje-error">';
                        echo '<p>No se pudo verificar el pago. Por favor, contacta al administrador.</p>';
                        echo '</div>';
                    });
                }
            }
        }
    }
    
    // Verificar otras variables específicas de PayPal (para método estándar)
    if (isset($_GET['tx']) && isset($_GET['st']) && $_GET['st'] === 'Completed') {
        $transaction_id = sanitize_text_field($_GET['tx']);
        $curso_id = get_the_ID();
        
        // Si el usuario no está logueado, guardamos en sesión y redirigimos
        if (!is_user_logged_in()) {
            if (!session_id() && !headers_sent()) {
                session_start();
            }
            $_SESSION['breogan_pending_paypal_tx'] = array(
                'tx' => $transaction_id,
                'curso_id' => $curso_id
            );
            wp_redirect(wp_login_url(get_permalink($curso_id)));
            exit;
        }
        
        // Dar acceso al curso
        update_user_meta(get_current_user_id(), 'breogan_curso_' . $curso_id, 'comprado');
        
        // Mostrar mensaje de éxito
        add_action('breogan_before_content', function() {
            echo '<div class="mensaje-exito">';
            echo '<p>¡Pago completado con éxito! Ya tienes acceso al curso.</p>';
            echo '</div>';
        });
        
        // Redirigir para limpiar URL
        wp_redirect(get_permalink($curso_id));
        exit;
    }
    
    // Verificar si hay una transacción pendiente después del login
    if (is_user_logged_in() && isset($_SESSION['breogan_pending_paypal_tx'])) {
        $pending = $_SESSION['breogan_pending_paypal_tx'];
        $curso_id = $pending['curso_id'];
        
        // Dar acceso al curso
        update_user_meta(get_current_user_id(), 'breogan_curso_' . $curso_id, 'comprado');
        
        // Limpiar sesión
        unset($_SESSION['breogan_pending_paypal_tx']);
        
        // Mostrar mensaje de éxito
        add_action('breogan_before_content', function() {
            echo '<div class="mensaje-exito">';
            echo '<p>¡Pago completado con éxito! Ya tienes acceso al curso.</p>';
            echo '</div>';
        });
    }
}
add_action('template_redirect', 'breogan_check_paypal_return');

/**
 * Mostrar mensaje antes del contenido
 * Esta acción se usa en las funciones anteriores
 */
function breogan_display_before_content_message() {
    do_action('breogan_before_content');
}