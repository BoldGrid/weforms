<?php

class WPUF_Contact_Form_Builder_Field_Settings extends WPUF_Form_Builder_Field_Settings {

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
                'title'     => __( 'Format', 'wpuf' ),
                'type'      => 'radio',
                'options'   => array(
                    'first-last'        => __( 'First and Last name', 'wpuf-contact-form' ),
                    'first-middle-last' => __( 'First, Middle and Last name', 'wpuf-contact-form' )
                ),
                'selected'  => 'first-last',
                'section'   => 'advanced',
                'priority'  => 20,
                'help_text' => __( 'Select format to use for the name field', 'wpuf' ),
            ),
        );

        $settings = array_merge( $name_settings, $settings );

        return array(
            'template'      => 'name_field',
            'title'         => __( 'Name', 'wpuf' ),
            'icon'          => 'user',
            'settings'      => $settings,
            'field_props'   => array(
                'input_type'       => 'name',
                'template'         => 'name_field',
                'required'         => 'yes',
                'label'            => __( 'Name', 'wpuf' ),
                'name'             => '',
                'is_meta'          => 'yes',
                'format'           => 'first-last',
                'help'             => '',
                'css'              => '',
                'id'               => 0,
                'is_new'           => true,
                'wpuf_cond'        => self::get_wpuf_cond_prop()
            )
        );
    }
}