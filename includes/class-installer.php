<?php

/**
 * The installer class
 *
 * @since 1.1.0
 */
class WeForms_Installer {

    /**
     * The installer class
     *
     * @return void
     */
    public function install() {

        $this->create_tables();
        $this->maybe_set_default_settings();
        $this->create_default_form();

        $installed = get_option( 'weforms_installed' );

        if ( ! $installed ) {
            update_option( 'weforms_installed', time() );
        }

        set_transient( 'weforms_activation_redirect', true, 30 );
        update_option( 'weforms_version', WEFORMS_VERSION );
    }

    /**
     * Create the table schema
     *
     * @return void
     */
    public function create_tables() {
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
    }

    /**
     * Set the required default settings key if not present
     *
     * This is required for setting up the component settings data
     *
     * @return void
     */
    public function maybe_set_default_settings() {
        $requires_update = false;
        $settings        = get_option( 'weforms_settings', array() );
        $additional_keys = array(
            'email_gateway' => 'wordpress',
            'credit'        => false,
            'recaptcha'     => array( 'key' => '', 'secret' => '' )
        );

        foreach ($additional_keys as $key => $value) {
            if ( ! isset( $settings[ $key ] ) ) {
                $settings[ $key ] = $value;

                $requires_update = true;
            }
        }

        if ( $requires_update ) {
            update_option( 'weforms_settings', $settings );
        }
    }

    /**
     * Create a default form
     *
     * @return void
     */
    public function create_default_form() {
        $version = get_option( 'weforms_version' );

        // seems like it's already installed
        if ( $version ) {
            return;
        }

        if ( ! function_exists( 'weforms_get_contactform_template_fields' ) ) {
            require_once dirname( __FILE__ ) . '/functions-template-contact-form.php';
        }

        $form_post_data = array(
            'post_title'  => __( 'Contact Form', 'weforms' ),
            'post_type'   => 'wpuf_contact_form',
            'post_status' => 'publish',
            'post_author' => get_current_user_id()
        );

        $form_id = wp_insert_post( $form_post_data );

        if ( is_wp_error( $form_id ) ) {
            return;
        }

        update_post_meta( $form_id, 'wpuf_form_settings', weforms_get_contactform_template_settings() );
        update_post_meta( $form_id, 'notifications', weforms_get_contactform_template_notification() );

        $form_fields = weforms_get_contactform_template_fields();

        if ( $form_fields ) {
            foreach ($form_fields as $menu_order => $field) {
                wp_insert_post( array(
                    'post_type'    => 'wpuf_input',
                    'post_status'  => 'publish',
                    'post_content' => maybe_serialize( $field ),
                    'post_parent'  => $form_id,
                    'menu_order'   => $menu_order
                ) );
            }
        }
    }
}