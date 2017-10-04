<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_URL extends WeForms_Form_Field_Text {

    function __construct() {
        $this->name       = __( 'Website URL', 'weforms' );
        $this->input_type = 'website_url';
        $this->icon       = 'link';
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
                <input
                    id="<?php echo $field_settings['name'] . '_' . $form_id; ?>"
                    type="url" class="url <?php echo ' wpuf_'.$field_settings['name'].'_'.$form_id; ?>"
                    data-duplicate="<?php echo $field_settings['duplicate'] ?>"
                    data-required="<?php echo $field_settings['required'] ?>"
                    data-type="text"
                    name="<?php echo esc_attr( $field_settings['name'] ); ?>"
                    placeholder="<?php echo esc_attr( $field_settings['placeholder'] ); ?>"
                    value="<?php echo esc_attr( $value ) ?>" size="<?php echo esc_attr( $field_settings['size'] ) ?>"
                    autocomplete="url"
                />
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
        $default_text_options = $this->get_default_text_option_settings( false ); // word_restriction = false
        $check_duplicate      = array(
            array(
                'name'          => 'duplicate',
                'title'         => '',
                'type'          => 'checkbox',
                'is_single_opt' => true,
                'options'       => array(
                    'true'   => __( 'Allow duplicate', 'weforms' )
                ),
                'default'       => 'true',
                'section'       => 'advanced',
                'priority'      => 23,
                'help_text'     => __( 'Set "yes" value for this option to allow duplicate value. By selecting "no" the form will not submit unless the value is unique.', 'weforms' ),
            )
        );
        return array_merge( $default_options, $default_text_options, $check_duplicate );
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults = $this->default_attributes();
        $defaults['duplicate'] = 'true';
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
       return esc_url( trim( $_POST[$field['name']] ) );
    }
}