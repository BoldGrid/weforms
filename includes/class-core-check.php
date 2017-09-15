<?php

/**
 * Check if the core exists and related tasks
 *
 * @since 1.1.0
 */
class WeForms_Core_Check {

    /**
     * The constructor
     */
    public function __construct() {
        add_action( 'init', array( $this, 'maybe_requires_core' ) );
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
                $plugin_file      = basename( dirname( __FILE__ ) ) . '/weforms.php';
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

        // hide wpuf page creation notice and tracking
        update_option( '_wpuf_page_created', '1' );
        update_option( 'wp-user-frontend_tracking_notice', 'hide' );

        wp_send_json_success();
    }
}