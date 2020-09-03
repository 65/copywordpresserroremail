<?php
/**
 * Plugin Name: Copy Wordpress Error Email
 * Plugin URI: https://www.medic52.com/
 * Description: A plugin that allows you to set the email address to cc on error emails if its different to the admin email address.
 * Version: 1.0.0
 * Author: Medic52
 * Author URI: https://www.medic52.com/
 * Text Domain: copy_wordpress_error_email
 *
 */

defined( 'ABSPATH' ) || exit;

// Define CWEE_PLUGIN_FILE.
if ( ! defined( 'CWEE_PLUGIN_FILE' ) ) {
    define( 'CWEE_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'CWEE_PLUGIN_URL' ) ) {
    define( 'CWEE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// Include the main CWEE class.
if ( ! class_exists( 'Copy_WP_Error_Email' ) ) {
    include_once dirname( __FILE__ ) . '/includes/class-copy-wordpress-error-email.php';
}

/**
 * Returns the main instance of CWEE.
 *
 * @since  1.0
 * @return Copy_WP_Error_Email
 */
function CWEE() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
    return Copy_WP_Error_Email::instance();
}

// Global for backwards compatibility.
$GLOBALS['copy_wp_error_email'] = CWEE();
