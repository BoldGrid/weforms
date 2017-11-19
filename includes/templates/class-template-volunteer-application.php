<?php

/**
 * Blank form template
 */
class WeForms_Template_Volunteer_Application extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = true;
        $this->title       = __( 'Volunteer Application Form', 'weforms' );
        $this->description = __( 'Get volunteer applications easily and find out which days your volunteers want to work according to their particular interests.', 'weforms' );
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/volunteer-application.png';
        $this->category    = 'application';
    }

    /**
     * Get the form fields
     *
     * @return array
     */
    public function get_form_fields() {
        $all_fields     = $this->get_available_fields();
        $form_fields    = array(
            array_merge( $all_fields['section_break']->get_field_props(), array(
              'label'       => __('Volunteer Application', 'weforms'),
              'description' => ' ',
            ) ),

            array_merge( $all_fields['name_field']->get_field_props(), array(
                'required'      => 'yes',
                'format'        => 'first-last',
                'first_name'    => array(
                    'placeholder'   => '',
                    'default'       => '',
                    'sub'           => __( 'First Name', 'weforms' )
                ),
                'last_name'     => array(
                    'placeholder'   => '',
                    'default'       => '',
                    'sub'           => __( 'Last Name', 'weforms' )
                ),
                'hide_subs'     => false,
                'name'          => 'format',
            ) ),

            array_merge( $all_fields['email_address']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __('Email Address', 'weforms'),
                'name'      =>  'email_address',
            ) ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __('Phone Number', 'weforms'),
                'name'      =>  'phone_number',
            ) ),

            array_merge( $all_fields['address_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __('Address', 'weforms'),
                'name'      =>  'address',
            ) ),

            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label'     => __('Skillsets or Area of Interests', 'weforms'),
                'name'      =>  'area_of_interests',
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Days of Work', 'weforms'),
                'name'      =>  'days_of_work',
                'options'   =>  array(
                    'monday'        => __('Monday', 'weforms'),
                    'tuesday'       => __('Tuesday', 'weforms'),
                    'wednesday'     => __('Wednesday', 'weforms'),
                    'thursday'      => __('Thursday', 'weforms'),
                    'friday'        => __('Friday', 'weforms'),
                    'sunday'        => __('Sunday', 'weforms'),
                    'satarday'      => __('Satarday', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label'     => __('Comments', 'weforms'),
                'name'      =>  'comment',
            ) ),
        );

        return $form_fields;
    }

}
