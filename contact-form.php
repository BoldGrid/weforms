<?php
/*
Plugin Name: WP User Frontend - Contact Form
Plugin URI: https://wedevs.com/
Description: Contact form plugin for WordPress
Version: 0.1
Author: weDevs
Author URI: https://wedevs.com/
License: GPL2
TextDomain: wpuf-contact-form
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
 * WPUF_Contact_Form class
 *
 * @class WPUF_Contact_Form The class that holds the entire WPUF_Contact_Form plugin
 */
class WPUF_Contact_Form {

    /**
     * Constructor for the WPUF_Contact_Form class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @uses register_activation_hook()
     * @uses register_deactivation_hook()
     * @uses is_admin()
     * @uses add_action()
     */
    public function __construct() {
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        // Localize our plugin
        add_action( 'init', array( $this, 'localization_setup' ) );
        add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
    }

    /**
     * Initializes the WPUF_Contact_Form() class
     *
     * Checks for an existing WPUF_Contact_Form() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new WPUF_Contact_Form();
        }

        return $instance;
    }

    public function init_plugin() {

        // bail out early if the core isn't installed
        if ( ! class_exists( 'WP_User_Frontend' ) ) {
            add_action( 'admin_notices', array( $this, 'core_activation_notice' ) );
            return;
        }

        // seems like we have the core, we shall pass!!!
        $this->define_constants();
        $this->includes();
        $this->init_classes();
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate() {

    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate() {

    }

    /**
     * The prompt to install the core plugin
     *
     * @return void
     */
    public function core_activation_notice() {
        ?>
        <div class="updated" id="wpuf-contact-form-installer-notice" style="padding: 1em; position: relative;">
            <h2><?php _e( 'Your Contact Form is almost ready!', 'wpuf-contact-form' ); ?></h2>

            <?php
                $plugin_file      = basename( dirname( __FILE__ ) ) . '/contact-form.php';
                $core_plugin_file = 'wp-user-frontend/wpuf.php';
            ?>
            <a href="<?php echo wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . $plugin_file . '&amp;plugin_status=all&amp;paged=1&amp;s=', 'deactivate-plugin_' . $plugin_file ); ?>" class="notice-dismiss" style="text-decoration: none;" title="<?php _e( 'Dismiss this notice', 'wpuf-contact-form' ); ?>"></a>

            <?php if ( file_exists( WP_PLUGIN_DIR . '/' . $core_plugin_file ) && is_plugin_inactive( 'wpuf-user-frontend' ) ): ?>
                <p><?php _e( 'You just need to activate the Core Plugin to make it functional.', 'wpuf-contact-form' ); ?></p>
                <p>
                    <a class="button button-primary" href="<?php echo wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $core_plugin_file . '&amp;plugin_status=all&amp;paged=1&amp;s=', 'activate-plugin_' . $core_plugin_file ); ?>"  title="<?php _e( 'Activate this plugin', 'wpuf-contact-form' ); ?>"><?php _e( 'Activate', 'wpuf-contact-form' ); ?></a>
                </p>
            <?php else: ?>
                <p><?php echo sprintf( __( "You just need to install the %sCore Plugin%s to make it functional.", "wpuf-contact-form" ), '<a target="_blank" href="https://wordpress.org/plugins/wp-user-frontend/">', '</a>' ); ?></p>

                <p>
                    <button id="wpuf-contact-form-installer" class="button"><?php _e( 'Install Now', 'wpuf-contact-form' ); ?></button>
                </p>
            <?php endif; ?>

        </div>
        <?php
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
        load_plugin_textdomain( 'wpuf-contact-form', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Define the constants
     *
     * @return void
     */
    private function define_constants() {
        define( 'WPUF_CONTACT_FORM_VERSION', '2.5' );
        define( 'WPUF_CONTACT_FORM_FILE', __FILE__ );
        define( 'WPUF_CONTACT_FORM_ROOT', dirname( __FILE__ ) );
        define( 'WPUF_CONTACT_FORM_INCLUDES', WPUF_CONTACT_FORM_ROOT . '/includes' );
        define( 'WPUF_CONTACT_FORM_ROOT_URI', plugins_url( '', __FILE__ ) );
        define( 'WPUF_CONTACT_FORM_ASSET_URI', WPUF_CONTACT_FORM_ROOT_URI . '/assets' );
    }

    /**
     * Include the required classes
     *
     * @return void
     */
    public function includes() {
        if ( is_admin() ) {
            require_once WPUF_CONTACT_FORM_INCLUDES . '/admin/admin.php';
            require_once WPUF_CONTACT_FORM_INCLUDES . '/admin/class-contact-form-builder.php';
        }

        require_once WPUF_CONTACT_FORM_INCLUDES . '/class-ajax.php';
    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes() {
        if ( is_admin() ) {
            new WPUF_Contact_Form_Admin();
            new WPUF_Contact_Form_Builder();
        }

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            new WPUF_Contact_Form_Ajax();
        }
    }

} // WPUF_Contact_Form

WPUF_Contact_Form::init();
