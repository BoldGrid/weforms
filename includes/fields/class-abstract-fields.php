<?php

/**
 * Form field abstract class
 *
 * @since 1.1.0
 */
abstract class WeForms_Field_Contract {

    /**
     * The field name
     *
     * @var string
     */
    protected $name = '';

    /**
     * Type of the field
     *
     * @var string
     */
    protected $input_type = '';

    /**
     * Icon of the field
     *
     * @var string
     */
    protected $icon = '';

    /**
     * Get the name of the field
     *
     * @return string
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Get field type
     *
     * @return string
     */
    public function get_type() {
        return $this->input_type;
    }

    /**
     * Get the fontawesome icon for this field
     *
     * @return string
     */
    public function get_icon() {
        return $this->icon;
    }

    /**
     * Render of the field in the frontend
     *
     * @param  integer  $form_id        The form id
     * @param  array    $field_settings The field configuration from the db
     * @param  string   $type           optional form type. e.g.: post, user
     * @param  integer  $data_id        if a post/user type field, the Post ID or the User ID
     *
     * @return void
     */
    abstract function render( $form_id, $field_settings, $type = '', $data_id = 0 );

    /**
     * Get the field option settings for form builder
     *
     * @return array
     */
    abstract function get_options_settings();

    /**
     * Get the field props
     *
     * Field props are the field properties that saves in the database
     *
     * @return array
     */
    abstract function get_field_props();

    /**
     * The JS template for using in the form builder
     *
     * @return array
     */
    public function get_js_settings() {
        return array(
            'template'      => $this->get_type(),
            'title'         => $this->get_name(),
            'icon'          => $this->get_icon(),
            'settings'      => $this->get_options_settings(),
            'field_props'   => $this->get_field_props()
        );
    }

    /**
     * Conditional property for all fields
     *
     * @return array
     */
    public function default_conditional_prop() {
        return array(
            'condition_status'  => 'no',
            'cond_field'        => array(),
            'cond_operator'     => array( '=' ),
            'cond_option'       => array( __( '- select -', 'weforms' ) ),
            'cond_logic'        => 'all'
        );
    }

    /**
     * Default attributes of a field
     *
     * Child classes should use this default setting and extend it by using `get_field_settings()` function
     *
     * @return array
     */
    public function default_attributes() {
        return array(
            'template'    => $this->get_type(),
            'name'        => '',
            'label'       => $this->get_name(),
            'requird'     => 'no',
            'id'          => 0,
            'css'         => '',
            'placeholder' => '',
            'default'     => '',
            'size'        => 40,
            'help'        => '',
            'is_meta'     => 'yes', // wpuf uses it to differentiate meta fields with core fields, maybe removed
            'is_new'      => true, // introduced by @edi, not sure what it does. Have to remove
            'wpuf_cond'   => $this->default_conditional_prop()
        );
    }

    /**
     * Common properties for all kinds of fields
     *
     * @param boolean $is_meta
     *
     * @return array
     */
    public static function get_default_option_settings( $is_meta = true ) {
        $common_properties = array(
            array(
                'name'      => 'label',
                'title'     => __( 'Field Label', 'weforms' ),
                'type'      => 'text',
                'section'   => 'basic',
                'priority'  => 10,
                'help_text' => __( 'Enter a title of this field', 'weforms' ),
            ),

            array(
                'name'      => 'help',
                'title'     => __( 'Help text', 'weforms' ),
                'type'      => 'text',
                'section'   => 'basic',
                'priority'  => 20,
                'help_text' => __( 'Give the user some information about this field', 'weforms' ),
            ),

            array(
                'name'      => 'required',
                'title'     => __( 'Required', 'weforms' ),
                'type'      => 'radio',
                'options'   => array(
                    'yes'   => __( 'Yes', 'weforms' ),
                    'no'    => __( 'No', 'weforms' ),
                ),
                'section'   => 'basic',
                'priority'  => 21,
                'default'   => 'no',
                'inline'    => true,
                'help_text' => __( 'Check this option to mark the field required. A form will not submit unless all required fields are provided.', 'weforms' ),
            ),

            array(
                'name'      => 'css',
                'title'     => __( 'CSS Class Name', 'weforms' ),
                'type'      => 'text',
                'section'   => 'advanced',
                'priority'  => 22,
                'help_text' => __( 'Provide a container class name for this field.', 'weforms' ),
            ),
        );

        if ( $is_meta ) {
            $common_properties[] = array(
                'name'      => 'name',
                'title'     => __( 'Meta Key', 'weforms' ),
                'type'      => 'text-meta',
                'section'   => 'basic',
                'priority'  => 11,
                'help_text' => __( 'Name of the meta key this field will save to', 'weforms' ),
            );
        }

        return $common_properties;
    }

    /**
     * Common properties of a text input field
     *
     * @param boolean $word_restriction
     *
     * @return array
     */
    public static function get_default_text_option_settings( $word_restriction = false ) {
        $properties = array(
            array(
                'name'      => 'placeholder',
                'title'     => __( 'Placeholder text', 'weforms' ),
                'type'      => 'text',
                'section'   => 'advanced',
                'priority'  => 10,
                'help_text' => __( 'Text for HTML5 placeholder attribute', 'weforms' ),
            ),

            array(
                'name'      => 'default',
                'title'     => __( 'Default value', 'weforms' ),
                'type'      => 'text',
                'section'   => 'advanced',
                'priority'  => 11,
                'help_text' => __( 'The default value this field will have', 'weforms' ),
            ),

            array(
                'name'      => 'size',
                'title'     => __( 'Size', 'weforms' ),
                'type'      => 'text',
                'variation' => 'number',
                'section'   => 'advanced',
                'priority'  => 20,
                'help_text' => __( 'Size of this input field', 'weforms' ),
            ),
        );

        if ( $word_restriction ) {
            $properties[] = array(
                'name'      => 'word_restriction',
                'title'     => __( 'Word Restriction', 'weforms' ),
                'type'      => 'text',
                'section'   => 'advanced',
                'priority'  => 15,
                'help_text' => __( 'Numebr of words the author to be restricted in', 'weforms' ),
            );
        }

        return apply_filters( 'wpuf-form-builder-common-text-fields-properties', $properties );
    }

    /**
     * Common properties of a textarea field
     *
     * @return array
     */
    public function get_default_textarea_option_settings() {
        return array(
            array(
                'name'      => 'rows',
                'title'     => __( 'Rows', 'weforms' ),
                'type'      => 'text',
                'section'   => 'advanced',
                'priority'  => 10,
                'help_text' => __( 'Number of rows in textarea', 'weforms' ),
            ),

            array(
                'name'      => 'cols',
                'title'     => __( 'Columns', 'weforms' ),
                'type'      => 'text',
                'section'   => 'advanced',
                'priority'  => 11,
                'help_text' => __( 'Number of columns in textarea', 'weforms' ),
            ),

            array(
                'name'      => 'placeholder',
                'title'     => __( 'Placeholder text', 'weforms' ),
                'type'      => 'text',
                'section'   => 'advanced',
                'priority'  => 12,
                'help_text' => __( 'Text for HTML5 placeholder attribute', 'weforms' ),
                'dependencies' => array(
                    'rich' => 'no'
                )
            ),

            array(
                'name'      => 'default',
                'title'     => __( 'Default value', 'weforms' ),
                'type'      => 'text',
                'section'   => 'advanced',
                'priority'  => 13,
                'help_text' => __( 'The default value this field will have', 'weforms' ),
            ),

            array(
                'name'      => 'rich',
                'title'     => __( 'Textarea', 'weforms' ),
                'type'      => 'radio',
                'options'   => array(
                    'no'    => __( 'Normal', 'weforms' ),
                    'yes'   => __( 'Rich textarea', 'weforms' ),
                    'teeny' => __( 'Teeny Rich textarea', 'weforms' ),
                ),
                'section'   => 'advanced',
                'priority'  => 14,
                'default'   => 'no',
            ),

            array(
                'name'      => 'word_restriction',
                'title'     => __( 'Word Restriction', 'weforms' ),
                'type'      => 'text',
                'section'   => 'advanced',
                'priority'  => 15,
                'help_text' => __( 'Numebr of words the author to be restricted in', 'weforms' ),
            ),
        );
    }

}