<?php

/**
 * Contact form template
 */
class WeForms_Template_Delete_Data_Request extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = true;
        $this->title       = __( 'Data Erasure Request Form', 'weforms' );
        $this->description = __( "Includes action to add users to WordPress' personal data delete tool, allowing admins to comply with the GDPR and other privacy regulations from the site's front end.", 'weforms' );
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/data-delete-form.png';
        $this->category    = 'default';
    }

    /**
     * Get the form fields
     *
     * @return array
     */
    public function get_form_fields() {
        $all_fields = $this->get_available_fields();

        $form_fields = [
            array_merge( $all_fields['custom_html']->get_field_props(), [
                'name'      => 'custom_html',
                'html'      => '<h3>Personal Data Erasure Request:</h3>
<p>If you have an account on this site or have left comments you can request to erase any personal data we hold about you. This does not include any data we are obliged to keep for administrative, legal, or security purposes.</p>

<p>Please use this form to request Personal Data erasure.</p>',
            ] ),

            array_merge( $all_fields['email_address']->get_field_props(), [
                'required'  => 'yes',
                'label'     => 'Your email address (required)',
                'name'      => 'user_email_address',
            ] ),
        ];

        return $form_fields;
    }

    /**
     * Get the form settings
     *
     * @return array
     */
    public function get_form_settings() {
        $defaults = $this->get_default_settings();

        return array_merge( $defaults, [
            'message'     => __( 'Your inquiry has been submitted. Please check your email to validate your data request.', 'weforms' ),
            'submit_text' => __( 'Send request', 'weforms' ),
        ] );
    }

    /**
     * Get the form notifications
     *
     * @return array
     */
    public function get_form_notifications() {
        $defaults = $this->get_default_notification();

        $message_body  = 'Hello, <br><br>';
        $message_body .= 'A request has been made to erase personal data of your account.<br><br>';
        $message_body .= 'To confirm this, please click on the following link: <br>';
        $message_body .= '{personal_data_erase_confirm_url} <br><br>';
        $message_body .= 'You can safely ignore and delete this email if you do not want to take this action. <br><br>';
        $message_body .= 'This email has been sent to {field:user_email_address} <br><br>';
        $message_body .= 'Regards, <br>';
        $message_body .= '{site_name} <br>';
        $message_body .= '{site_url}';

        $form_notifications = [
            array_merge( $defaults[0], [
                'active'                => 'true',
                'name'                  => 'User Notification',
                'subject'               => '[{form_name}] Confirm Action: Erase Personal Data',
                'to'                    => '{field:user_email_address}',
                'replyTo'               => '{admin_email}',
                'message'               => $message_body,
                'fromName'              => '{site_name}',
                'fromAddress'           => '{admin_email}',
                'cc'                    => '',
                'bcc'                   => '',
            ] ),
        ];

        return $form_notifications;
    }
}
