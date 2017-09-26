<?php

class WPUF_Contact_Form_Template_Event_Registration extends WPUF_Post_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = true;
        $this->title       = __( 'Event Registration', 'weforms' );
        $this->description = __( 'Get your visitors to register for an upcoming event quickly with this registration form template.', 'weforms' );
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/contact.png';
        $this->category    = 'event';

        $this->form_fields = array(
            array(
                'input_type'       => 'name',
                'template'         => 'name_field',
                'required'         => 'yes',
                'label'            => __( 'Name', 'weforms' ),
                'name'             => 'name',
                'is_meta'          => 'yes',
                'format'           => 'first-last',
                'first_name'       => array(
                    'placeholder'      => '',
                    'default'          => '',
                    'sub'              => __( 'First', 'weforms' ),
                ),
                'middle_name'      => array(
                    'placeholder'      => '',
                    'default'          => '',
                    'sub'              => __( 'Middle', 'weforms' ),
                ),
                'last_name'        => array(
                    'placeholder'      => '',
                    'default'          => '',
                    'sub'              => __( 'Last', 'weforms' ),
                ),
                'hide_subs'        => '',
                'help'             => '',
                'css'              => '',
                'wpuf_cond'        => $this->conditionals
            ),
            array(
                'input_type'       => 'text',
                'template'         => 'email_address',
                'required'         => 'yes',
                'label'            => __( 'Email', 'weforms' ),
                'name'             => 'email',
                'is_meta'          => 'yes',
                'help'             => '',
                'css'              => '',
                'placeholder'      => '',
                'default'          => '',
                'size'             => 40,
                'wpuf_cond'        => $this->conditionals
            ),
            array(
                'input_type'       => 'text',
                'template'         => 'text_field',
                'required'         => 'yes',
                'label'            => __( 'Phone', 'weforms' ),
                'name'             => 'phone',
                'is_meta'          => 'yes',
                'help'             => '',
                'css'              => '',
                'placeholder'      => '',
                'default'          => '',
                'size'             => 40,
                'word_restriction' => '',
                'wpuf_cond'        => $this->conditionals
            ),
            array(
                'input_type'       => 'text',
                'template'         => 'text_field',
                'required'         => 'no',
                'label'            => __( 'Company', 'weforms' ),
                'name'             => 'company',
                'is_meta'          => 'yes',
                'help'             => '',
                'css'              => '',
                'placeholder'      => '',
                'default'          => '',
                'size'             => 40,
                'word_restriction' => '',
                'wpuf_cond'        => $this->conditionals
            ),
            array(
                'input_type'       => 'text',
                'template'         => 'website_url',
                'required'         => 'no',
                'label'            => __( 'Website', 'weforms' ),
                'name'             => 'website',
                'is_meta'          => 'yes',
                'help'             => '',
                'css'              => '',
                'placeholder'      => 'https://',
                'default'          => '',
                'size'             => 40,
                'wpuf_cond'        => $this->conditionals
            ),
            array(
                'input_type'       => 'radio',
                'template'         => 'radio_field',
                'required'         => 'yes',
                'label'            => __( 'Have you attended before?', 'weforms' ),
                'name'             => 'attended_before',
                'is_meta'          => 'yes',
                'help'             => '',
                'css'              => '',
                'selected'         => '',
                'inline'           => 'no',
                'options'          => array(
                    'yes' => __( 'Yes', 'weforms' ),
                    'no'  => __( 'No', 'weforms' ),
                ),
                'wpuf_cond'        => $this->conditionals
            ),
            array(
                'input_type'       => 'checkbox',
                'template'         => 'checkbox_field',
                'required'         => 'yes',
                'label'            => __( 'Dietary Requirements', 'weforms' ),
                'name'             => 'dietary_requirements',
                'is_meta'          => 'yes',
                'help'             => '',
                'css'              => '',
                'selected'         => array(),
                'inline'           => 'no',
                'options'          => array(
                    'none'             => __( 'None', 'weforms' ),
                    'vegeterian'       => __( 'Vegeterian', 'weforms' ),
                    'vegan'            => __( 'Vegan', 'weforms' ),
                    'lactose-free'     => __( 'Lactose-free', 'weforms' ),
                    'gluten-free'      => __( 'Gluten-free', 'weforms' ),
                    'kosher'           => __( 'Kosher', 'weforms' ),
                    'halal'            => __( 'Halal', 'weforms' ),
                    'allergies-other'  => __( 'Allergies/Other', 'weforms' ),
                ),
                'wpuf_cond'        => $this->conditionals
            ),
            array(
                'input_type'       => 'radio',
                'template'         => 'radio_field',
                'required'         => 'yes',
                'label'            => 'Do you require any special assistance?',
                'name'             => 'special_assistance',
                'is_meta'          => 'yes',
                'help'             => '',
                'css'              => '',
                'selected'         => '',
                'inline'           => 'no',
                'options'          => array(
                    'yes' => __( 'Yes', 'weforms' ),
                    'no'  => __( 'No', 'weforms' ),
                ),
                'wpuf_cond'        => $this->conditionals
            ),
            array(
                'input_type'       => 'textarea',
                'template'         => 'textarea_field',
                'required'         => 'no',
                'label'            => __( 'Comments or Questions', 'weforms' ),
                'name'             => 'comments',
                'is_meta'          => 'yes',
                'help'             => '',
                'css'              => '',
                'rows'             => 5,
                'cols'             => 25,
                'placeholder'      => '',
                'default'          => '',
                'rich'             => 'no',
                'word_restriction' => '',
                'wpuf_cond'        => $this->conditionals
            ),
        );

        $this->form_settings = array(
            'redirect_to'        => 'same',
            'message'            => __( 'Thanks for signing up! We will get in touch with you shortly.', 'weforms' ),
            'page_id'            => '',
            'url'                => '',
            'submit_text'        => __( 'Register', 'weforms' ),
            'schedule_form'      => 'false',
            'schedule_start'     => '',
            'schedule_end'       => '',
            'sc_pending_message' => __( 'Form submission hasn\'t been started yet', 'weforms' ),
            'sc_expired_message' => __( 'Form submission is now closed.', 'weforms' ),
            'require_login'      => 'false',
            'req_login_message'  => __( 'You need to login to submit a query.', 'weforms' ),
            'limit_entries'      => 'false',
            'limit_number'       => '100',
            'limit_message'      => __( 'Sorry, we have reached the maximum number of submissions.', 'weforms' ),
            'label_position'     => 'above',
        );

        $this->form_notifications = array(
            array(
                'active'      => 'true',
                'name'        => __( 'Admin Notification', 'weforms' ),
                'subject'     => '[{form_name}] ' . __( 'New Form Submission', 'weforms' ) . ' #{entry_id}',
                'to'          => '{admin_email}',
                'replyTo'     => '{field:email}',
                'message'     => '{all_fields}',
                'fromName'    => '{site_name}',
                'fromAddress' => '{admin_email}',
                'cc'          => '',
                'bcc'         => '',
            ),
        );
    }
}
