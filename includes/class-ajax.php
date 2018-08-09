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

        // read logs
        add_action( 'wp_ajax_weforms_read_logs', array( $this, 'get_logs' ) );
        add_action( 'wp_ajax_weforms_delete_logs', array( $this, 'delete_logs' ) );

        // create from a template
        add_filter( 'wp_ajax_weforms_contact_form_template', array( $this, 'create_form_from_template' ) );

        // form settings
        add_action( 'wp_ajax_weforms_save_settings', array( $this, 'save_settings' ) );
        add_action( 'wp_ajax_weforms_get_settings', array( $this, 'get_settings' ) );

        // import
        add_action( 'wp_ajax_weforms_import_form', array( $this, 'import_form' ) );

        // form editing
        add_action( 'wp_ajax_weforms_get_form', array( $this, 'get_form' ) );
        add_action( 'wp_ajax_wpuf_form_builder_save_form', array( $this, 'save_form' ) );

        // entries
        add_action( 'wp_ajax_weforms_form_entries', array( $this, 'get_entries' ) );

        add_action( 'wp_ajax_weforms_form_entry_details', array( $this, 'get_entry_detail' ) );
        add_action( 'wp_ajax_weforms_form_entry_trash', array( $this, 'trash_entry' ) );
        add_action( 'wp_ajax_weforms_form_entry_delete', array( $this, 'delete_entry' ) );
        add_action( 'wp_ajax_weforms_form_entry_restore', array( $this, 'restore_entry' ) );

        add_action( 'wp_ajax_weforms_form_entry_trash_bulk', array( $this, 'bulk_delete_entry' ) );
        add_action( 'wp_ajax_weforms_form_entry_restore_bulk', array( $this, 'bulk_restore_entry' ) );

        // frontend requests
        add_action( 'wp_ajax_weforms_frontend_submit', array( $this, 'handle_frontend_submission' ) );
        add_action( 'wp_ajax_nopriv_weforms_frontend_submit', array( $this, 'handle_frontend_submission' ) );
    }

    /**
     * Administrator validation
     *
     * @return void
     */
    public function check_admin() {
        if ( ! current_user_can( weforms_form_access_capability() ) ) {
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

        $form = weforms()->form->get( $form_id );

        $data = array(
            'post'          => $form->data,
            'form_fields'   => $form->get_fields(),
            'settings'      => $form->get_settings(),
            'notifications' => $form->get_notifications(),
            'integrations'  => $form->get_integrations()
        );

        wp_send_json_success( $data );
    }

    /**
     * Save the form
     *
     * @return void
     */
    public function save_form() {
        parse_str( $_POST['form_data'], $form_data );

        if ( ! wp_verify_nonce( $form_data['wpuf_form_builder_nonce'], 'wpuf_form_builder_save_form' ) ) {
            wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

        if ( empty( $form_data['wpuf_form_id'] ) ) {
            wp_send_json_error( __( 'Invalid form id', 'weforms' ) );
        }

        $form_fields   = isset( $_POST['form_fields'] ) ? $_POST['form_fields'] : '';
        $notifications = isset( $_POST['notifications'] ) ? $_POST['notifications'] : '';
        $settings      = array();
        $integrations  = array();

        if ( isset( $_POST['settings'] ) ) {
            $settings = (array) json_decode( wp_unslash( $_POST['settings'] ) );
        } else {
            $settings = isset( $form_data['wpuf_settings'] ) ? $form_data['wpuf_settings'] : array();
        }

        if ( isset( $_POST['integrations'] ) ) {
            $integrations = (array) json_decode( wp_unslash( $_POST['integrations'] ) );
        }

        $form_fields   = wp_unslash( $form_fields );
        $notifications = wp_unslash( $notifications );

        $form_fields   = json_decode( $form_fields, true );
        $notifications = json_decode( $notifications, true );

        $data = array(
            'form_id'           => absint( $form_data['wpuf_form_id'] ),
            'post_title'        => sanitize_text_field( $form_data['post_title'] ),
            'form_fields'       => $form_fields,
            'form_settings'     => $settings,
            'form_settings_key' => isset( $form_data['form_settings_key'] ) ? $form_data['form_settings_key'] : '',
            'notifications'     => $notifications,
            'integrations'      => $integrations
        );

        $form_fields = weforms()->form->save( $data );

        wp_send_json_success( array( 'form_fields' => $form_fields ) );
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
            'posts_per_page' => isset( $_POST['posts_per_page'] ) ? intval( $_POST['posts_per_page'] ) : 10,
            'paged'          => isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1,
            'order'          => 'DESC',
            'orderby'        => 'post_date'
        );

        $args = apply_filters( 'weforms_ajax_get_contact_forms_args', $args );

        $contact_forms = weforms()->form->get_forms( $args );

        array_map(
            function( $form ) {
					$form->entries  = $form->num_form_entries();
					$form->views    = $form->num_form_views();
					$form->payments = $form->num_form_payments();
			}, $contact_forms['forms']
        );

        $contact_forms = $this->filter_contact_forms( $contact_forms );
        $contact_forms = apply_filters( 'weforms_ajax_get_contact_forms', $contact_forms );

        wp_send_json_success( $contact_forms );
    }

    /**
     * Filter
     *
     * @return void
     */
    public function filter_contact_forms( &$contact_forms ) {

        if ( isset( $_REQUEST['filter'] ) && $_REQUEST['filter'] == 'entries' ) {
            foreach ( $contact_forms['forms'] as $key => &$form ) {
                if ( isset( $form->entries ) && ! $form->entries ) {
                    unset( $contact_forms['forms'][ $key ] );
                }
            }

            $contact_forms['meta']['total'] = count( $contact_forms['forms'] );
        }

        return $contact_forms;
    }

    /**
     * Get the names of contact forms for generating dropdown
     *
     * @return void
     */
    public function get_contact_form_names() {
        check_ajax_referer( 'weforms' );

        $this->check_admin();

        $contact_forms = weforms()->form->all();
        $response      = array();

        foreach ( $contact_forms['forms'] as $form ) {
            $response[] = array(
                'id'    => $form->get_id(),
                'title' => $form->get_name() . ' (#' . $form->get_id() . ')'
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

        $form_id = weforms()->form->create( $form_name );

        if ( is_wp_error( $form_id ) ) {
            wp_send_json_error( $form_id->get_error_message() );
        }

        wp_send_json_success( array(
            'form_id'   => $form_id,
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

        weforms()->form->delete( $form_id );

        wp_send_json_success();
    }

    public function delete_form_bulk() {
        check_ajax_referer( 'weforms' );

        $this->check_admin();

        $form_ids = isset( $_POST['ids'] ) ? array_map( 'absint', $_POST['ids'] ) : array();

        if ( ! $form_ids ) {
            wp_send_json_error( __( 'No form ids provided!', 'weforms' ) );
        }

        foreach ( $form_ids as $form_id ) {
            weforms()->form->delete( $form_id );
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

        $form = weforms()->form->duplicate( $form_id );

        wp_send_json_success( $form );
    }

    /**
     * Create form from a template
     *
     * @return void
     */
    public function create_form_from_template() {
        check_ajax_referer( 'weforms' );

        $template = isset( $_REQUEST['template'] ) ? sanitize_text_field( $_REQUEST['template'] ) : '';

        $form_id = weforms()->templates->create( $template );

        if ( is_wp_error( $form_id ) ) {
            wp_send_json_error( __( 'Could not create the form', 'weforms' ) );
        }

        wp_send_json_success( array(
            'id' => $form_id
        ) );
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

        if ( isset( $settings['no_conflict'] ) ) {
            $requires_wpuf_update              = true;
            $wpuf_update_array['no_conflict']  = $settings['no_conflict'];
        }

        if ( $requires_wpuf_update ) {
            $wpuf_settings = get_option( 'wpuf_general', array() );

            $wpuf_settings = array_merge( $wpuf_settings, $wpuf_update_array );
            update_option( 'wpuf_general', $wpuf_settings );
        }

        do_action( 'weforms_save_settings', $settings );

        $settings = apply_filters( 'weforms_after_save_settings', $settings );

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

        $settings = weforms_get_settings();

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
        $status       = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : 'publish';
        $per_page     = 20;
        $offset       = ( $current_page - 1 ) * $per_page;

        if ( ! $form_id ) {
            wp_send_json_error( __( 'No form id provided!', 'weforms' ) );
        }

        $entries = weforms_get_form_entries(
            $form_id, array(
				'number' => $per_page,
				'offset' => $offset,
				'status' => $status,
            )
        );

        $columns       = weforms_get_entry_columns( $form_id );
        $total_entries = weforms_count_form_entries( $form_id, $status );

        array_map(
            function( $entry ) use ( $columns ) {
					$entry_id = $entry->id;
					$entry->fields = array();

				foreach ( $columns as $meta_key => $label ) {
					$value                    = weforms_get_entry_meta( $entry_id, $meta_key, true );
					$entry->fields[ $meta_key ] = str_replace( WeForms::$field_separator, ' ', $value );
				}
			}, $entries
        );

        $entries = apply_filters( 'weforms_get_entries', $entries, $form_id );

        $response = array(
            'columns'    => $columns,
            'entries'    => $entries,
            'form_title' => get_post_field( 'post_title', $form_id ),
            'pagination' => array(
                'total'    => $total_entries,
                'per_page' => $per_page,
                'pages'    => ceil( $total_entries / $per_page ),
                'current'  => $current_page
            ),
            'meta' => array(
                'total'      => weforms_count_form_entries( $form_id ),
                'totalTrash' => weforms_count_form_entries( $form_id, 'trash' ),
            ),
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

        $form_id  = isset( $_REQUEST['form_id'] ) ? intval( $_REQUEST['form_id'] ) : 0;
        $entry_id = isset( $_REQUEST['entry_id'] ) ? intval( $_REQUEST['entry_id'] ) : 0;

        $form           = weforms()->form->get( $form_id );
        $form_settings  = $form->get_settings();
        $entry          = $form->entries()->get( $entry_id );
        $fields         = $entry->get_fields();
        $metadata       = $entry->get_metadata();
        $payment        = $entry->get_payment_data();

        if ( isset( $payment->payment_data ) && is_serialized( $payment->payment_data ) ) {
            $payment->payment_data = unserialize( $payment->payment_data );
        }

        if ( false === $fields ) {
            wp_send_json_error( __( 'No form fields found!', 'weforms' ) );
        }

        if ( sizeof( $fields ) < 1 ) {
            $fields[] = array(
				'label' => __( 'No form fields found!', 'weforms' )
			);
        }

        $has_empty          = false;
        $answers            = array();
        $respondentPoints   = isset($form_settings['total_points']) ? floatval( $form_settings['total_points'] ) : 0 ;

        foreach ( $fields as $key => $field ) {

            if ( $form_settings['quiz_form'] == 'yes' ) {
                $selectedAnswers    = isset($field['selected_answers']) ? $field['selected_answers'] : '';
                $givenAnswer        = isset($field['value']) ? $field['value'] : '';
                $options            = isset($field['options']) ? $field['options'] : '';
                $template           = $field['template'];
                $fieldPoints        = isset($field['points']) ? floatval( $field['points'] ) : 0;

                if ( $template == 'radio_field' || $template == 'dropdown_field' ) {
                    $answers[$field['name']] = true;

                    if ( empty($givenAnswer) ) {
                        $answers[$field['name']] = false;
                        $respondentPoints  -= $fieldPoints;
                    }else {
                        foreach ($options as $key => $value) {
                            if ( $givenAnswer == $value ) {
                                if ( $key != $selectedAnswers ) {
                                    $answers[$field['name']] = false;
                                    $respondentPoints  -= $fieldPoints;
                                }
                            }
                        }
                    }
                } elseif ( $template == 'checkbox_field' || $template == 'multiple_select' ) {
                    $answers[$field['name']] = true;
                    $userAnswer = [];

                    foreach ($options as $key => $value) {
                        foreach ($givenAnswer as $answer) {
                            if ($value == $answer) {
                                $userAnswer[] = $key;
                            }
                        }
                    }

                    $userAnswer   = implode('|', $userAnswer);
                    $rightAnswers = implode('|', $selectedAnswers);

                    if ( $userAnswer != $rightAnswers || empty($userAnswer) ) {
                        $answers[$field['name']] = false;
                        $respondentPoints  -= $fieldPoints;
                    }
                }
            } elseif ( empty( $field['value'] ) ) {
				$has_empty      = true;
				break;
            }

        }

        $response = array(
            'form_fields'       => $fields,
            'form_settings'     => $form_settings,
            'meta_data'         => $metadata,
            'payment_data'      => $payment,
            'has_empty'         => $has_empty,
            'respondent_points' => $respondentPoints,
            'answers'           => $answers,
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
     * Trash an entry
     *
     * @return void
     */
    public function delete_entry() {
        check_ajax_referer( 'weforms' );

        $this->check_admin();

        $entry_id = isset( $_REQUEST['entry_id'] ) ? intval( $_REQUEST['entry_id'] ) : 0;

        weforms_delete_entry( $entry_id );

        wp_send_json_success();
    }

    /**
     * Restore Entry
     *
     * @return void
     */
    public function restore_entry() {
        check_ajax_referer( 'weforms' );

        $this->check_admin();

        $entry_id = isset( $_REQUEST['entry_id'] ) ? intval( $_REQUEST['entry_id'] ) : 0;

        weforms_change_entry_status( $entry_id, 'publish' );
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
        $permanent = isset( $_POST['permanent'] ) && ( $_POST['permanent'] ) ? true : false;

        if ( ! $entry_ids ) {
            wp_send_json_error( __( 'No entry ids provided!', 'weforms' ) );
        }

        foreach ( $entry_ids as $entry_id ) {
            if ( $permanent ) {
                weforms_delete_entry( $entry_id );
            } else {
                weforms_change_entry_status( $entry_id, 'trash' );
            }
        }

        wp_send_json_success();
    }

    /**
     * Bulk trash entries
     *
     * @return void
     */
    public function bulk_restore_entry() {
        check_ajax_referer( 'weforms' );

        $this->check_admin();

        $entry_ids = isset( $_POST['ids'] ) ? array_map( 'absint', $_POST['ids'] ) : array();

        if ( ! $entry_ids ) {
            wp_send_json_error( __( 'No entry ids provided!', 'weforms' ) );
        }

        foreach ( $entry_ids as $entry_id ) {
            weforms_change_entry_status( $entry_id, 'publish' );
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

        $form          = weforms()->form->get( $form_id );
        $form_settings = $form->get_settings();
        $form_fields   = $form->get_fields();
        $entry_fields  = $form->prepare_entries();
        $form_entries  = weforms_get_form_entries( $form_id, array( 'number'  => '', 'offset'  => '' ) );

        if ( $form_fields && count( $form_entries ) && count( $entry_fields ) ) {

            foreach ( $entry_fields as $field_key => $field_value ) {
                $duplicate_check = false;
                $field_label = 'This';

                foreach ( $form_fields as $form_field ) {
                    if ( in_array( $form_field['template'], array( 'text_field', 'website_url', 'numeric_text_field', 'email_address' ) ) && $form_field['name'] == $field_key && isset( $form_field['duplicate'] ) && 'no' == $form_field['duplicate'] ) {
                        $duplicate_check = true;
                        $field_label     = $form_field['label'];
                    }
                }

                if ( $duplicate_check ) {

                    foreach ( $form_entries as $entry ) {
                        $existing = weforms_get_entry_meta( $entry->id, $field_key, true );

                        if ( $existing && $field_value == $existing ) {
                            wp_send_json( array(
                                'success' => false,
                                'error'   => sprintf( __( '"%s" field requires a unique entry and "%s" has already been used.', 'weforms' ), $field_label, $field_value )
                            ) );
                        }
                    }
                }
            }
        }

        if ( ! $form_fields ) {
            wp_send_json( array(
                'success'     => false,
                'error'       => __( 'No form field was found.', 'weforms' ),
            ) );
        }

        if ( $form->has_field( 'recaptcha' ) ) {
            $this->validate_reCaptcha();
        }

        // vaidate submission
        $this->validate_submission( $entry_fields, $form, $form_settings, $form_fields );

        $entry_fields = apply_filters( 'weforms_before_entry_submission', $entry_fields, $form, $form_settings, $form_fields );

        $entry_id = weforms_insert_entry( array(
            'form_id' => $form_id
        ), $entry_fields );

        if ( is_wp_error( $entry_id ) ) {
            wp_send_json( array(
                'success' => false,
                'error'   => $entry_id->get_error_message(),
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

        $field_search = $field_replace = array();

        foreach ( $form_fields as $r_field ) {
            $field_search[] = '{'.$r_field['name'].'}';
            if ( $r_field['template'] == 'name_field' ) {
                $field_replace[] = implode( ' ' , explode( '|', $entry_fields[$r_field['name']] ));
            } else {
                $field_replace[] = $entry_fields[$r_field['name']];
            }
        }
        $message = str_replace( $field_search, $field_replace, $form_settings['message'] );

        // send the response
        $response = apply_filters( 'weforms_entry_submission_response', array(
            'success'      => true,
            'redirect_to'  => $redirect_to,
            'show_message' => $show_message,
            'message'      => $message,
            'data'         => $_POST,
            'form_id'      => $form_id,
            'entry_id'     => $entry_id,
        ) );

        $notification = new WeForms_Notification( array(
            'form_id'  => $form_id,
            'page_id'  => $page_id,
            'entry_id' => $entry_id
        ) );

        $notification->send_notifications();

        weforms_clear_buffer();
        wp_send_json( $response );
    }

    /**
     * reCaptcha Validation
     *
     * @return void
     */
    function validate_reCaptcha() {

        if ( class_exists( 'WPUF_ReCaptcha' ) ) {
            $recaptcha_class = 'WPUF_Recaptcha';
        } else {
            require_once WEFORMS_INCLUDES . '/library/reCaptcha/recaptchalib.php';
            require_once WEFORMS_INCLUDES . '/library/reCaptcha/recaptchalib_noCaptcha.php';
            $recaptcha_class = 'Weforms_ReCaptcha';
        }

        $invisible = isset( $_POST['g-recaptcha-response'] ) ? false : true;

        $recaptcha_settings = weforms_get_settings( 'recaptcha' );
        $secret             = isset( $recaptcha_settings->secret ) ? $recaptcha_settings->secret : '';

        if ( ! $invisible ) {

            $response = null;
            $reCaptcha = new $recaptcha_class( $secret );

            $resp = $reCaptcha->verifyResponse(
                $_SERVER['REMOTE_ADDR'],
                $_POST['g-recaptcha-response']
            );

            if ( ! $resp->success ) {
                wp_send_json( array(
                    'success'     => false,
                    'error'       => __( 'reCAPTCHA validation failed', 'weforms' ),
                ) );
            }
		} else {

            $recap_challenge = isset( $_POST['recaptcha_challenge_field'] ) ? $_POST['recaptcha_challenge_field'] : '';
            $recap_response  = isset( $_POST['recaptcha_response_field'] ) ? $_POST['recaptcha_response_field'] : '';

            $resp            = recaptcha_check_answer( $secret, $_SERVER['REMOTE_ADDR'], $recap_challenge, $recap_response );

            if ( ! $resp->is_valid ) {

                ob_clean();

                wp_send_json( array(
                    'success'     => false,
                    'error'       => __( 'reCAPTCHA validation failed', 'weforms' ),
                ) );
            }
        }

    }

    /**
     * Validate submission
     *
     * @param array  $entry_fields
     * @param object $form
     * @param array  $form_settings
     * @param array  $form_fields
     *
     * @return bool|json
     */
    function validate_submission( $entry_fields, $form, $form_settings, $form_fields ) {

        foreach ( $form_fields as $key => $field ) {

            //skip custom html field as it is not saved
            if ( 'custom_html' == $field['name'] )
                continue;

            $value = $entry_fields[ $field['name'] ];

            // if ( isset( $field['required'] ) && $field['required'] && empty( $value ) ) {
            // wp_send_json( array(
            // 'success'     => false,
            // 'error'       => __( sprintf( '%s field is required', $field['label'] ), 'weforms' ),
            // ) );
            // }
            if ( 'single_product' === $field['template'] ) {

                if ( ! $value ) {
                    $value = array();
                }

                $value['price']    = isset( $value['price'] ) ? floatval( $value['price'] ) : 0;
                $value['quantity'] = isset( $value['quantity'] ) ? floatval( $value['quantity'] ) : 0;
                $quantity          = isset( $field['quantity'] ) ? $field['quantity'] : array();
                $price             = isset( $field['price'] ) ? $field['price'] : array();

                if ( isset( $price['is_flexible'] ) && $price['is_flexible'] ) {

                    $min = isset( $price['min'] ) ? floatval( $price['min'] ) : 0;
                    $max = isset( $price['max'] ) ? floatval( $price['max'] ) : 0;

                    if ( $value['price'] < $min ) {

                        wp_send_json( array(
                            'success'     => false,
                            'error'       => __( sprintf(
                                '%s price must be equal or greater than %s',
                                $field['weforms'],
                                $min),
                            'weforms' ),
                        ) );
                    }

                    if ( $max && $value['price'] > $max ) {

                        wp_send_json( array(
                            'success'     => false,
                            'error'       => __( sprintf(
                                '%s price must be equal or less than %s',
                                $field['weforms'],
                                $max),
                            'weforms' ),
                        ) );
                    }
                }

                if ( isset( $quantity['status'] ) && $quantity['status'] ) {

                    $min = isset( $quantity['min'] ) ? floatval( $quantity['min'] ) : 0;
                    $max = isset( $quantity['max'] ) ? floatval( $quantity['max'] ) : 0;

                    if ( $value['quantity'] < $min ) {

                        wp_send_json( array(
                            'success'     => false,
                            'error'       => __( sprintf(
                                '%s quantity must be equal or greater than %s',
                                $field['weforms'],
                                $min),
                            'weforms' ),
                        ) );
                    }

                    if ( $max && $value['quantity'] > $max ) {

                        wp_send_json( array(
                            'success'     => false,
                            'error'       => __( sprintf(
                                '%s quantity must be equal or less than %s',
                                $field['weforms'],
                                $max),
                            'weforms' ),
                        ) );
                    }
                }
			}
        }
    }

    public static function prepare_meta_fields( $meta_vars ) {
        // loop through custom fields
        // skip files, put in a key => value paired array for later executation
        // process repeatable fields separately
        // if the input is array type, implode with separator in a field
        $files          = array();
        $meta_key_value = array();
        $multi_repeated = array(); // multi repeated fields will in sotre duplicated meta key

        foreach ( $meta_vars as $key => $value ) {

            switch ( $value['template'] ) {

                // put files in a separate array, we'll process it later
                case 'file_upload':
                case 'image_upload':
                    $files[] = array(
                        'name'  => $value['name'],
                        'value' => isset( $_POST['wpuf_files'][ $value['name'] ] ) ? $_POST['wpuf_files'][ $value['name'] ] : array(),
                        'count' => $value['count']
                    );
                    break;

                case 'repeat_field':
                    // if it is a multi column repeat field
                    if ( isset( $value['multiple'] ) && $value['multiple'] == 'true' ) {

                        // if there's any items in the array, process it
                        if ( $_POST[ $value['name'] ] ) {

                            $ref_arr = array();
                            $cols    = count( $value['columns'] );
                            $first   = array_shift( array_values( $_POST[ $value['name'] ] ) ); // first element
                            $rows    = count( $first );

                            // loop through columns
                            for ( $i = 0; $i < $rows; $i++ ) {

                                // loop through the rows and store in a temp array
                                $temp = array();
                                for ( $j = 0; $j < $cols; $j++ ) {

                                    $temp[] = $_POST[ $value['name'] ][ $j ][ $i ];
                                }

                                // store all fields in a row with WeForms::$field_separator separated
                                $ref_arr[] = implode( WeForms::$field_separator, $temp );
                            }

                            // now, if we found anything in $ref_arr, store to $multi_repeated
                            if ( $ref_arr ) {
                                $multi_repeated[ $value['name'] ] = array_slice( $ref_arr, 0, $rows );
                            }
                        }
                    } else {
                        $meta_key_value[ $value['name'] ] = implode( WeForms::$field_separator, $_POST[ $value['name'] ] );
                    }

                    break;

                case 'address_field':
                    if ( isset( $_POST[ $value['name'] ] ) && is_array( $_POST[ $value['name'] ] ) ) {
                        foreach ( $_POST[ $value['name'] ] as $address_field => $field_value ) {
                            $meta_key_value[ $value['name'] ][ $address_field ] = sanitize_text_field( $field_value );
                        }
                    }

                    break;

                case 'text_field':
                case 'email_address':
                case 'numeric_text_field':
                case 'date_field':
                    $meta_key_value[ $value['name'] ] = sanitize_text_field( trim( $_POST[ $value['name'] ] ) );

                    break;

                case 'textarea_field':
                    $meta_key_value[ $value['name'] ] = wp_kses_post( $_POST[ $value['name'] ] );

                    break;

                case 'dropdown_field':
                case 'radio_field':
                    $val                            = $_POST[ $value['name'] ];
                    $meta_key_value[ $value['name'] ] = isset( $value['options'][ $val ] ) ? $value['options'][ $val ] : '';
                    break;

                case 'multiple_select':
                case 'checkbox_field':
                    $val                            = ( is_array( $_POST[ $value['name'] ] ) && $_POST[ $value['name'] ] ) ? $_POST[ $value['name'] ] : array();
                    $meta_key_value[ $value['name'] ] = $val;

                    if ( $val ) {
                        $new_val = array();

                        foreach ( $val as $option_key ) {
                            $new_val[] = isset( $value['options'][ $option_key ] ) ? $value['options'][ $option_key ] : '';
                        }

                        $meta_key_value[ $value['name'] ] = implode( WeForms::$field_separator, $new_val );
                    }
                    break;

                default:
                    // if it's an array, implode with this->separator
                    if ( is_array( $_POST[ $value['name'] ] ) ) {

                        $meta_key_value[ $value['name'] ] = implode( WeForms::$field_separator, $_POST[ $value['name'] ] );

                    } else {
                        $meta_key_value[ $value['name'] ] = trim( $_POST[ $value['name'] ] );
                    }

                    break;
            }
		} //end foreach

        return array( $meta_key_value, $multi_repeated, $files );
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

    /**
     * Read Log file
     *
     * @return json
     **/
    function get_logs() {

        check_ajax_referer( 'weforms' );

        $this->check_admin();

        $file = weforms_log_file_path();

        if ( ! file_exists( $file ) ) {
			return;
        }

        $data = file_get_contents( $file );
        $data = explode( "\n", $data );
        $data = array_reverse( $data );
        $data = array_filter( $data );

        if ( empty( $data ) ) {
			return;
        }

        $logs = array();

        foreach ( $data as $key => $row ) {

            preg_match( '/\[(?<time>.+?)\]\[(?<type>.+?)\](?<message>.+)/im', $row, $log );

            if ( empty( $log['message'] ) ) {
                $log = array();
                $log['message'] = ! empty( $log['message'] ) ? $log['message'] : $row;
            }

            if ( ! empty( $log['time'] ) ) {
				$human_time = human_time_diff( strtotime( $log['time'] ) , current_time( 'timestamp' ) );
				$log['time'] = $human_time ? $human_time . ' ' . __( 'ago', 'weforms' ) : $log['time'];
            }

            $logs[] = $log;
        }

        wp_send_json_success( $logs );
    }

    /**
     * Read Log file
     *
     * @return json
     **/
    function delete_logs() {

        check_ajax_referer( 'weforms' );

        $this->check_admin();

        $file = weforms_log_file_path();

        if ( ! file_exists( $file ) ) {
			return;
        }

        @unlink( $file );

        wp_send_json_success();
    }

}
