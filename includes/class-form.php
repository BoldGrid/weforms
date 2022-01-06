<?php

/**
 * The Form Class
 *
 * @since 1.1.0
 */
class WeForms_Form {

    /**
     * The form id
     *
     * @var int
     */
    public $id = 0;

    /**
     * The form title
     *
     * @var string
     */
    public $name = '';

    /**
     * Holds the post data object
     *
     * @var WP_post|null
     */
    public $data = null;

    /**
     * Form fields
     *
     * @var array
     */
    public $form_fields = [];

    /**
     * The Constructor
     *
     * @param int|WP_Post $form
     */
    public function __construct( $form = null ) {
        if ( is_numeric( $form ) ) {
            $the_post = get_post( $form );

            if ( $the_post ) {
                $this->id   = $the_post->ID;
                $this->name = $the_post->post_title;
                $this->data = $the_post;
            }
        } elseif ( is_a( $form, 'WP_Post' ) ) {
            $this->id   = $form->ID;
            $this->name = $form->post_title;
            $this->data = $form;
        }
    }

    /**
     * Get the form ID
     *
     * @return int
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Get the form name
     *
     * @return string
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Get all form fields of this form
     *
     * @return array
     */
    public function get_fields() {

        // return if already fetched
        if ( $this->form_fields ) {
            return $this->form_fields;
        }

        $fields = get_children( [
            'post_parent' => $this->id,
            'post_status' => 'publish',
            'post_type'   => 'wpuf_input',
            'numberposts' => '-1',
            'orderby'     => 'menu_order',
            'order'       => 'ASC',
        ] );

        $form_fields = [];

        foreach ( $fields as $key => $content ) {
            $field = maybe_unserialize( $content->post_content );

            if ( empty( $field['template']  ) ) {
                continue;
            }

            $field['id'] = $content->ID;

            // Add inline property for radio and checkbox fields
            $inline_supported_fields = apply_filters( 'inline_supported_fields_list', [ 'radio_field', 'checkbox_field' ] );

            if ( in_array( $field['template'], $inline_supported_fields ) ) {
                if ( !isset( $field['inline'] ) ) {
                    $field['inline'] = 'no';
                }
            }

            // Add 'selected' property
            $option_based_fields = apply_filters( 'option_based_fields_list', [ 'dropdown_field', 'multiple_select', 'radio_field', 'checkbox_field' ] );

            if ( in_array( $field['template'], $option_based_fields ) ) {
                if ( !isset( $field['selected'] ) ) {
                    if ( 'dropdown_field' === $field['template'] || 'radio_field' === $field['template'] ) {
                        $field['selected'] = '';
                    } else {
                        $field['selected'] = [];
                    }
                }
            }

            // Add 'multiple' key for template:repeat
            if ( 'repeat_field' === $field['template'] && !isset( $field['multiple'] ) ) {
                $field['multiple'] = '';
            }

            if ( 'recaptcha' === $field['template'] ) {
                $field['name']              = 'recaptcha';
                $field['enable_no_captcha'] = isset( $field['enable_no_captcha'] ) ? $field['enable_no_captcha'] : '';
                $field['recaptcha_theme']   = isset( $field['recaptcha_theme'] ) ? $field['recaptcha_theme'] : 'light';
            }

            // Check if meta_key has changed when saving form compared current entries
            if ( isset( $field['name'] ) ) {
                $field['original_name'] = $field['name'];
            }
            $form_fields[] = apply_filters( 'weforms-get-form-field', $field, $this->id );
        }

        $this->form_fields = apply_filters( 'weforms-get-form-fields', $form_fields, $this->id );

        return $this->form_fields;
    }

    /**
     * Check if any perticular field template is used in a form
     *
     * @return array
     */
    public function has_field( $field_template ) {
        foreach ( $this->get_fields() as $key => $field ) {
            if ( isset( $field['template'] ) && $field['template'] == $field_template ) {
                return true;
            }
        }
    }

    /**
     * Get all fields by a template
     *
     * @return array
     */
    public function search_fields( $field_template ) {
        $fields = [];

        foreach ( $this->get_fields() as $key => $field ) {
            if ( isset( $field['template'] ) && $field['template'] == $field_template ) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    /**
     * Search fileds and get the first one
     *
     * @return array
     */
    public function search_field( $field_template ) {
        $fields = $this->search_fields( $field_template );

        return isset( $fields[0] ) ? $fields[0] : [];
    }

    /**
     * Get formatted field name/values
     *
     * @return array
     */
    public function get_field_values() {
        $values = [];
        $fields = $this->get_fields();

        if ( !$fields ) {
            return $values;
        }

        $ignore_fields  = apply_filters( 'ignore_fields_list', [ 'recaptcha', 'section_break' ] );
        $options_fields = apply_filters( 'option_fields_list', [ 'dropdown_field', 'radio_field', 'multiple_select', 'checkbox_field' ] );

        foreach ( $fields as $field ) {
            if ( in_array( $field['template'], $ignore_fields ) ) {
                continue;
            }

            // Prepare column field data
            if ( $field['template'] == 'column_field' ) {
                $inner_columns = $field['inner_fields'];

                if ( !empty( $inner_columns ) ) {
                    foreach ( $inner_columns as $column => $column_fields ) {
                        if ( !empty( $column_fields ) ) {
                            foreach ( $column_fields as $field_obj ) {
                                if ( in_array( $field_obj['template'], $ignore_fields ) ) {
                                    continue;
                                }

                                if ( !isset( $field_obj['name'] ) ) {
                                    continue;
                                }

                                $field_value = [
                                    'label' => isset( $field_obj['label'] ) ? $field_obj['label'] : '',
                                    'type'  => $field_obj['template'],
                                ];

                                // put options if this is an option field
                                if ( in_array( $field_obj['template'], $options_fields ) ) {
                                    $field_value['options'] = $field_obj['options'];
                                }

                                $values[ $field_obj['name'] ] = array_merge( $field_obj, $field_value );
                            }
                        }
                    }
                }
            }

            if ( !isset( $field['name'] ) ) {
                continue;
            }

            $value = [
                'label' => isset( $field['label'] ) ? $field['label'] : '',
                'type'  => $field['template'],
            ];

            // put options if this is an option field
            if ( in_array( $field['template'], $options_fields ) ) {
                $value['options'] = $field['options'];
            }

            $values[ $field['name'] ] = array_merge( $field, $value );
        }

        return apply_filters( 'weforms_get_field_values', $values );
    }

    /**
     * Get form settings
     *
     * @return array
     */
    public function get_settings() {
        $settings = get_post_meta( $this->id, 'wpuf_form_settings', true );
        $default  = weforms_get_default_form_settings();

        return apply_filters( 'weforms-get-form-settings', array_merge( $default, $settings ), $this->id );
    }

    /**
     * Check if the form submission is open
     *
     * @return bool|WP_Error
     */
    public function is_submission_open() {
        $settings = $this->get_settings();

        $needs_login  = ( isset( $settings['require_login'] ) && $settings['require_login'] == 'true' ) ? true : false;
        $has_limit    = ( isset( $settings['limit_entries'] ) && $settings['limit_entries'] == 'true' ) ? true : false;
        $is_scheduled = ( isset( $settings['schedule_form'] ) && $settings['schedule_form'] == 'true' ) ? true : false;

        if ( $this->data->post_status != 'publish' ) {
            return new WP_Error( 'needs-publish', __( 'The form is not published yet.', 'weforms' ) );
        }

        if ( $needs_login && !is_user_logged_in() ) {
            return new WP_Error( 'needs-login', $settings['req_login_message'] );
        }

        if ( $has_limit ) {
            $limit        = (int) $settings['limit_number'];
            $form_entries = $this->num_form_entries();

            if ( $limit <= $form_entries ) {
                return new WP_Error( 'entry-limit', $settings['limit_message'] );
            }
        }

        if ( $is_scheduled ) {
            $start_time   = strtotime( $settings['schedule_start'] );
            $end_time     = strtotime( $settings['schedule_end'] );
            $current_time = current_time( 'timestamp' );

            // too early?
            if ( $current_time < $start_time ) {
                return new WP_Error( 'form-pending', $settings['sc_pending_message'] );
            } elseif ( $current_time > $end_time ) {
                return new WP_Error( 'form-expired', $settings['sc_expired_message'] );
            }
        }

        return apply_filters( 'weforms_is_submission_open', true, $settings, $this );
    }

    /**
     * Get form notifications
     *
     * @return array
     */
    public function get_notifications() {
        $notifications =  get_post_meta( $this->id, 'notifications', true );
        $defualt       = weforms_get_default_form_notification();

        if ( !$notifications ) {
            $notifications = [];
        }

        $notifications = array_map( function ( $notification ) use ( $defualt ) {
            if ( empty( $notification ) ) {
                $notification = [];
            }

            return array_merge( $defualt, $notification );
        }, $notifications );

        return $notifications;
    }

    /**
     * Get all the integrations
     *
     * @return array
     */
    public function get_integrations() {
        $integrations =  get_post_meta( $this->id, 'integrations', true );

        if ( !$integrations ) {
            return [];
        }

        return $integrations;
    }

    /**
     * Get entries of this form
     *
     * @return \WeForms_Form_Entry_Manager
     */
    public function entries() {
        return new WeForms_Form_Entry_Manager( $this->id, $this );
    }

    /**
     * When a user is editing their form they may change a fields name.
     * This method will loop through existing entries to match the new field names.
     *
     * @since 1.6.9
     *
     * @param int $form_id
     * @param array $form_fields
     */
    public function maybe_update_entries( $form_fields ) {
        $changed_fields = $this->get_changed_fields( $form_fields );
        // Loop through changed fields and update entries
        foreach ( $changed_fields as $old => $new) {
            $updated_fields = $this->rename_field( $old, $new );
        }
     }

    /**
     * When a user is editing their form they may change a fields name.
     * This method will loop through all fields that have changed.
     *
     * @since 1.6.9
     *
     * @param int $form_id
     * @param array $form_fields
     *
     * @return array
     */
    public function get_changed_fields( $form_fields ) {
        $changed_fields = array();
        foreach ( $form_fields as $field ) {
            $org_field = $field['original_name'];
            // All form fields should have an original name.
            if ( empty( $field['original_name'] ) ) {
                continue;
            }
            if ( $field['name'] !== $field['original_name'] ) {
                $changed_fields[$field['original_name']] = $field['name'];
            } else {
                continue;
            }
        }
        return $changed_fields;

    }

    /**
     * When a user changes the field names of a form, the existing entries will need updated.
     * This method will loop through the existing entries and update them will the new names.
     *
     * @since 1.6.9
     *
     * @param int $form_id
     * @param array $form_fields
     *
     * @return array
     */
    public function rename_field ( $old, $new ) {
        global $wpdb;

        $entries  = weforms_get_form_entries( $this->id );

        foreach ( $entries as $entry ) {
            $entry_id    = $entry->id;
            $values      = weforms_get_entry_meta( $entry_id );
            $update_keys = $wpdb->update( $wpdb->weforms_entrymeta, array( 'meta_key' => $new ), array( 'meta_key' => $old, 'weforms_entry_id' => $entry_id ) );
        }
    }

    /**
     * Get number of form entries
     *
     * @return int
     */
    public function num_form_entries() {
        return weforms_count_form_entries( $this->id );
    }

    /**
     * Get number of form payments
     *
     * @return int
     */
    public function num_form_payments() {
        return weforms_count_form_payments( $this->id );
    }

    /**
     * Get form author details
     *
     * @return array
     */
    public function get_form_author_details() {
        return weforms_get_form_author_details( $this->id );
    }

    /**
     * Get the number of form views
     *
     * @return int
     */
    public function num_form_views() {
        return weforms_get_form_views( $this->id );
    }

    /**
     * prepare_entries
     *
     * @return array
     */
    public function prepare_entries( $args = [] ) {
        $fields       = weforms()->fields->get_fields();
        $form_fields  = $this->get_fields();

        $entry_fields = [];

        $ignore_list  = apply_filters( 'wefroms_entry_ignore_list', [
            'recaptcha', 'section_break', 'step_start',
        ] );

        foreach ( $form_fields as $field ) {
            if ( in_array( $field['template'], $ignore_list ) ) {
                continue;
            }

            if ( !array_key_exists( $field['template'], $fields ) ) {
                continue;
            }

            // Prepare column field data
            if ( $field['template'] == 'column_field' ) {
                $inner_columns = $field['inner_fields'];

                if ( !empty( $inner_columns ) ) {
                    foreach ( $inner_columns as $column => $column_fields ) {
                        if ( !empty( $column_fields ) ) {
                            foreach ( $column_fields as $field_obj ) {
                                if ( in_array( $field_obj['template'], $ignore_list ) ) {
                                    continue;
                                }

                                $fieldClass                         = $fields[ $field_obj['template'] ];
                                $entry_fields[ $field_obj['name'] ] = $fieldClass->prepare_entry( $field_obj );
                            }
                        }
                    }
                }
            }

            $field_class = $fields[ $field['template'] ];

            $entry_fields[ $field['name'] ] = $field_class->prepare_entry( $field, $args );
        }

        return apply_filters( 'weforms_prepare_entries', $entry_fields );
    }

    /**
     * Check if the form submission is open On API
     *
     * @return bool|WP_Error
     */
    public function is_api_form_submission_open() {
        $response            = [];
        $settings            = $this->get_settings();
        $form_entries        = $this->num_form_entries();
        $needs_login         = isset( $settings['require_login'] ) ? $settings['require_login'] : 'false';
        $limit_entries       = isset( $settings['limit_entries'] ) ? $settings['limit_entries'] : 'false';
        $schedule_form       = isset( $settings['schedule_form'] ) ? $settings['schedule_form'] : 'false';
        $limit_number        = isset( $settings['limit_number'] ) ? $settings['limit_number'] : '';
        $schedule_start      = isset( $settings['schedule_start'] ) ? $settings['schedule_start'] : '';
        $schedule_end        = isset( $settings['schedule_end'] ) ? $settings['schedule_end'] : '';
        $schedule_start_date = date_i18n( 'M d, Y', strtotime( $schedule_start ) );
        $schedule_end_date   = date_i18n( 'M d, Y', strtotime( $schedule_end ) );

        if ( $this->data->post_status != 'publish' ) {
            $response['type'] = __( 'Closed', 'weforms' );
        }

        if ( $this->isFormStatusClosed( $settings, $form_entries ) ) {
            $response['type'] = __( 'Closed', 'weforms' );
        } else {
            $response['type'] = __( 'Open', 'weforms' );
        }

        if (  $limit_entries === 'true' ) {
            if ( $schedule_form === 'true' && $this->isExpiredForm( $schedule_end ) ) {
                $response['message'] = __( "Expired at {$schedule_end_date}", 'weforms' );
            } elseif ( $schedule_form === 'true' && $this->isPendingForm( $schedule_start ) ) {
                $response['message'] = __( "Starts at {$schedule_start_date}", 'weforms' );
            } elseif ( $form_entries >= $limit_number ) {
                $response['message'] = __( 'Reached maximum entry limit', 'weforms' );
            } else {
                $remaining_entries   = $limit_number - $form_entries;
                $response['message'] = __( "{$remaining_entries} entries remaining ", 'weforms' );
            }
        } elseif ( $schedule_form === 'true' ) {
            if ( $this->isPendingForm( $schedule_start ) ) {
                $response['message'] = __( "Starts at {$schedule_start_date}", 'weforms' );
            } elseif ( $this->isExpiredForm( $schedule_end ) ) {
                $response['message'] = __( "Expired at {$schedule_end_date}", 'weforms' );
            } elseif ( $this->isOpenForm( $schedule_start, $schedule_end ) ) {
                $response['message'] = __( "Expires at {$schedule_end_date}", 'weforms' );
            }
        } elseif ( $needs_login === 'true' ) {
            $response['message'] =__( 'Requires login', 'weforms' );
        }

        return apply_filters( 'weforms_is_submission_open', $response, $settings, $this );
    }

    public function isPendingForm( $scheduleStart ) {
        $currentTime = current_time( 'timestamp' );
        $startTime   = strtotime( $scheduleStart );

        if ( $currentTime < $startTime ) {
            return true;
        }

        return false;
    }

    public function isExpiredForm( $scheduleEnd ) {
        $currentTime = current_time( 'timestamp' );
        $endTime     = strtotime( $scheduleEnd );

        if ( $currentTime > $endTime ) {
            return true;
        }

        return false;
    }

    public function isOpenForm( $scheduleStart, $scheduleEnd ) {
        $currentTime = current_time( 'timestamp' );
        $startTime   = strtotime( $scheduleStart );
        $endTime     = strtotime( $scheduleEnd );

        if ( $currentTime > $startTime && $currentTime < $endTime ) {
            return true;
        }

        return false;
    }

    public function isFormStatusClosed( $formSettings, $entries ) {
        if ( $formSettings['schedule_form'] === 'true' && $this->isPendingForm( $formSettings['schedule_start'] ) ) {
            return true;
        }

        if ( $formSettings['schedule_form'] === 'true' && $this->isExpiredForm( $formSettings['schedule_end'] ) ) {
            return true;
        }

        if ( $formSettings['limit_entries'] === 'true' && $entries >= $formSettings['limit_number'] ) {
            return true;
        }

        return false;
    }
}
