<?php
/**
 * Plugin Name: Y+S Wordpress
 * Plugin URI:
 * Description: Y+S Wordpress plugin for API development.
 * Version: 0.0.1
 * Author: Stretch & Quinn
 * Author URI: youngshand.com
 * License: GPL2
 */

if($_SERVER['REQUEST_URI'] === '/'){
    header("Location: http://" . $_SERVER['SERVER_NAME'] . '/wp-admin');
    exit;
}

/* Filter the single_template with our custom function */
add_filter('single_template', 'my_custom_template');

function my_custom_template($single) {

    global $wp_query, $post;

    /* Checks for single template by post type */
    if ($post->post_type == "POST TYPE NAME"){
        if(file_exists(PLUGIN_PATH . '/Custom_File.php'))
            return PLUGIN_PATH . '/Custom_File.php';
    }
    return $single;
}

function ys_wordpress_plugin_activate() {

    // Require parent plugin ACF
    if ( ! is_plugin_active('advanced-custom-fields-pro/acf.php' ) ) {
        // Stop activation redirect and show error
        wp_die('Sorry, but this plugin requires the Parent Plugin ACF to be installed and active. <br><a href="' . network_admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
    }

    // Require parent plugin rest-api
    if ( ! is_plugin_active('rest-api/plugin.php' ) ) {
        // Stop activation redirect and show error
        wp_die('Sorry, but this plugin requires the Parent Plugin rest-api to be installed and active. <br><a href="' . network_admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
    }

}
register_activation_hook( __FILE__, 'ys_wordpress_plugin_activate' );

/* Add default colors to acf picker */
add_action( 'acf/input/admin_enqueue_scripts', function() {
  wp_enqueue_script( 'acf-ys-colors', plugin_dir_url(__FILE__) . '/assets/js/ys-colors.js', 'acf-input', '1.0.0', true );
});

add_filter('acf/settings/google_api_key', function () {
    // return 'AIzaSyDQgx_wZSVn7HOS-tPlEWO1b8jI-XIJK1U';
    return 'AIzaSyCGAT9xR0CFmK-OBP7-5dqmPkyO3HQQToc';
});

/* Allow SVG uploads. */
add_filter( 'upload_mimes', function( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
} );

require_once(__DIR__.'/acf/site_settings.php');
require_once(__DIR__.'/acf/admin-settings.php');
require_once(__DIR__.'/acf/custom_fields/pages_custom_fields.php');
require_once(__DIR__.'/acf/custom_fields/signup_custom_fields.php');
require_once(__DIR__.'/acf/custom_fields/flexible_display.php');
require_once(__DIR__.'/acf/post_types.php');
require_once(__DIR__.'/routing/routes.php');
require_once(__DIR__.'/template_redirector.php');

?>
