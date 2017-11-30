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
        $form_fields = array(
            array_merge( $all_fields['name_field']->get_field_props(), array(
                'required'   => 'yes',
                'format'     => 'first-middle-last',

                'first_name' => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'First Name', 'weforms' )
                ),

                'middle_name'     => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'Middle Name', 'weforms' )
                ),
                'last_name'       => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'Last Name', 'weforms' )
                ),
                'hide_subs'       => false,
                'name'            => 'format',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      => __('Age', 'weforms'),
                'name'       => 'age',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'      => __('Preferred Name or Nickname', 'weforms'),
                'name'       => 'nickname',
            ) ),

            array_merge( $all_fields['dropdown_field']->get_field_props(), array(
                'label'      => __('Patient\'s gender' , 'weforms'),
                'name'       => 'nickname',
                'options'    => array(
                    'male'    => __('Male', 'weforms'),
                    'female'  => __('Female', 'weforms'),
                ),

                'first'      => ' ',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'      => __('Spouses Name' , 'weforms'),
                'name'       => 'spause_name',
            ) ),

            array_merge( $all_fields['address_field']->get_field_props(), array(
                'label'      => __('Address' , 'weforms'),
                'name'       => 'address',
            ) ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      => __('SSN' , 'weforms'),
                'name'       => 'ssn',
            ) ),

            array_merge( $all_fields['date_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      => __('Patient Birth Date' , 'weforms'),
                'name'       => 'patient_birth_date',
            ) ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'label'      => __('Home Phone' , 'weforms'),
                'name'       => 'hone_phone',
            ) ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'label'      => __('Work Phone' , 'weforms'),
                'name'       => 'work_phone',
            ) ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'label'      => __('Cell Phone' , 'weforms'),
                'name'       => 'cell_phone',
            ) ),

            array_merge( $all_fields['email_address']->get_field_props(), array(
                'required'   =>  'yes',
                'label'      => __('Patient E-Mail' , 'weforms'),
                'name'       => 'patient_email',
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'      => __('Work Status' , 'weforms'),
                'name'       => 'work_status',
                'options'    => array(

                    'employed'      => __('Employed', 'weforms'),
                    'unemployed'    => __('Unemployed', 'weforms'),
                    'retired'       => __('Retired', 'weforms'),
                    'disabled'      => __('Disabled fom work', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'      => __('Employer' , 'weforms'),
                'name'       => 'employer',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'      => __('Occupation' , 'weforms'),
                'name'       => 'occupation',
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'      => __('Marital Status' , 'weforms'),
                'name'       => 'marital_status',
                'options'    => array(
                    'single'                =>  __('Single', 'weforms'),
                    'married'               =>  __('Married', 'weforms'),
                    'widowed'               =>  __('Widowed', 'weforms'),
                    'divorced'              =>  __('Divorced', 'weforms'),
                    'separated'             =>  __('Separated', 'weforms'),
                    'domestic_partner'      =>  __('Domestic Partner', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'      => __('Subscriber Name' , 'weforms'),
                'name'       => 'subscriber_name',
            ) ),

            array_merge( $all_fields['date_field']->get_field_props(), array(
                'label'      => __('Subscriber Birth Date' , 'weforms'),
                'name'       => 'subscriber_birth_date',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'      => __('Subscriber SSN/ID' , 'weforms'),
                'name'       => 'subscriber_ssn_id',
            ) ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'label'      => __('Group Number' , 'weforms'),
                'name'       => 'group_number',
            ) ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'label'      => __('Behavioral Health Insurance Carrier (may be different than medical)' , 'weforms'),
                'name'       => 'group_number',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'      => __('Subscriber Employer' , 'weforms'),
                'name'       => 'group_number',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'      => __('PCP Name' , 'weforms'),
                'name'       => 'pcp_name',
            ) ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'label'      => __('PCP Phone Number' , 'weforms'),
                'name'       => 'pcp_phone_Number',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'      => __('PCP Fax #' , 'weforms'),
                'name'       => 'pcp_fax',
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'      => __('Do you use tobacco in any form?' , 'weforms'),
                'name'       => 'tobacco',
                'options'    => array(
                    'yes'    => __('Yes', 'weforms'),
                    'no'     => __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label' => __('If yes, please list type , amount and frequency of use' , 'weforms'),
                'name'  => 'amount_and_frequency',
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'  => __('Do you use Alcohol in any form?' , 'weforms'),
                'name'   => 'use_alcohol',
                'options'    => array(
                    'yes'    => __('Yes', 'weforms'),
                    'no'     => __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'  => __('Do you use Alcohol in any form?' , 'weforms'),
                'name'   => 'alcohol_friquency',
            ) ),

            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label'  => __('Are you taking any medication? If yes, please list medication and doage per day' , 'weforms'),
                'name'   => 'medicin_list',
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Abnormal Bleeding' , 'weforms'),
                'name'      => 'abnormal_bleeding',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Alcohol Abuse' , 'weforms'),
                'name'      => 'alcohol_abuse',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Allergies' , 'weforms'),
                'name'      => 'allergies',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Anemia' , 'weforms'),
                'name'      => 'anemia',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Angina Pectoris' , 'weforms'),
                'name'      => 'angina_pectoris',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Arthritis' , 'weforms'),
                'name'      => 'arthritis',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Artificial Heart Valve' , 'weforms'),
                'name'      => 'heart_valve',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Asthma' , 'weforms'),
                'name'      => 'asthma',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Blood Transfusion' , 'weforms'),
                'name'      => 'blood_transfusion',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Cancer' , 'weforms'),
                'name'      => 'cancer',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Chemotherapy' , 'weforms'),
                'name'      => 'chemotherapy',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Congenital Heart Defect' , 'weforms'),
                'name'      => 'congenital_heart_defect',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Diabetes' , 'weforms'),
                'name'      => 'diabetes',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Difficulty Breathing' , 'weforms'),
                'name'      => 'difficulty_breathing',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Drug Abuse' , 'weforms'),
                'name'      => 'drug_abuse',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Emphysema' , 'weforms'),
                'name'      => 'emphysema',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Epilepsy' , 'weforms'),
                'name'      => 'epilepsy',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Facial Surgery' , 'weforms'),
                'name'      => 'facial_surgery',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Fainting Spells' , 'weforms'),
                'name'      => 'fainting_spells',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Fever Blisters' , 'weforms'),
                'name'      => 'fever_blisters',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Frequent Headaches' , 'weforms'),
                'name'      => 'frequent_headaches',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Glaucoma' , 'weforms'),
                'name'      => 'glaucoma',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('HIV + AIDS' , 'weforms'),
                'name'      => 'hiv_aids',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Heart Attack' , 'weforms'),
                'name'      => 'heart_attack',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Heart Murmur' , 'weforms'),
                'name'      => 'heart_murmur',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Heart Surgery' , 'weforms'),
                'name'      => 'heart_surgery',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Hemophilia' , 'weforms'),
                'name'      => 'hemophilia',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Hepatitis A' , 'weforms'),
                'name'      => 'hepatitis_a',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Hepatitis B' , 'weforms'),
                'name'      => 'hepatitis_b',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Hepatitis C' , 'weforms'),
                'name'      => 'hepatitis_c',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('High Blood Pressure' , 'weforms'),
                'name'      => 'high_blood_pressure',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Joint Replacement' , 'weforms'),
                'name'      => 'joint_replacement',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Kidney Problems' , 'weforms'),
                'name'      => 'kidney_problems',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Liver Disease' , 'weforms'),
                'name'      => 'liver_disease',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Low Blood Pressure' , 'weforms'),
                'name'      => 'low_blood_pressure',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Mitral Valve Prolaps' , 'weforms'),
                'name'      => 'mitral_valve_prolaps',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Pace Maker' , 'weforms'),
                'name'      => 'pace_maker',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Psychiatric Care' , 'weforms'),
                'name'      => 'psychiatric_care',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Radiation Therapy' , 'weforms'),
                'name'      => 'radiation_therapy',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Rheumatic Fever' , 'weforms'),
                'name'      => 'rheumatic_fever',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Seizures' , 'weforms'),
                'name'      => 'seizures',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Sexually Transmitted Disease' , 'weforms'),
                'name'      => 'sexually_transmitted_disease',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Shingles' , 'weforms'),
                'name'      => 'shingles',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Sickle Cell Disease' , 'weforms'),
                'name'      => 'sickle_cell_disease',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Sinus Problems' , 'weforms'),
                'name'      => 'sinus_problems',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Stroke' , 'weforms'),
                'name'      => 'stroke',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Thyroid Problems' , 'weforms'),
                'name'      => 'thyroid_problems',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Tuberculosis' , 'weforms'),
                'name'      => 'tuberculosis',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Ulcers' , 'weforms'),
                'name'      => 'ulcers',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'     => __('How may we help you today?' , 'weforms'),
                'name'      => 'help_today',
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Your current dental health is' , 'weforms'),
                'name'      => 'ulcers',
                'options'   =>  array(
                    'good'    =>  __('Good', 'weforms'),
                    'fair'    =>  __('Fair', 'weforms'),
                    'poor'    =>  __('Poor', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Do you require antibiotics before dental treatment?' , 'weforms'),
                'name'      => 'require_antibiotics',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Are you currently in pain?' , 'weforms'),
                'name'      => 'currently_pain',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Do you now or have you had any pain/discomfort in your jaw joint?' , 'weforms'),
                'name'      => 'discomfort',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Are you aware of clenching or grinding your teeth?' , 'weforms'),
                'name'      => 'grinding_your_teeth',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Does it hurt when you chew or open wide to take a bite?' , 'weforms'),
                'name'      => 'hurt_when',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Do you have any jaw symptoms or headaches upon waking up in the morning?' , 'weforms'),
                'name'      => 'jaw_symptoms',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Do you have pain in the face, cheeks, jaw, joints, throat or temples?' , 'weforms'),
                'name'      => 'face_pain',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Do you like your smile?' , 'weforms'),
                'name'      => 'your_smile',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Is there anything you would like to change about your smile?' , 'weforms'),
                'name'      => 'change_your_smile',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Are you happy with the color of your teeth?' , 'weforms'),
                'name'      => 'teeth_color',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Have you ever had gum disease?', 'weforms'),
                'name'      => 'gum_disease',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Do your gums bleed?', 'weforms'),
                'name'      => 'gums_bleed',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Have you ever had a deep cleaning or scaling and root planing?', 'weforms'),
                'name'      => 'root_planing',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'     => __('Floss/Week', 'weforms'),
                'name'      => 'floss_week',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'     => __('Brush/Day', 'weforms'),
                'name'      => 'brush_day',
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Are your teeth sensitive to heat, cold or anything else?', 'weforms'),
                'name'      => 'teeth_sensitive',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Do you take fluoride supplements?', 'weforms'),
                'name'      => 'fluoride_supplements',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Have you ever had a serious/difficult problem with any previous dental work?', 'weforms'),
                'name'      => 'previous_dental_work',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Have you ever had any unfavorable dental experiences?', 'weforms'),
                'name'      => 'unfavorable',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Are you apprehensive about dental treatment?', 'weforms'),
                'name'      => 'dental_apprehensive',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'     => __('Do you gag easily?', 'weforms'),
                'name'      => 'gag_easily',
                'options'   =>  array(
                    'yes'       =>  __('Yes', 'weforms'),
                    'no'        =>  __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'     => __('When was your last dental cleaning?', 'weforms'),
                'name'      => 'dental cleaning',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'     => __('When was you last dental visit?', 'weforms'),
                'name'      => 'dental_visit',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'     => __('How can we accommodate you better during your dental visit?', 'weforms'),
                'name'      => 'accommodate_you_better',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'     => __('Is there any specific service and/or concern you would like to inquire about?', 'weforms'),
                'name'      => 'dental_inquire',
            ) ),
        );

        return $form_fields;
    }

}
