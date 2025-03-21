<?php
/*
Plugin Name: Test Admin Post
Description: Prueba si admin-post.php funciona en WordPress.
Version: 1.0
Author: Diego Fernández Goás
*/

if (!defined('ABSPATH')) exit; // Evitar acceso directo

function test_admin_post() {
    error_log("✅ Admin Post ha sido ejecutado correctamente.");
    wp_die("✅ Admin Post funciona.");
}
add_action('admin_post_test_admin_post', 'test_admin_post');
add_action('admin_post_nopriv_test_admin_post', 'test_admin_post');
