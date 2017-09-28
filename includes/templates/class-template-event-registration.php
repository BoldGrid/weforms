<?php

/**
 * Event registration form template
 */
class WeForms_Template_Event_Registration extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = true;
        $this->title       = __( 'Event Registration', 'weforms' );
        $this->description = __( 'Get your visitors to register for an upcoming event quickly with this registration form template.', 'weforms' );
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/event.png';
        $this->category    = 'event';
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
                'name'     => 'name',
            ) ),
            array_merge( $all_fields['email_address']->get_field_props(), array(
                'required' => 'yes',
                'name'     => 'email',
            ) ),
            array_merge( $all_fields['text_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __( 'Phone', 'weforms' ),
                'name'     => 'phone',
            ) ),
            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label' => __( 'Company', 'weforms' ),
                'name'  => 'compnay',
            ) ),
            array_merge( $all_fields['website_url']->get_field_props(), array(
                'required'    => 'no',
                'label'       => __( 'Website', 'weforms' ),
                'name'        => 'website',
                'placeholder' => 'https://',
            ) ),
            array_merge( $all_fields['radio_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __( 'Have you attended before?', 'weforms' ),
                'name'     => 'attended_before',
                'inline'   => 'no',
                'options'  => array(
                    'yes' => __( 'Yes', 'weforms' ),
                    'no'  => __( 'No', 'weforms' ),
                ),
            ) ),
            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __( 'Dietary Requirements', 'weforms' ),
                'name'     => 'dietary_requirements',
                'inline'   => 'no',
                'options'  => array(
                    'none'             => __( 'None', 'weforms' ),
                    'vegeterian'       => __( 'Vegeterian', 'weforms' ),
                    'vegan'            => __( 'Vegan', 'weforms' ),
                    'lactose-free'     => __( 'Lactose-free', 'weforms' ),
                    'gluten-free'      => __( 'Gluten-free', 'weforms' ),
                    'kosher'           => __( 'Kosher', 'weforms' ),
                    'halal'            => __( 'Halal', 'weforms' ),
                    'allergies-other'  => __( 'Allergies/Other', 'weforms' ),
                ),
            ) ),
            array_merge( $all_fields['radio_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => 'Do you require any special assistance?',
                'name'     => 'special_assistance',
                'inline'   => 'no',
                'options'  => array(
                    'yes' => __( 'Yes', 'weforms' ),
                    'no'  => __( 'No', 'weforms' ),
                ),
            ) ),
            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'required' => 'no',
                'label'    => __( 'Comments or Questions', 'weforms' ),
                'name'     => 'comments',
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
            'message'     => __( 'Thanks for signing up! We will get in touch with you shortly.', 'weforms' ),
            'submit_text' => __( 'Register', 'weforms' ),
        ) );
    }

}
