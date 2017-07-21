<?php

class WeForms_Builder_Field_Settings extends WPUF_Form_Builder_Field_Settings {

    public static function get_field_settings() {
        return array(
            'name_field' => self::name_field(),
        );
    }

    /**
     * Name field settings
     *
     * @return array
     */
    public static function name_field() {
        $settings = self::get_common_properties();

        $name_settings = array(
            array(
                'name'      => 'format',
                'title'     => __( 'Format', 'weforms' ),
                'type'      => 'radio',
                'options'   => array(
                    'first-last'        => __( 'First and Last name', 'weforms' ),
                    'first-middle-last' => __( 'First, Middle and Last name', 'weforms' )
                ),
                'selected'  => 'first-last',
                'section'   => 'advanced',
                'priority'  => 20,
                'help_text' => __( 'Select format to use for the name field', 'weforms' ),
            ),
            array(
                'name'      => 'sub-labels',
                'title'     => __( 'Label', 'weforms' ),
                'type'      => 'name',
                'section'   => 'advanced',
                'priority'  => 21,
                'help_text' => __( 'Select format to use for the name field', 'weforms' ),
            ),
            array(
                'name'          => 'hide_subs',
                'title'         => '',
                'type'          => 'checkbox',
                'is_single_opt' => true,
                'options'       => array(
                    'true'   => __( 'Hide Sub Labels', 'weforms' )
                ),
                'section'       => 'advanced',
                'priority'      => 23,
                'help_text'     => '',
            ),
        );

        $settings = array_merge( $name_settings, $settings );

        return array(
            'template'      => 'name_field',
            'title'         => __( 'Name', 'weforms' ),
            'icon'          => 'user',
            'settings'      => $settings,
            'field_props'   => array(
                'input_type'       => 'name',
                'template'         => 'name_field',
                'required'         => 'yes',
                'label'            => __( 'Name', 'weforms' ),
                'name'             => '',
                'is_meta'          => 'yes',
                'format'           => 'first-last',
                'first_name' => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'First', 'weforms' )
                ),
                'middle_name' => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'Middle', 'weforms' )
                ),
                'last_name' => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'Last', 'weforms' )
                ),
                'hide_subs'        => false,
                'help'             => '',
                'css'              => '',
                'id'               => 0,
                'is_new'           => true,
                'wpuf_cond'        => self::get_wpuf_cond_prop()
            )
        );
    }
}