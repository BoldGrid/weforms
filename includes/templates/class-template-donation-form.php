<?php
/**
 * Blank form template
 */
class Weforms_Donation_Form extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = true;
        $this->title       = __( 'Donation Form', 'weforms' );
        $this->description = __( 'Inspire people to donate more on your site with this form', 'weforms' );
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/donation-form.png';
        $this->category    = 'payment';
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
                'label'    => 'Phone Number',
                'name'     => 'phone_number',
            ) ),
            array_merge( $all_fields['radio_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => 'Type Of Donation',
                'name'     => 'type_of_donation',
                'options'  => array(
                    'donation_1' => __( 'Donation-1', 'weforms' ),
                    'donation_2' => __( 'Donation-2', 'weforms' ),
                    'donation_3' => __( 'Donation-3', 'weforms' ),
                ),
            ) ),
            array_merge( $all_fields['single_product']->get_field_props(), array(
                'required'      => 'yes',
                'label'         =>  __('Donation Amount', 'weforms'),
                'name'          =>  'price',
                'price'         =>  array(
                    'price'     =>  10,
                    'type'      => 'donation',
                ),
            ) ),
            array_merge( $all_fields['payment_method']->get_field_props(), array(
                'required'      => 'yes',
                'label'         =>  __('Payment Method', 'weforms'),
                'name'          =>  'payment_method',
            ) ),
            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label'    => 'Comments',
                'name'     => 'comments'
            ) ),
        );

        return $form_fields;
    }

}
