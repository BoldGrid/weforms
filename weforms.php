<?php
/**
 * Plugin Name: weForms
 * Description: The best contact form plugin for WordPress
 * Plugin URI: https://wedevs.com/weforms/
 * Author: weDevs
 * Author URI: https://wedevs.com/
 * Version: 1.0.4
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: weforms
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2017 weDevs LLC (email: info@wedevs.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * WeForms class
 *
 * @class WeForms The class that holds the entire WeForms plugin
 */
final class WeForms {

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '1.0.4';

    /**
     * Form field value seperator
     *
     * @var string
     */
    static $field_separator = '| ';

    /**
     * Holds various class instances
     *
     * @var array
     */
    private $container = array();

    /**
     * Constructor for the WeForms class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    public function __construct() {
        $this->define_constants();

        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __get( $prop ) {
        if ( array_key_exists( $prop, $this->container ) ) {
            return $this->container[ $prop ];
        }

        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __isset( $prop ) {
        return isset( $this->{$prop} ) || isset( $this->container[ $prop ] );
    }

    /**
     * Initializes the WeForms() class
     *
     * Checks for an existing WeForms() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new WeForms();
        }

        return $instance;
    }

    /**
     * Define the constants
     *
     * @return void
     */
    private function define_constants() {
        define( 'WEFORMS_VERSION', '1.0' );
        define( 'WEFORMS_FILE', __FILE__ );
        define( 'WEFORMS_ROOT', dirname( __FILE__ ) );
        define( 'WEFORMS_INCLUDES', WEFORMS_ROOT . '/includes' );
        define( 'WEFORMS_ROOT_URI', plugins_url( '', __FILE__ ) );
        define( 'WEFORMS_ASSET_URI', WEFORMS_ROOT_URI . '/assets' );
    }

    /**
     * Load the plugin after WP User Frontend is loaded
     *
     * @return void
     */
    public function init_plugin() {
        $this->includes();
        $this->init_hooks();

        do_action( 'weforms_loaded' );
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate() {
        require_once WEFORMS_INCLUDES . '/class-installer.php';

        $installer = new WeForms_Installer();
        $installer->install();
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate() {

    }

    /**
     * Include the required classes
     *
     * @return void
     */
    public function includes() {

        require_once WEFORMS_INCLUDES . '/compat/class-abstract-wpuf-integration.php';

        if ( is_admin() ) {
            // compatibility
            require_once WEFORMS_INCLUDES . '/compat/class-abstract-post-form-template.php';

            require_once WEFORMS_INCLUDES . '/admin/class-admin.php';
            require_once WEFORMS_INCLUDES . '/admin/class-admin-welcome.php';
            require_once WEFORMS_INCLUDES . '/class-importer-manager.php';

            require_once WEFORMS_INCLUDES . '/admin/class-form-template.php';
            require_once WEFORMS_INCLUDES . '/admin/class-pro-integrations.php';
        } else {
            require_once WEFORMS_INCLUDES . '/class-frontend-form.php';

            // add reCaptcha library if not found
            if ( !function_exists( 'recaptcha_get_html' ) ) {
                require_once WEFORMS_INCLUDES . '/library/reCaptcha/recaptchalib.php';
                require_once WEFORMS_INCLUDES . '/library/reCaptcha/recaptchalib_noCaptcha.php';
            }
        }

        require_once WEFORMS_INCLUDES . '/class-scripts-styles.php';
        require_once WEFORMS_INCLUDES . '/class-emailer.php';
        require_once WEFORMS_INCLUDES . '/class-field-manager.php';
        require_once WEFORMS_INCLUDES . '/class-form-manager.php';
        require_once WEFORMS_INCLUDES . '/class-form-entry-manager.php';
        require_once WEFORMS_INCLUDES . '/class-form-entry.php';
        require_once WEFORMS_INCLUDES . '/class-form.php';

        require_once WEFORMS_INCLUDES . '/integrations/class-abstract-integration.php';
        require_once WEFORMS_INCLUDES . '/class-integration-manager.php';

        require_once WEFORMS_INCLUDES . '/class-ajax.php';
        require_once WEFORMS_INCLUDES . '/class-ajax-upload.php';
        require_once WEFORMS_INCLUDES . '/class-notification.php';
        require_once WEFORMS_INCLUDES . '/class-form-preview.php';
        require_once WEFORMS_INCLUDES . '/functions.php';
        require_once WEFORMS_INCLUDES . '/functions-template-contact-form.php';
    }

    /**
     * Initialize the hooks
     *
     * @return void
     */
    public function init_hooks() {

        // Localize our plugin
        add_action( 'init', array( $this, 'localization_setup' ) );

        // initialize the classes
        add_action( 'init', array( $this, 'init_classes' ) );
        add_action( 'init', array( $this, 'wpdb_table_shortcuts' ), 0 );

        add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
    }

    /**
     * Set WPDB table shortcut names
     *
     * @return void
     */
    public function wpdb_table_shortcuts() {
        global $wpdb;

        $wpdb->weforms_entries   = $wpdb->prefix . 'weforms_entries';
        $wpdb->weforms_entrymeta = $wpdb->prefix . 'weforms_entrymeta';
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
        load_plugin_textdomain( 'weforms', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes() {

        if ( is_admin() ) {
            $this->container['admin']        = new WeForms_Admin();
            // $this->container['welcome']      = new WeForms_Admin_Welcome();
            $this->container['templates']    = new WeForms_Form_Template();
            $this->container['integrations'] = new WeForms_Pro_Integrations();
            $this->container['importer']     = new WeForms_Importer_Manager();
        } else {
            $this->container['frontend'] = new WeForms_Frontend_Form();
        }

        $this->container['emailer']      = new WeForms_Emailer();
        $this->container['form']         = new WeForms_Form_Manager();
        $this->container['fields']       = new WeForms_Field_Manager();
        $this->container['integrations'] = new WeForms_Integration_Manager();
        $this->container['preview']      = new WeForms_Form_Preview();
        $this->container['scripts']      = new WeForms_Scripts_Styles();

        // instantiate the integrations
        $this->integrations->get_integrations();

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            $this->container['ajax']        = new WeForms_Ajax();
            $this->container['ajax_upload'] = new WeForms_Ajax_Upload();
        }
    }

    /**
     * Plugin action links
     *
     * @param  array  $links
     *
     * @return array
     */
    function plugin_action_links( $links ) {

        $links[] = '<a href="https://wedevs.com/docs/weforms/contact-forms/?utm_source=weforms-action-link&utm_medium=textlink&utm_campaign=plugin-docs-link" target="_blank">' . __( 'Docs', 'weforms' ) . '</a>';
        $links[] = '<a href="' . admin_url( 'admin.php?page=weforms' ) . '">' . __( 'Settings', 'weforms' ) . '</a>';

        return $links;
    }

} // WeForms

/**
 * Initialize the plugin
 *
 * @return \WeForms
 */
function weforms() {
    return WeForms::init();
}

// kick-off
weforms();
