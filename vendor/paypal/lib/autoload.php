<?php
// Autoload personalizado para PayPal SDK
function paypal_sdk_autoloader($class) {
    $base_dir = __DIR__ . '/PayPal/';
    
    // Eliminar el prefijo PayPal\ si existe
    $class = str_replace('PayPal\\', '', $class);
    
    // Convertir namespace a ruta de archivo
    $file = $base_dir . str_replace('\\', '/', $class) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
}

// Registrar el autoloader
spl_autoload_register('paypal_sdk_autoloader');