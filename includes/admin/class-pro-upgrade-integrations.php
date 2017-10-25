<?php

/**
 * The Pro Integration Prompt Class
 */
class WeForms_Pro_Integration_Prompt extends WeForms_Abstract_Integration {

    /**
     * Check if it's a pro field
     *
     * @return boolean
     */
    public function is_pro() {
        return true;
    }
}

/**
 * Mailchimp
 */
class WeForms_Pro_Integration_MailChimp extends WeForms_Pro_Integration_Prompt {

    function __construct() {
        $this->id    = 'mailchimp';
        $this->title = __( 'MailChimp', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-mailchimp.png';
    }
}

/**
 * Campaign Monitor
 */
class WeForms_Pro_Integration_CM extends WeForms_Pro_Integration_Prompt {

    function __construct() {
        $this->id    = 'campaign-monitor';
        $this->title = __( 'Campaign Monitor', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-campaign-monitor.png';
    }
}

/**
 * Constant Contact
 */
class WeForms_Pro_Integration_CC extends WeForms_Pro_Integration_Prompt {

    function __construct() {
        $this->id    = 'constant-contact';
        $this->title = __( 'Constant Contact', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-constant-contact.png';
    }
}


/**
 * AWeber
 */
class WeForms_Pro_Integration_AWeber extends WeForms_Pro_Integration_Prompt {

    function __construct() {
        $this->id    = 'aweber';
        $this->title = __( 'AWeber', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-aweber.png';
    }
}


/**
 * ConvertKit
 */
class WeForms_Pro_Integration_ConvertKit extends WeForms_Pro_Integration_Prompt {

    function __construct() {
        $this->id    = 'convertkit';
        $this->title = __( 'ConvertKit', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-convertkit.png';
    }
}

