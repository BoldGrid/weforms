<?php

/**
 * Importer Abstract Class
 */
abstract class WeForms_Importer_Abstract {

    /**
     * The importer ID
     *
     * @var string
     */
    public $id = '';

    /**
     * The ajax action string
     *
     * @var string
     */
    protected $action = '';

    /**
     * Conditional stub
     *
     * @var array
     */
    public $conditionals = array(
        'condition_status' => 'no',
        'cond_field'       => array(),
        'cond_operator'    => array( '=' ),
        'cond_option'      => array( '- select -' ),
        'cond_logic'       => 'all'
    );

    abstract protected function get_forms();
    abstract protected function get_form_name( $form );
    abstract protected function get_form_fields( $form );
    abstract protected function get_form_settings( $form );
    abstract protected function get_form_notifications( $form );

    /**
     * Import forms
     *
     * @return void
     */
    public function import_forms() {
        $forms = $this->get_forms();

        if ( $forms ) {
            foreach ($forms as $form) {
                $form_name     = $this->get_form_name( $form );
                $form_fields   = $this->get_form_fields( $form );
                $settings      = $this->get_form_settings( $form );
                $notifications = $this->get_form_notifications( $form );

                if ( $form_fields ) {
                    $form_id = $this->insert_form( $form_name );

                    foreach ($form_fields as $menu_order => $form_field) {
                        $this->insert_form_field( $form_id, $form_field, $menu_order );
                    }

                    $this->update_settings( $form_id, $settings );
                    $this->update_notification( $form_id, $notifications );
                }
            }
        }
    }

    /**
     * Check capability if able to process
     *
     * @return void
     */
    public function check_caps() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You are not allowed.', 'weforms' ) );
        }
    }

    /**
     * If the prompt is dismissed
     *
     * @return boolean
     */
    public function is_dimissed() {
        return 'yes' == get_option( 'weforms_dismiss_notice_' . $this->id );
    }

    /**
     * Dismiss the prompt
     *
     * @return void
     */
    public function dismiss_prompt() {
        update_option( 'weforms_dismiss_notice_' . $this->id, 'yes' );
    }

    /**
     * Dismiss the notice
     *
     * @return void
     */
    public function dismiss_notice() {
        $this->dismiss_prompt();

        wp_send_json_success();
    }


    public function get_form_field( $type, $args = array() ) {
        $defaults = array(
            'required'    => 'no',
            'label'       => '',
            'name'        => '',
            'help'        => '',
            'css_class'   => '',
            'placeholder' => '',
            'value'       => '',
            'default'     => '',
            'options'     => array(),
            'step'        => '',
            'min'         => '',
            'max'         => '',
            'extension'   => '',
            'max_size'    => '', // file size
        );

        $args = wp_parse_args( $args, $defaults );

        switch ( $type ) {
            case 'text':
                $field_content = array(
                    'input_type'       => 'text',
                    'template'         => 'text_field',
                    'required'         => $args['required'],
                    'label'            => $args['label'],
                    'name'             => $args['name'],
                    'is_meta'          => 'yes',
                    'help'             => $args['help'],
                    'css'              => $args['css_class'],
                    'placeholder'      => $args['placeholder'],
                    'default'          => $args['default'],
                    'size'             => $args['size'],
                    'word_restriction' => '',
                    'wpuf_cond'        => $this->conditionals
                );
                break;

            case 'email':
                $field_content = array(
                    'input_type'       => 'text',
                    'template'         => 'email_address',
                    'required'         => $args['required'],
                    'label'            => $args['label'],
                    'name'             => $args['name'],
                    'is_meta'          => 'yes',
                    'help'             => $args['help'],
                    'css'              => $args['css_class'],
                    'placeholder'      => $args['placeholder'],
                    'default'          => $args['default'],
                    'size'             => $args['size'],
                    'word_restriction' => '',
                    'wpuf_cond'        => $this->conditionals
                );
                break;

            case 'textarea':
                $field_content = array(
                    'input_type'       => 'textarea',
                    'template'         => 'textarea_field',
                    'required'         => $args['required'],
                    'label'            => $args['label'],
                    'name'             => $args['name'],
                    'is_meta'          => 'yes',
                    'help'             => $args['help'],
                    'css'              => $args['css_class'],
                    'rows'             => 5,
                    'cols'             => 25,
                    'placeholder'      => $args['placeholder'],
                    'default'          => $args['default'],
                    'rich'             => 'no',
                    'word_restriction' => '',
                    'wpuf_cond'        => $this->conditionals
                );
                break;

            case 'select':
                $field_content = array(
                    'input_type' => 'select',
                    'template'   => 'dropdown_field',
                    'required'   => $args['required'],
                    'label'      => $args['label'],
                    'name'       => $args['name'],
                    'is_meta'    => 'yes',
                    'help'       => '',
                    'css'        => $args['css_class'],
                    'selected'   => '',
                    'inline'     => 'no',
                    'options'    => $args['options'],
                    'wpuf_cond'  => $this->conditionals
                );
                break;

            case 'date':
                $field_content = array(
                    'input_type'      => 'date',
                    'template'        => 'date_field',
                    'required'         => $args['required'],
                    'label'            => $args['label'],
                    'name'             => $args['name'],
                    'is_meta'         => 'yes',
                    'help'            => '',
                    'css'             => $args['css_class'],
                    'format'          => 'dd/mm/yy',
                    'time'            => '',
                    'is_publish_time' => '',
                    'wpuf_cond'       => $this->conditionals
                );
                break;

            case 'range':
            case 'number':

                $field_content = array(
                    'input_type'      => 'numeric_text',
                    'template'        => 'numeric_text_field',
                    'required'        => $args['required'],
                    'label'           => $args['label'],
                    'name'            => $args['name'],
                    'is_meta'         => 'yes',
                    'help'            => '',
                    'css'             => $args['css_class'],
                    'placeholder'     => $args['placeholder'],
                    'default'         => $args['value'],
                    'size'            => 40,
                    'step_text_field' => $args['step'],
                    'min_value_field' => $args['min'],
                    'max_value_field' => $args['max'],
                    'wpuf_cond'       => $this->conditionals
                );

                break;

            case 'url':
                $field_content = array(
                    'input_type'       => 'text',
                    'template'         => 'website_url',
                    'required'         => $args['required'],
                    'label'            => $args['label'],
                    'name'             => $args['name'],
                    'is_meta'          => 'yes',
                    'help'             => '',
                    'css'              => $args['css_class'],
                    'placeholder'      => '',
                    'default'          => '',
                    'size'             => 40,
                    'word_restriction' => '',
                    'wpuf_cond'        => $this->conditionals
                );
                break;

            case 'range':
                # code...
                break;

            case 'checkbox':
                $field_content = array(
                    'input_type' => 'checkbox',
                    'template'   => 'checkbox_field',
                    'required'   => $args['required'],
                    'label'      => $args['label'],
                    'name'       => $args['name'],
                    'is_meta'    => 'yes',
                    'help'       => '',
                    'css'        => $args['css_class'],
                    'selected'   => '',
                    'inline'     => 'no',
                    'options'    => $args['options'],
                    'wpuf_cond'  => $this->conditionals
                );
                break;

            case 'radio':
                $field_content = array(
                    'input_type' => 'radio',
                    'template'   => 'radio_field',
                    'required'   => $args['required'],
                    'label'      => $args['label'],
                    'name'       => $args['name'],
                    'is_meta'    => 'yes',
                    'help'       => '',
                    'css'        => $args['css_class'],
                    'selected'   => '',
                    'inline'     => 'no',
                    'options'    => $args['options'],
                    'wpuf_cond'  => $this->conditionals
                );
                break;

            case 'toc':
                $field_content = array(
                    'input_type'       => 'toc',
                    'template'         => 'toc',
                    'required'         => $args['required'],
                    'name'             => $args['name'],
                    'description'      => $args['label'],
                    'name'             => '',
                    'is_meta'          => 'yes',
                    'show_checkbox'    => true,
                    'wpuf_cond'        => $this->conditionals
                );
                break;

            case 'recaptcha':
                $field_content = array(
                    'input_type'       => 'recaptcha',
                    'template'         => 'recaptcha',
                    'required'         => $args['required'],
                    'label'            => $args['label'],
                    'name'             => $args['name'],
                    'recaptcha_type'   => 'enable_no_captcha',
                    'is_meta'          => 'yes',
                    'help'             => '',
                    'css'             => $args['css_class'],
                    'placeholder'      => '',
                    'default'          => '',
                    'size'             => 40,
                    'word_restriction' => '',
                    'wpuf_cond'        => $this->conditionals
                );
                break;

            case 'file':
                $field_content = array(
                    'input_type' => 'file_upload',
                    'template'   => 'file_upload',
                    'required'   => $args['required'],
                    'label'      => $args['label'],
                    'name'       => $args['name'],
                    'is_meta'    => 'yes',
                    'help'       => '',
                    'css'        => $args['css_class'],
                    'max_size'   => $args['max_size'],
                    'count'      => '1',
                    'extension'  => $args['extension'],
                    'wpuf_cond'  => $this->conditionals
                );
                break;
        }

        return $field_content;
    }

    /**
     * Default form settings
     *
     * @return array
     */
    public function get_default_form_settings() {
        $form_settings = array(
            'redirect_to'        => 'same',
            'message'            => __( 'Thanks for contacting us! We will get in touch with you shortly.', 'weforms' ),
            'page_id'            => '',
            'url'                => '',
            'submit_text'        => __( 'Submit Query', 'weforms' ),
            'schedule_form'      => 'false',
            'schedule_start'     => '',
            'schedule_end'       => '',
            'sc_pending_message' => __( 'Form submission hasn\'t been started yet', 'weforms' ),
            'sc_expired_message' => __( 'Form submission is now closed.', 'weforms' ),
            'require_login'      => 'false',
            'req_login_message'  => __( 'You need to login to submit a query.', 'weforms' ),
            'limit_entries'      => 'false',
            'limit_number'       => '1000',
            'limit_message'      => __( 'Sorry, we have reached the maximum number of submissions.', 'weforms' ),
            'label_position'     => 'above',
        );

        return $form_settings;
    }

    /**
     * Insert a form
     *
     * @param  string $form_name
     *
     * @return ID|WP_Error
     */
    public function insert_form( $form_name ) {
        $weforms_form = array(
            'post_title'  => sprintf( '[%s] %s', strtoupper( $this->id ), $form_name ),
            'post_type'   => 'wpuf_contact_form',
            'post_status' => 'publish',
            'post_author' => get_current_user_id()
        );

        return wp_insert_post( $weforms_form );
    }

    /**
     * Insert a form field
     *
     * @param  array $field
     * @param  int $form_id
     * @param  int $menu_order
     *
     * @return int|WP_Error
     */
    public function insert_form_field( $field, $form_id, $menu_order ) {
        $form_field = array(
            'post_type'    => 'wpuf_input',
            'post_status'  => 'publish',
            'post_content' => maybe_serialize( $field ),
            'post_parent'  => $form_id,
            'menu_order'   => $menu_order
        );

        return wp_insert_post( $form_field );
    }

    /**
     * Update form settings
     *
     * @param  int $form_id
     * @param  array $settings
     *
     * @return void
     */
    public function update_settings( $form_id, $settings ) {
        update_post_meta( $form_id, 'wpuf_form_settings', $settings );
    }

    /**
     * Update Notification
     *
     * @param  int $form_id
     * @param  array $notification
     *
     * @return void
     */
    public function update_notification( $form_id, $notification ) {
        update_post_meta( $form_id, 'notifications', $notifications );
    }
}
