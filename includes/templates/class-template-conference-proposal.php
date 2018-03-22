<?php
/**
 * Blank form template
 */
class Weforms_Template_Conference_Proposal extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = true;
        $this->title       = __( 'Conference Proposal', 'weforms' );
        $this->description = __( 'A winning conference in any industry demands the highest quality candidates and presentations.', 'weforms' );
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/conference-proposal.png';
        $this->category    = 'request';
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
                'label'      =>  'Name',
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
            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'          =>  __('Job Title', 'weforms'),
                'name'           =>  'job_title',
            ) ),
            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'          =>  __('Company  Name', 'weforms'),
                'name'           =>  'company_name',
            ) ),
            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label'         =>  __('Biography', 'weforms'),
                'name'          =>  'biography',
            ) ),
            array_merge( $all_fields['email_address']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __('Email Address', 'weforms'),
                'name'     => 'email_address',
            ) ),
            array_merge( $all_fields['numeric_text_field']->get_field_props(), array(
                'label'    => __('Phone Number', 'weforms'),
                'name'     => 'phone_number',
            ) ),
            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'    => __('Proposal Title', 'weforms'),
                'name'     => 'proposals_title',
            ) ),
            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label'         =>  __('Short Description', 'weforms'),
                'name'          =>  'short_description',
            ) ),
            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label'         =>  __('Abstract', 'weforms'),
                'name'          =>  'abstract',
            ) ),
            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label'         =>  __('Abstract', 'weforms'),
                'name'          =>  'abstract',
            ) ),
            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'    => 'Topics',
                'name'     => 'topics',
                'options'  => array(
                    'firt_opic'     => __( 'Topic First', 'weforms' ),
                    'second_topic'  => __( 'Topic Second', 'weforms' ),
                    'third_topic'   => __( 'Topic Third', 'weforms' ),
                ),
            ) ),
            array_merge( $all_fields['dropdown_field']->get_field_props(), array(
                'label'    => 'Session Type',
                'name'     => 'session_type',
                'options'  => array(
                    'presentation'     => __( 'Presentation', 'weforms' ),
                    'panel'            => __( 'Panel', 'weforms' ),
                    'workshop'         => __( 'Work Shop', 'weforms' ),
                    'other'            => __( 'Other', 'weforms' ),
                ),
            ) ),
            array_merge( $all_fields['dropdown_field']->get_field_props(), array(
                'label'    => 'Audience Level',
                'name'     => 'audience_level',
                'options'  => array(
                    'novice'             => __( 'Novice', 'weforms' ),
                    'intermediate'       => __( 'Intermediate', 'weforms' ),
                    'expert'             => __( 'Expert', 'weforms' ),
                ),
            ) ),
            array_merge( $all_fields['text_field']->get_field_props(), array(
                'label'    => 'Video URL',
                'name'     => 'video_url'
            ) ),
            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label'    => __('Additional Information', 'weforms'),
                'name'     => 'additional_information'
            ) ),

        );

        return $form_fields;
    }

}