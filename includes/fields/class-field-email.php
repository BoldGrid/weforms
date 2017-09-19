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
     * @param  integer  $form_id
     * @param  array  $field_settings
     *
     * @return void
     */
    public function render( $form_id, $field_settings ) {
        $value = $field_settings['default'];
        ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <?php $this->print_label( $field_settings ); ?>

            <div class="wpuf-fields">
                <input id="wpuf-<?php echo $field_settings['name']; ?>" type="email" class="email <?php echo ' wpuf_'.$field_settings['name'].'_'.$form_id; ?>" data-required="<?php echo $field_settings['required'] ?>" data-type="email" name="<?php echo esc_attr( $field_settings['name'] ); ?>" placeholder="<?php echo esc_attr( $field_settings['placeholder'] ); ?>" value="<?php echo esc_attr( $value ) ?>" size="<?php echo esc_attr( $field_settings['size'] ) ?>" />
                <?php $this->help_text( $field_settings ); ?>
            </div>
        </li>
        <?php
    }
}