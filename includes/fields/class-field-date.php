<?php

/**
 * Date Field Class
 */
class WeForms_Form_Field_Date_Free extends WeForms_Field_Contract {

    public function __construct() {
        $this->name       = __( 'Date / Time', 'weforms' );
        $this->input_type = 'date_field';
        $this->icon       = 'calendar-o';
    }

    /**
     * Render the date field
     *
     * @param array $field_settings
     * @param int   $form_id
     *
     * @return void
     */
    public function render( $field_settings, $form_id ) {
        $use_theme_css = isset( $form_settings['use_theme_css'] ) ? $form_settings['use_theme_css'] : 'wpuf-style';
        $value         = '';
        ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <?php $this->print_label( $field_settings ); ?>

            <div class="wpuf-fields">
                <input
                    id="wpuf-date-<?php echo esc_attr( $field_settings['name'] ); ?>"
                    type="text"
                    <?php
                        $field_settings['enforce_format'] = ! isset( $field_settings['enforce_format'] ) ? '' : $field_settings['enforce_format'];
                        echo esc_attr( $field_settings['enforce_format'] !== 'yes' ) ? '' : 'readonly';
                    ?>
                    class="datepicker <?php echo ' wpuf_'.esc_attr( $field_settings['name'] ).'_'. esc_attr($form_id); ?>"
                    data-required="<?php echo esc_attr($field_settings['required']) ?>"
                    data-type="text"
                    data-style="<?php echo esc_attr( $use_theme_css ); ?>"
                    name="<?php echo esc_attr( $field_settings['name'] ); ?>"
                    placeholder="<?php echo esc_attr( $field_settings['format'] ); ?>"
                    value="<?php echo esc_attr( $value ) ?>"
                    size="30"
                />
                <?php $this->help_text( $field_settings ); ?>
            </div>
        </li>
        <?php

            $name   = $field_settings['name'];
            $format = $field_settings["format"];

            if ( $field_settings['time'] == 'yes' ) {
                $script = "jQuery(function($) {
                    $('#wpuf-date-{$name}').datetimepicker({ dateFormat: '{$format}' });
                });";
            } else {
                $script = "jQuery(function($) {
                    $('#wpuf-date-{$name}').datepicker({ dateFormat: '{$format}' });
                });";
            }

            wp_add_inline_script( 'wpuf-form', $script );
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        $default_options      = $this->get_default_option_settings();

        $settings = [
            [
                'name'      => 'format',
                'title'     => esc_html__( 'Date Format', 'weforms' ),
                'type'      => 'text',
                'section'   => 'advanced',
                'priority'  => 23,
                'help_text' => esc_html__( 'The date format', 'weforms' ),
            ],
            [
                'name'          => 'time',
                'title'         => '',
                'type'          => 'checkbox',
                'is_single_opt' => true,
                'options'       => [
                    'yes'   => esc_html__( 'Enable time input', 'weforms' ),
                ],
                'section'       => 'advanced',
                'priority'      => 24,
                'help_text'     => '',
            ],
            [
                'name'          => 'is_publish_time',
                'title'         => '',
                'type'          => 'checkbox',
                'is_single_opt' => true,
                'options'       => [
                    'yes'   => esc_html__( 'Set this as publish time input', 'weforms' ),
                ],
                'section'       => 'advanced',
                'priority'      => 24,
                'help_text'     => '',
            ],
            [
              'name'            => 'enforce_format',
              'title'           => __( 'Toggle Keyboard input for Date', 'weforms' ),
              'type'            => 'checkbox',
              'section'         => 'advanced',
              'is_single_opt'   => true,
              'options'         => [
                  'yes'   => esc_html__( 'Force Datepicker Input', 'weforms' ),
              ],
              'default'         => 'yes',
              'priority'        => 24,
              'help_text'       => esc_html__( 'Disables Keyboard Input and uses the Datepicker Format', 'weforms' ),
            ],
        ];

        return array_merge( $default_options, $settings );
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults = $this->default_attributes();
        $props    = [
            'format'            => 'dd/mm/yy',
            'time'              => '',
            'is_publish_time'   => '',
            'enforce_format'    => '',
        ];

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

        $args = ! empty( $args ) ? $args : weforms_clean( $_POST );

        return sanitize_text_field( trim( $args[$field['name']] ) );
    }
}
