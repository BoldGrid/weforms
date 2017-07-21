<?php

class WPUF_Contact_Form_Template_Support extends WPUF_Post_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = true;
        $this->title       = __( 'Support Form', 'weforms' );
        $this->description = __( 'Enable your users for asking support questions.', 'weforms' );

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
                'help'             => __( 'Please provide a valid email address so we can get back to you', 'weforms' ),
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
                'label'               => __( 'Department', 'weforms' ),
                'name'                => 'department',
                'is_meta'             => 'yes',
                'help'                => '',
                'css'                 => '',
                'selected'            => '',
                'inline'              => 'no',
                'options'             => array(
                    'sales'               => __( 'Sales', 'weforms' ),
                    'support'             => __( 'Customer Support', 'weforms' ),
                    'product_development' => __( 'Product Development', 'weforms' ),
                    'other'               => __( 'Other', 'weforms' ),
                ),
                'wpuf_cond'        => $this->conditionals
            ),
            array(
                'input_type'       => 'text',
                'template'         => 'text_field',
                'required'         => 'yes',
                'label'            => __( 'Subject', 'weforms' ),
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
                'label'            => __( 'Message', 'weforms' ),
                'name'             => 'message',
                'is_meta'          => 'yes',
                'help'             => __( 'How may we help you? Please be brief as much as possible.', 'weforms' ),
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
            'message'            => __( 'Thanks for contacting us! We will get in touch with you shortly.', 'weforms' ),
            'page_id'            => '',
            'url'                => '',
            'submit_text'        => __( 'Submit Query', 'weforms' ),
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
                'name'        => 'Admin Notification',
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
