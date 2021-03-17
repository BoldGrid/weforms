<?php

/**
 * Form field manager class
 *
 * @since 1.1.0
 */
class WeForms_Field_Manager {

    /**
     * The fields
     *
     * @var array
     */
    private $fields = [];

    /**
     * Going to store WeForms_Notification instance for tmp. Will refactor
     *
     * @var array
     */
    private $notification = null;

    /**
     * Get all the registered fields
     *
     * @return array
     */
    public function get_fields() {
        if ( !empty( $this->fields ) ) {
            return $this->fields;
        }

        $this->register_field_types();

        return $this->fields;
    }

    /**
     * Register the field types
     *
     * @return void
     */
    private function register_field_types() {
        require_once __DIR__ . '/fields/class-abstract-fields.php';
        require_once __DIR__ . '/fields/class-field-text.php';
        require_once __DIR__ . '/fields/class-field-name.php';
        require_once __DIR__ . '/fields/class-field-email.php';
        require_once __DIR__ . '/fields/class-field-textarea.php';
        require_once __DIR__ . '/fields/class-field-column.php';
        require_once __DIR__ . '/fields/class-field-checkbox.php';
        require_once __DIR__ . '/fields/class-field-radio.php';
        require_once __DIR__ . '/fields/class-field-dropdown.php';
        require_once __DIR__ . '/fields/class-field-multidropdown.php';
        require_once __DIR__ . '/fields/class-field-url.php';
        require_once __DIR__ . '/fields/class-field-sectionbreak.php';
        require_once __DIR__ . '/fields/class-field-html.php';
        require_once __DIR__ . '/fields/class-field-hidden.php';
        require_once __DIR__ . '/fields/class-field-image.php';
        require_once __DIR__ . '/fields/class-field-recaptcha.php';
        require_once __DIR__ . '/fields/class-field-humanpresence.php';
        require_once __DIR__ . '/fields/class-field-date.php';

        $fields = [
            'text_field'          => new WeForms_Form_Field_Text(),
            'name_field'          => new WeForms_Form_Field_Name(),
            'date_field'          => new WeForms_Form_Field_Date_Free(),
            'email_address'       => new WeForms_Form_Field_Email(),
            'textarea_field'      => new WeForms_Form_Field_Textarea(),
            'radio_field'         => new WeForms_Form_Field_Radio(),
            'checkbox_field'      => new WeForms_Form_Field_Checkbox(),
            'column_field'        => new WeForms_Form_Field_Column(),
            'dropdown_field'      => new WeForms_Form_Field_Dropdown(),
            'multiple_select'     => new WeForms_Form_Field_MultiDropdown(),
            'website_url'         => new WeForms_Form_Field_URL(),
            'section_break'       => new WeForms_Form_Field_SectionBreak(),
            'custom_html'         => new WeForms_Form_Field_HTML(),
            'custom_hidden_field' => new WeForms_Form_Field_Hidden(),
            'image_upload'        => new WeForms_Form_Field_Image(),
            'recaptcha'           => new WeForms_Form_Field_reCaptcha(),
            'humanpresence'       => new WeForms_Form_Field_HumanPresence(),
        ];

        $this->fields = apply_filters( 'weforms_form_fields', $fields );
    }

    /**
     * Get field groups
     *
     * @return array
     */
    public function get_field_groups() {
        $groups = [
            [
                'title'  => __( 'Custom Fields', 'weforms' ),
                'id'     => 'custom-fields',
                'fields' => apply_filters( 'weforms_field_groups_custom',
                    [
                        'name_field',
                        'text_field',
                        'textarea_field',
                        'dropdown_field',
                        'multiple_select',
                        'radio_field',
                        'checkbox_field',
                        'website_url',
                        'email_address',
                        'custom_hidden_field',
                        'image_upload',
                    ]
                  ),
            ],

            [
                'title'  => __( 'Others', 'weforms' ),
                'id'     => 'others',
                'fields' => apply_filters( 'weforms_field_groups_others',
                    [
                        'column_field',
                        'section_break',
                        'custom_html',
                        'recaptcha',
                        'humanpresence'
                    ]
                  ),
            ],
        ];

        return apply_filters( 'weforms_field_groups', $groups );
    }

    /**
     * Get fields JS setting for the form builder
     *
     * @return array
     */
    public function get_js_settings() {
        $fields   = $this->get_fields();
        $js_array = [];

        if ( $fields ) {
            foreach ( $fields as $type => $object ) {
                $js_array[ $type ] = $object->get_js_settings();
            }
        }

        return $js_array;
    }

    /**
     * Render the form fields
     *
     * @param int   $form_id
     * @param array $fields
     * @param array $atts
     *
     * @return void
     */
    public function render_fields( $fields, $form_id, $atts = [] ) {
        if ( !$fields ) {
            return;
        }

        $this->notification = new WeForms_Notification();
        $this->notification->set_merge_tags();

        $fields = apply_filters( 'weforms_render_fields', $fields, $form_id );

        foreach ( $fields as $field ) {
            if ( !$field_object = $this->field_exists( $field['template'] ) ) {
                if ( defined( 'WP_DEBUG' && WP_DEBUG ) ) {
                    echo '<h4 style="color: red;"><em>' . esc_attr( $field['template'] ). '</em> field not found.</h4>';
                }

                continue;
            }

            $field = $this->dynamic_fields( $field, $form_id, $atts );
            $field = $this->replace_tags( $field, $form_id, $atts );
            $field_object->render( $field, $form_id );
            $field_object->conditional_logic( $field, $form_id );
        }
    }

    /**
     * Filter dynamic fields
     *
     * @param array $form_field
     * @param int   $form_id
     *
     * @return void
     */
    public function dynamic_fields( $form_field, $form_id, $atts = [] ) {
        if ( !isset( $form_field['dynamic'] ) || empty( $form_field['dynamic']['status'] ) || empty( $form_field['dynamic']['param_name'] ) ) {
            return $form_field;
        }


        $param_name = $form_field['dynamic']['param_name'];

        if ( isset( $form_field['options'] ) ) {
            $form_field['options'] = apply_filters( 'weforms_field_options_' . $param_name, $form_field['options'], $form_id );

            if ( isset(  $_GET[ $param_name ] ) && is_array( $_GET[ $param_name ]  ) ) {
                $form_field['default'] = array_merge( $form_field['options'], sanitize_text_field( wp_unslash( $_GET[ $param_name ] ) ) );
            }
        }

        foreach ( [ 'default', 'selected' ] as $key => $default_key ) {
            if ( isset( $form_field[ $default_key ] ) ) {
                $form_field[ $default_key ] = apply_filters( 'weforms_field_default_value_' . $param_name, $form_field[ $default_key ], $form_id );

                if ( isset( $atts[ $param_name ] ) ) {
                    $form_field[ $default_key ] = $atts[ $param_name ];
                }

                if ( isset( $_GET[ $param_name ] ) ) {
                    if ( is_array( $form_field[ $default_key ] ) == is_array( sanitize_text_field( wp_unslash( $_GET[ $param_name ] ) ) ) ) {
                        $form_field[ $default_key ] = sanitize_text_field( wp_unslash( $_GET[ $param_name ] ) );
                    }

                    if ( ! is_array( $form_field[ $default_key ] ) == ! is_array( sanitize_text_field( wp_unslash( $_GET[ $param_name ] ) ) ) ) {
                        $form_field[ $default_key ] = sanitize_text_field( wp_unslash( $_GET[ $param_name ] ) );
                    }
                }
            }
        }

        return $form_field;
    }

    /**
     * Replace merge tags
     *
     * @param array $form_field
     * @param int   $form_id
     *
     * @return void
     */
    public function replace_tags( $form_field, $form_id, $atts = [] ) {
        $fields = [
            'default',
            'placeholder',
            'first_name'  => [ 'placeholder', 'default' ],
            'middle_name' => [ 'placeholder', 'default' ],
            'last_name'   => [ 'placeholder', 'default' ],
        ];

        foreach ( $fields as $key => $field ) {
            if ( is_array( $field ) ) {
                foreach ( $field as $k => $sub_field ) {
                    if ( !empty( $form_field[ $key ] ) && !empty( $form_field[ $key ][ $sub_field ] ) ) {
                        $form_field[ $key ][ $sub_field ] = $this->notification->replace_tags( $form_field[ $key ][ $sub_field ] );
                    }
                }
            } else {
                if ( !empty( $form_field[ $field ] ) && is_string( $form_field[ $field ] ) ) {
                    $form_field[ $field ] = $this->notification->replace_tags( $form_field[ $field ] );
                }
            }
        }

        return $form_field;
    }

    /**
     * Check if a field exists
     *
     * @param string $field_type
     *
     * @return bool
     */
    public function field_exists( $field_type ) {
        if ( array_key_exists( $field_type, $this->get_fields() ) ) {
            return $this->fields[ $field_type ];
        }

        return false;
    }
}
