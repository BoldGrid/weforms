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
                'title'     => __( 'Format', 'wpuf-contact-form' ),
                'type'      => 'radio',
                'options'   => array(
                    'first-last'        => __( 'First and Last name', 'wpuf-contact-form' ),
                    'first-middle-last' => __( 'First, Middle and Last name', 'wpuf-contact-form' )
                ),
                'selected'  => 'first-last',
                'section'   => 'advanced',
                'priority'  => 20,
                'help_text' => __( 'Select format to use for the name field', 'wpuf-contact-form' ),
            ),
            array(
                'name'      => 'sub-labels',
                'title'     => __( 'Label', 'wpuf-contact-form' ),
                'type'      => 'name',
                'section'   => 'advanced',
                'priority'  => 21,
                'help_text' => __( 'Select format to use for the name field', 'wpuf-contact-form' ),
            ),
            array(
                'name'          => 'hide_subs',
                'title'         => '',
                'type'          => 'checkbox',
                'is_single_opt' => true,
                'options'       => array(
                    'true'   => __( 'Hide Sub Labels', 'wpuf-contact-form' )
                ),
                'section'       => 'advanced',
                'priority'      => 23,
                'help_text'     => '',
            ),
        );

        $settings = array_merge( $name_settings, $settings );

        return array(
            'template'      => 'name_field',
            'title'         => __( 'Name', 'wpuf-contact-form' ),
            'icon'          => 'user',
            'settings'      => $settings,
            'field_props'   => array(
                'input_type'       => 'name',
                'template'         => 'name_field',
                'required'         => 'yes',
                'label'            => __( 'Name', 'wpuf-contact-form' ),
                'name'             => '',
                'is_meta'          => 'yes',
                'format'           => 'first-last',
                'first_name' => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'First', 'wpuf-contact-form' )
                ),
                'middle_name' => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'Middle', 'wpuf-contact-form' )
                ),
                'last_name' => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'Last', 'wpuf-contact-form' )
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