<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_Hidden extends WeForms_Field_Contract {

    public function __construct() {
        $this->name       = __( 'Hidden Field', 'weforms' );
        $this->input_type = 'custom_hidden_field';
        $this->icon       = 'eye-slash';
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
        if ( isset( $field_settings['dynamic']['param_name'] ) ) {
            if ( isset( $_GET[ $field_settings['dynamic']['param_name'] ] ) ) {
                $value = sanitize_text_field( wp_unslash( $_GET[$field_settings['dynamic']['param_name']] ) );
            } else {
                $value = $field_settings['meta_value'];
            }
        } else {
            $value = $field_settings['meta_value'];
        }
        ?>
        <input type="hidden" name="<?php echo esc_attr( $field_settings['name'] ); ?>" value="<?php echo esc_attr( $value ); ?>">
        <?php
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        $settings = [
            [
                'name'      => 'name',
                'title'     => __( 'Meta Key', 'weforms' ),
                'type'      => 'text',
                'section'   => 'basic',
                'priority'  => 10,
                'help_text' => __( 'Name of the meta key this field will save to', 'weforms' ),
            ],
            [
                'name'      => 'meta_value',
                'title'     => __( 'Meta Value', 'weforms' ),
                'type'      => 'text',
                'section'   => 'basic',
                'priority'  => 11,
                'help_text' => __( 'Enter the meta value', 'weforms' ),
            ],
            [
                'name'          => 'dynamic',
                'title'         => '',
                'type'          => 'dynamic-field',
                'section'       => 'advanced',
                'priority'      => 23,
                'help_text'     => __( 'Check this option to allow field to be populated dynamically using hooks/query string/shortcode', 'weforms' ),
            ],
        ];

        return $settings;
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $props = [
            'template'      => $this->get_type(),
            'name'          => '',
            'meta_value'    => '',
            'is_meta'       => 'yes',
            'id'            => 0,
            'is_new'        => true,
            'wpuf_cond'     => null,
        ];

        return $props;
    }
}
