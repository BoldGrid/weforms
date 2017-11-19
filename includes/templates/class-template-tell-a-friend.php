<?php
/**
 * A simple contact form for sending message
 */
class WeForms_Template_Tell_A_Friend extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled            = true;
        $this->title              = __( 'Tell A Friend Form', 'weforms' );
        $this->description        = __( 'Form used by a website owner that allows for visitors to " tell a friend".', 'weforms' );
        $this->category    = 'default';
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/tell-a-friend.png';
        $this->category    = 'others';
    }

    /**
     * Get the form fields
     *
     * @return array
     */
    public function get_form_fields() {
        $all_fields = $this->get_available_fields();

        $form_fields = array(
            array_merge( $all_fields['email_address']->get_field_props(), array(
                'required' => 'yes',
                'label'    => 'From',
                'name'     => 'email_from',
            ) ),
            array_merge( $all_fields['email_address']->get_field_props(), array(
                'required' => 'yes',
                'label'    => 'To',
                'name'     => 'email_to',
            ) ),
            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => __( 'Message', 'weforms' ),
                'name'     => 'message',
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
            'message'     => __( 'Thanks for sending your message! I will get in touch with you shortly.', 'weforms' ),
            'submit_text' => __( 'Send Message', 'weforms' ),
        ) );
    }


    /**
     * Get the form notifications
     *
     * @return  array
     */
    public function get_form_notifications() {
    	$defaults = $this->get_default_notification();

    	$form_notifications = array(
    		array_merge($defaults[0], array(
    			'active' 				=> 'true',
        		'name' 					=> 'Admin Notification',
        		'subject' 				=> '[{form_name}] New Form Submission #{entry_id}',
        		'to' 					=> '{admin_email}',
        		'replyTo' 				=> '{field:email}',
        		'message' 				=> '{all_fields}',
        		'fromName' 				=> '{site_name}',
        		'fromAddress' 			=> '{admin_email}',
        		'cc' 					=> '',
        		'bcc' 					=> '',
    		) ),
    		array_merge($defaults[0], array(
    			'active' 				=> 'true',
				'name' 					=> 'To Notification',
				'subject' 				=> 'New Message From [{field:from}] ',
				'to' 					=> '{field:to}',
				'replyTo' 				=> '{field:from}',
				'message' 				=> '{field:message}',
				'fromName' 				=> 'From Name',
				'fromAddress' 			=> '{field:from}',
				'cc' 					=> '',
				'bcc' 					=> '',
    		) ),
    	);

    	return $form_notifications;
    }

}
