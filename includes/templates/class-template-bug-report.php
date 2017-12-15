<?php

/**
 * Blank form template
 */
class WeForms_Template_Bug_Report extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = true;
        $this->title       = __( 'Report a bug', 'weforms' );
        $this->description = __( 'Here\'s a great way to make a form for use with reporting issues, feedback, suggestions, and questions or bugtracking', 'weforms' );
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/bug_report.png';
        $this->category    = 'feedback';

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
                'label'           => __( 'Report a bug', 'weforms' ),
                'description'     => ' ',
            ) ),

            array_merge( $all_fields['step_start']->get_field_props(), array(
                'label'      => __( 'First Step', 'weforms' ),
                'name'       => 'report_bug_step',
                'step_start' => array(
                    'prev_button_text' => __( 'Previous', 'weforms' ),
                    'next_button_text' => __( 'Next', 'weforms' )
                ),
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'required'         =>  'yes',
                'label'            => __( 'Full Name', 'weforms' ),
                'name'             => 'full_name',
            ) ),

            array_merge( $all_fields['email_address']->get_field_props(), array(
                'required'         =>  'yes',
                'label'            => __( 'Email Address', 'weforms' ),
                'name'             => 'email_address',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'required'         =>  'yes',
                'label'            => __( 'Problem Title', 'weforms' ),
                'name'             => 'email_address',
            ) ),

            array_merge( $all_fields['dropdown_field']->get_field_props(), array(
                'label'            => __( 'Problem Status', 'weforms' ),
                'name'             => 'problem_status',
                'options'          => array(
                    'open'      => __('Open', 'weforms'),
                    'fixed'     => __('Fixed', 'weforms'),
                    'verified'  => __('Veried', 'weforms'),
                    'closed'    => __('Closed', 'weforms'),
                    'invalid'   => __('Invalid', 'weforms'),
                ),
                'first'            => '  ',
            ) ),

            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'required'         =>  'yes',
                'label'            => __( 'Summary Information', 'weforms' ),
                'name'             => 'summary_information',
            ) ),

            array_merge( $all_fields['step_start']->get_field_props(), array(
                'label'         => __( 'Second Step', 'weforms' ),
                'name'          => 'report_bug_step_two',
                'step_start'    => array(
                    'prev_button_text' => __( 'Previous', 'weforms' ),
                    'next_button_text' => __( 'Next', 'weforms' )
                ),
            ) ),

            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'required'      => 'yes',
                'label'         => __( 'Steps to Reproduce', 'weforms' ),
                'name'          => 'report_bug_two',
                'help_text'     => __( 'Include any setup or preparation work and the steps we can take to reproduce the problem', 'weforms' ),
            ) ),

            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'required'      => 'yes',
                'label'         => __( 'Results', 'weforms' ),
                'name'          => 'result',
                'help_text'     => __( 'Describe your results and how they differed from what you expected.', 'weforms' ),
            ) ),

            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label'         => __( 'Regression', 'weforms' ),
                'name'          => 'regression',
                'help_text'     => __( 'Provide information on steps taken to isolate the problem. Under what conditions or circumstances does the problem occur or not occur.', 'weforms' ),
            ) ),

            array_merge( $all_fields['checkbox_field']->get_field_props(), array(
                'label'         => __( 'Is there a Workaround?', 'weforms' ),
                'name'          => 'workaround',
                'options'       =>  array(
                    'yes'       => __('yes', 'weforms'),
                    'no'        => __('No', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label'         => __( 'Documentation & Notes', 'weforms' ),
                'name'          => 'documentation_notes',
                'help_text'     => __( 'Document any additional information that might be useful in resolving the problem.', 'weforms' ),
            ) ),

            array_merge( $all_fields['dropdown_field']->get_field_props(), array(
                'required'      => 'yes',
                'label'         => __( 'Reproducibility', 'weforms' ),
                'name'          => 'reproducibility',
                'options'       =>  array(
                    'try'              => __('I didn\'t try', 'weforms'),
                    'rare'             => __('Rarely', 'weforms'),
                    'sometimes'        => __('Sometimes', 'weforms'),
                    'always'           => __('Always', 'weforms'),
                ),

                'first'         => ' ',

            ) ),

            array_merge( $all_fields['dropdown_field']->get_field_props(), array(
                'required'      => 'yes',
                'label'         => __( 'Classification', 'weforms' ),
                'name'          => 'classification',
                'options'       =>  array(
                    'security'          => __('Security', 'weforms'),
                    'crash'             => __('Crash/Hang/Data loss', 'weforms'),
                    'performance'       => __('Performance/UI-Usability', 'weforms'),
                    'serious_bug'       => __('Serious Bug', 'weforms'),
                    'other_bug'         => __('Other Bug', 'weforms'),
                    'feature'           => __('Feature (New)', 'weforms'),
                    'enhancement'       => __('Enhancement', 'weforms'),
                ),

                'first'         => ' ',

            ) ),

            array_merge( $all_fields['dropdown_field']->get_field_props(), array(
                'required'      => 'yes',
                'label'         => __( 'Severity', 'weforms' ),
                'name'          => 'severity',
                'options'       =>  array(
                    'trivial'   => __('Trivial', 'weforms'),
                    'normal'    => __('Normal', 'weforms'),
                    'major'     => __('Major', 'weforms'),
                    'critical'  => __('Critical', 'weforms'),
                ),

                'first'         => ' ',

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
