<?php
/**
 * Plugin Name: weForms
 * Description: The best contact form plugin for WordPress
 * Plugin URI: https://wedevs.com/weforms/
 * Author: weDevs
 * Author URI: https://wedevs.com/
 * Version: 1.3.4
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
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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
    public $version = '1.3.4';

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
     * Minimum PHP version required
     *
     * @var string
     */
    private $min_php = '5.4.0';


    /**
     * Constructor for the WeForms class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    public function __construct() {

        $this->define_constants();

        if ( ! $this->is_supported_php() ) {
            register_activation_hook( __FILE__, array( $this, 'auto_deactivate' ) );
            add_action( 'admin_notices', array( $this, 'php_version_notice' ) );
            return;
        }

        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        add_action( 'admin_init', array( $this, 'plugin_upgrades' ) );
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
        define( 'WEFORMS_VERSION', $this->version );
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
     */
    public function activate() {

        // prepare the environment
        require_once WEFORMS_INCLUDES . '/functions.php';
        require_once WEFORMS_INCLUDES . '/class-installer.php';
        require_once WEFORMS_INCLUDES . '/class-field-manager.php';
        require_once WEFORMS_INCLUDES . '/class-form-manager.php';
        require_once WEFORMS_INCLUDES . '/class-template-manager.php';

        if ( ! array_key_exists( 'fields', $this->container ) ) {
            $this->container['fields'] = new WeForms_Field_Manager();
        }

        if ( ! array_key_exists( 'form', $this->container ) ) {
            $this->container['form'] = new WeForms_Form_Manager();
        }

        if ( ! array_key_exists( 'templates', $this->container ) ) {
            $this->container['templates'] = new WeForms_Template_Manager();
        }

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

        if ( $this->is_request( 'admin' ) ) {
            // compatibility
            require_once WEFORMS_INCLUDES . '/class-template-manager.php';

            require_once WEFORMS_INCLUDES . '/admin/class-admin.php';
            require_once WEFORMS_INCLUDES . '/admin/class-admin-welcome.php';
            require_once WEFORMS_INCLUDES . '/class-importer-manager.php';

            require_once WEFORMS_INCLUDES . '/admin/class-pro-upgrades.php';
            require_once WEFORMS_INCLUDES . '/admin/class-promotion.php';
            require_once WEFORMS_INCLUDES . '/admin/class-shortcode-button.php';
            require_once WEFORMS_INCLUDES . '/admin/class-privacy.php';

        } else {

            // add reCaptcha library if not found
            if ( ! function_exists( 'recaptcha_get_html' ) ) {
                require_once WEFORMS_INCLUDES . '/library/reCaptcha/recaptchalib.php';
                require_once WEFORMS_INCLUDES . '/library/reCaptcha/recaptchalib_noCaptcha.php';
            }
        }

        if ( $this->is_request( 'frontend' ) || $this->is_request( 'ajax' ) ) {
            require_once WEFORMS_INCLUDES . '/class-frontend-form.php';
        }

        require_once WEFORMS_INCLUDES . '/admin/class-wedevs-insights.php';
        require_once WEFORMS_INCLUDES . '/class-scripts-styles.php';
        require_once WEFORMS_INCLUDES . '/admin/class-gutenblock.php';
        require_once WEFORMS_INCLUDES . '/class-emailer.php';
        require_once WEFORMS_INCLUDES . '/class-field-manager.php';
        require_once WEFORMS_INCLUDES . '/class-form-manager.php';
        require_once WEFORMS_INCLUDES . '/class-form-entry-manager.php';
        require_once WEFORMS_INCLUDES . '/class-form-entry.php';
        require_once WEFORMS_INCLUDES . '/class-form.php';
        require_once WEFORMS_INCLUDES . '/class-form-widget.php';

        require_once WEFORMS_INCLUDES . '/integrations/class-abstract-integration.php';
        require_once WEFORMS_INCLUDES . '/class-integration-manager.php';

        require_once WEFORMS_INCLUDES . '/class-ajax.php';
        require_once WEFORMS_INCLUDES . '/class-ajax-upload.php';
        require_once WEFORMS_INCLUDES . '/class-notification.php';
        require_once WEFORMS_INCLUDES . '/class-form-preview.php';
        require_once WEFORMS_INCLUDES . '/class-dokan-integration.php';
        require_once WEFORMS_INCLUDES . '/functions.php';
    }

    /**
     * Do plugin upgrades
     *
     * @since 1.1.2
     *
     * @return void
     */
    function plugin_upgrades() {

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        require_once WEFORMS_INCLUDES . '/class-upgrades.php';

        $upgrader = new WeForms_Upgrades();

        if ( $upgrader->needs_update() ) {
            $upgrader->perform_updates();
        }
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

        if ( $this->is_request( 'admin' ) ) {
            $this->container['admin']        = new WeForms_Admin();
            // $this->container['welcome']      = new WeForms_Admin_Welcome();
            $this->container['templates']    = new WeForms_Template_Manager();
            $this->container['pro_upgrades'] = new WeForms_Pro_Upgrades();
            $this->container['importer']     = new WeForms_Importer_Manager();
            $this->container['promo_offer']  = new WeForms_Admin_Promotion();
            $this->container['privacy']      = new WeForms_Privacy();
        }

        if ( $this->is_request( 'frontend' ) || $this->is_request( 'ajax' ) ) {
            $this->container['frontend'] = new WeForms_Frontend_Form();
        }

        $this->container['insights']            = new WeDevs_Insights( 'weforms', 'weForms', __FILE__ );
        $this->container['emailer']             = new WeForms_Emailer();
        $this->container['form']                = new WeForms_Form_Manager();
        $this->container['fields']              = new WeForms_Field_Manager();
        $this->container['integrations']        = new WeForms_Integration_Manager();
        $this->container['preview']             = new WeForms_Form_Preview();
        $this->container['scripts']             = new WeForms_Scripts_Styles();
        $this->container['block']               = new weForms_FormBlock();
        $this->container['dokan_integration']   = new weForms_Dokan_Integration();
        // instantiate the integrations
        $this->integrations->get_integrations();

        if ( $this->is_request( 'ajax' ) ) {
            $this->container['ajax']        = new WeForms_Ajax();
            $this->container['ajax_upload'] = new WeForms_Ajax_Upload();
        }
    }

    /**
     * The main logging function
     *
     * @uses error_log
     * @param string $type type of the error. e.g: debug, error, info
     * @param string $msg
     */
    public static function log( $type = '', $msg = '' ) {

        // default we are turning the debug mood on, but can be turned off
        if ( defined( 'WEFORMS_DEBUG_LOG' ) &&  false === WEFORMS_DEBUG_LOG ) {
           return;
        }

        $msg = sprintf( "[%s][%s] %s\n", date( 'd.m.Y h:i:s' ), $type, $msg );
        @error_log( $msg, 3, weforms_log_file_path() );
    }

    /**
     * Plugin action links
     *
     * @param  array $links
     *
     * @return array
     */
    function plugin_action_links( $links ) {

        $links[] = '<a href="https://wedevs.com/docs/weforms/?utm_source=weforms-action-link&utm_medium=textlink&utm_campaign=plugin-docs-link" target="_blank">' . __( 'Docs', 'weforms' ) . '</a>';
        $links[] = '<a href="' . admin_url( 'admin.php?page=weforms' ) . '">' . __( 'Settings', 'weforms' ) . '</a>';

        return $links;
    }

    /**
     * Check if the PHP version is supported
     *
     * @return bool
     */
    public function is_supported_php( $min_php = null ) {

        $min_php = $min_php ? $min_php : $this->min_php;

        if ( version_compare( PHP_VERSION, $min_php , '<=' ) ) {
            return false;
        }

        return true;
    }

    /**
     * Show notice about PHP version
     *
     * @return void
     */
    function php_version_notice() {

        if ( $this->is_supported_php() || ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $error = __( 'Your installed PHP Version is: ', 'weforms' ) . PHP_VERSION . '. ';
        $error .= __( 'The <strong>weForms</strong> plugin requires PHP version <strong>', 'weforms' ) . $this->min_php . __( '</strong> or greater.', 'weforms' );
        ?>
        <div class="error">
            <p><?php printf( $error ); ?></p>
        </div>
        <?php
    }

    /**
     * Bail out if the php version is lower than
     *
     * @return void
     */
    function auto_deactivate() {
        if ( $this->is_supported_php() ) {
            return;
        }

        deactivate_plugins( plugin_basename( __FILE__ ) );

        $error = __( '<h1>An Error Occured</h1>', 'weforms' );
        $error .= __( '<h2>Your installed PHP Version is: ', 'weforms' ) . PHP_VERSION . '</h2>';
        $error .= __( '<p>The <strong>weforms</strong> plugin requires PHP version <strong>', 'weforms' ) . $this->min_php . __( '</strong> or greater', 'weforms' );
        $error .= __( '<p>The version of your PHP is ', 'weforms' ) . '<a href="http://php.net/supported-versions.php" target="_blank"><strong>' . __( 'unsupported and old', 'weforms' ) . '</strong></a>.';
        $error .= __( 'You should update your PHP software or contact your host regarding this matter.</p>', 'weforms' );

        wp_die( $error, __( 'Plugin Activation Error', 'weforms' ), array( 'back_link' => true ) );
    }

    /**
     * What type of request is this?
     *
     * @since 1.2.3
     *
     * @param  string $type admin, ajax, cron, api or frontend.
     *
     * @return bool
     */
    private function is_request( $type ) {

        switch ( $type ) {
            case 'admin' :
                return is_admin();

            case 'ajax' :
                return defined( 'DOING_AJAX' );

            case 'cron' :
                return defined( 'DOING_CRON' );

            case 'api':
                return defined( 'REST_REQUEST' );

            case 'frontend' :
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
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
