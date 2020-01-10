<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_HTML extends WeForms_Field_Contract {

    public function __construct() {
        $this->name       = __( 'Custom HTML', 'weforms' );
        $this->input_type = 'custom_html';
        $this->icon       = 'code';
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
        ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <div class="wpuf-fields <?php echo 'html_' . esc_attr( $form_id ); ?><?php echo ' wpuf_'. esc_attr( $field_settings['name'] ).'_'. esc_attr( $form_id ); ?>">
                <?php echo esc_attr( $field_settings['html'] ); ?>
            </div>

        </li>
        <?php
    }

    /**
     * It's a full width block
     *
     * @return bool
     */
    public function is_full_width() {
        return true;
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        $settings = [
            [
                'name'      => 'html',
                'title'     => __( 'Html Codes', 'weforms' ),
                'type'      => 'textarea',
                'section'   => 'basic',
                'priority'  => 11,
                'help_text' => __( 'Paste your HTML codes, WordPress shortcodes will also work here', 'weforms' ),
            ],
            [
                'name'          => 'name',
                'title'         => __( 'Meta Key', 'weforms' ),
                'type'          => 'text-meta',
                'section'       => 'basic',
                'priority'      => 12,
                'help_text'     => __( 'Name of the meta key this field will save to', 'weforms' ),
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
            'template'  => $this->get_type(),
            'label'     => $this->get_name(),
            'html'      => sprintf( '%s', __( 'HTML Section', 'weforms' ) ),
            'id'        => 0,
            'is_new'    => true,
            'wpuf_cond' => $this->default_conditional_prop(),
        ];

        return $props;
    }
}
