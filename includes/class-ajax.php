<?php

/**
 * The ajax handler class
 */
class WPUF_Contact_Form_Ajax {

    public function __construct() {
        add_action( 'wp_ajax_wpuf_contact_form_list', array( $this, 'get_contact_forms' ) );
        add_action( 'wp_ajax_wpuf_contact_form_create', array( $this, 'create_form' ) );
        add_action( 'wp_ajax_wpuf_contact_form_delete', array( $this, 'delete_form' ) );
    }

    public function get_contact_forms() {
        $args = array(
            'post_type' => 'wpuf_contact_form'
        );

        $forms = new WP_Query( $args );

        // var_dump( $forms->get_posts() );
        // var_dump( $forms );
        $response = array(
            'forms' => $forms->get_posts(),
            'total' => (int) $forms->found_posts,
            'pages' => (int) $forms->max_num_pages()
        );

        wp_send_json_success( $response );
        //wp_send_json_error();
    }

    public function create_form() {
        $form_name = isset( $_POST['form_name'] ) ? sanitize_text_field( $_POST['form_name'] ) : '';

        if ( empty( $form_name )) {
            wp_send_json_error( __( 'Please provide a form name', 'wpuf-contact-form' ) );
        }

        $post_id = wp_insert_post( array(
            'post_title'  => $form_name,
            'post_type'   => 'wpuf_contact_form',
            'post_status' => 'publish'
        ) );

        if ( is_wp_error( $post_id )) {
            wp_send_json_error( $post_id->get_error_message() );
        }

        wp_send_json_success( array(
            'form_id'   => $post_id,
            'form_name' => $form_name
        ) );
    }

    public function delete_form() {
        $form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;

        if ( ! $form_id ) {
            wp_send_json_error( __( 'No form id provided!', 'wpuf-contact-form' ) );
        }

        wp_delete_post( $form_id, true );
        wp_send_json_success();
    }
}