<?php

/**
 * Blank form template
 */
class WeForms_Template_Product_Order_Form extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = class_exists( 'WeForms_Pro' );
        $this->title       = __( 'Product Order Form', 'weforms' );
        $this->description = __( 'A simple order form template with quantity, color and size options. Ideal for preparing an order form in seconds.', 'weforms' );
        $this->category    = 'default';
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/product-order-form.png';
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
            array_merge( $all_fields['step_start']->get_field_props(), array(
                'label'      => __( 'Product Order', 'weforms' ),
                'name'       => 'personal_order',
                'step_start' => array(
                    'prev_button_text' => __( 'Previous', 'weforms' ),
                    'next_button_text' => __( 'Next', 'weforms' )
                ),
            ) ),
            array_merge( $all_fields['name_field']->get_field_props(), array(
                'required'      => 'yes',
                'format'        => 'first-middle-last',
                'first_name'    => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'First', 'weforms' )
                ),

                'middle_name'   => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'Middle', 'weforms' )
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
            array_merge( $all_fields['address_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => 'Billing Address',
                'name'     => 'billing_address',
            ) ),
            array_merge( $all_fields['step_start']->get_field_props(), array(
                'label'          => __( 'Product', 'weforms' ),
                'name'           => 'product',
                'step_start'     => array(
                    'prev_button_text' => __( 'Previous', 'weforms' ),
                    'next_button_text' => __( 'Next', 'weforms' )
                ),
            ) ),
            array_merge( $all_fields['single_product']->get_field_props(), array(
                'label'         =>  __('T-Shirt', 'weforms'),
                'name'          =>  't_shirt',
                'price'         =>  array(
                    'price'     =>  10,
                ),
                'quantity'      =>  array(
                    'status'    =>  'yes'
                ),
            ) ),
            array_merge( $all_fields['single_product']->get_field_props(), array(
                'label'         =>  __('Sweatshirt', 'weforms'),
                'name'          =>  'sweatshirt',
                'price'         =>  array(
                    'price'     =>  10,
                ),
                'quantity'      =>   array(
                    'status'    =>  'yes'
                ),
            ) ),
            array_merge( $all_fields['single_product']->get_field_props(), array(
                'label'         =>   __('Shoes', 'weforms'),
                'name'          =>   'shoes',
                'price'         =>    array(
                    'price'     =>    10,
                ),
                'quantity'      =>    array(
                    'status'    =>    'yes'
                ),
            ) ),
            array_merge( $all_fields['total']->get_field_props(), array(
               'label'     => __( 'Total', 'weforms' ),
               'price'     => 0,
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'        =>  __('Send Gift', 'weforms'),
                'name'         =>  'send_gift',
                'options'      =>   array(
                    'yes'   =>  __('Yes', 'weforms'),
                    'no'    =>  __('No', 'weforms'),
                ),
            ) ),
            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label'        =>   __('Additional Requests', 'weforms'),
                'name'         =>   'additional_request',
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
