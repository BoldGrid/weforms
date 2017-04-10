<?php

/**
 * The ajax handler class
 */
class WPUF_Contact_Form_Ajax {

    public function __construct() {

        // backend requests
        add_action( 'wp_ajax_wpuf_contact_form_list', array( $this, 'get_contact_forms' ) );
        add_action( 'wp_ajax_wpuf_contact_form_create', array( $this, 'create_form' ) );
        add_action( 'wp_ajax_wpuf_contact_form_delete', array( $this, 'delete_form' ) );
        add_action( 'wp_ajax_wpuf_contact_form_entries', array( $this, 'get_entries' ) );
        add_action( 'wp_ajax_wpuf_contact_form_entry_details', array( $this, 'get_entry_detail' ) );

        // frontend requests
        add_action( 'wp_ajax_wpuf_submit_contact', array( $this, 'handle_frontend_submission' ) );
    }

    public function get_contact_forms() {
        $args = array(
            'post_type' => 'wpuf_contact_form'
        );

        $forms = new WP_Query( $args );
        $contact_forms = $forms->get_posts();

        array_map( function($form) {
            $form->entries = wpuf_cf_count_form_entries( $form->ID );
        }, $contact_forms);

        // var_dump( $forms->get_posts() );
        // var_dump( $forms );
        $response = array(
            'forms' => $contact_forms,
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

    public function get_entries() {
        $form_id      = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;
        $current_page = isset( $_REQUEST['page'] ) ? intval( $_REQUEST['page'] ) : 1;
        $per_page     = 20;
        $offset       = ( $current_page - 1 ) * $per_page;

        if ( ! $form_id ) {
            wp_send_json_error( __( 'No form id provided!', 'wpuf-contact-form' ) );
        }

        $entries = wpuf_cf_get_form_entries( $form_id, array(
            'number' => $per_page,
            'offset' => $offset
        ) );

        $columns       = wpuf_cf_get_entry_columns( $form_id );
        $total_entries = wpuf_cf_count_form_entries( $form_id );

        array_map( function( $entry ) use ($columns) {
            $entry_id = $entry->id;
            $entry->fields = array();

            foreach ($columns as $meta_key => $label) {
                $entry->fields[$meta_key] = wpuf_cf_get_entry_meta( $entry_id, $meta_key, true );
            }
        }, $entries );

        $response = array(
            'columns'    => $columns,
            'entries'    => $entries,
            'form_title' => get_post_field( 'post_title', $form_id ),
            'pagination' => array(
                'total'    => $total_entries,
                'per_page' => $per_page,
                'pages'    => ceil( $total_entries / $per_page ),
                'current'  => $current_page
            )
        );

        paginate_links( );

        wp_send_json_success( $response );
    }

    public function get_entry_detail() {
        $entry_id = isset( $_REQUEST['entry_id'] ) ? intval( $_REQUEST['entry_id'] ) : 0;
        $entry    = wpuf_cf_get_entry( $entry_id );

        if ( !$entry ) {
            wp_send_json_error( __( 'No such entry found!', 'wpuf-contact-form' ) );
        }

        $data   = array();
        $fields = wpuf_cf_get_form_field_labels( $entry->form_id );

        if ( ! $fields ) {
            wp_send_json_error( __( 'No form fields found!', 'wpuf-contact-form' ) );
        }

        foreach ($fields as $meta_key => $label ) {
            $data[ $meta_key ] = wpuf_cf_get_entry_meta( $entry_id, $meta_key, true );
        }

        $response = array(
            'form_fields' => $fields,
            'meta_data' => $data
        );
        wp_send_json_success( $response );
    }

    public function handle_frontend_submission() {
        check_ajax_referer( 'wpuf_form_add' );

        $form_id       = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;
        $form_vars     = WPUF_Render_Form::get_input_fields( $form_id );
        $form_settings = wpuf_get_form_settings( $form_id );

        list( $post_vars, $taxonomy_vars, $meta_vars ) = $form_vars;

        if ( !$meta_vars ) {
            wp_send_json( array(
                'success'     => false,
                'error'       => __( 'No form field was found.', 'wpuf-contact-form' ),
            ) );
        }

        // process the form fields
        $entry_fields = $this->prepare_entry_fields( $meta_vars );

        $entry_id = wpuf_cf_insert_entry( array(
            'form_id' => $form_id
        ), $entry_fields );

        if ( is_wp_error( $entry_id ) ) {
            wp_send_json( array(
                'success'     => false,
                'error'       => $entry_id->get_error_message(),
            ) );
        }

        // redirect URL
        $show_message = false;
        $redirect_to  = false;

        if ( $form_settings['redirect_to'] == 'page' ) {
            $redirect_to = get_permalink( $form_settings['page_id'] );
        } elseif ( $form_settings['redirect_to'] == 'url' ) {
            $redirect_to = $form_settings['url'];
        } elseif ( $form_settings['redirect_to'] == 'same' ) {
            $show_message = true;
        } else {
            $redirect_to = get_permalink( $post_id );
        }

        // send the response
        $response = array(
            'success'      => true,
            'redirect_to'  => $redirect_to,
            'show_message' => $show_message,
            'message'      => $form_settings['message']
        );

        // wpuf_clear_buffer();
        wp_send_json( $response );
    }

    public function prepare_entry_fields( $form_fields ) {
        $entry_fields = array();

        list( $meta_key_value, $multi_repeated, $files ) = WPUF_Frontend_Form_Post::prepare_meta_fields( $form_fields );

        // var_dump( $meta_key_value, $multi_repeated, $files );
        if ( $meta_key_value ) {
            foreach ($meta_key_value as $key => $value) {
                $entry_fields[ $key ] = $value;
            }
        }

        return $entry_fields;
    }
}