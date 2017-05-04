<?php
/*
Plugin Name: Best Contact Form
Plugin URI: https://wedevs.com/wp-user-frontend-pro/
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
        $this->define_constants();

        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        // Localize our plugin
        add_action( 'init', array( $this, 'localization_setup' ) );
        add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );

        // install the core
        add_action( 'wp_ajax_wpuf_cf_install_wpuf', array( $this, 'install_wp_user_frontend' ) );
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
        global $wpdb;

        // bail out early if the core isn't installed
        if ( ! class_exists( 'WP_User_Frontend' ) ) {
            add_action( 'admin_notices', array( $this, 'core_activation_notice' ) );
            return;
        }

        $wpdb->wpuf_cf_entries   = $wpdb->prefix . 'wpuf_cf_entries';
        $wpdb->wpuf_cf_entrymeta = $wpdb->prefix . 'wpuf_cf_entrymeta';

        // seems like we have the core, we shall pass!!!
        $this->includes();
        $this->init_classes();
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate() {
        global $wpdb;

        $collate = '';

        if ( $wpdb->has_cap( 'collation' ) ) {
            if ( ! empty($wpdb->charset ) ) {
                $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
            }

            if ( ! empty($wpdb->collate ) ) {
                $collate .= " COLLATE $wpdb->collate";
            }
        }

        $table_schema = array(
            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}wpuf_cf_entries` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `form_id` bigint(20) unsigned DEFAULT NULL,
                `user_id` bigint(20) unsigned DEFAULT NULL,
                `user_ip` int(11) unsigned DEFAULT NULL,
                `user_device` varchar(50) DEFAULT NULL,
                `referer` varchar(255) DEFAULT NULL,
                `status` varchar(10) DEFAULT 'publish',
                `created_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `form_id` (`form_id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}wpuf_cf_entrymeta` (
                `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `wpuf_cf_entry_id` bigint(20) unsigned DEFAULT NULL,
                `meta_key` varchar(255) DEFAULT NULL,
                `meta_value` longtext,
                PRIMARY KEY (`meta_id`),
                KEY `meta_key` (`meta_key`),
                KEY `entry_id` (`wpuf_cf_entry_id`)
            ) $collate;",
        );

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        foreach ( $table_schema as $table ) {
            dbDelta( $table );
        }

        update_option( 'wpuf_cf_version', WPUF_CONTACT_FORM_VERSION );
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
            <h2><?php _e( 'Your Contact Form is almost ready!', 'best-contact-form' ); ?></h2>

            <?php
                $plugin_file      = basename( dirname( __FILE__ ) ) . '/contact-form.php';
                $core_plugin_file = 'wp-user-frontend/wpuf.php';
            ?>
            <a href="<?php echo wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . $plugin_file . '&amp;plugin_status=all&amp;paged=1&amp;s=', 'deactivate-plugin_' . $plugin_file ); ?>" class="notice-dismiss" style="text-decoration: none;" title="<?php _e( 'Dismiss this notice', 'best-contact-form' ); ?>"></a>

            <?php if ( file_exists( WP_PLUGIN_DIR . '/' . $core_plugin_file ) && is_plugin_inactive( 'wpuf-user-frontend' ) ): ?>
                <p><?php _e( 'You just need to activate the Core Plugin to make it functional.', 'best-contact-form' ); ?></p>
                <p>
                    <a class="button button-primary" href="<?php echo wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $core_plugin_file . '&amp;plugin_status=all&amp;paged=1&amp;s=', 'activate-plugin_' . $core_plugin_file ); ?>"  title="<?php _e( 'Activate this plugin', 'best-contact-form' ); ?>"><?php _e( 'Activate', 'best-contact-form' ); ?></a>
                </p>
            <?php else: ?>
                <p><?php echo sprintf( __( "You just need to install the %sCore Plugin%s to make it functional.", "wpuf-contact-form" ), '<a target="_blank" href="https://wordpress.org/plugins/wp-user-frontend/">', '</a>' ); ?></p>

                <p>
                    <button id="wpuf-contact-form-installer" class="button"><?php _e( 'Install Now', 'best-contact-form' ); ?></button>
                </p>
            <?php endif; ?>
        </div>

        <script type="text/javascript">
            (function ($) {
                var wrapper = $('#wpuf-contact-form-installer-notice');

                wrapper.on('click', '#wpuf-contact-form-installer', function (e) {
                    var self = $(this);

                    e.preventDefault();
                    self.addClass('install-now updating-message');
                    self.text('<?php echo esc_js( 'Installing...', 'best-contact-form' ); ?>');

                    var data = {
                        action: 'wpuf_cf_install_wpuf',
                        _wpnonce: '<?php echo wp_create_nonce('wpuf-installer-nonce'); ?>'
                    };

                    $.post(ajaxurl, data, function (response) {
                        if (response.success) {
                            self.attr('disabled', 'disabled');
                            self.removeClass('install-now updating-message');
                            self.text('<?php echo esc_js( 'Installed', 'best-contact-form' ); ?>');

                            window.location.reload();
                        }
                    });
                });
            })(jQuery);
        </script>
        <?php
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
        load_plugin_textdomain( 'best-contact-form', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Define the constants
     *
     * @return void
     */
    private function define_constants() {
        define( 'WPUF_CONTACT_FORM_VERSION', '1.0' );
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
            require_once WPUF_CONTACT_FORM_INCLUDES . '/admin/class-form-template.php';
        } else {
            require_once WPUF_CONTACT_FORM_INCLUDES . '/class-frontend-form.php';
        }

        require_once WPUF_CONTACT_FORM_INCLUDES . '/class-ajax.php';
        require_once WPUF_CONTACT_FORM_INCLUDES . '/class-notification.php';
        require_once WPUF_CONTACT_FORM_INCLUDES . '/functions.php';
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
            new WPUF_Contact_Form_Template();
        } else {
            new WPUF_Contact_Form_Frontend();
        }

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            new WPUF_Contact_Form_Ajax();
        }
    }

    /**
     * Install the WP User Frontend plugin via ajax
     *
     * @return void
     */
    public function install_wp_user_frontend() {

        if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpuf-installer-nonce' ) ) {
            wp_send_json_error( __( 'Error: Nonce verification failed', 'wpuf-pro' ) );
        }

        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        $plugin = 'wp-user-frontend';
        $api    = plugins_api( 'plugin_information', array( 'slug' => $plugin, 'fields' => array( 'sections' => false ) ) );

        $upgrader = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );
        $result   = $upgrader->install( $api->download_link );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result );
        }

        $result = activate_plugin( 'wp-user-frontend/wpuf.php' );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result );
        }

        wp_send_json_success();
    }

} // WPUF_Contact_Form

/**
 * Initialize the plugin
 *
 * @return \WPUF_Contact_Form
 */
function wpuf_contact_form() {
    return WPUF_Contact_Form::init();
}

// kick-off
wpuf_contact_form();
