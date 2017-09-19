<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_HTML extends WeForms_Field_Contract {

    function __construct() {
        $this->name       = __( 'Custom HTML', 'weforms' );
        $this->input_type = 'custom_html';
        $this->icon       = 'code';
    }

    /**
     * Render the text field
     *
     * @param  array  $field_settings
     * @param  integer  $form_id
     *
     * @return void
     */
    public function render( $field_settings, $form_id ) {
        ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>

            <div class="wpuf-fields <?php echo 'html_' . $form_id; ?>">
                <?php echo $field_settings['html']; ?>
            </div>

        </li>
        <?php
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