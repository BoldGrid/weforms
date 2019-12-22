<?php

/**
 * Blank form template
 */
class WeForms_Template_Employee_Information extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = class_exists( 'WeForms_Pro' );
        $this->title       = __( 'Employee Information', 'weforms' );
        $this->description = __( 'Keep a record of your employee’s information with this form. This includes personal information, job information and emergency contact information sections.', 'weforms' );
        $this->category    = 'default';
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/employee_information.png';
        $this->category    = 'employment';
    }

    /**
     * Get the form fields
     *
     * @return array
     */
    public function get_form_fields() {
        $all_fields = $this->get_available_fields();

        $form_fields = [
            array_merge( $all_fields['step_start']->get_field_props(), [
                'label'      => __( 'Personal Information', 'weforms' ),
                'name'       => 'personal_information',
                'step_start' => [
                    'prev_button_text' => __( 'Previous', 'weforms' ),
                    'next_button_text' => __( 'Next', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['name_field']->get_field_props(), [
                'required'      => 'yes',
                'format'        => 'first-middle-last',
                'first_name'    => [
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'First', 'weforms' ),
                ],

                'middle_name'   => [
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'Middle', 'weforms' ),
                ],
                'last_name'     => [
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'Last', 'weforms' ),
                ],
                'hide_subs'     => false,
                'name'          => 'format',
            ] ),

            array_merge( $all_fields['address_field']->get_field_props(), [
                'required' => 'yes',
                'label'    => 'Address',
                'name'     => 'address',
            ] ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), [
                'required' => 'yes',
                'label'    => __( 'Phone Number', 'weforms' ),
                'name'     => 'phone_number',
            ] ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), [
                'label'    => __( 'Alt. Phone Number', 'weforms' ),
                'name'     => 'alt_phone_number',
            ] ),

            array_merge( $all_fields['email_address']->get_field_props(), [
                'label'    => __( 'Email Address', 'weforms' ),
                'name'     => 'email_address',
            ] ),

            array_merge( $all_fields['date_field']->get_field_props(), [
                'label'    => __( 'Birth Date', 'weforms' ),
                'name'     => 'birth_date',
            ] ),

            array_merge( $all_fields['dropdown_field']->get_field_props(), [
                'label'    => __( 'Marital Status', 'weforms' ),
                'name'     => 'marital_status',
                'options'  => [
                    'single'    => __( 'Single', 'weforms' ),
                    'married'   => __( 'Married', 'weforms' ),
                    'widowed'   => __( 'Widowed', 'weforms' ),
                    'divorced'  => __( 'Divorced', 'weforms' ),
                ],

                'first'    => ' ',
            ] ),

            array_merge( $all_fields['name_field']->get_field_props(), [
                'label'      => __( 'Spouse\'s Name', 'weforms' ),
                'format'     => 'first-last',
                'first_name' => [
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'First', 'weforms' ),
                ],
                'last_name'  => [
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'Last', 'weforms' ),
                ],
                'hide_subs'       => false,
                'name'            => 'spause_name',
            ] ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), [
                'label'    => __( 'Spouse\' s Work Phone', 'weforms' ),
                'name'     => 'spouse_work_phone_number',
            ] ),

            array_merge( $all_fields['step_start']->get_field_props(), [
                'label'          => __( 'Job Information', 'weforms' ),
                'name'           => 'job_information',
                'step_start'     => [
                    'prev_button_text' => __( 'Previous', 'weforms' ),
                    'next_button_text' => __( 'Next', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'    => __( 'Title', 'weforms' ),
                'name'     => 'title',
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'    => __( 'Employee ID', 'weforms' ),
                'name'     => 'employee_id',
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'    => __( 'Supervisor', 'weforms' ),
                'name'     => 'supervisor',
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'    => __( 'Department', 'weforms' ),
                'name'     => 'department',
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'    => __( 'Work Location', 'weforms' ),
                'name'     => 'work_location',
            ] ),

            array_merge( $all_fields['email_address']->get_field_props(), [
                'label'    => __( 'Email Address', 'weforms' ),
                'name'     => 'work_email_address',
            ] ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), [
                'label'    => __( 'Work Number', 'weforms' ),
                'name'     => 'work_number',
            ] ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), [
                'label'    => __( 'Cell Phone', 'weforms' ),
                'name'     => 'cell_phone',
            ] ),

            array_merge( $all_fields['date_field']->get_field_props(), [
                'label'    => __( 'Start Date', 'weforms' ),
                'name'     => 'start_date',
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'       => __( 'Salary', 'weforms' ),
                'name'        => 'salary',
                'placeholder' => '$',
            ] ),

            array_merge( $all_fields['step_start']->get_field_props(), [
                'label'      => __( 'Emergency Contact information', 'weforms' ),
                'name'       => 'emergency_contact_information',
                'step_start' => [
                    'prev_button_text' => __( 'Previous', 'weforms' ),
                    'next_button_text' => __( 'Next', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['name_field']->get_field_props(), [
                'format'     => 'first-last',
                'first_name' => [
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'First', 'weforms' ),
                ],

                'last_name'  => [
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'Last', 'weforms' ),
                ],
                'hide_subs'  => false,
                'name'       => 'emargency_person_name',
            ] ),

            array_merge( $all_fields['name_field']->get_field_props(), [
                'label'    => __( 'Address', 'weforms' ),
                'name'     => 'emargency_address',
            ] ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), [
                'label' => 'Phone Number',
                'name'  => 'ematgency_phone_number',
            ] ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), [
                'label' => 'Alt. Phone Number',
                'name'  => 'alt_ematgency_phone_number',
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label' => 'Relationship',
                'name'  => 'relationship',
            ] ),
        ];

        return $form_fields;
    }

    /**
     * Get the form settings
     *
     * @return array
     */
    public function get_form_settings() {
        $defaults = $this->get_default_settings();

        return array_merge( $defaults, [
            'submit_text'                => __( 'Submit', 'weforms' ),
            'label_position'             => 'above',
            'enable_multistep'           => true,
            'multistep_progressbar_type' => 'step_by_step',
        ] );
    }
}
