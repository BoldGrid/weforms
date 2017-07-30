<?php
/**
 * Plugin Name: weForms
 * Description: The best contact form plugin for WordPress
 * Plugin URI: https://wedevs.com/weforms/
 * Author: weDevs
 * Author URI: https://wedevs.com/
 * Version: 1.0
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
    public $version = '0.1.0';

    /**
     * Constructor for the WeForms class
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

        add_action( 'init', array( $this, 'maybe_requires_core' ) );
        add_action( 'wpuf_loaded', array( $this, 'init_plugin' ) );
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
            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}weforms_entries` (
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

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}weforms_entrymeta` (
                `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `weforms_entry_id` bigint(20) unsigned DEFAULT NULL,
                `meta_key` varchar(255) DEFAULT NULL,
                `meta_value` longtext,
                PRIMARY KEY (`meta_id`),
                KEY `meta_key` (`meta_key`),
                KEY `entry_id` (`weforms_entry_id`)
            ) $collate;",
        );

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        foreach ( $table_schema as $table ) {
            dbDelta( $table );
        }

        update_option( 'weforms_version', WEFORMS_VERSION );
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

        if ( is_admin() ) {
            require_once WEFORMS_INCLUDES . '/admin/class-admin.php';
            require_once WEFORMS_INCLUDES . '/admin/class-form-template.php';
            require_once WEFORMS_INCLUDES . '/admin/class-pro-integrations.php';
        } else {
            require_once WPUF_ROOT . '/class/render-form.php';
            require_once WEFORMS_INCLUDES . '/class-frontend-form.php';
        }

        require_once WEFORMS_INCLUDES . '/class-ajax.php';
        require_once WEFORMS_INCLUDES . '/class-notification.php';
        require_once WEFORMS_INCLUDES . '/functions.php';
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

        add_filter( 'wpuf_integrations', array( $this, 'register_default_integrations' ) );

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
     * Check if the core WP User Frontend is installed
     *
     * @return boolean
     */
    public function is_core_installed() {
        return class_exists( 'WP_User_Frontend' );
    }

    /**
     * If the core isn't installed
     *
     * @return void
     */
    public function maybe_requires_core() {
        if ( $this->is_core_installed() ) {
            return;
        }

        // show the notice
        add_action( 'admin_notices', array( $this, 'core_activation_notice' ) );

        // install the core
        add_action( 'wp_ajax_wpuf_cf_install_wpuf', array( $this, 'install_wp_user_frontend' ) );
    }

    /**
     * The prompt to install the core plugin
     *
     * @return void
     */
    public function core_activation_notice() {
        ?>
        <div class="updated" id="wpuf-contact-form-installer-notice" style="padding: 1em; position: relative;">
            <h2><?php _e( 'weForms is almost ready!', 'weforms' ); ?></h2>

            <?php
                $plugin_file      = basename( dirname( __FILE__ ) ) . '/contact-form.php';
                $core_plugin_file = 'wp-user-frontend/wpuf.php';
            ?>
            <a href="<?php echo wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . $plugin_file . '&amp;plugin_status=all&amp;paged=1&amp;s=', 'deactivate-plugin_' . $plugin_file ); ?>" class="notice-dismiss" style="text-decoration: none;" title="<?php _e( 'Dismiss this notice', 'weforms' ); ?>"></a>

            <?php if ( file_exists( WP_PLUGIN_DIR . '/' . $core_plugin_file ) && is_plugin_inactive( 'wpuf-user-frontend' ) ): ?>
                <p><?php _e( 'You just need to activate the Core Plugin to make it functional.', 'weforms' ); ?></p>
                <p>
                    <a class="button button-primary" href="<?php echo wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $core_plugin_file . '&amp;plugin_status=all&amp;paged=1&amp;s=', 'activate-plugin_' . $core_plugin_file ); ?>"  title="<?php _e( 'Activate this plugin', 'weforms' ); ?>"><?php _e( 'Activate', 'weforms' ); ?></a>
                </p>
            <?php else: ?>
                <p><?php echo sprintf( __( "You just need to install the %sCore Plugin%s to make it functional.", "wpuf-contact-form" ), '<a target="_blank" href="https://wordpress.org/plugins/wp-user-frontend/">', '</a>' ); ?></p>

                <p>
                    <button id="wpuf-contact-form-installer" class="button"><?php _e( 'Install Now', 'weforms' ); ?></button>
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
                    self.text('<?php echo esc_js( 'Installing...', 'weforms' ); ?>');

                    var data = {
                        action: 'wpuf_cf_install_wpuf',
                        _wpnonce: '<?php echo wp_create_nonce('wpuf-installer-nonce'); ?>'
                    };

                    $.post(ajaxurl, data, function (response) {
                        if (response.success) {
                            self.attr('disabled', 'disabled');
                            self.removeClass('install-now updating-message');
                            self.text('<?php echo esc_js( 'Installed', 'weforms' ); ?>');

                            window.location.href = '<?php echo admin_url( 'admin.php?page=weforms' ); ?>';
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
        load_plugin_textdomain( 'weforms', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes() {

        if ( is_admin() ) {
            new WeForms_Admin();
            new WeForms_Form_Template();
            new WeForms_Pro_Integrations();
        } else {
            new WeForms_Frontend();
        }

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            new WeForms_Ajax();
        }
    }

    /**
     * Install the WP User Frontend plugin via ajax
     *
     * @return void
     */
    public function install_wp_user_frontend() {

        if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpuf-installer-nonce' ) ) {
            wp_send_json_error( __( 'Error: Nonce verification failed', 'weforms' ) );
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

    /**
     * Plugin action links
     *
     * @param  array  $links
     *
     * @return array
     */
    function plugin_action_links( $links ) {

        $links[] = '<a href="' . admin_url( 'admin.php?page=weforms' ) . '">' . __( 'Settings', 'weforms' ) . '</a>';

        return $links;
    }

    /**
     * Register default integrations
     *
     * @param  array $integrations
     *
     * @return array
     */
    public function register_default_integrations( $integrations ) {
        require_once WEFORMS_INCLUDES . '/integrations/slack/class-integration-slack.php';

        $integrations = array_merge( $integrations, array( 'WeForms_Integration_Slack' ) );

        return $integrations;
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
