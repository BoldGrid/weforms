<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_Email extends WeForms_Form_Field_Text {

    function __construct() {
        $this->name       = __( 'Email Address', 'weforms' );
        $this->input_type = 'email_address';
        $this->icon       = 'envelope-o';
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
        $value = $field_settings['default'];
        ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <?php $this->print_label( $field_settings, $form_id ); ?>

            <div class="wpuf-fields">
                <input id="<?php echo $field_settings['name'] . '_' . $form_id; ?>" type="email" class="email <?php echo ' wpuf_'.$field_settings['name'].'_'.$form_id; ?>" data-required="<?php echo $field_settings['required'] ?>" data-type="email" name="<?php echo esc_attr( $field_settings['name'] ); ?>" placeholder="<?php echo esc_attr( $field_settings['placeholder'] ); ?>" value="<?php echo esc_attr( $value ) ?>" size="<?php echo esc_attr( $field_settings['size'] ) ?>" />
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

        return $defaults;
    }

    /**
     * Prepare entry
     *
     * @param $field
     *
     * @return mixed
     */
    public function prepare_entry( $field ) {
       return sanitize_text_field( trim( $_POST[$field['name']] ) );
    }
}