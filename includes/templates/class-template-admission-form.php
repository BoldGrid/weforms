<?php

/**
 * Blank form template
 */
class WeForms_Template_Admission_Form extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = class_exists( 'WeForms_Pro' );
        $this->title       = __( 'Admissions Form', 'weforms' );
        $this->description = __( 'A sample admissions and registration form for your educational institution on this multi-page admissions form to ask for user details and an application fee.', 'weforms' );
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/admission_form.png';
        $this->category    = 'registration';
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
                'format'     => 'first-middle-last',

                'first_name' => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'First', 'weforms' )
                ),

                'middle_name'     => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'Middle', 'weforms' )
                ),
                'last_name'       => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'Last', 'weforms' )
                ),
                'hide_subs'       => false,
                'name'            => 'format',
            ) ),

            array_merge( $all_fields['date_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __( 'Birth Date', 'weforms' ),
                'name'     => 'birth_date',
            ) ),

            array_merge( $all_fields['radio_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __( 'Gender', 'weforms' ),
                'name'     => 'gender',
                'options'  =>  array(
                    'male'              =>  'Male',
                    'female'            => 'Female',
                    'decline_to_answer' => 'Decline to Answer'
                ),
            ) ),

            array_merge( $all_fields['radio_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __( 'Are you Hispanic or Latino?', 'weforms' ),
                'name'     => 'latino',
                'options'  =>  array(
                    'yes'               =>  'Yes',
                    'no'                => 'No',
                    'decline_to_answer' => 'Decline to Answer'
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __( 'Race:', 'weforms' ),
                'name'     => 'race',
                'options'  =>  array(
                    'american'          => 'American Indian/Alaskan Native ',
                    'asian'             => 'Asian',
                    'black'             => 'Black',
                    'native_hawaiian'   => 'Native Hawaiian/Other Pacific Islander',
                    'white'             =>  'White',
                    'decline_to_answer' => 'Decline to Answer',
                ),
            ) ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __( 'Phone Number', 'weforms' ),
                'name'     => 'phone_number',
            ) ),

            array_merge( $all_fields['email_address']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __( 'Email Address', 'weforms' ),
                'name'     => 'email_address',
            ) ),


            array_merge( $all_fields['address_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __( 'Mailing Address', 'weforms' ),
                'name'     => 'mailing_address',
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __( 'Payment Method', 'weforms' ),
                'name'     => 'payment_method',
                'options'  =>  array(
                    'credit_card'   => 'Credit Card',
                    'mail_a_check'  =>  'Mail a Check',
                    'in_person'     =>  'In Person at School',
                ),
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
            'submit_text'   => __( 'Submit', 'weforms' ),
        ) );
    }

}
