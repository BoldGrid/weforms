<?php

/**
 * Job Appplication Form Template
 */
class WeForms_Template_Job_Application extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = class_exists( 'WeForms_Pro' );
        $this->title       = __( 'Job Application Form', 'weforms' );
        $this->description = __( 'This simple template is the easy and fastest way to apply online. Gather information and upload resume using the form.', 'weforms' );
        $this->category    = 'application';
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/job-application.png';
        $this->category    = 'employment';

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
                'label'    => 'Full Name',
                'name'     => 'full_name',
            ) ),
            array_merge( $all_fields['address_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => 'Current Address',
                'name'     => 'current_address',
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
            array_merge( $all_fields['dropdown_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => 'Applying For Position',
                'name'     => 'applying_for_position',
                'options'  => array(
                    'work_1' => __( 'Work 1', 'weforms' ),
                    'work_2' => __( 'Work 2', 'weforms' ),
                    'work_3' => __( 'Work 3', 'weforms' ),
                    'any_position' => __( 'Any Position', 'weforms' )
                ),
            ) ),
            array_merge( $all_fields['date_field']->get_field_props(), array(
                'label'    => 'Start Date',
                'name'     => 'start_date'
            ) ),
            array_merge( $all_fields['file_upload']->get_field_props(), array(
                'required' => 'yes',
                'label'    => 'Upload Resume',
                'name'     => 'upload_resume'
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
            'message'     => __( 'Thanks for applying! We will get in touch with you shortly.', 'weforms' ),
            'submit_text' => __( 'Apply', 'weforms' ),
        ) );
    }

}
