<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_SectionBreak extends WeForms_Field_Contract {

    public function __construct() {
        $this->name       = __( 'Section Break', 'weforms' );
        $this->input_type = 'section_break';
        $this->icon       = 'columns';
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
        $description   = isset( $field_settings['description'] ) ? $field_settings['description'] : '';
        $name          = isset( $field_settings['name'] ) ? $field_settings['name'] : '';
        $use_theme_css = isset( $form_settings['use_theme_css'] ) ? $form_settings['use_theme_css'] : 'wpuf-style';
        ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <div class="wpuf-section-wrap wpuf-fields <?php echo 'section_' . esc_attr( $form_id ); ?><?php echo ' wpuf_' . esc_html( $name ) . '_' . esc_attr( $form_id ); ?>" data-style="<?php echo esc_attr( $use_theme_css ); ?>">
                <h2 class="wpuf-section-title"><?php echo esc_attr( $field_settings['label'] ); ?></h2>
                <div class="wpuf-section-details"><?php echo esc_attr( $description ); ?></div>
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
        $options = [
            [
                'name'      => 'label',
                'title'     => __( 'Title', 'weforms' ),
                'type'      => 'text',
                'section'   => 'basic',
                'priority'  => 10,
                'help_text' => __( 'Title of the section', 'weforms' ),
            ],
            [
                'name'          => 'name',
                'title'         => __( 'Meta Key', 'weforms' ),
                'type'          => 'text-meta',
                'section'       => 'basic',
                'priority'      => 11,
                'help_text'     => __( 'Name of the meta key this field will save to', 'weforms' ),
            ],
            [
                'name'      => 'description',
                'title'     => __( 'Description', 'weforms' ),
                'type'      => 'textarea',
                'section'   => 'basic',
                'priority'  => 12,
                'help_text' => __( 'Some details text about the section', 'weforms' ),
            ],
        ];

        return $options;
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $props = [
            'template'    => $this->get_type(),
            'label'       => $this->get_name(),
            'description' => __( 'Some description about this section', 'weforms' ),
            'id'          => 0,
            'is_new'      => true,
            'wpuf_cond'   => $this->default_conditional_prop(),
        ];

        return $props;
    }
}
