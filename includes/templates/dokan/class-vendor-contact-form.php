<?php

/**
 * Vendor contact form template
 */
class WeForms_Vendor_Contact_Form extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = true;
        $this->title       = __( 'Vendor Contact Form', 'weforms' );
        $this->description = __( 'Create a vendor contact form for your site.', 'weforms' );
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/contact.png';
        $this->category    = 'default';

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
                'name'          => 'seller_name',
                'label'         => 'Seller Name',
                'hide_subs'     => 'true',
                'auto_populate' => 'yes'
            ) ),
            array_merge( $all_fields['email_address']->get_field_props(), array(
                'required'      => 'yes',
                'name'          => 'email',
                'auto_populate' => 'yes',
            ) ),
            array_merge( $all_fields['text_field']->get_field_props(), array(
                'required'      => 'yes',
                'name'          => 'subject',
                'label'         => 'Subject',
            ) ),
            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __( 'Message', 'weforms' ),
                'name'     => 'message',
            ) ),
        );

        return $form_fields;
    }

}
