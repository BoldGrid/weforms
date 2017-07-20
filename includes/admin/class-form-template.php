<?php

/**
 * The template manager
 */
class WPUF_Contact_Form_Template {

    public function __construct() {
        add_filter( 'wp_ajax_weforms_contact_form_template', array( $this, 'create_contact_form_from_template' ) );
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
        check_ajax_referer( 'best-contact-form' );

        $template_name = isset( $_REQUEST['template'] ) ? sanitize_text_field( $_REQUEST['template'] ) : '';

        if ( ! $template_name ) {
            return;
        }

        if ( 'blank_form' == $template_name ) {
            $this->create_blank_form();
        }

        $template_object = $this->get_template_object( $template_name );

        if ( false === $template_object ) {
            return;
        }

        $current_user = get_current_user_id();
        $form_title   = $template_object->get_title();
        $has_existing = get_page_by_title( $form_title, 'OBJECT', 'wpuf_contact_form' );

        $form_post_data = array(
            'post_title'  => $form_title,
            'post_type'   => 'wpuf_contact_form',
            'post_status' => 'publish',
            'post_author' => $current_user
        );

        $form_id = wp_insert_post( $form_post_data );

        if ( is_wp_error( $form_id ) ) {
            return;
        }

        // if there is an existing form with same name, update this one with its ID
        if ( $has_existing ) {
            wp_update_post( array(
                'ID'         => $form_id,
                'post_title' => $form_title . ' (#' . $form_id . ')'
            ) );
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

        wp_send_json_success( array(
            'id' => $form_id
        ) );
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

        require_once WEFORMS_INCLUDES . '/admin/form-templates/contact-form.php';

        $template_object = new WPUF_Contact_Form_Template_Contact();
        update_post_meta( $form_id, 'wpuf_form_settings', $template_object->get_form_settings() );
        update_post_meta( $form_id, 'notifications', $template_object->get_form_notifications() );

        wp_send_json_success( array(
            'id' => $form_id
        ) );
    }
}
