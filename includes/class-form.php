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
     * @var integer
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
     * @var null|WP_post
     */
    public $data = null;

    /**
     * Form fields
     *
     * @var array
     */
    public $form_fields = array();

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
     * @return integer
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

        $fields = get_children(array(
            'post_parent' => $this->id,
            'post_status' => 'publish',
            'post_type'   => 'wpuf_input',
            'numberposts' => '-1',
            'orderby'     => 'menu_order',
            'order'       => 'ASC',
        ));

        $form_fields = array();

        foreach ( $fields as $key => $content ) {

            $field = maybe_unserialize( $content->post_content );

            if ( empty( $field['template']  ) ) {
                continue;
            }


            $field['id'] = $content->ID;

            // Add inline property for radio and checkbox fields
            $inline_supported_fields = apply_filters( 'inline_supported_fields_list', array( 'radio_field', 'checkbox_field' ) );
            if ( in_array( $field['template'] , $inline_supported_fields ) ) {
                if ( ! isset( $field['inline'] ) ) {
                    $field['inline'] = 'no';
                }
            }

            // Add 'selected' property
            $option_based_fields = apply_filters( 'option_based_fields_list', array( 'dropdown_field', 'multiple_select', 'radio_field', 'checkbox_field' ) );
            if ( in_array( $field['template'] , $option_based_fields ) ) {
                if ( ! isset( $field['selected'] ) ) {

                    if ( 'dropdown_field' === $field['template'] || 'radio_field' === $field['template'] ) {
                        $field['selected'] = '';
                    } else {
                        $field['selected'] = array();
                    }

                }
            }

            // Add 'multiple' key for template:repeat
            if ( 'repeat_field' === $field['template'] && ! isset( $field['multiple'] ) ) {
                $field['multiple'] = '';
            }

            if ( 'recaptcha' === $field['template'] ) {
                $field['name']              = 'recaptcha';
                $field['enable_no_captcha'] = isset( $field['enable_no_captcha'] ) ? $field['enable_no_captcha'] : '';
                $field['recaptcha_theme']   = isset( $field['recaptcha_theme'] ) ? $field['recaptcha_theme'] : 'light';
            }

            $form_fields[] = apply_filters( 'weforms-get-form-field', $field );
        }

        $this->form_fields = apply_filters( 'weforms-get-form-fields', $form_fields );

        return $this->form_fields;
    }

    /**
     * Check if any perticular field template is used in a form
     *
     * @return array
     */
    public function has_field( $field_template ) {

        foreach ( $this->get_fields() as $key => $field) {
            if ( isset( $field['template'] ) && $field['template'] == $field_template) {
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

        $fields = array();

        foreach ( $this->get_fields() as $key => $field) {
            if ( isset( $field['template'] ) && $field['template'] == $field_template) {
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

        $fields = $this->search_fields($field_template);

        return isset($fields[0]) ? $fields[0] : array();
    }

    /**
     * Get formatted field name/values
     *
     * @return array
     */
    public function get_field_values() {
        $values = array();
        $fields = $this->get_fields();

        if ( ! $fields ) {
            return $values;
        }

        $ignore_fields  = apply_filters( 'ignore_fields_list', array( 'recaptcha' ) );
        $options_fields = apply_filters( 'option_fields_list', array( 'dropdown_field', 'radio_field', 'multiple_select', 'checkbox_field' ) );

        foreach ($fields as $field) {

            if ( in_array( $field['template'], $ignore_fields ) ) {
                continue;
            }

            if ( ! isset( $field['name'] ) ) {
                continue;
            }

            $value = array(
                'label' => isset( $field['label'] ) ? $field['label'] : '',
                'type'  => $field['template'],
            );

            // put options if this is an option field
            if ( in_array( $field['template'], $options_fields ) ) {
                $value['options'] = $field['options'];
            }

            $values[ $field['name'] ] = array_merge( $field, $value);
        }

        return apply_filters( 'weforms_get_field_values', $values );;
    }

    /**
     * Get form settings
     *
     * @return array
     */
    public function get_settings() {

        $settings = get_post_meta( $this->id, 'wpuf_form_settings', true );
        $default  = weforms_get_default_form_settings();

        return array_merge( $default, $settings);
    }

    /**
     * Check if the form submission is open
     *
     * @return boolean|WP_Error
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

            if ( $limit < $form_entries ) {
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

        return apply_filters( 'weforms_is_submission_open', true, $settings, $this);
    }

    /**
     * Get form notifications
     *
     * @return array
     */
    public function get_notifications() {
        $notifications =  get_post_meta( $this->id, 'notifications', true );
        $defualt       = weforms_get_default_form_notification();

        if ( ! $notifications ) {
            $notifications = array();
        }

        $notifications = array_map( function( $notification ) use ( $defualt ) {
            return array_merge( $defualt, $notification);
        }, $notifications);

        return $notifications;
    }

    /**
     * Get all the integrations
     *
     * @return array
     */
    public function get_integrations() {
        $integrations =  get_post_meta( $this->id, 'integrations', true );

        if ( ! $integrations ) {
            return array();
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
     * Get number of form entries
     *
     * @return integer
     */
    public function num_form_entries() {
        return weforms_count_form_entries( $this->id );
    }

    /**
     * Get number of form payments
     *
     * @return integer
     */
    public function num_form_payments() {
        return weforms_count_form_payments( $this->id );
    }

    /**
     * Get the number of form views
     *
     * @return integer
     */
    public function num_form_views() {
        return weforms_get_form_views( $this->id );
    }


    /**
     * prepare_entries
     *
     * @return array
     */
    public function prepare_entries() {

        $fields       = weforms()->fields->get_fields();
        $form_fields  = $this->get_fields();

        $entry_fields = array();

        $ignore_list  = apply_filters('wefroms_entry_ignore_list', array(
            'recaptcha'
        ) );

        foreach ($form_fields as $field) {

            if ( in_array( $field['template'], $ignore_list ) ) {
                continue;
            }

            if ( ! array_key_exists( $field['template'], $fields ) ) {
                continue;
            }

            $field_class = $fields[ $field['template'] ];

            $entry_fields[ $field['name'] ] = $field_class->prepare_entry( $field );
        }

        return apply_filters( 'weforms_prepare_entries', $entry_fields );
    }
}
