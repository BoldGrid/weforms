<?php

/**
 * The template manager
 */
class WPUF_Contact_Form_Template {

    public function __construct() {
        add_action( 'admin_footer', array( $this, 'render_form_templates' ) );

        add_filter( 'admin_action_wpuf_contact_form_template', array( $this, 'create_contact_form_from_template' ) );
    }

    /**
     * Render the forms in the modal
     *
     * @return void
     */
    public function render_form_templates() {
        if ( get_current_screen()->id != 'user-frontend_page_wpuf-contact-forms' ) {
            return;
        }

        $registry       = wpuf_cf_get_form_templates();
        $action_name    = 'wpuf_contact_form_template';
        $blank_form_url = esc_url( add_query_arg( array(
            'action'   => $action_name,
            'template' => 'blank_form',
            '_wpnonce' => wp_create_nonce( 'wpuf_create_from_template' )
        ), admin_url( 'admin.php' ) ) );

        if ( ! $registry ) {
            return;
        }

        include WPUF_ROOT . '/admin/html/modal.php';
    }

    /**
     * Get a template object by name from the registry
     *
     * @param  string $template
     *
     * @return boolean|WPUF_Post_Form_Template
     */
    public function get_template_object( $template ) {
        $registry = wpuf_cf_get_form_templates();

        if ( ! array_key_exists( $template, $registry ) ) {
            return false;
        }

        $template_object = $registry[ $template ];

        if ( ! is_a( $template_object, 'WPUF_Post_Form_Template') ) {
            return false;
        }

        return $template_object;
    }

    /**
     * Create a posting form from a post template
     *
     * @return void
     */
    public function create_contact_form_from_template() {
        check_admin_referer( 'wpuf_create_from_template' );

        $template_name = isset( $_GET['template'] ) ? sanitize_text_field( $_GET['template'] ) : '';

        if ( ! $template_name ) {
            return;
        }

        if ( 'blank_form' == $template_name ) {
            return $this->create_blank_form();
        }

        $template_object = $this->get_template_object( $template_name );

        if ( false === $template_object ) {
            return;
        }

        // var_dump( $template_object ); die();
        $current_user = get_current_user_id();

        $form_post_data = array(
            'post_title'  => $template_object->get_title(),
            'post_type'   => 'wpuf_contact_form',
            'post_status' => 'publish',
            'post_author' => $current_user
        );

        $form_id = wp_insert_post( $form_post_data );

        if ( is_wp_error( $form_id ) ) {
            return;
        }

        // form has been created, lets setup
        update_post_meta( $form_id, 'wpuf_form_settings', $template_object->get_form_settings() );
        update_post_meta( $form_id, 'notifications', $template_object->get_form_notifications() );

        $form_fields = $template_object->get_form_fields();

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

        wp_redirect( admin_url( 'admin.php?page=wpuf-contact-forms&action=edit&id=' . $form_id ) );
        exit;
    }

    /**
     * Create a blank form from the contact form template setting
     *
     * @return void
     */
    public function create_blank_form() {
        $current_user = get_current_user_id();

        $form_post_data = array(
            'post_title'  => 'Blank Form',
            'post_type'   => 'wpuf_contact_form',
            'post_status' => 'publish',
            'post_author' => $current_user
        );

        $form_id = wp_insert_post( $form_post_data );
        if ( is_wp_error( $form_id ) ) {
            return;
        }

        require_once WPUF_CONTACT_FORM_INCLUDES . '/admin/form-templates/contact-form.php';

        $template_object = new WPUF_Contact_Form_Template_Contact();
        update_post_meta( $form_id, 'wpuf_form_settings', $template_object->get_form_settings() );
        update_post_meta( $form_id, 'notifications', $template_object->get_form_notifications() );

        wp_redirect( admin_url( 'admin.php?page=wpuf-contact-forms&action=edit&id=' . $form_id ) );
        exit;
    }
}
