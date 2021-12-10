<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_Radio extends WeForms_Form_Field_Checkbox {

    public function __construct() {
        $this->name       = __( 'Radio', 'weforms' );
        $this->input_type = 'radio_field';
        $this->icon       = 'dot-circle-o';
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
        $use_theme_css = isset( $form_settings['use_theme_css'] ) ? $form_settings['use_theme_css'] : 'wpuf-style';
        $selected      = isset( $field_settings['selected'] ) ? $field_settings['selected'] : ''; ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <?php $this->print_label( $field_settings, $form_id ); ?>

            <?php do_action( 'weforms_radio_field_after_label', $field_settings ); ?>
            <div class="wpuf-fields" data-required="<?php echo esc_attr( $field_settings['required'] ) ?>" data-type="radio" data-style="<?php echo esc_attr( $use_theme_css ); ?>">

                <?php
                if ( $field_settings['options'] && count( $field_settings['options'] ) > 0 ) {
                    foreach ( $field_settings['options'] as $value => $option ) {
                        ?>

                        <label <?php echo $field_settings['inline'] == 'yes' ? 'class="wpuf-radio-inline"' : 'class="wpuf-radio-block"'; ?>>
                            <input
                                name="<?php echo esc_attr($field_settings['name']); ?>"
                                class="<?php echo 'wpuf_'. esc_attr( $field_settings['name'] ). '_'. esc_attr( $form_id ); ?>"
                                type="radio"
                                value="<?php echo esc_attr( $value ); ?>"<?php checked( $selected, $value ); ?>
                            />
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
            $this->get_default_option_dropdown_settings(),
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

        $dropdown_options = apply_filters( 'weforms_radio_field_option_settings', $dropdown_options );

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
            'selected' => '',
            'inline'   => 'no',
            'options'  => [ 'Option' => __( 'Option', 'weforms' ) ],
        ];

        $props = apply_filters( 'weforms_radio_field_props', $props );

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
        if( empty( $_POST[ '_wpnonce' ] ) ) {
             wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'wpuf_form_add' ) ) {
            wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

        $args = ! empty( $args ) ? $args : weforms_clean( $_POST );
        $val  = $args[$field['name']];

        return isset( $field['options'][$val] ) ? $field['options'][$val] : $val;
    }
}
