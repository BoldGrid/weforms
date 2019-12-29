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
     * Render the text field
     *
     * @param array $field_settings
     * @param int   $form_id
     *
     * @return void
     */
    public function render( $field_settings, $form_id ) {
        $value = ''; ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <?php $this->print_label( $field_settings ); ?>

            <div class="wpuf-fields">
                <input id="wpuf-date-<?php echo esc_attr( $field_settings['name'] ); ?>" type="text" class="datepicker <?php echo ' wpuf_'.esc_attr( $field_settings['name'] ).'_'. esc_attr($form_id); ?>" data-required="<?php echo esc_attr($field_settings['required']) ?>" data-type="text" name="<?php echo esc_attr( $field_settings['name'] ); ?>" placeholder="<?php echo esc_attr( $field_settings['format'] ); ?>" value="<?php echo esc_attr( $value ) ?>" size="30" />
                <?php $this->help_text( $field_settings ); ?>
            </div>
            <script type="text/javascript">
                jQuery(function($) {
                    <?php if ( $field_settings['time'] == 'yes' ) { ?>
                    $("#wpuf-date-<?php echo esc_attr($field_settings['name']); ?>").datetimepicker({ dateFormat: '<?php echo esc_attr($field_settings["format"]); ?>' });
                    <?php } else { ?>
                    $("#wpuf-date-<?php echo esc_attr($field_settings['name']); ?>").datepicker({ dateFormat: '<?php echo esc_attr($field_settings["format"]); ?>' });
                    <?php } ?>
                });
            </script>

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

        $settings = [
            [
                'name'      => 'format',
                'title'     => __( 'Date Format', 'weforms' ),
                'type'      => 'text',
                'section'   => 'advanced',
                'priority'  => 23,
                'help_text' => __( 'The date format', 'weforms' ),
            ],
            [
                'name'          => 'time',
                'title'         => '',
                'type'          => 'checkbox',
                'is_single_opt' => true,
                'options'       => [
                    'yes'   => __( 'Enable time input', 'weforms' ),
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
                    'yes'   => __( 'Set this as publish time input', 'weforms' ),
                ],
                'section'       => 'advanced',
                'priority'      => 24,
                'help_text'     => '',
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
