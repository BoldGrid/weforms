<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_Radio extends WeForms_Form_Field_Checkbox {

    function __construct() {
        $this->name       = __( 'Radio', 'weforms' );
        $this->input_type = 'radio_field';
        $this->icon       = 'dot-circle-o';
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
        $selected = isset( $field_settings['selected'] ) ? $field_settings['selected'] : '';
        ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <?php $this->print_label( $field_settings ); ?>

            <div class="wpuf-fields" data-required="<?php echo $field_settings['required'] ?>" data-type="radio">

                <?php
                if ( $field_settings['options'] && count( $field_settings['options'] ) > 0 ) {
                    foreach ($field_settings['options'] as $value => $option) {
                        ?>

                        <label <?php echo $field_settings['inline'] == 'yes' ? 'class="wpuf-radio-inline"' : 'class="wpuf-radio-block"'; ?>>
                            <input name="<?php echo $field_settings['name']; ?>" class="<?php echo 'wpuf_'.$field_settings['name']. '_'. $form_id; ?>" type="radio" value="<?php echo esc_attr( $value ); ?>"<?php checked( $selected, $value ); ?> />
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
        $default_options  = $this->get_default_option_settings();
        $dropdown_options = array(
            $this->get_default_option_dropdown_settings(),

            array(
                'name'          => 'inline',
                'title'         => __( 'Show in inline list', 'wpuf' ),
                'type'          => 'radio',
                'options'       => array(
                    'yes'   => __( 'Yes', 'wpuf' ),
                    'no'    => __( 'No', 'wpuf' ),
                ),
                'default'       => 'no',
                'inline'        => true,
                'section'       => 'advanced',
                'priority'      => 23,
                'help_text'     => __( 'Show this option in an inline list', 'wpuf' ),
            )
        );

        return array_merge( $default_options, $dropdown_options );
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults = $this->default_attributes();
        $props    = array(
            'selected' => '',
            'inline'   => 'no',
            'options'  => array( 'Option' => __( 'Option', 'wpuf' ) ),
        );

        return array_merge( $defaults, $props );
    }
}