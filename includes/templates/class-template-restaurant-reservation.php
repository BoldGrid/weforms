<?php

/**
 * Blank form template
 */
class WeForms_Template_Restaurant_Reservation extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = class_exists( 'WeForms_Pro' );
        $this->title       = __( 'Restaurant Reservation Form', 'weforms' );
        $this->description = __( 'Reservation form for restaurant booking for services. It captures preliminary booking inputs that are used for preparation. Great for for hoteliers.', 'weforms' );
        $this->category    = 'default';
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/restaurant-reservation.png';
        $this->category    = 'reservation';

    }

    /**
     * Get the form fields
     *
     * @return array
     */
    public function get_form_fields() {
        $all_fields = $this->get_available_fields();

        $form_fields = array(
            array_merge( $all_fields['name_field']->get_field_props(), array(
                'required'      => 'yes',
                'label'         =>  'Full Name',
                'format'        => 'first-last',
                'first_name'    => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'First', 'weforms' )
                ),
                'last_name'     => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'Last', 'weforms' )
                ),
                'hide_subs'     => false,
                'name'          => 'format',
            ) ),
            array_merge( $all_fields['email_address']->get_field_props(), array(
                'label'    => __( 'Email Address', 'weforms' ),
                'name'     => 'email_address',
            ) ),
            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __( 'Contact Number', 'weforms' ),
                'name'     => 'phone_number',
            ) ),
            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __( 'Number of Guest', 'weforms' ),
                'name'     => 'number_of_guest',
            ) ),
            array_merge( $all_fields['date_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __( 'Date', 'weforms' ),
                'name'     => 'date',
            ) ),
            array_merge( $all_fields['dropdown_field']->get_field_props(), array(
                'required'      => true,
                'label'         => __('Table Reservation', 'weforms'),
                'name'          => 'table_reservation',
                'options'       => array(
                    'yes'       => __('Yes', 'weforms'),
                    'no'        => __('No', 'weforms'),
                ),
            ) ),
            array_merge( $all_fields['dropdown_field']->get_field_props(), array(
                'required'      =>   true,
                'label'         =>   __('Reservation Type', 'weforms'),
                'name'          =>   'reservation_type',
                'options'       =>   array(
                    'dinner'          =>  __('Dinner', 'weforms'),
                    'vip_mezzanine'   =>  __('VIP/Mezzanine', 'weforms'),
                    'birthday'        =>  __('Birthday/Aniversary', 'weforms'),
                    'nightlife'       =>  __('Night Life', 'weforms'),
                    'private'         =>  __('Private', 'weforms'),
                    'wedding'         =>  __('Wedding', 'weforms'),
                    'corporate'       =>  __('Corporate', 'weforms'),
                    'holiday'         =>  __('Holiday', 'weforms'),
                    'other'           =>  __('Other', 'weforms'),
                ),
            ) ),
            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'      =>  __('If Other above, please specify?', 'weforms'),
                'name'       =>  'other_above'
            ) ),
            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label'      =>  __('Any Special Request?', 'weforms'),
                'name'       =>  'special_request'
            ) ),
        );

        return $form_fields;
    }



    /**
     * Get the form settings
     *
     * @return array
     */
    public function get_form_settings() {
        $defaults = $this->get_default_settings();

        return array_merge( $defaults, array(
            'submit_text'                => __( 'Submit', 'weforms' ),
            'label_position'             => 'above',
            'enable_multistep'           => true,
            'multistep_progressbar_type' => 'step_by_step',
        ) );
    }

}
