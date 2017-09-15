<?php

/**
 * The welcome class after install
 *
 * @since 1.1.0
 */
class WeForms_Admin_Welcome {

    function __construct() {
        add_action( 'admin_menu', array( $this, 'register_menu'  ) );
        add_action( 'admin_head', array( $this, 'hide_menu' ) );
        add_action( 'admin_init', array( $this, 'redirect_to_page' ), 9999 );
    }

    /**
     * Register the admin menu to setup the welcome message
     *
     * @return void
     */
    public function register_menu() {
        add_dashboard_page( __( 'Welcome to weForms', 'weforms' ), __( 'Welcome to weForms', 'weforms' ), 'manage_options', 'weforms-welcome', array( $this, 'welcome_page' ) );
    }

    /**
     * Hide the menu as we don't want to show the welcome page in admin menu
     *
     * @return void
     */
    public function hide_menu() {
        remove_submenu_page( 'index.php', 'weforms-welcome' );
    }

    /**
     * Redirect to the welcome page once the plugin is installed
     *
     * @return void
     */
    public function redirect_to_page() {
        if ( ! get_transient( 'weforms_activation_redirect' ) ) {
            return;
        }

        delete_transient( 'weforms_activation_redirect' );

        // Only do this for single site installs.
        if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
            return;
        }

        wp_safe_redirect( admin_url( 'index.php?page=weforms-welcome' ) );
        exit;
    }

    /**
     * Render the welcome page
     *
     * @return void
     */
    public function welcome_page() {
        echo "Hello There!";
    }
}
