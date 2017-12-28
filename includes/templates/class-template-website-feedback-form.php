<?php

/**
 * Blank form template
 */
class WeForms_Template_Website_Feedback extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = class_exists('WeForms_Pro');
        $this->title       = __( 'Website Feedback', 'weforms' );
        $this->description = __( 'If you have, own, or manage a website then this is a form that is definitely for you. This form allows users and visitors of your website to rate and give feedback including comments about your website which can help you to improve it!', 'weforms' );
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/website-feedback.png';
        $this->category    = 'feedback';
    }

    /**
     * Get the form fields
     *
     * @return array
     */
    public function get_form_fields() {
        $all_fields      = $this->get_available_fields();

        $get_form_fields = array(
            array_merge( $all_fields['custom_html']->get_field_props(), array(
                'html'      => sprintf( '<h3>%s</h3>', __( 'Website Feedback', 'weforms' ) ),
            ) ),
            array_merge( $all_fields['name_field']->get_field_props(), array(
                'required'      => 'yes',
                'label'         => 'Full Name',
                'format'        => 'first-last',

                'first_name'    => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'First', 'weforms' )
                ),

                'last_name'     => array(
                    'placeholder'       => '',
                    'default'           => '',
                    'sub'               => __( 'Last', 'weforms' )
                ),
                'hide_subs'     => false,
                'name'          => 'format',
            ) ),

            array_merge( $all_fields['email_address']->get_field_props(), array(
                'required'      => 'yes',
                'label'         => 'Email Address',
                'name'          => 'email_address',
            ) ),

            array_merge( $all_fields['ratings']->get_field_props(), array(
                'required'      => 'yes',
                'label'         => __('User Friendliness', 'weforms'),
                'name'          => 'user_friendliness',
                'options'       => array(
                    '1'             => __('1', 'weforms'),
                    '2'             => __('2', 'weforms'),
                    '3'             => __('3', 'weforms'),
                    '4'             => __('4', 'weforms'),
                    '5'             => __('5', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['ratings']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __('Visual Appeal', 'weforms'),
                'name'      => 'visual_appeal',
                'options'   => array(
                    '1'         => __('1', 'weforms'),
                    '2'         => __('2', 'weforms'),
                    '3'         => __('3', 'weforms'),
                    '4'         => __('4', 'weforms'),
                    '5'         => __('5', 'weforms'),
                ),
            ) ),


            array_merge( $all_fields['ratings']->get_field_props(), array(
                'label'     => __('Correct Info', 'weforms'),
                'name'      => 'correct_info',
                'options'   => array(
                    '1'         => __('1', 'weforms'),
                    '2'         => __('2', 'weforms'),
                    '3'         => __('3', 'weforms'),
                    '4'         => __('4', 'weforms'),
                    '5'         => __('5', 'weforms'),
                ),
            ) ),

            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'required'      => 'yes',
                'label'         => __('Suggestion, Comments, Or Other Things You Like or Don\'t Like', 'weforms'),
                'name'          => 'suggestion_comment',
            ) ),
        );

        return $get_form_fields;
    }

}
