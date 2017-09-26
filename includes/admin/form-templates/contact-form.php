<?php

class WPUF_Contact_Form_Template_Contact extends WPUF_Post_Form_Template {

    public function __construct() {
        parent::__construct();
		
		$this->enabled     = true;
		$this->title       = __( 'Contact Form', 'weforms' );
		$this->description = __( 'Create a simple contact form for your site.', 'weforms' );
		$this->image       = WEFORMS_ASSET_URI . '/images/form-template/contact.png';

        $this->form_fields        = weforms_get_contactform_template_fields();
        $this->form_settings      = weforms_get_contactform_template_settings();
        $this->form_notifications = weforms_get_contactform_template_notification();
    }
}
