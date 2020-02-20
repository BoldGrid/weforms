<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_Checkbox extends WeForms_Field_Contract {

    public function __construct() {
        $this->name       = __( 'Checkbox', 'weforms' );
        $this->input_type = 'checkbox_field';
        $this->icon       = 'check-square-o';
    }

    /**
     * Render the text field
     *
     * @param array $field_settings
     * @param int   $form_id
     *
     * @return void
     */
    public function render( $field_settings, $form_id ) {
        $selected = !empty( $field_settings['selected'] ) ? $field_settings['selected'] : []; ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <?php $this->print_label( $field_settings, $form_id ); ?>

            <?php do_action( 'weforms_checkbox_field_after_label', $field_settings ); ?>
            <div class="wpuf-fields" data-required="<?php echo esc_attr( $field_settings['required'] ) ?>" data-type="radio">

                <?php
                if ( $field_settings['options'] && count( $field_settings['options'] ) > 0 ) {
                    foreach ( $field_settings['options'] as $value => $option ) {
                        ?>
                        <label <?php echo $field_settings['inline'] == 'yes' ? 'class="wpuf-checkbox-inline"' : 'class="wpuf-checkbox-block"'; ?>>
                            <input type="checkbox" class="<?php echo 'wpuf_' . esc_attr( $field_settings['name'] ). '_'. esc_attr($form_id); ?>" name="<?php echo esc_attr( $field_settings['name'] ); ?>[]" value="<?php echo esc_attr( $value ); ?>"<?php echo in_array( $value, $selected ) ? ' checked="checked"' : ''; ?> />
                            <?php echo $option; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </label>
                        <?php
                    }
                } ?>

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
        $default_options  = $this->get_default_option_settings( true, [ 'width' ] );
        $dropdown_options = [
            $this->get_default_option_dropdown_settings( true ),

            [
                'name'          => 'inline',
                'title'         => __( 'Show in inline list', 'weforms' ),
                'type'          => 'radio',
                'options'       => [
                    'yes'   => __( 'Yes', 'weforms' ),
                    'no'    => __( 'No', 'weforms' ),
                ],
                'default'       => 'no',
                'inline'        => true,
                'section'       => 'advanced',
                'priority'      => 23,
                'help_text'     => __( 'Show this option in an inline list', 'weforms' ),
            ],
        ];

        $dropdown_options = apply_filters( 'weforms_checkbox_field_option_settings', $dropdown_options );

        return array_merge( $default_options, $dropdown_options );
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults = $this->default_attributes();
        $props    = [
            'selected'          => [],
            'inline'            => 'no',
            'options'           => [ 'Option' => __( 'Option', 'weforms' ) ],
        ];

        $props = apply_filters( 'weforms_checkbox_field_props', $props );

        return array_merge( $defaults, $props );
    }

    /**
     * Prepare entry
     *
     * @param $field
     *
     * @return mixed
     */
    public function prepare_entry( $field, $args = [] ) {
        if( empty( $_POST['_wpnonce'] ) ) {
             wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'wpuf_form_add' ) ) {
            wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

        $args        = ! empty( $args ) ? $args : weforms_clean( $_POST );
        $entry_value = ( is_array( $args[ $field[ 'name' ] ] ) && $args[ $field[ 'name' ] ] ) ? $args[ $field[ 'name' ] ] : array();

        if ( $entry_value ) {
            $new_val = [];

            foreach ( $entry_value as $option_key ) {
                $new_val[] = isset( $field['options'][$option_key] ) ? $field['options'][$option_key] : $option_key;
            }

            $entry_value = implode( WeForms::$field_separator, $new_val );
        } else {
            $entry_value = '';
        }

        return $entry_value;
    }
}
