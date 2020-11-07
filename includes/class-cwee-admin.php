<?php
/**
 * Copy WP Error Email Admin
 *
 * @class    CWEE_Admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * CWEE_Admin class.
 */
class CWEE_Admin {

    /**
     * Constructor.
     */
    public function __construct() {
        // Add menus.
        add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );

        // Enqueue styles / scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
    }

    public function admin_scripts() {
        $screen = get_current_screen();
        wp_register_style( 'cwee_admin_styles', CWEE()->plugin_url() . '/assets/css/admin.css', array(), CWEE_VERSION );

        if( isset( $screen->base ) && $screen->base == 'settings_page_'.CWEE_TEXT_DOMAIN ) {
            wp_enqueue_style( 'cwee_admin_styles' );
        }
    }

    /**
     * Add menu items.
     */
    public function admin_menu() {
        add_options_page( __(CWEE_PLUGIN_NAME, CWEE_TEXT_DOMAIN), __(CWEE_PLUGIN_NAME, CWEE_TEXT_DOMAIN), 'manage_options', CWEE_TEXT_DOMAIN, array( $this, 'settings_page' ));
    }

    /**
     * Init the settings page.
     */
    public function settings_page() {

        $optionsName = CWEE_TEXT_DOMAIN.'_options';

        if( isset( $_POST[$optionsName] ) ){
            $submittedData = (array) $_POST[$optionsName];
            $options = get_option($optionsName);
            $newOptions = array_merge((array)$options, $submittedData);
            update_option( $optionsName, $newOptions );
        }
        $options = get_option($optionsName);
        $emails = isset($options['emails']) ? $options['emails'] : '';
        ?>
        <div class="wrap" id="cweeWrap">
            <h2><?php echo CWEE_PLUGIN_NAME . __(' Settings', CWEE_TEXT_DOMAIN); ?></h2>

            <div class="row">
                <div class="col-8">
            <form method="post" action="">
                <?php settings_fields(CWEE_TEXT_DOMAIN.'_options'); ?>

                <div class="dataFeedURLWrap">
                    <?php

                    ?>
                    <h3>Additional Emails</h3>
                    <p class="newEmails">
                        <input type="text" name="<?php echo esc_html($optionsName);?>[emails]" id="additional_emails" value="<?php echo esc_attr($emails);?>" class="">
                    </p>
                    <p class="help">Enter your emails in order to cc on error emails.
                    </p>
                    <?php submit_button('Save'); ?>
                </div>

            </form>
                </div>
                <div class="col-4 sidebarCol">
                    <div class="sidebarInner">
                        <p>This plugin is brought to you by Medic52.</p>
                        <p>Medic52 is a risk management data platform for the leisure and outdoor industry that removes the pain of managing the associated paperwork.</p>
                        <p>Medic52 has a one of a kind map based dispatch, configurable incident collection forms, training passport, asset management and incident investigation library.</p>
                        <!--<p>Trail & Lift status is part of the asset management system and enables a ski resort to keep their website up to date with what is happening on the hill through a live data feed.</p>-->
                        <p><a href="https://www.medic52.com/">Website</a></p>
                        <p><a href="https://www.medic52.com/">Documentation</a></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

}

return new CWEE_Admin();
