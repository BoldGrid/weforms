<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_HTML extends WeForms_Field_Contract {

    function __construct() {
        $this->name       = __( 'Custom HTML', 'weforms' );
        $this->input_type = 'custom_html';
        $this->icon       = 'code';
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
        <li <?php $this->print_list_attributes( $field_settings ); ?>>

            <div class="wpuf-fields <?php echo 'html_' . $form_id; ?><?php echo ' wpuf_'.$field_settings['name'].'_'.$form_id; ?>">
                <?php echo $field_settings['html']; ?>
            </div>

        </li>
        <?php
    }

    /**
     * It's a full width block
     *
     * @return boolean
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
        $settings = array(
            array(
                'name'      => 'html',
                'title'     => __( 'Html Codes', 'weforms' ),
                'type'      => 'textarea',
                'section'   => 'basic',
                'priority'  => 11,
                'help_text' => __( 'Paste your HTML codes, WordPress shortcodes will also work here', 'weforms' ),
            ),
            array(
                'name'          => 'name',
                'title'         => __( 'Meta Key', 'weforms' ),
                'type'          => 'text-meta',
                'section'       => 'basic',
                'priority'      => 12,
                'help_text'     => __( 'Name of the meta key this field will save to', 'weforms' ),
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
            'template'  => $this->get_type(),
            'label'     => $this->get_name(),
            'html'      => sprintf( '%s', __( 'HTML Section', 'weforms' ) ),
            'id'        => 0,
            'is_new'    => true,
            'wpuf_cond' => $this->default_conditional_prop()
        );

        return $props;
    }
}
