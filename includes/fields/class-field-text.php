<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_Text extends WeForms_Field_Contract {

    public function __construct() {
        $this->name       = __( 'Text', 'weforms' );
        $this->input_type = 'text_field';
        $this->icon       = 'text-width';
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
        $value         = $field_settings['default'];
        $form_settings = weforms()->form->get( $form_id )->get_settings();
        $use_theme_css = isset( $form_settings['use_theme_css'] ) ? $form_settings['use_theme_css'] : 'wpuf-style';
        ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <?php $this->print_label( $field_settings, $form_id ); ?>

            <div class="wpuf-fields">
                <input
                    class="textfield <?php echo 'wpuf_' . esc_attr( $field_settings['name'] ) . '_' . esc_attr( $form_id ); ?>"
                    id="<?php echo esc_attr( $field_settings['name'] ) . '_' . esc_attr( $form_id ); ?>"
                    type="text"
                    data-duplicate="<?php echo esc_attr( $field_settings['duplicate'] ) ? esc_attr( $field_settings['duplicate'] ) : 'no'; ?>"
                    data-required="<?php echo esc_attr( $field_settings['required'] ) ?>"
                    data-type="text" name="<?php echo esc_attr( $field_settings['name'] ); ?>"
                    placeholder="<?php echo esc_attr( $field_settings['placeholder'] ); ?>"
                    value="<?php echo esc_attr( $value ); ?>"
                    size="<?php echo esc_attr( $field_settings['size'] ); ?>"
                    data-style="<?php echo esc_attr( $use_theme_css ); ?>"
                />

                <span class="wpuf-wordlimit-message wpuf-help"></span>
                <?php $this->help_text( $field_settings ); ?>
            </div>
            <?php
            if ( isset( $field_settings['word_restriction'] ) && $field_settings['word_restriction'] ) {
                $this->check_word_restriction_func(
                    $field_settings['word_restriction'],
                    'no',
                    $field_settings['name'] . '_' . $form_id
                  );
            }

        $mask_option = isset( $field_settings['mask_options'] ) ? $field_settings['mask_options'] : '';

        if ( $mask_option ) {
            ?>
                <script>
                    jQuery(document).ready(function($) {
                        var text_field = $( "input[name*=<?php echo esc_attr( $field_settings['name'] ); ?>]" );
                        switch ( '<?php echo esc_attr( $mask_option ); ?>' ) {
                            case 'us_phone':
                                text_field.mask('(999) 999-9999');
                                break;
                            case 'date':
                                text_field.mask('99/99/9999');
                                break;
                            case 'tax_id':
                                text_field.mask('99-9999999');
                                break;
                            case 'ssn':
                                text_field.mask('999-99-9999');
                                break;
                            case 'zip':
                                text_field.mask('99999');
                                break;
                            default:
                                break;
                        }
                    });
                </script>
            <?php
        } ?>
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
        $default_text_options = $this->get_default_text_option_settings( true );
        $check_duplicate      = [
            [
                'name'          => 'duplicate',
                'title'         => 'No Duplicates',
                'type'          => 'checkbox',
                'is_single_opt' => true,
                'options'       => [
                    'no'   => __( 'Unique Values Only', 'weforms' ),
                ],
                'default'       => '',
                'section'       => 'advanced',
                'priority'      => 23,
                'help_text'     => __( 'Select this option to limit user input to unique values only. This will require that a value entered in a field does not currently exist in the entry database for that field.', 'weforms' ),
            ],
        ];

        $text_options = array_merge( $default_options, $default_text_options, $check_duplicate );

        return apply_filters( 'weforms_text_field_option_settings', $text_options );
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults = $this->default_attributes();
        $props    = [
            'word_restriction' => '',
            'duplicate'        => '',
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
        if( empty( $_POST[ '_wpnonce' ] ) ) {
             wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'wpuf_form_add' ) ) {
            wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

        $args = ! empty( $args ) ? $args : weforms_clean( $_POST );

        return sanitize_text_field( trim( $args[$field['name']] ) );
    }
}
