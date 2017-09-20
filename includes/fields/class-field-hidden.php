<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_Hidden extends WeForms_Field_Contract {

    function __construct() {
        $this->name       = __( 'Hidden Field', 'weforms' );
        $this->input_type = 'custom_hidden_field';
        $this->icon       = 'eye-slash';
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
        ?>
        <input type="hidden" name="<?php echo esc_attr( $field_settings['name'] ); ?>" value="<?php echo esc_attr( $field_settings['meta_value'] ); ?>">
        <?php
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        $settings = array(
            array(
                'name'      => 'name',
                'title'     => __( 'Meta Key', 'wpuf' ),
                'type'      => 'text',
                'section'   => 'basic',
                'priority'  => 10,
                'help_text' => __( 'Name of the meta key this field will save to', 'wpuf' ),
            ),

            array(
                'name'      => 'meta_value',
                'title'     => __( 'Meta Value', 'wpuf' ),
                'type'      => 'text',
                'section'   => 'basic',
                'priority'  => 11,
                'help_text' => __( 'Enter the meta value', 'wpuf' ),
            ),
        );

        return $settings;
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $props = array(
            'template'    => $this->get_type(),
            'name'          => '',
            'meta_value'    => '',
            'is_meta'       => 'yes',
            'id'            => 0,
            'is_new'        => true,
            'wpuf_cond'     => null
        );

        return $props;
    }
}