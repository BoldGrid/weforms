<?php

/**
 * Support form template
 */
class WeForms_Template_Support extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = true;
        $this->title       = __( 'Support Form', 'weforms' );
        $this->description = __( 'Enable your users for asking support questions.', 'weforms' );
        $this->category    = 'default';
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/support.png';
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
                'required' => 'yes',
                'name'     => 'name'
            ) ),
            array_merge( $all_fields['email_address']->get_field_props(), array(
                'required' => 'yes',
                'name'     => 'email',
                'help'     => __( 'Please provide a valid email address so we can get back to you', 'weforms' ),
            ) ),
            array_merge( $all_fields['radio_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __( 'Department', 'weforms' ),
                'name'     => 'department',
                'options'  => array(
                    'sales'               => __( 'Sales', 'weforms' ),
                    'support'             => __( 'Customer Support', 'weforms' ),
                    'product_development' => __( 'Product Development', 'weforms' ),
                    'other'               => __( 'Other', 'weforms' ),
                ),
            ) ),
            array_merge( $all_fields['text_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __( 'Subject', 'weforms' ),
                'name'     => 'subject',
            ) ),
            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label' => __( 'Message', 'weforms' ),
                'name'  => 'message',
                'help'  => __( 'How may we help you? Please be brief as much as possible.', 'weforms' ),
            ) ),
        );

        return $form_fields;
    }
}
