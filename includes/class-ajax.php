<?php

/**
 * The ajax handler class
 */
class WeForms_Ajax {

    public function __construct() {

        // backend requests
        add_action( 'wp_ajax_weforms_form_list', array( $this, 'get_contact_forms' ) );
        add_action( 'wp_ajax_weforms_form_names', array( $this, 'get_contact_form_names' ) );
        add_action( 'wp_ajax_weforms_form_create', array( $this, 'create_form' ) );
        add_action( 'wp_ajax_weforms_form_delete', array( $this, 'delete_form' ) );
        add_action( 'wp_ajax_weforms_form_delete_bulk', array( $this, 'delete_form_bulk' ) );
        add_action( 'wp_ajax_weforms_form_duplicate', array( $this, 'duplicate_form' ) );

        // form settings
        add_action( 'wp_ajax_weforms_save_settings', array( $this, 'save_settings' ) );
        add_action( 'wp_ajax_weforms_get_settings', array( $this, 'get_settings' ) );

        // import
        add_action( 'wp_ajax_weforms_import_form', array( $this, 'import_form' ) );

        // form editing
        add_action( 'wp_ajax_weforms_get_form', array( $this, 'get_form' ) );

        // entries
        add_action( 'wp_ajax_weforms_form_entries', array( $this, 'get_entries' ) );
        add_action( 'wp_ajax_weforms_form_entry_details', array( $this, 'get_entry_detail' ) );
        add_action( 'wp_ajax_weforms_form_entry_trash', array( $this, 'trash_entry' ) );
        add_action( 'wp_ajax_weforms_form_entry_trash_bulk', array( $this, 'bulk_delete_entry' ) );

        // frontend requests
        add_action( 'wp_ajax_wpuf_submit_contact', array( $this, 'handle_frontend_submission' ) );
        add_action( 'wp_ajax_nopriv_wpuf_submit_contact', array( $this, 'handle_frontend_submission' ) );
    }

    /**
     * Administrator validation
     *
     * @return void
     */
    public function check_admin() {
        if ( !current_user_can( weforms_form_access_capability() ) ) {
            wp_send_json_error( __( 'You do not have sufficient permission.', 'weforms' ) );
        }
    }

    /**
     * Get a form to edit
     *
     * @return void
     */
    public function get_form() {

        $this->check_admin();

        $form_id = isset( $_REQUEST['form_id'] ) ? absint( $_REQUEST['form_id'] ) : 0;

        $data = array(
            'post'          => get_post( $form_id ),
            'form_fields'   => wpuf_get_form_fields( $form_id ),
            'settings'      => wpuf_get_form_settings( $form_id ),
            'notifications' => wpuf_get_form_notifications( $form_id ),
            'integrations'  => wpuf_get_form_integrations( $form_id )
        );

        wp_send_json_success( $data );
    }

    /**
     * Get all contact forms
     *
     * @return void
     */
    public function get_contact_forms() {
        check_ajax_referer( 'weforms' );

        $this->check_admin();

        $args = array(
            'post_type'      => 'wpuf_contact_form',
            'posts_per_page' => 10,
            'paged'          => isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1,
            'order'          => 'DESC',
            'orderby'        => 'post_date'
        );

        $forms         = new WP_Query( $args );
        $contact_forms = $forms->get_posts();

        array_map( function($form) {
            $form->entries = weforms_count_form_entries( $form->ID );
            $form->views   = weforms_get_form_views( $form->ID );
        }, $contact_forms);

        // var_dump( $forms->get_posts() );
        // var_dump( $forms );
        $response = array(
            'forms' => $contact_forms,
            'total' => (int) $forms->found_posts,
            'pages' => (int) $forms->max_num_pages
        );

        wp_send_json_success( $response );
    }

    /**
     * Get the names of contact forms for generating dropdown
     *
     * @return void
     */
    public function get_contact_form_names() {
        check_ajax_referer( 'weforms' );

        $this->check_admin();

        $args = array(
            'post_type'      => 'wpuf_contact_form',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'order'          => 'ASC',
            'orderby'        => 'post_title'
        );

        $response      = array();
        $forms         = new WP_Query( $args );
        $contact_forms = $forms->get_posts();

        foreach ($contact_forms as $form) {
            $response[] = array(
                'id'    => $form->ID,
                'title' => $form->post_title . ' (#' . $form->ID . ')'
            );
        }

        wp_send_json_success( $response );
    }

    /**
     * Create a form
     *
     * @return void
     */
    public function create_form() {
        check_ajax_referer( 'weforms' );

        $this->check_admin();

        $form_name = isset( $_POST['form_name'] ) ? sanitize_text_field( $_POST['form_name'] ) : '';

        if ( empty( $form_name ) ) {
            wp_send_json_error( __( 'Please provide a form name', 'weforms' ) );
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

    /**
     * Delete a form
     *
     * @return void
     */
    public function delete_form() {
        check_ajax_referer( 'weforms' );

        $this->check_admin();

        $form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;

        if ( ! $form_id ) {
            wp_send_json_error( __( 'No form id provided!', 'weforms' ) );
        }

        wpuf_delete_form( $form_id, true );
        wp_send_json_success();
    }

    public function delete_form_bulk() {
        check_ajax_referer( 'weforms' );

        $this->check_admin();

        $form_ids = isset( $_POST['ids'] ) ? array_map( 'absint', $_POST['ids'] ) : array();

        if ( ! $form_ids ) {
            wp_send_json_error( __( 'No form ids provided!', 'weforms' ) );
        }

        foreach ($form_ids as $form_id) {
            wpuf_delete_form( $form_id, true );
        }

        wp_send_json_success();
    }

    /**
     * Duplicate a form
     *
     * @return voiud
     */
    public function duplicate_form() {
        check_ajax_referer( 'weforms' );

        $this->check_admin();

        $form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;

        $duplicate_id = wpuf_duplicate_form( $form_id );
        $form = get_post( $duplicate_id );

        $form->entires = 0;
        $form->views   = 0;

        wp_send_json_success( $form );
    }

    /**
     * Save the weForms settings
     *
     * @return void
     */
    public function save_settings() {
        check_ajax_referer( 'weforms' );

        $this->check_admin();

        $requires_wpuf_update = false;
        $wpuf_update_array    = array();
        $settings             = (array) json_decode( wp_unslash( $_POST['settings'] ) );

        update_option( 'weforms_settings', $settings );

        // wpuf settings sync
        if ( isset( $settings['gmap_api'] ) ) {
            $requires_wpuf_update = true;
            $wpuf_update_array['gmap_api_key'] = $settings['gmap_api'];
        }

        if ( isset( $settings['recaptcha'] ) ) {
            $requires_wpuf_update                   = true;
            $wpuf_update_array['recaptcha_public']  = $settings['recaptcha']->key;
            $wpuf_update_array['recaptcha_private'] = $settings['recaptcha']->secret;
        }

        if ( $requires_wpuf_update ) {
            $wpuf_settings = get_option( 'wpuf_general', array() );

            $wpuf_settings = array_merge( $wpuf_settings, $wpuf_update_array );
            update_option( 'wpuf_general', $wpuf_settings );
        }

        do_action( 'weforms_save_settings', $settings );

        wp_send_json_success( $settings );
    }

    /**
     * Get the weForms Settings
     *
     * @return void
     */
    public function get_settings() {
        check_ajax_referer( 'weforms' );

        $this->check_admin();

        $settings = get_option( 'weforms_settings', array() );

        // checking to prevent js error, will be removed in future
        if ( ! isset( $settings['credit'] ) ) {
            $settings['credit'] = false;
        }

        if ( ! isset( $settings['permission'] ) ) {
            $settings['permission'] = 'manage_options';
        }

        wp_send_json_success( $settings );
    }

    /**
     * Get all entries
     *
     * @return void
     */
    public function get_entries() {
        check_ajax_referer( 'weforms' );

        $this->check_admin();

        $form_id      = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;
        $current_page = isset( $_REQUEST['page'] ) ? intval( $_REQUEST['page'] ) : 1;
        $per_page     = 20;
        $offset       = ( $current_page - 1 ) * $per_page;

        if ( ! $form_id ) {
            wp_send_json_error( __( 'No form id provided!', 'weforms' ) );
        }

        $entries = weforms_get_form_entries( $form_id, array(
            'number' => $per_page,
            'offset' => $offset
        ) );

        $columns       = weforms_get_entry_columns( $form_id );
        $total_entries = weforms_count_form_entries( $form_id );

        array_map( function( $entry ) use ($columns) {
            $entry_id = $entry->id;
            $entry->fields = array();

            foreach ($columns as $meta_key => $label) {
                $value                    = weforms_get_entry_meta( $entry_id, $meta_key, true );
                $entry->fields[$meta_key] = str_replace( WPUF_Render_Form::$separator, ' ', $value );
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

        wp_send_json_success( $response );
    }

    /**
     * Get an entry details
     *
     * @return void
     */
    public function get_entry_detail() {
        check_ajax_referer( 'weforms' );

        $this->check_admin();

        $entry_id = isset( $_REQUEST['entry_id'] ) ? intval( $_REQUEST['entry_id'] ) : 0;
        $entry    = weforms_get_entry( $entry_id );

        if ( !$entry ) {
            wp_send_json_error( __( 'No such entry found!', 'weforms' ) );
        }

        $info   = array(
            'form_title' => get_post_field( 'post_title', $entry->form_id ),
            'created'    => date_i18n( 'F j, Y g:i a', strtotime( $entry->created_at ) ),
            'ip'         => $entry->ip_address,
            'user'       => $entry->user_id ? get_user_by( 'id', $entry->user_id )->display_name : false,
            'referer'    => $entry->referer,
            'device'     => $entry->user_device
        );

        $data = weforms_get_entry_data( $entry_id );

        if ( false === $data ) {
            wp_send_json_error( __( 'No form fields found!', 'weforms' ) );
        }

        $response = array(
            'form_fields' => $data['fields'],
            'meta_data'   => $data['data'],
            'info'        => $info
        );

        wp_send_json_success( $response );
    }

    /**
     * Trash an entry
     *
     * @return void
     */
    public function trash_entry() {
        check_ajax_referer( 'weforms' );

        $this->check_admin();

        $entry_id = isset( $_REQUEST['entry_id'] ) ? intval( $_REQUEST['entry_id'] ) : 0;

        weforms_change_entry_status( $entry_id, 'trash' );
        wp_send_json_success();
    }

    /**
     * Bulk trash entries
     *
     * @return void
     */
    public function bulk_delete_entry() {
        check_ajax_referer( 'weforms' );

        $this->check_admin();

        $entry_ids = isset( $_POST['ids'] ) ? array_map( 'absint', $_POST['ids'] ) : array();

        if ( ! $entry_ids ) {
            wp_send_json_error( __( 'No entry ids provided!', 'weforms' ) );
        }

        foreach ($entry_ids as $entry_id) {
            weforms_change_entry_status( $entry_id, 'trash' );
        }

        wp_send_json_success();
    }

    /**
     * Handle the frontend submission
     *
     * @return void
     */
    public function handle_frontend_submission() {
        check_ajax_referer( 'wpuf_form_add' );

        $form_id       = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;
        $page_id       = isset( $_POST['page_id'] ) ? intval( $_POST['page_id'] ) : 0;

        $form_vars     = WPUF_Render_Form::get_input_fields( $form_id );
        $form_settings = wpuf_get_form_settings( $form_id );

        list( $post_vars, $taxonomy_vars, $meta_vars ) = $form_vars;

        if ( !$meta_vars ) {
            wp_send_json( array(
                'success'     => false,
                'error'       => __( 'No form field was found.', 'weforms' ),
            ) );
        }

        // process the form fields
        $entry_fields = $this->prepare_entry_fields( $meta_vars );

        $entry_id = weforms_insert_entry( array(
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

        // Fire a hook for integration
        do_action( 'weforms_entry_submission', $entry_id, $form_id, $page_id, $form_settings );

        // send the response
        $response = array(
            'success'      => true,
            'redirect_to'  => $redirect_to,
            'show_message' => $show_message,
            'message'      => $form_settings['message']
        );

        $notification = new WeForms_Notification( array(
            'form_id'  => $form_id,
            'page_id'  => $page_id,
            'entry_id' => $entry_id
        ) );

        $notification->send_notifications();

        wpuf_clear_buffer();
        wp_send_json( $response );
    }

    /**
     * Prepare meta fields by it's types
     *
     * @param  array $form_fields
     *
     * @return array
     */
    public function prepare_entry_fields( $form_fields ) {
        $entry_fields = array();

        list( $meta_key_value, $multi_repeated, $files ) = WPUF_Render_Form::prepare_meta_fields( $form_fields );

        // var_dump( $meta_key_value, $multi_repeated, $files );
        if ( $meta_key_value ) {
            foreach ($meta_key_value as $key => $value) {
                $entry_fields[ $key ] = $value;
            }
        }

        if ( $files ) {
            foreach ( $files as $file_input ) {
                $entry_fields[ $file_input['name'] ] = $file_input['value'];
            }
        }

        return $entry_fields;
    }

    public static function prepare_meta_fields( $meta_vars ) {
        // loop through custom fields
        // skip files, put in a key => value paired array for later executation
        // process repeatable fields separately
        // if the input is array type, implode with separator in a field

        $files          = array();
        $meta_key_value = array();
        $multi_repeated = array(); //multi repeated fields will in sotre duplicated meta key

        foreach ($meta_vars as $key => $value) {

            switch ( $value['input_type'] ) {

                // put files in a separate array, we'll process it later
                case 'file_upload':
                case 'image_upload':

                    $files[] = array(
                        'name'  => $value['name'],
                        'value' => isset( $_POST['wpuf_files'][$value['name']] ) ? $_POST['wpuf_files'][$value['name']] : array(),
                        'count' => $value['count']
                    );
                    break;

                case 'repeat':

                    // if it is a multi column repeat field
                    if ( isset( $value['multiple'] ) && $value['multiple'] == 'true' ) {

                        // if there's any items in the array, process it
                        if ( $_POST[$value['name']] ) {

                            $ref_arr = array();
                            $cols    = count( $value['columns'] );
                            $first   = array_shift( array_values( $_POST[$value['name']] ) ); //first element
                            $rows    = count( $first );

                            // loop through columns
                            for ($i = 0; $i < $rows; $i++) {

                                // loop through the rows and store in a temp array
                                $temp = array();
                                for ($j = 0; $j < $cols; $j++) {

                                    $temp[] = $_POST[$value['name']][$j][$i];
                                }

                                // store all fields in a row with WPUF_Render_Form::$separator separated
                                $ref_arr[] = implode( WPUF_Render_Form::$separator, $temp );
                            }

                            // now, if we found anything in $ref_arr, store to $multi_repeated
                            if ( $ref_arr ) {
                                $multi_repeated[$value['name']] = array_slice( $ref_arr, 0, $rows );
                            }
                        }
                    } else {
                        $meta_key_value[$value['name']] = implode( WPUF_Render_Form::$separator, $_POST[$value['name']] );
                    }

                    break;

                case 'address':

                    if ( isset( $_POST[ $value['name'] ] ) && is_array( $_POST[ $value['name'] ] ) ) {
                        foreach ( $_POST[ $value['name'] ] as $address_field => $field_value ) {
                            $meta_key_value[ $value['name'] ][ $address_field ] = sanitize_text_field( $field_value );
                        }
                    }

                    break;

                case 'text':
                case 'email':
                case 'number':
                case 'date':

                    $meta_key_value[$value['name']] = sanitize_text_field( trim( $_POST[$value['name']] ) );

                    break;

                case 'textarea':

                    $meta_key_value[$value['name']] = wp_kses_post( $_POST[$value['name']] );

                    break;

                case 'select':
                case 'radio':

                    $val                            = $_POST[$value['name']];
                    $meta_key_value[$value['name']] = isset( $value['options'][$val] ) ? $value['options'][$val] : '';
                    break;

                case 'multiselect':
                case 'checkbox':

                    $val                            = ( is_array( $_POST[$value['name']] ) && $_POST[$value['name']] ) ? $_POST[$value['name']] : array();
                    $meta_key_value[$value['name']] = $val;

                    if ( $val ) {
                        $new_val = array();

                        foreach ($val as $option_key) {
                            $new_val[] = isset( $value['options'][$option_key] ) ? $value['options'][$option_key] : '';
                        }

                        $meta_key_value[$value['name']] = implode( WPUF_Render_Form::$separator, $new_val );
                    }
                    break;

                default:
                    // if it's an array, implode with this->separator
                    if ( is_array( $_POST[$value['name']] ) ) {

                        if ( $value['input_type'] == 'address' ) {
                            $meta_key_value[$value['name']] = $_POST[$value['name']];
                        } else {
                            $meta_key_value[$value['name']] = implode( WPUF_Render_Form::$separator, $_POST[$value['name']] );
                        }
                    } else {
                        $meta_key_value[$value['name']] = trim( $_POST[$value['name']] );
                    }

                    break;
            }

        } //end foreach

        return array($meta_key_value, $multi_repeated, $files);
    }

    /**
     * Import a form from a JSON file
     *
     * @return void
     */
    public function import_form() {
        check_ajax_referer( 'weforms' );

        $this->check_admin();

        $the_file = isset( $_FILES['importFile'] ) ? $_FILES['importFile'] : false;

        if ( ! $the_file ) {
            wp_send_json_error( __( 'No file found to import.', 'weforms' ) );
        }

        $file_ext  = pathinfo( $the_file['name'], PATHINFO_EXTENSION );

        if ( ! class_exists( 'WeForms_Admin_Tools' ) ) {
            require_once dirname( __FILE__ ) . '/admin/class-admin-tools.php';
        }

        if ( ( $file_ext == 'json' ) && ( $the_file['size'] < 500000 ) ) {

            $status = WeForms_Admin_Tools::import_json_file( $the_file['tmp_name'] );

            if ( $status ) {
                wp_send_json_success( __( 'The forms have been imported successfully!', 'weforms' ) );
            } else {
                wp_send_json_error( __( 'Something went wrong importing the file.', 'weforms' ) );
            }

        } else {
            wp_send_json_error( __( 'Invalid file or file size too big.', 'weforms' ) );
        }
    }

}