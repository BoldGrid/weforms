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
    private $fields = array();

    /**
     * Get all the registered fields
     *
     * @return array
     */
    public function get_fields() {

        if ( ! empty( $this->fields ) ) {
            return $this->fields;
        }

        $this->register_field_types();

        return $this->fields;
    }

    /**
     * Get field groups
     *
     * @return array
     */
    public function get_field_groups() {

        $groups = array(
            array(
                'title'  => __( 'Custom Fields', 'weforms' ),
                'id'     => 'custom-fields',
                'fields' => apply_filters( 'weforms_field_groups_custom', array(
                    'name_field', 'text_field', 'textarea_field', 'dropdown_field', 'multiple_select',
                    'radio_field', 'checkbox_field', 'website_url', 'email_address',
                    'custom_hidden_field', 'image_upload'
                ) )
            ),

            array(
                'title'  => __( 'Others', 'weforms' ),
                'id'     => 'others',
                'fields' => apply_filters( 'weforms_field_groups_others', array(
                    'section_break', 'custom_html', 'recaptcha'
                ) )
            )
        );

        return apply_filters( 'weforms-field-groups', $groups );
    }

    /**
     * Get fields JS setting for the form builder
     *
     * @return array
     */
    public function get_js_settings() {
        $fields   = $this->get_fields();
        $js_array = array();

        if ( $fields ) {
            foreach ($fields as $type => $object) {
                $js_array[ $type ] = $object->get_js_settings();
            }
        }

        return $js_array;
    }

    /**
     * Register the field types
     *
     * @return void
     */
    private function register_field_types() {
        require_once dirname( __FILE__ ) . '/fields/class-abstract-fields.php';

        require_once dirname( __FILE__ ) . '/fields/class-field-text.php';
        require_once dirname( __FILE__ ) . '/fields/class-field-name.php';
        require_once dirname( __FILE__ ) . '/fields/class-field-email.php';

        require_once dirname( __FILE__ ) . '/fields/class-field-textarea.php';

        require_once dirname( __FILE__ ) . '/fields/class-field-checkbox.php';
        require_once dirname( __FILE__ ) . '/fields/class-field-radio.php';
        require_once dirname( __FILE__ ) . '/fields/class-field-dropdown.php';
        require_once dirname( __FILE__ ) . '/fields/class-field-multidropdown.php';
        require_once dirname( __FILE__ ) . '/fields/class-field-url.php';
        require_once dirname( __FILE__ ) . '/fields/class-field-sectionbreak.php';
        require_once dirname( __FILE__ ) . '/fields/class-field-html.php';
        require_once dirname( __FILE__ ) . '/fields/class-field-hidden.php';
        require_once dirname( __FILE__ ) . '/fields/class-field-image.php';
        require_once dirname( __FILE__ ) . '/fields/class-field-recaptcha.php';

        $fields = array(
            'text_field'          => new WeForms_Form_Field_Text(),
            'name_field'          => new WeForms_Form_Field_Name(),
            'email_address'       => new WeForms_Form_Field_Email(),
            'textarea_field'      => new WeForms_Form_Field_Textarea(),
            'radio_field'         => new WeForms_Form_Field_Radio(),
            'checkbox_field'      => new WeForms_Form_Field_Checkbox(),
            'dropdown_field'      => new WeForms_Form_Field_Dropdown(),
            'multiple_select'     => new WeForms_Form_Field_MultiDropdown(),
            'website_url'         => new WeForms_Form_Field_URL(),
            'section_break'       => new WeForms_Form_Field_SectionBreak(),
            'custom_html'         => new WeForms_Form_Field_HTML(),
            'custom_hidden_field' => new WeForms_Form_Field_Hidden(),
            'image_upload'        => new WeForms_Form_Field_Image(),
            'recaptcha'           => new WeForms_Form_Field_reCaptcha(),
        );

        $this->fields = apply_filters( 'weforms_form_fields', $fields );
    }

    /**
     * Render the form fields
     *
     * @param  integer $form_id
     * @param  array $fields
     *
     * @return void
     */
    public function render_fields( $fields, $form_id ) {
        if ( ! $fields ) {
            return;
        }

        foreach ($fields as $field) {
            if ( ! $field_object = $this->field_exists( $field['template'] ) ) {
                echo '<h4 style="color: red;"><em>' . $field['template'] . '</em> field not found.</h4>';
                continue;
            }

            $field_object->render( $field, $form_id );
            $field_object->conditional_logic( $field, $form_id );
        }
    }

    /**
     * Check if a field exists
     *
     * @param  string $field_type
     *
     * @return boolean
     */
    public function field_exists( $field_type ) {
        if ( array_key_exists( $field_type, $this->get_fields() ) ) {
            return $this->fields[ $field_type ];
        }

        return false;
    }
}