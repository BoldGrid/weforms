<?php

class WPUF_Contact_Form_Template_Support extends WPUF_Post_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = true;
        $this->title       = __( 'Support Form', 'wpuf-contact-form' );
        $this->description = __( 'Enable your users for asking support questions.', 'wpuf-contact-form' );

        $this->form_fields = array(
            array(
                'input_type'       => 'name',
                'template'         => 'name_field',
                'required'         => 'yes',
                'label'            => 'Name',
                'name'             => 'name',
                'is_meta'          => 'yes',
                'format'           => 'first-last',
                'first_name'       => array(
                    'placeholder'      => '',
                    'default'          => '',
                    'sub'              => 'First',
                ),
                'middle_name'      => array(
                    'placeholder'      => '',
                    'default'          => '',
                    'sub'              => 'Middle',
                ),
                'last_name'        => array(
                    'placeholder'      => '',
                    'default'          => '',
                    'sub'              => 'Last',
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
                'label'            => 'Email',
                'name'             => 'email',
                'is_meta'          => 'yes',
                'help'             => 'Please provide a valid email address so we can get back to you',
                'css'              => '',
                'placeholder'      => '',
                'default'          => '',
                'size'             => 40,
                'wpuf_cond'        => $this->conditionals
            ),
            array(
                'input_type'          => 'radio',
                'template'            => 'radio_field',
                'required'            => 'yes',
                'label'               => 'Department',
                'name'                => 'department',
                'is_meta'             => 'yes',
                'help'                => '',
                'css'                 => '',
                'selected'            => '',
                'inline'              => 'no',
                'options'             => array(
                    'sales'               => 'Sales',
                    'support'             => 'Customer Support',
                    'product_development' => 'Product Development',
                    'other'               => 'Other',
                ),
                'wpuf_cond'        => $this->conditionals
            ),
            array(
                'input_type'       => 'text',
                'template'         => 'text_field',
                'required'         => 'yes',
                'label'            => 'Subject',
                'name'             => 'subject',
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
                'input_type'       => 'textarea',
                'template'         => 'textarea_field',
                'required'         => 'yes',
                'label'            => 'Message',
                'name'             => 'message',
                'is_meta'          => 'yes',
                'help'             => 'How may we help you? Please be brief as much as possible.',
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
            'message'            => 'Thanks for contacting us! We will get in touch with you shortly.',
            'page_id'            => '',
            'url'                => '',
            'submit_text'        => 'Submit Query',
            'schedule_form'      => 'false',
            'schedule_start'     => '',
            'schedule_end'       => '',
            'sc_pending_message' => 'Form submission hasn\'t been started yet',
            'sc_expired_message' => 'Form submission is now closed.',
            'require_login'      => 'false',
            'req_login_message'  => 'You need to login to submit a query.',
            'limit_entries'      => 'false',
            'limit_number'       => '100',
            'limit_message'      => 'Sorry, we have reached the maximum number of submissions.',
            'label_position'     => 'above',
        );

        $this->form_notifications = array(
            array(
                'active'      => 'true',
                'name'        => 'Admin Notification',
                'subject'     => '[{form_name}] New Form Submission #{entry_id}',
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
