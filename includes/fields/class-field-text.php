<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_Text extends WeForms_Field_Contract {

    function __construct() {
        $this->name       = __( 'Text', 'weforms' );
        $this->input_type = 'text_field';
        $this->icon       = 'text-width';
    }

    /**
     * Render the text field
     *
     * @param  integer  $form_id
     * @param  array  $field_settings
     * @param  string  $type
     * @param  integer $data_id
     *
     * @return void
     */
    public function render( $form_id, $field_settings, $type = '', $data_id = 0 ) {
        echo 'rendering text field';
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        $default_options      = $this->get_default_option_settings();
        $default_text_options = $this->get_default_text_option_settings();

        return array_merge( $default_options, $default_text_options );
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults = $this->default_attributes();
        $props    = array(
            'word_restriction' => '',
        );

        return array_merge( $defaults, $props );
    }
}