<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_SectionBreak extends WeForms_Field_Contract {

    function __construct() {
        $this->name       = __( 'Section Break', 'weforms' );
        $this->input_type = 'section_break';
        $this->icon       = 'columns';
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

        $description = isset( $field_settings['description'] ) ? $field_settings['description'] : '';
        $name        = isset( $field_settings['name'] ) ? $field_settings['name'] : '';
        ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <div class="wpuf-section-wrap wpuf-fields <?php echo 'section_' . $form_id; ?><?php echo ' wpuf_' . $name . '_' . $form_id; ?>">
                <h2 class="wpuf-section-title"><?php echo $field_settings['label']; ?></h2>
                <div class="wpuf-section-details"><?php echo $description; ?></div>
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
        $options = array(
            array(
                'name'      => 'label',
                'title'     => __( 'Title', 'weforms' ),
                'type'      => 'text',
                'section'   => 'basic',
                'priority'  => 10,
                'help_text' => __( 'Title of the section', 'weforms' ),
            ),
            array(
                'name'          => 'name',
                'title'         => __( 'Meta Key', 'weforms' ),
                'type'          => 'text-meta',
                'section'       => 'basic',
                'priority'      => 11,
                'help_text'     => __( 'Name of the meta key this field will save to', 'weforms' ),
            ),
            array(
                'name'      => 'description',
                'title'     => __( 'Description', 'weforms' ),
                'type'      => 'textarea',
                'section'   => 'basic',
                'priority'  => 12,
                'help_text' => __( 'Some details text about the section', 'weforms' ),
            ),
        );

        return $options;
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $props = array(
            'template'    => $this->get_type(),
            'label'       => $this->get_name(),
            'description' => __( 'Some description about this section', 'weforms' ),
            'id'          => 0,
            'is_new'      => true,
            'wpuf_cond'   => $this->default_conditional_prop()
        );

        return $props;
    }
}
