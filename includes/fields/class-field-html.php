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
        /**
         * Ensure the field name is set. There have been cases where the name array key is not set.
         * Not sure why and we were unable to replicate without manually removing the key.
         * This is a failsafe to ensure the field name is set.
         */
        if ( ! isset( $field_settings['name'] ) ) {
            $field_settings['name'] = 'custom_html';
        }
        $use_theme_css    = isset( $form_settings['use_theme_css'] ) ? $form_settings['use_theme_css'] : 'wpuf-style';
        ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <div class="wpuf-fields <?php echo 'html_' . esc_attr( $form_id ); ?><?php echo ' wpuf_'. esc_attr( $field_settings['name'] ).'_'. esc_attr( $form_id ); ?>" data-style="<?php echo esc_attr( $use_theme_css ); ?>">
                <?php echo $field_settings['html']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
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
