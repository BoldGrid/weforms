<?php

/**
 * Contact Form 7
 *
 * Import contact form 7 forms
 */
class WeForms_Importer_GF extends WeForms_Importer_Abstract {

    function __construct() {
        $this->id = 'gf';
        $this->title     = 'Gravity Form';
        $this->shortcode = 'gravityform';

        parent::__construct();
    }

    /**
     * See if the plugin exists
     *
     * @return boolean
     */
    public function plugin_exists() {
        return class_exists( 'GFForms' );
    }

    /**
     * Get all forms
     *
     * @since  1.0.0
     *
     * @return [type]
     */
    public function get_forms() {
        return GFAPI::get_forms();
    }

    /**
     * Get form title
     *
     * @since  1.0.0
     *
     * @param  array $form
     *
     * @return string
     */
    public function get_form_name( $form ) {
        return $form['title'];
    }

    /**
     * Mapping all form fileds
     *
     * @since  1.0.0
     *
     * @param  array $form
     *
     * @return array
     */
    public function get_form_fields( $form ) {
        $form_fields = array();

        $form_tags  = $form['fields'];

        if ( ! $form_tags ) {
            return;
        }

        foreach ( $form_tags as $menu_order => $field ) {

            switch ( $field->type ) {

                case 'text':
                case 'email':
                case 'date':
                case 'url':

                    $form_fields[] = $this->get_form_field( $field->type, array(
                        'required'    => $field->isRequired ? 'yes' : 'no',
                        'label'       => $field->label,
                        'name'        => $this->get_meta_key( $field ),
                        'css_class'   => $field->cssClass,
                        'help'        => $field->description,
                        'placeholder' => $field->placeholder,
                    ) );
                    break;

                case 'textarea':

                    $form_fields[] = array(
                        'input_type'  => 'textarea',
                        'template'    => 'textarea_field',
                        'required'    => $field->isRequired ? 'yes' : 'no',
                        'label'       => $field->label,
                        'name'        => $this->get_meta_key( $field ),
                        'css'         => $field->cssClass,
                        'help'        => $field->description,
                        'placeholder' => $field->placeholder,
                        'is_meta'     => 'yes',
                        'rows'        => 5,
                        'cols'        => 25,
                        'default'     => $field->defaultValue,
                        'rich'        => $field->useRichTextEditor ? 'yes' : 'no',
                        'wpuf_cond'   => $this->conditionals
                    );
                    break;

                case 'select':
                case 'radio':
                case 'checkbox':
                    $form_fields[] = $this->get_form_field( $field->type, array(
                        'required'  => $field->isRequired ? 'yes' : 'no',
                        'label'     => $field->label,
                        'name'      => $this->get_meta_key( $field ),
                        'css_class' => $field->cssClass,
                        'help'      => $field->description,
                        'options'   => $this->get_options( $field ),
                    ) );
                    break;


                case 'multiselect':
                    $form_fields[] = array(
                        'input_type' => 'multiselect',
                        'template'   => 'multiple_select',
                        'required'   => $field->isRequired ? 'yes' : 'no',
                        'label'      => $field->label,
                        'name'       => $this->get_meta_key( $field ),
                        'is_meta'    => 'yes',
                        'css'        => $field->cssClass,
                        'help'       => $field->description,
                        'selected'   => array(),
                        'options'    => $this->get_options( $field ),
                        'wpuf_cond'  => $this->conditionals
                    );
                    break;

                case 'number':
                    $form_fields[] = $this->get_form_field( $field->type, array(
                        'required'  => $field->isRequired ? 'yes' : 'no',
                        'label'     => $field->label,
                        'name'      => $this->get_meta_key( $field ),
                        'css_class' => $field->cssClass,
                        'help'      => $field->description,
                        'options'   => $this->get_options( $field ),
                        'value'     => $field->defaultValue,
                        'min'       => $field->rangeMin,
                        'max'       => $field->rangeMax,
                    ) );
                    break;

                 case 'address':
                    $form_fields[] = array(
                        'input_type' => 'address',
                        'template'   => 'address_field',
                        'required'   => $field->isRequired ? 'yes' : 'no',
                        'label'      => $field->label,
                        'name'       => $this->get_meta_key( $field ),
                        'is_meta'    => 'yes',
                        'css_class'  => $field->cssClass,
                        'help'       => $field->description,
                        'address'    => $this->get_address_field_data( $field )
                    );
                    break;

                case 'fileupload':
                    $extenion_type = array();
                    $file_ext = explode( ',', $field->allowedExtensions );

                    foreach( $file_ext as $ext ) {
                        $allowed_ext = $this->get_file_type( $ext );
                        if ( $allowed_ext ) {
                            $extenion_type[] = $data;
                        }
                    }

                    $form_fields[] = $this->get_form_field( 'file', array(
                        'required'  => $field->isRequired ? 'yes' : 'no',
                        'label'     => $field->label,
                        'name'      => $this->get_meta_key( $field ),
                        'css_class' => $field->cssClass,
                        'help'      => $field->description,
                        'max_size'  => !empty( $field->maxFileSize ) ? $field->maxFileSize * 1024 : 1024,
                        'extension' => array_unique( $extenion_type ),
                    ) );
                    break;

                case 'hidden':
                    $form_fields[] = array(
                        'input_type' => 'hidden',
                        'template'   => 'custom_hidden_field',
                        'label'      => $field->label,
                        'name'       => $this->get_meta_key( $field ),
                        'is_meta'    => 'yes',
                        'meta_value' => $field->defaultValue,
                        'wpuf_cond'  => ''
                    );

                    break;

                case 'name':
                    if ( $this->get_name_format( $field ) ) {
                        $form_fields[] = array(
                            'input_type' => 'name',
                            'template'   => 'name_field',
                            'required'   => $field->isRequired ? 'yes' : 'no',
                            'label'      => $field->label,
                            'name'       => $this->get_meta_key( $field ),
                            'is_meta'    => 'yes',
                            'css'        => $field->cssClass,
                            'wpuf_cond'  => $this->conditionals,
                            'format'     => $this->get_name_format( $field ),
                            'first_name' => array(
                                'sub'         => isset( $field->inputs[1]['label'] ) ? $field->inputs[1]['label'] : '',
                                'default'     => isset( $field->inputs[1]['defaultValue'] ) ? $field->inputs[1]['defaultValue'] : '',
                                'placeholder' => isset( $field->inputs[1]['placeholder'] ) ? $field->inputs[1]['placeholder'] : ''
                            ),
                            'middle_name' => array(
                                'sub'         => isset( $field->inputs[2]['label'] ) ? $field->inputs[2]['label'] : '',
                                'default'     => isset( $field->inputs[2]['defaultValue'] ) ? $field->inputs[2]['defaultValue'] : '',
                                'placeholder' => isset( $field->inputs[2]['placeholder'] ) ? $field->inputs[2]['placeholder'] : ''
                            ),
                            'last_name' => array(
                                'sub'         => isset( $field->inputs[3]['label'] ) ? $field->inputs[3]['label'] : '',
                                'default'     => isset( $field->inputs[3]['defaultValue'] ) ? $field->inputs[3]['defaultValue'] : '',
                                'placeholder' => isset( $field->inputs[3]['placeholder'] ) ? $field->inputs[3]['placeholder'] : ''
                            ),
                            'hide_subs'  => ''
                        );
                    }
                    break;

                case 'captcha':
                    $form_fields[] = $this->get_form_field( 'recaptcha', array(
                        'required'  => $field->isRequired ? 'yes' : 'no',
                        'label'     => $field->label,
                        'name'      => $this->get_meta_key( $field ),
                        'css_class' => $field->cssClass,
                    ) );
                    break;
            }
        }

        return $form_fields;
    }

    /**
     * Mapping form settings
     *
     * @since  1.0.0
     *
     * @param  array $form
     *
     * @return array
     */
    public function get_form_settings( $form ) {
        $default_settings = $this->get_default_form_settings();
        $start_date       = '';
        $end_date         = '';

        if (  isset( $form['scheduleForm'] ) && $form['scheduleForm'] ) {
            $start_time =  sprintf( "%02d", $form['scheduleStartHour'] ) . ':' . sprintf( "%02d", $form['scheduleStartMinute'] ) . ' ' . $form['scheduleStartAmpm'];
            $end_time   = sprintf( "%02d", $form['scheduleEndHour'] ) . ':' . sprintf( "%02d", $form['scheduleEndMinute'] ) . ' ' . $form['scheduleEndAmpm'];
            $start_date = date( 'Y-m-d H:i:s', strtotime( $form['scheduleStart'] . $start_time ) );
            $end_date   = date( 'Y-m-d H:i:s', strtotime( $form['scheduleEnd'] . $end_time ) );
        }

        $form_label_position = ! empty( $form['labelPlacement'] ) ? $form['labelPlacement'] : 'top_label';

        $form_settings = array(
            'redirect_to'        => 'same',
            'message'            => __( 'Thanks for contacting us! We will get in touch with you shortly.', 'weforms' ),
            'page_id'            => '',
            'url'                => '',
            'submit_text'        => ! empty( $form['button']['text'] ) ? $form['button']['text'] : __( 'Submit Query', 'weforms' ),
            'schedule_form'      => ( isset( $form['scheduleForm'] ) && $form['scheduleForm'] )  ? 'true' : 'false',
            'schedule_start'     => $start_date,
            'schedule_end'       => $end_date,
            'sc_pending_message' => ! empty( $form['schedulePendingMessage'] ) ? $form['schedulePendingMessage'] : '',
            'sc_expired_message' => ! empty( $form['scheduleMessage'] ) ? $form['scheduleMessage'] : '' ,
            'require_login'      => ! empty( $form['requireLogin'] ) ? 'true' : 'false',
            'req_login_message'  => ! empty( $form['requireLoginMessage'] ) ? $form['requireLoginMessage'] : '',
            'limit_entries'      => ! empty( $form['limitEntries'] ) ? 'true' : 'false',
            'limit_number'       => ! empty( $form['limitEntriesCount'] ) ? $form['limitEntriesCount'] : '',
            'limit_message'      => ! empty( $form['limitEntriesMessage'] ) ? $form['limitEntriesMessage'] : '',
            'label_position'     => $this->get_label_position( $form_label_position ),
        );

        $settings = wp_parse_args( $form_settings, $default_settings );

        return $settings;
    }

    /**
     * Mapping form notification settings
     *
     * @since  1.0.0
     *
     * @param  array $form
     *
     * @return array
     */
    public function get_form_notifications( $form ) {
        $notifications = array();

        if ( empty( $form['notifications'] ) ) {
            return $notifications;
        }

        foreach ( $form['notifications'] as $key => $notification ) {
            $notifications[] = array(
                'active'      => ( isset( $notification['isActive'] ) && $notification['isActive'] ) ? 'true' : 'false',
                'name'        => $notification['name'],
                'subject'     => str_replace( '[your-subject]', '{field:your-subject}', $notification['subject'] ),
                'to'          => $notification['to'],
                'replyTo'     => '{field:your-email}',
                'message'     => '{all_fields}',
                'fromName'    => '{site_name}',
                'fromAddress' => '{admin_email}',
                'cc'          => '',
                'bcc'         => '',
            );
        }

        return $notifications;
    }

    /**
     * Get the form id
     *
     * @param  mixed $form
     *
     * @return int
     */
    protected function get_form_id( $form ) {
        return $form['id'];
    }

    /**
    * Get unique meta key for field name
    *
    * @since 1.0.0
    *
    * @return string [slug format]
    **/
    public function get_meta_key( $field ) {
        return str_replace( '-', '_', sanitize_title( $field->label . '-' . $field->id ) );
    }

    /**
    * Get selecte, checkbox, multiselect and radio field options value
    *
    * @since 1.0.0
    *
    * @return void
    **/
    public function get_options( $field ) {
        if ( empty( $field['choices'] ) ) {
            return array();
        }

        return wp_list_pluck( $field['choices'], 'text', 'value' );
    }

    /**
    * Get address field data
    *
    * @since 1.0.0
    *
    * @return void
    **/
    public function get_address_field_data( $field ) {
        $address = array();
        $address_data_key = array( 'street_address', 'street_address2', 'city_name', 'state', 'zip', 'country_select' );

        foreach ( $address_data_key as $key => $value ) {
            $address[$value] = array(
                'checked'     => ( isset( $field->inputs[$key]['isHidden'] ) && $field->inputs[$key]['isHidden'] ) ? '' : 'checked',
                'type'        => ( 'country_select' == $value ) ? 'select' : 'text',
                'required'    => $field->isRequired ? 'checked' : '',
                'label'       => ! empty( $field->inputs[$key]['customLabel'] ) ? $field->inputs[$key]['customLabel'] : $field->inputs[$key]['label'],
                'value'       => ! empty( $field->inputs[$key]['defaultValue'] ) ? $field->inputs[$key]['defaultValue'] : '',
                'placeholder' => ! empty( $field->inputs[$key]['placeholder'] ) ? $field->inputs[$key]['placeholder'] : ''
            );

            if ( 'country_select' == $value ) {
                $address[$value]['country_list_visibility_opt_name'] = 'all';
                $address[$value]['country_select_hide_list'] = array();
                $address[$value]['country_select_show_list'] = array();
            }
        }

        return $address;
    }

    /**
     * Get file type for upload files
     *
     * @param  string $extension
     *
     * @return boolean|string
     */
    private function get_file_type( $extension ) {
        $allowed_extensions = weforms_allowed_extensions();

        foreach ($allowed_extensions as $type => $extensions) {
            $_extensions = explode( ',', $extensions['ext'] );

            if ( in_array( $extension, $_extensions ) ) {
                return $type;
            }
        }

        return false;
    }

    /**
    * Get name format
    *
    * @since 1.0.0
    *
    * @return void
    **/
    private function get_name_format( $field ) {
        if ( ! isset( $field->inputs[1]['isHidden'] ) && ! isset( $field->inputs[2]['isHidden'] ) && ! isset( $field->inputs[3]['isHidden'] ) ) {
            return 'first-middle-last';
        } else if ( ! isset( $field->inputs[1]['isHidden'] ) && ! isset( $field->inputs[3]['isHidden'] ) ) {
            return 'first-last';
        } else {
            return false;
        }
    }

    /**
    * Get label placement position
    *
    * @since 1.0.0
    *
    * @return void
    **/
    public function get_label_position( $label ) {
        $labels = array(
            'top_label' => 'above',
            'left_label' => 'left',
            'right_label' => 'right'
        );

        return $labels[$label];
    }

}