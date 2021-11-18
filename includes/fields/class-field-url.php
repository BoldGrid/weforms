<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_URL extends WeForms_Form_Field_Text {

    public function __construct() {
        $this->name       = __( 'Website URL', 'weforms' );
        $this->input_type = 'website_url';
        $this->icon       = 'link';
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
        $use_theme_css = isset( $form_settings['use_theme_css'] ) ? $form_settings['use_theme_css'] : 'wpuf-style';
        ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <?php $this->print_label( $field_settings, $form_id ); ?>

            <div class="wpuf-fields">
                <input
                    title = "<?php echo esc_attr__( 'The URL must contain a protocol i.e. http://example.com', 'weforms' ); ?>"
                    id = "<?php echo esc_attr( $field_settings['name'] ) . '_' . esc_attr( $form_id ); ?>"
                    type = "url" class="url <?php echo ' wpuf_'. esc_attr( $field_settings['name'] ).'_'. esc_attr( $form_id ); ?>"
                    data-duplicate = "<?php echo esc_attr( $field_settings['duplicate'] ) ? esc_attr( $field_settings['duplicate'] ) : 'no'; ?>"
                    data-required = "<?php echo esc_attr( $field_settings['required'] ) ?>"
                    data-type = "url"
                    data-style="<?php echo esc_attr( $use_theme_css ); ?>"
                    name = "<?php echo esc_attr( $field_settings['name'] ); ?>"
                    placeholder = "<?php echo esc_attr( $field_settings['placeholder'] ); ?>"
                    value = "<?php echo esc_attr( $value ); ?>" size="<?php echo esc_attr( $field_settings['size'] ); ?>"
                    autocomplete = "url"
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

        return array_merge( $default_options, $default_text_options, $check_duplicate );
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults              = $this->default_attributes();
        $defaults['duplicate'] = '';

        return $defaults;
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

        $args = ! empty( $args ) ? $args : weforms_clean( $_POST  );

        return esc_url( trim( $args[$field['name']] ) );
    }
}
