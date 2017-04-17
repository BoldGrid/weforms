<?php

class WPUF_Contact_Form_Template_Contact extends WPUF_Post_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = true;
        $this->title       = __( 'Contact Form', 'wpuf-contact-form' );
        $this->description = __( 'Create a simple contact form for your site.', 'wpuf-contact-form' );
    }
}
