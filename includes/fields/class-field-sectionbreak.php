<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_SectionBreak extends WeForms_Field_Contract {

    function __construct() {
        $this->name       = __( 'Section Break', 'weforms' );
        $this->input_type = 'section_break';
        $this->icon       = 'columns';
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
            <div class="wpuf-section-wrap <?php echo 'section_' . $form_id; ?>">
                <h2 class="wpuf-section-title"><?php echo $field_settings['label']; ?></h2>
                <div class="wpuf-section-details"><?php echo $field_settings['description']; ?></div>
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