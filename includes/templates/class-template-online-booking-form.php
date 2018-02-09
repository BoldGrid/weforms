<?php
/**
 * Blank form template
 */
class Weforms_Template_Online_Booking_Form extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = class_exists( 'WeForms_Pro' );
        $this->title       = __( 'Online Booking Form', 'weforms' );
        $this->description = __( 'Have more guests book with this easy to fill booking form.', 'weforms' );
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/online-booking-form.png';
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
                'required'   => 'yes',
                'format'     => 'first-last',

                'first_name' => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'First Name', 'weforms' )
                ),
                'last_name'       => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'Last Name', 'weforms' )
                ),
                'hide_subs'       => false,
                'name'            => 'format',
            ) ),
            array_merge( $all_fields['email_address']->get_field_props(), array(
                'required' => 'yes',
                'label'    => 'Email Address',
                'name'     => 'email_address',
            ) ),
            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'required' =>  true,
                'label'    => 'Phone Number',
                'name'     => 'phone_number',
            ) ),
            array_merge( $all_fields['date_field']->get_field_props(), array(
                'label'    =>   __('Departure Date/Time', 'weforms'),
                'name'     =>   'departure_date',
            ) ),
            array_merge( $all_fields['date_field']->get_field_props(), array(
                'label'    =>   __('Return Date/Time', 'weforms'),
                'name'     =>   'return_date',
            ) ),
            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'required' => true,
                'label'    => 'Pickup Address',
                'name'     => 'pickup_address'
            ) ),
            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'required' => true,
                'label'    => 'Destination Address',
                'name'     => 'destination_address'
            ) ),
            array_merge( $all_fields['dropdown_field']->get_field_props(), array(
                'required' => true,
                'label'    => 'Journey Type',
                'name'     => 'journey_type',
                'options'  =>   array(
                    'one_way'   =>  __('One Way', 'weforms'),
                    'return'    =>  __('Return', 'weforms'),
                ),
            ) ),
            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'required' =>  true,
                'label'    => 'Number of Passengers',
                'name'     => 'number_of_passengers',
            ) ),
            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'required' => true,
                'label'    => 'Additional Message',
                'name'     => 'additional_message'
            ) ),
        );

        return $form_fields;
    }

}