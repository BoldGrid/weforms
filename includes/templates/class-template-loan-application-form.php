<?php

/**
 * Blank form template
 */
class WeForms_Template_Loan_Application_Form extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = class_exists( 'WeForms_Pro' );
        $this->title       = __( 'Loan Application Form', 'weforms' );
        $this->description = __( 'Use this load application to quickly process loan applications much smoother!', 'weforms' );
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/loan-application.png';
        $this->category    = 'application';

    }

    /**
     * Get the form fields
     *
     * @return array
     */
    public function get_form_fields() {
        $all_fields = $this->get_available_fields();

        $form_fields = array(
            array_merge( $all_fields['dropdown_field']->get_field_props(), array(
                'label'      =>  __('Title', 'weforms'),
                'name'       => 'title',
                'options'    =>  array(
                    'mr'     =>  __('Ms', 'weforms'),
                    'mrs'    =>  __('Mrs', 'weforms'),
                    'mrs'    =>  __('Mr', 'weforms'),
                ),
                'first'      => ' ',
            ) ),

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

            array_merge( $all_fields['date_field']->get_field_props(), array(
                'label'      => __('Birth Date', 'weforms'),
                'name'       => 'birth_date',
            ) ),

            array_merge( $all_fields['radio_field']->get_field_props(), array(
                'label'      => __('Marital Status', 'weforms'),
                'name'       => 'marital_status',
                'options'    => array(
                    'single'    => __('Single', 'weforms'),
                    'married'   => __('Married', 'weforms'),
                    'other'     => __('Other', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['email_address']->get_field_props(), array(
                'required'   => 'yes',
                'label'      => __('Email Address', 'weforms'),
                'name'       => 'email',
            ) ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      => __('Phone', 'weforms'),
                'name'       => 'phone',
            ) ),

            array_merge( $all_fields['address_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      => __('Address', 'weforms'),
                'name'       => 'address',
            ) ),

            array_merge( $all_fields['radio_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      => __('How long have you lived in your given?', 'weforms'),
                'name'       => 'duration',
                'options'    => array(
                    'one'    => __('0-1 Year', 'weforms'),
                    'two'    => __('1-2 Year', 'weforms'),
                    'trhee'  => __('3-4 Year', 'weforms'),
                    'four'   => __('5+ Year', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      => __('Gross Monthly Income', 'weforms'),
                'name'       => 'gross_monthly',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      => __('Monthly rent/mortgage', 'weforms'),
                'name'       => 'monthly_rently',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      => __('Down Payment Amount', 'weforms'),
                'name'       => 'payment_amount',
            ) ),

            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label'      => __('Comments', 'weforms'),
                'name'       => 'comment',
            ) ),

            array_merge( $all_fields['custom_html']->get_field_props(), array(
                'html'       => sprintf( '<p>%s</p>', __( 'I authorize prospective Credit Grantors/Lending/Leasing Companies to obtain personal and credit information about me from my employer and credit bureau, or credit reporting agency, any person who has or may have any financial dealing with me, or from any references I have provided. This information, as well as that provided by me in the application, will be referred to in connection with this lease and any other relationships we may establish from time to time. Any personal and credit information obtained may be disclosed from time to time to other lenders, credit bureaus or other credit reporting agencies', 'weforms' ) ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      => ' ',
                'name'       => 'agrrement',
                'options'    => array(
                    'yes'    => __('Yes', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['custom_html']->get_field_props(), array(
                'html'       => sprintf( '<p>%s</p>', __( 'I hereby agree that the information given is true, accurate and complete as of the date of this application submission. ', 'weforms' ) ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      => ' ',
                'name'       => 'agrrement_2',
                'options'    => array(
                    'yes'    => __('Yes', 'weforms'),
                ),
            ) ),

        );

        return $form_fields;
    }

}
