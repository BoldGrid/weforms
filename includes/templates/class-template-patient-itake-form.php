<?php

/**
 * Blank form template
 */
class WeForms_Template_Patient_Intake_Form extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = class_exists( 'WeForms_Pro' );
        $this->title       = __( 'Patient Intake Form', 'weforms' );
        $this->description = __( 'This is a Patient Intake Form which gathers Medical History Data and useful information.', 'weforms' );
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/patient-intake-form.png';
        $this->category    = 'registration';
    }

    /**
     * Get the form fields
     *
     * @return array
     */
    public function get_form_fields() {
        $all_fields  = $this->get_available_fields();
        $form_fields = [
            array_merge( $all_fields['name_field']->get_field_props(), [
                'required'   => 'yes',
                'format'     => 'first-middle-last',
                'first_name' => [
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'First Name', 'weforms' ),
                ],

                'middle_name'     => [
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'Middle Name', 'weforms' ),
                ],
                'last_name'       => [
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'Last Name', 'weforms' ),
                ],
                'hide_subs'       => false,
                'name'            => 'format',
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'required'   => 'yes',
                'label'      => __( 'Age', 'weforms' ),
                'name'       => 'age',
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'      => __( 'Preferred Name or Nickname', 'weforms' ),
                'name'       => 'nickname',
            ] ),

            array_merge( $all_fields['dropdown_field']->get_field_props(), [
                'label'      => __( 'Patient\'s gender', 'weforms' ),
                'name'       => 'nickname',
                'options'    => [
                    'male'    => __( 'Male', 'weforms' ),
                    'female'  => __( 'Female', 'weforms' ),
                ],

                'first'      => ' ',
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'      => __( 'Spouses Name', 'weforms' ),
                'name'       => 'spause_name',
            ] ),

            array_merge( $all_fields['address_field']->get_field_props(), [
                'label'      => __( 'Address', 'weforms' ),
                'name'       => 'address',
            ] ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), [
                'required'   => 'yes',
                'label'      => __( 'SSN', 'weforms' ),
                'name'       => 'ssn',
            ] ),

            array_merge( $all_fields['date_field']->get_field_props(), [
                'required'   => 'yes',
                'label'      => __( 'Patient Birth Date', 'weforms' ),
                'name'       => 'patient_birth_date',
            ] ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), [
                'label'      => __( 'Home Phone', 'weforms' ),
                'name'       => 'hone_phone',
            ] ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), [
                'label'      => __( 'Work Phone', 'weforms' ),
                'name'       => 'work_phone',
            ] ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), [
                'label'      => __( 'Cell Phone', 'weforms' ),
                'name'       => 'cell_phone',
            ] ),

            array_merge( $all_fields['email_address']->get_field_props(), [
                'required'   => 'yes',
                'label'      => __( 'Patient E-Mail', 'weforms' ),
                'name'       => 'patient_email',
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'      => __( 'Work Status', 'weforms' ),
                'name'       => 'work_status',
                'options'    => [
                    'employed'      => __( 'Employed', 'weforms' ),
                    'unemployed'    => __( 'Unemployed', 'weforms' ),
                    'retired'       => __( 'Retired', 'weforms' ),
                    'disabled'      => __( 'Disabled fom work', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'      => __( 'Employer', 'weforms' ),
                'name'       => 'employer',
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'      => __( 'Occupation', 'weforms' ),
                'name'       => 'occupation',
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'      => __( 'Marital Status', 'weforms' ),
                'name'       => 'marital_status',
                'options'    => [
                    'single'                => __( 'Single', 'weforms' ),
                    'married'               => __( 'Married', 'weforms' ),
                    'widowed'               => __( 'Widowed', 'weforms' ),
                    'divorced'              => __( 'Divorced', 'weforms' ),
                    'separated'             => __( 'Separated', 'weforms' ),
                    'domestic_partner'      => __( 'Domestic Partner', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'      => __( 'Subscriber Name', 'weforms' ),
                'name'       => 'subscriber_name',
            ] ),

            array_merge( $all_fields['date_field']->get_field_props(), [
                'label'      => __( 'Subscriber Birth Date', 'weforms' ),
                'name'       => 'subscriber_birth_date',
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'      => __( 'Subscriber SSN/ID', 'weforms' ),
                'name'       => 'subscriber_ssn_id',
            ] ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), [
                'label'      => __( 'Group Number', 'weforms' ),
                'name'       => 'group_number',
            ] ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), [
                'label'      => __( 'Behavioral Health Insurance Carrier (may be different than medical)', 'weforms' ),
                'name'       => 'group_number',
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'      => __( 'Subscriber Employer', 'weforms' ),
                'name'       => 'group_number',
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'      => __( 'PCP Name', 'weforms' ),
                'name'       => 'pcp_name',
            ] ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), [
                'label'      => __( 'PCP Phone Number', 'weforms' ),
                'name'       => 'pcp_phone_Number',
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'      => __( 'PCP Fax #', 'weforms' ),
                'name'       => 'pcp_fax',
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'      => __( 'Do you use tobacco in any form?', 'weforms' ),
                'name'       => 'tobacco',
                'options'    => [
                    'yes'    => __( 'Yes', 'weforms' ),
                    'no'     => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label' => __( 'If yes, please list type , amount and frequency of use', 'weforms' ),
                'name'  => 'amount_and_frequency',
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'      => __( 'Do you use Alcohol in any form?', 'weforms' ),
                'name'       => 'use_alcohol',
                'options'    => [
                    'yes'    => __( 'Yes', 'weforms' ),
                    'no'     => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'  => __( 'Do you use Alcohol in any form?', 'weforms' ),
                'name'   => 'alcohol_friquency',
            ] ),

            array_merge( $all_fields['textarea_field']->get_field_props(), [
                'label'  => __( 'Are you taking any medication? If yes, please list medication and doage per day', 'weforms' ),
                'name'   => 'medicin_list',
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Abnormal Bleeding', 'weforms' ),
                'name'      => 'abnormal_bleeding',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Alcohol Abuse', 'weforms' ),
                'name'      => 'alcohol_abuse',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Allergies', 'weforms' ),
                'name'      => 'allergies',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Anemia', 'weforms' ),
                'name'      => 'anemia',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Angina Pectoris', 'weforms' ),
                'name'      => 'angina_pectoris',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Arthritis', 'weforms' ),
                'name'      => 'arthritis',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Artificial Heart Valve', 'weforms' ),
                'name'      => 'heart_valve',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Asthma', 'weforms' ),
                'name'      => 'asthma',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Blood Transfusion', 'weforms' ),
                'name'      => 'blood_transfusion',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Cancer', 'weforms' ),
                'name'      => 'cancer',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Chemotherapy', 'weforms' ),
                'name'      => 'chemotherapy',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Congenital Heart Defect', 'weforms' ),
                'name'      => 'congenital_heart_defect',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Diabetes', 'weforms' ),
                'name'      => 'diabetes',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Difficulty Breathing', 'weforms' ),
                'name'      => 'difficulty_breathing',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Drug Abuse', 'weforms' ),
                'name'      => 'drug_abuse',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Emphysema', 'weforms' ),
                'name'      => 'emphysema',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Epilepsy', 'weforms' ),
                'name'      => 'epilepsy',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Facial Surgery', 'weforms' ),
                'name'      => 'facial_surgery',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Fainting Spells', 'weforms' ),
                'name'      => 'fainting_spells',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Fever Blisters', 'weforms' ),
                'name'      => 'fever_blisters',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Frequent Headaches', 'weforms' ),
                'name'      => 'frequent_headaches',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Glaucoma', 'weforms' ),
                'name'      => 'glaucoma',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'HIV + AIDS', 'weforms' ),
                'name'      => 'hiv_aids',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Heart Attack', 'weforms' ),
                'name'      => 'heart_attack',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Heart Murmur', 'weforms' ),
                'name'      => 'heart_murmur',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Heart Surgery', 'weforms' ),
                'name'      => 'heart_surgery',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Hemophilia', 'weforms' ),
                'name'      => 'hemophilia',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Hepatitis A', 'weforms' ),
                'name'      => 'hepatitis_a',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Hepatitis B', 'weforms' ),
                'name'      => 'hepatitis_b',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Hepatitis C', 'weforms' ),
                'name'      => 'hepatitis_c',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'High Blood Pressure', 'weforms' ),
                'name'      => 'high_blood_pressure',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Joint Replacement', 'weforms' ),
                'name'      => 'joint_replacement',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Kidney Problems', 'weforms' ),
                'name'      => 'kidney_problems',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Liver Disease', 'weforms' ),
                'name'      => 'liver_disease',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Low Blood Pressure', 'weforms' ),
                'name'      => 'low_blood_pressure',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Mitral Valve Prolaps', 'weforms' ),
                'name'      => 'mitral_valve_prolaps',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Pace Maker', 'weforms' ),
                'name'      => 'pace_maker',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Psychiatric Care', 'weforms' ),
                'name'      => 'psychiatric_care',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Radiation Therapy', 'weforms' ),
                'name'      => 'radiation_therapy',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Rheumatic Fever', 'weforms' ),
                'name'      => 'rheumatic_fever',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Seizures', 'weforms' ),
                'name'      => 'seizures',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Sexually Transmitted Disease', 'weforms' ),
                'name'      => 'sexually_transmitted_disease',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Shingles', 'weforms' ),
                'name'      => 'shingles',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Sickle Cell Disease', 'weforms' ),
                'name'      => 'sickle_cell_disease',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Sinus Problems', 'weforms' ),
                'name'      => 'sinus_problems',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Stroke', 'weforms' ),
                'name'      => 'stroke',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Thyroid Problems', 'weforms' ),
                'name'      => 'thyroid_problems',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Tuberculosis', 'weforms' ),
                'name'      => 'tuberculosis',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Ulcers', 'weforms' ),
                'name'      => 'ulcers',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'     => __( 'How may we help you today?', 'weforms' ),
                'name'      => 'help_today',
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Your current dental health is', 'weforms' ),
                'name'      => 'ulcers',
                'options'   => [
                    'good'    => __( 'Good', 'weforms' ),
                    'fair'    => __( 'Fair', 'weforms' ),
                    'poor'    => __( 'Poor', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Do you require antibiotics before dental treatment?', 'weforms' ),
                'name'      => 'require_antibiotics',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Are you currently in pain?', 'weforms' ),
                'name'      => 'currently_pain',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Do you now or have you had any pain/discomfort in your jaw joint?', 'weforms' ),
                'name'      => 'discomfort',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Are you aware of clenching or grinding your teeth?', 'weforms' ),
                'name'      => 'grinding_your_teeth',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Does it hurt when you chew or open wide to take a bite?', 'weforms' ),
                'name'      => 'hurt_when',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Do you have any jaw symptoms or headaches upon waking up in the morning?', 'weforms' ),
                'name'      => 'jaw_symptoms',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Do you have pain in the face, cheeks, jaw, joints, throat or temples?', 'weforms' ),
                'name'      => 'face_pain',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Do you like your smile?', 'weforms' ),
                'name'      => 'your_smile',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Is there anything you would like to change about your smile?', 'weforms' ),
                'name'      => 'change_your_smile',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Are you happy with the color of your teeth?', 'weforms' ),
                'name'      => 'teeth_color',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Have you ever had gum disease?', 'weforms' ),
                'name'      => 'gum_disease',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Do your gums bleed?', 'weforms' ),
                'name'      => 'gums_bleed',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Have you ever had a deep cleaning or scaling and root planing?', 'weforms' ),
                'name'      => 'root_planing',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'     => __( 'Floss/Week', 'weforms' ),
                'name'      => 'floss_week',
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'     => __( 'Brush/Day', 'weforms' ),
                'name'      => 'brush_day',
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Are your teeth sensitive to heat, cold or anything else?', 'weforms' ),
                'name'      => 'teeth_sensitive',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Do you take fluoride supplements?', 'weforms' ),
                'name'      => 'fluoride_supplements',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Have you ever had a serious/difficult problem with any previous dental work?', 'weforms' ),
                'name'      => 'previous_dental_work',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Have you ever had any unfavorable dental experiences?', 'weforms' ),
                'name'      => 'unfavorable',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Are you apprehensive about dental treatment?', 'weforms' ),
                'name'      => 'dental_apprehensive',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), [
                'label'     => __( 'Do you gag easily?', 'weforms' ),
                'name'      => 'gag_easily',
                'options'   => [
                    'yes'       => __( 'Yes', 'weforms' ),
                    'no'        => __( 'No', 'weforms' ),
                ],
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'     => __( 'When was your last dental cleaning?', 'weforms' ),
                'name'      => 'dental cleaning',
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'     => __( 'When was you last dental visit?', 'weforms' ),
                'name'      => 'dental_visit',
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'     => __( 'How can we accommodate you better during your dental visit?', 'weforms' ),
                'name'      => 'accommodate_you_better',
            ] ),

            array_merge( $all_fields['text_field']->get_field_props(), [
                'label'     => __( 'Is there any specific service and/or concern you would like to inquire about?', 'weforms' ),
                'name'      => 'dental_inquire',
            ] ),
        ];

        return $form_fields;
    }
}
