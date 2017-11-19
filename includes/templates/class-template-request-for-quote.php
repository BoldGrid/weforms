<?php

/**
 * Blank form template
 */
class WeForms_Template_Request_For_Quote extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = class_exists( 'WeForms_Pro' );
        $this->title       = __( 'Request for Quote', 'weforms' );
        $this->description = __( 'This form will make your customers request for quote easier.', 'weforms' );
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/qoute_request.png';
        $this->category    = 'request';
    }

    /**
     * Get the form fields
     *
     * @return array
     */
    public function get_form_fields() {

        $all_fields = $this->get_available_fields();

        $form_fields = array(

            array_merge($all_fields['name_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __('Name', 'weforms'),
                'format'   => 'first-last',
                'name'     => 'format',
            )),

            array_merge($all_fields['numeric_text_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __('Phone Number', 'weforms'),
                'name'      => 'phone_number',
            )),

            array_merge($all_fields['email_address']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __('Email Address', 'weforms'),
                'name'     => 'email_address',
            )),

            array_merge($all_fields['radio_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __('Preferred Method of Contact', 'weforms'),
                'name'      =>  'preferred_method',
                'options'   => array(
                    'prefer_phone'   => 'Phone',
                    'prefer_email'   => 'Email',
                    'prefer_either'  => 'Either',
                ),
            )),


            array_merge($all_fields['radio_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __('Preferred Type of Trip', 'weforms'),
                'name'      =>  'preferred_trip',
                'options'   => array(
                    'one_way'       => 'One Way',
                    'round_trip'    => 'Round Trip',
                    'hourly'        => 'Hourly',
                ),
            )),


            array_merge($all_fields['address_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __('Pickup Address ', 'weforms'),
                'name'      =>  'pickup_address',
            )),

            array_merge($all_fields['dropdown_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __('Number of Passengers', 'weforms'),
                'name'      =>  'number_of_passengers',
                'options'   =>  array(
                    '1'         =>     '1',
                    '2'         =>     '2',
                    '3'         =>     '3',
                    '4'         =>     '4',
                    '5'         =>     '5',
                    '6'         =>     '6',
                    '7'         =>     '7',
                    '8'         =>     '8',
                    '9'         =>     '9',
                    '10'        =>     '10',
                    '11'        =>     '11',
                    '12'        =>     '12',
                    '13'        =>     '13',
                    '14'        =>     '14',
                ),
            )),

            array_merge($all_fields['dropdown_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __('Number of Hours ', 'weforms'),
                'name'      =>  'number_of_hours ',
                'options'   =>  array(
                    '1'         =>     '1',
                    '2'         =>     '2',
                    '3'         =>     '3',
                    '4'         =>     '4',
                    '5'         =>     '5',
                    '6'         =>     '6',
                    '7'         =>     '7',
                    '8'         =>     '8',
                    '9'         =>     '9',
                    '10'        =>     '10',
                    '11'        =>     '11',
                    '12'        =>     '12',
                    '13'        =>     '13',
                    '14'        =>     '14',
                ),
            )),

            array_merge($all_fields['textarea_field']->get_field_props(), array(
                'label'     => __('Comments/Special Requests', 'weforms'),
                'name'      =>  'comments_request'
            )),
        );

        return $form_fields;
    }

}
