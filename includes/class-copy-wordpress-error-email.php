<?php
/**
 * Copy WP Error Email setup
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main Copy_WP_Error_Email Class.
 *
 * @class Copy_WP_Error_Email
 */
final class Copy_WP_Error_Email {
    /**
     * Copy_WP_Error_Email version.
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * The single instance of the class.
     *
     * @var Copy_WP_Error_Email
     * @since 1.0
     */
    protected static $_instance = null;

    /**
     * Main Copy_WP_Error_Email Instance.
     *
     * Ensures only one instance of Copy_WP_Error_Email is loaded or can be loaded.
     *
     * @since 1.0
     * @static
     * @return Copy_WP_Error_Email - Main instance.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Copy_WP_Error_Email Constructor.
     */
    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    /**
     * When WP has loaded all plugins, trigger the `cwee_plugins_loaded` hook.
     *
     * This ensures `cwee_plugins_loaded` is called only after all other plugins
     * are loaded, to avoid issues caused by plugin directory naming changing
     * the load order.
     *
     * @since 1.0.0
     */
    public function on_plugins_loaded() {
        do_action( 'cwee_plugins_loaded' );
    }

    /**
     * Hook into actions and filters.
     *
     * @since 1.0
     */
    private function init_hooks() {
        register_deactivation_hook(CWEE_PLUGIN_BASENAME, array($this, 'plugin_deactivation'));

        add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), -1 );


        // add new email address to recovery email
        add_filter( 'recovery_mode_email', array( $this, 'cwee_recovery_mode_email'));
    }

    /**
     * Define CWEE Constants.
     */
    private function define_constants() {
        $upload_dir = wp_upload_dir( null, false );

        $this->define( 'CWEE_ABSPATH', dirname( CWEE_PLUGIN_FILE ) . '/' );
        $this->define( 'CWEE_PLUGIN_BASENAME', plugin_basename( CWEE_PLUGIN_FILE ) );
        $this->define( 'CWEE_VERSION', $this->version );
        $this->define( 'CWEE_PLUGIN_NAME', 'Copy Wordpress Error Email' );
        $this->define( 'CWEE_TEXT_DOMAIN', 'copy_wordpress_error_email' );

    }

    /**
     * Define constant if not already set.
     *
     * @param string      $name  Constant name.
     * @param string|bool $value Constant value.
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    /**
     * Returns true if the request is a non-legacy REST API request.
     *
     * Legacy REST requests should still run some extra code for backwards compatibility.
     *
     * @todo: replace this function once core WP function is available: https://core.trac.wordpress.org/ticket/42061.
     *
     * @return bool
     */
    public function is_rest_api_request() {
        if ( empty( $_SERVER['REQUEST_URI'] ) ) {
            return false;
        }

        $rest_prefix         = trailingslashit( rest_get_url_prefix() );
        $is_rest_api_request = ( false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) ); // phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

        return apply_filters( 'cwee_is_rest_api_request', $is_rest_api_request );
    }

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     * @return bool
     */
    private function is_request( $type ) {
        switch ( $type ) {
            case 'admin':
                return is_admin();
            case 'ajax':
                return defined( 'DOING_AJAX' );
            case 'cron':
                return defined( 'DOING_CRON' );
            case 'frontend':
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! $this->is_rest_api_request();
        }
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes() {

        if ( $this->is_request( 'admin' ) ) {
            include_once CWEE_ABSPATH . 'includes/class-cwee-admin.php';
        }
    }

    /**
     * Get the plugin url.
     *
     * @return string
     */
    public function plugin_url() {
        return untrailingslashit( plugins_url( '/', CWEE_PLUGIN_FILE ) );
    }

    /**
     * When deactivated plugin.
     *
     */
    public function plugin_deactivation() {
        //$optionsName = CWEE_TEXT_DOMAIN.'_options';
        //delete_option( $optionsName );
    }

    /**
     * Main function - cc new emails
     *
     */
    public function cwee_recovery_mode_email($email) {
        $optionsName = CWEE_TEXT_DOMAIN.'_options';
        $options = get_option($optionsName);
        $emails = isset($options['emails']) ? $options['emails'] : '';
        if(!empty($emails))
            $email['headers'] .= 'Cc: ' . $emails . "\r\n";
        return $email;
    }

}