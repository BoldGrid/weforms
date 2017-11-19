<?php

/**
 * Blank form template
 */
class WeForms_Template_Job_Listing extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = true;
        $this->title       = __( 'Job Listing Form', 'weforms' );
        $this->description = __( 'This simple template is the easy and fastest way to apply online. Gather information and upload resume using the form.', 'weforms' );
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/job_listing.png';
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
            array_merge( $all_fields['section_break']->get_field_props(), array(
                'label'         =>  __('Contact Person', 'weforms'),
                'description'   => ' ',
                'name'          => 'contact_person',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'      =>  __('Company Name', 'weforms'),
                'name'       => 'company_name',
            ) ),

            array_merge( $all_fields['dropdown_field']->get_field_props(), array(
                'label'      =>  __('Salutation', 'weforms'),
                'name'       => 'salutation',
                'options'    => array(
                    'mr'        => __('Mr', 'weforms'),
                    'mrs'       => __('Mrs', 'weforms'),
                    'miss'      => __('Miss', 'weforms'),
                    'ms.'       => __('Ms.', 'weforms'),
                    'dr.'       => __('Dr.', 'weforms'),
                    'prof'      => __('Prof.', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['name_field']->get_field_props(), array(
                'required'   => 'yes',
                'format'     => 'first-middle-last',

                'first_name'    => array(
                    'placeholder'   => '',
                    'default'       => '',
                    'sub'           => __( 'First Name', 'weforms' )
                ),

                'middle_name'   => array(
                    'placeholder'   => '',
                    'default'       => '',
                    'sub'           => __( 'Middle Name', 'weforms' )
                ),

                'last_name'     => array(
                    'placeholder'   => '',
                    'default'       => '',
                    'sub'           => __( 'Last Name', 'weforms' )
                ),
                'hide_subs'     => false,
                'name'          => 'format',
            ) ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      =>  __('Phone Number', 'weforms'),
                'name'       => 'phone_number',
            ) ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'label'      =>  __('Fax Number', 'weforms'),
                'name'       => 'fax_number',
            ) ),

            array_merge( $all_fields['address_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      =>  __('Address', 'weforms'),
                'name'       => 'address',
            ) ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      =>  __('Postal Code', 'weforms'),
                'name'       => 'postal_code',
            ) ),

            array_merge( $all_fields['country_list_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      =>  __('Province', 'weforms'),
                'name'       => 'province',
            ) ),

            array_merge( $all_fields['country_list_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      =>  __('Country', 'weforms'),
                'name'       => 'country',
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'      =>  ' ',
                'name'       => 'contact_information',
                'options'    =>  array(
                    'show_information'    =>    __('Show contact information in public posting', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['section_break']->get_field_props(), array(
                'label'         =>  __('Position Information', 'weforms'),
                'description'   => ' ',
                'name'          => 'position_information',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      =>  __('Company Name', 'weforms'),
                'name'       => 'position_company_name',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'      =>  __('Department / Division', 'weforms'),
                'name'       => 'department',
            ) ),

            array_merge( $all_fields['address_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      =>  __('Department / Division', 'weforms'),
                'name'       => 'department',
            ) ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'label'      =>  __('Postal Code', 'weforms'),
                'name'       => 'position_postal_code',
            ) ),

            array_merge( $all_fields['country_list_field']->get_field_props(), array(
                'label'      =>  __('Province', 'weforms'),
                'name'       => 'position_province',
            ) ),

            array_merge( $all_fields['country_list_field']->get_field_props(), array(
                'label'      =>  __('Country', 'weforms'),
                'name'       => 'position_country',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'      =>  __('Position Title', 'weforms'),
                'name'       => 'position_title',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'      =>  __('Reference Number', 'weforms'),
                'name'       => 'reference_number',
            ) ),

            array_merge( $all_fields['website_url']->get_field_props(), array(
                'label'      =>  __('Job Posting Url', 'weforms'),
                'name'       => 'job_posting_url',
            ) ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'label'      =>  __('Min Salary', 'weforms'),
                'name'       => 'min_salary',
            ) ),

            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'label'      =>  __('Max Salary', 'weforms'),
                'name'       => 'max_salary',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'      =>  __('Salary Details', 'weforms'),
                'name'       => 'salary_details',
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'      => ' ',
                'name'       => 'salary_negotiable',
                'options'    => array(
                    'salary_negotiable_check'   =>  __('Salary Negotiable', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['dropdown_field']->get_field_props(), array(
                'label'      => 'Type of Employment',
                'name'       => 'type_of_employment',
                'options'    => array(
                    'full_time'   =>  __('Full Time', 'weforms'),
                    'part_time'   =>  __('Part Time', 'weforms'),
                ),

                'first'      => ' ',
            ) ),

            array_merge( $all_fields['dropdown_field']->get_field_props(), array(
                'label'      => 'Type of Contract',
                'name'       => 'type_of_Contract',
                'options'    => array(
                    'permanent'   =>  __('Permanent', 'weforms'),
                    'term'        =>  __('Term/Contract', 'weforms'),
                    'locum'       =>  __('Locum', 'weforms'),
                ),

                'first'      => ' ',
            ) ),

            array_merge( $all_fields['dropdown_field']->get_field_props(), array(
                'label'      => 'Type Of Position',
                'name'       => 'type_of_position',
                'options'    => array(
                    'academic'                 =>  __('Academic', 'weforms'),
                    'administrative'           =>  __('Administrative', 'weforms'),
                    'obstetrics'               =>  __('Obstetrics', 'weforms'),
                    'gynaecology'              =>  __('Gynaecology', 'weforms'),
                    'obstetrics_gynaecology'   =>  __('Obstetrics/Gynaecology', 'weforms'),
                    'other'                    =>  __('Other', 'weforms'),
                ),

                'first'      => ' ',
            ) ),

            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label'      => 'Job Summary',
                'name'       => 'job_summary',
            ) ),

            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label'      => 'Roles and Responsibilities',
                'name'       => 'roles_and_responsibilities',
            ) ),

            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label'      => 'Skills and Competencies',
                'name'       => 'skills_and_competencies',
            ) ),

            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label'      => 'Education and Experience',
                'name'       => 'education_and_experience',
            ) ),

            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label'      => 'Other',
                'name'       => 'other',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      => 'Start Date of Employment',
                'name'       => 'start_date_of_employment',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      => 'Application Deadline',
                'name'       => 'application_deadline',
            ) ),

            array_merge( $all_fields['section_break']->get_field_props(), array(
                'label'         =>  __('Payment Information', 'weforms'),
                'name'          => 'payment_information',
                'description'   => ' ',
            ) ),

            array_merge( $all_fields['radio_field']->get_field_props(), array(
                'title'      =>  __('Posting Duration (in Months)', 'weforms'),
                'name'       => 'posting_duration',
                'options'    => array(
                    'one'    => __('$ 350.00 - 1 Month Posting Duration', 'weforms'),
                    'two'    => __('$ 600.00 - 2 Month Posting Duration', 'weforms'),
                    'three'  => __('$ 850.00 - 3 Month Posting Duration', 'weforms'),
                ),
            ) ),
        );

        return $form_fields;
    }

}
