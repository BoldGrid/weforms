<?php

class WPUF_Contact_Form_Template_Event_Registration extends WPUF_Post_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = true;
        $this->title       = __( 'Event Registration', 'best-contact-form' );
        $this->description = __( 'Get your visitors to register for an upcoming event quickly with this registration form template.', 'best-contact-form' );

        $this->form_fields = array(
            array(
                'input_type'       => 'name',
                'template'         => 'name_field',
                'required'         => 'yes',
                'label'            => __( 'Name', 'best-contact-form' ),
                'name'             => 'name',
                'is_meta'          => 'yes',
                'format'           => 'first-last',
                'first_name'       => array(
                    'placeholder'      => '',
                    'default'          => '',
                    'sub'              => __( 'First', 'best-contact-form' ),
                ),
                'middle_name'      => array(
                    'placeholder'      => '',
                    'default'          => '',
                    'sub'              => __( 'Middle', 'best-contact-form' ),
                ),
                'last_name'        => array(
                    'placeholder'      => '',
                    'default'          => '',
                    'sub'              => __( 'Last', 'best-contact-form' ),
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
                'label'            => __( 'Email', 'best-contact-form' ),
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
                'label'            => __( 'Phone', 'best-contact-form' ),
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
                'label'            => __( 'Company', 'best-contact-form' ),
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
                'label'            => __( 'Website', 'best-contact-form' ),
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
                'label'            => __( 'Have you attended before?', 'best-contact-form' ),
                'name'             => 'attended_before',
                'is_meta'          => 'yes',
                'help'             => '',
                'css'              => '',
                'selected'         => '',
                'inline'           => 'no',
                'options'          => array(
                    'yes' => __( 'Yes', 'best-contact-form' ),
                    'no'  => __( 'No', 'best-contact-form' ),
                ),
                'wpuf_cond'        => $this->conditionals
            ),
            array(
                'input_type'       => 'checkbox',
                'template'         => 'checkbox_field',
                'required'         => 'yes',
                'label'            => __( 'Dietary Requirements', 'best-contact-form' ),
                'name'             => 'dietary_requirements',
                'is_meta'          => 'yes',
                'help'             => '',
                'css'              => '',
                'selected'         => array(),
                'inline'           => 'no',
                'options'          => array(
                    'none'             => __( 'None', 'best-contact-form' ),
                    'vegeterian'       => __( 'Vegeterian', 'best-contact-form' ),
                    'vegan'            => __( 'Vegan', 'best-contact-form' ),
                    'lactose-free'     => __( 'Lactose-free', 'best-contact-form' ),
                    'gluten-free'      => __( 'Gluten-free', 'best-contact-form' ),
                    'kosher'           => __( 'Kosher', 'best-contact-form' ),
                    'halal'            => __( 'Halal', 'best-contact-form' ),
                    'allergies-other'  => __( 'Allergies/Other', 'best-contact-form' ),
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
                    'yes' => __( 'Yes', 'best-contact-form' ),
                    'no'  => __( 'No', 'best-contact-form' ),
                ),
                'wpuf_cond'        => $this->conditionals
            ),
            array(
                'input_type'       => 'textarea',
                'template'         => 'textarea_field',
                'required'         => 'no',
                'label'            => __( 'Comments or Questions', 'best-contact-form' ),
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
            'message'            => __( 'Thanks for signing up! We will get in touch with you shortly.', 'best-contact-form' ),
            'page_id'            => '',
            'url'                => '',
            'submit_text'        => __( 'Register', 'best-contact-form' ),
            'schedule_form'      => 'false',
            'schedule_start'     => '',
            'schedule_end'       => '',
            'sc_pending_message' => __( 'Form submission hasn\'t been started yet', 'best-contact-form' ),
            'sc_expired_message' => __( 'Form submission is now closed.', 'best-contact-form' ),
            'require_login'      => 'false',
            'req_login_message'  => __( 'You need to login to submit a query.', 'best-contact-form' ),
            'limit_entries'      => 'false',
            'limit_number'       => '100',
            'limit_message'      => __( 'Sorry, we have reached the maximum number of submissions.', 'best-contact-form' ),
            'label_position'     => 'above',
        );

        $this->form_notifications = array(
            array(
                'active'      => 'true',
                'name'        => __( 'Admin Notification', 'best-contact-form' ),
                'subject'     => '[{form_name}] ' . __( 'New Form Submission', 'best-contact-form' ) . ' #{entry_id}',
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
