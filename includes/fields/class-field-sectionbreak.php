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
        ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <div class="wpuf-section-wrap <?php echo 'section_' . $form_id; ?>">
                <h2 class="wpuf-section-title"><?php echo $field_settings['label']; ?></h2>
                <div class="wpuf-section-details"><?php echo $field_settings['description']; ?></div>
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
        $options = array(
            array(
                'name'      => 'label',
                'title'     => __( 'Title', 'wpuf' ),
                'type'      => 'text',
                'section'   => 'basic',
                'priority'  => 10,
                'help_text' => __( 'Title of the section', 'wpuf' ),
            ),

            array(
                'name'      => 'description',
                'title'     => __( 'Description', 'wpuf' ),
                'type'      => 'textarea',
                'section'   => 'basic',
                'priority'  => 11,
                'help_text' => __( 'Some details text about the section', 'wpuf' ),
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
            'description' => __( 'Some description about this section', 'wpuf' ),
            'id'          => 0,
            'is_new'      => true,
            'wpuf_cond'   => null
        );

        return $props;
    }
}