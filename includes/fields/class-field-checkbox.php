<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_Checkbox extends WeForms_Field_Contract {

    function __construct() {
        $this->name       = __( 'Checkbox', 'weforms' );
        $this->input_type = 'checkbox_field';
        $this->icon       = 'check-square-o';
    }

    /**
     * Render the text field
     *
     * @param  integer  $form_id
     * @param  array  $field_settings
     *
     * @return void
     */
    public function render( $form_id, $field_settings ) {
        $selected = isset( $field_settings['selected'] ) ? $field_settings['selected'] : array();
        ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <?php $this->print_label( $field_settings ); ?>

            <div class="wpuf-fields" data-required="<?php echo $field_settings['required'] ?>" data-type="radio">

                <?php
                if ( $field_settings['options'] && count( $field_settings['options'] ) > 0 ) {

                    foreach ($field_settings['options'] as $value => $option) {

                        ?>
                        <label <?php echo $field_settings['inline'] == 'yes' ? 'class="wpuf-checkbox-inline"' : 'class="wpuf-checkbox-block"'; ?>>
                            <input type="checkbox" class="<?php echo 'wpuf_'.$field_settings['name']. '_'. $form_id; ?>" name="<?php echo $field_settings['name']; ?>[]" value="<?php echo esc_attr( $value ); ?>"<?php echo in_array( $value, $selected ) ? ' checked="checked"' : ''; ?> />
                            <?php echo $option; ?>
                        </label>
                        <?php
                    }
                }
                ?>

                <?php $this->help_text( $field_settings ); ?>

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