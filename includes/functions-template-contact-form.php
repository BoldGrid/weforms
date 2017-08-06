<?php

/**
 * Get form fields of contact form template
 *
 * @since 1.0.0-beta.3
 *
 * @return array
 */
function weforms_get_contactform_template_fields() {
    $conditionals = array(
        'condition_status' => 'no',
        'cond_field'       => array(),
        'cond_operator'    => array( '=' ),
        'cond_option'      => array( '- select -' ),
        'cond_logic'       => 'all'
    );

    return array(
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
            'wpuf_cond'        => $conditionals
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
            'wpuf_cond'        => $conditionals
        ),
        array(
            'input_type'       => 'textarea',
            'template'         => 'textarea_field',
            'required'         => 'yes',
            'label'            => __( 'Message', 'weforms' ),
            'name'             => 'message',
            'is_meta'          => 'yes',
            'help'             => '',
            'css'              => '',
            'rows'             => 5,
            'cols'             => 25,
            'placeholder'      => '',
            'default'          => '',
            'rich'             => 'no',
            'word_restriction' => '',
            'wpuf_cond'        => $conditionals
        ),
    );
}

/**
 * Get settings of contact form template
 *
 * @since 1.0.0-beta.3
 *
 * @return array
 */
function weforms_get_contactform_template_settings() {
    return array(
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
}

/**
 * Get notifications of contact form template
 *
 * @since 1.0.0-beta.3
 *
 * @return array
 */
function weforms_get_contactform_template_notification() {
    return array(
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
