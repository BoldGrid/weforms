<?php

/**
 * The Pro Integration Prompt Class
 */
class WeForms_Pro_Integration_Prompt extends WeForms_Abstract_Integration {
    /**
     * Check if it's a pro field
     *
     * @return bool
     */
    public function is_pro() {
        return true;
    }
}

/**
 * Mailchimp
 */
class WeForms_Pro_Integration_MailChimp extends WeForms_Pro_Integration_Prompt {
    public function __construct() {
        $this->id    = 'mailchimp';
        $this->title = __( 'MailChimp', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-mailchimp.svg';
    }
}

/**
 * Campaign Monitor
 */
class WeForms_Pro_Integration_CM extends WeForms_Pro_Integration_Prompt {
    public function __construct() {
        $this->id    = 'campaign-monitor';
        $this->title = __( 'Campaign Monitor', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-campaign-monitor.svg';
    }
}

/**
 * Constant Contact
 */
class WeForms_Pro_Integration_CC extends WeForms_Pro_Integration_Prompt {
    public function __construct() {
        $this->id    = 'constant-contact';
        $this->title = __( 'Constant Contact', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-constant-contact.svg';
    }
}

/**
 * AWeber
 */
class WeForms_Pro_Integration_AWeber extends WeForms_Pro_Integration_Prompt {
    public function __construct() {
        $this->id    = 'aweber';
        $this->title = __( 'AWeber', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-aweber.svg';
    }
}

/**
 * ConvertKit
 */
class WeForms_Pro_Integration_ConvertKit extends WeForms_Pro_Integration_Prompt {
    public function __construct() {
        $this->id    = 'convertkit';
        $this->title = __( 'ConvertKit', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-convertkit.svg';
    }
}

/**
 * GetResponse
 */
class WeForms_Pro_Integration_GetResponse extends WeForms_Pro_Integration_Prompt {
    public function __construct() {
        $this->id    = 'getresponse';
        $this->title = __( 'GetResponse', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-get-response.png';
    }
}

/**
 * Google Analytics
 */
class WeForms_Pro_Integration_GoogleAnalytics extends WeForms_Pro_Integration_Prompt {
    public function __construct() {
        $this->id    = 'google_analytics';
        $this->title = __( 'Google Analytics', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-google-analytics.svg';
    }
}

/**
 * Google Sheets
 */
class WeForms_Pro_Integration_GoogleSheets extends WeForms_Pro_Integration_Prompt {
    public function __construct() {
        $this->id    = 'google_sheets';
        $this->title = __( 'Google Sheets', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-google-sheets.svg';
    }
}

/**
 * HubSpot
 */
class WeForms_Pro_Integration_HubSpot extends WeForms_Pro_Integration_Prompt {
    public function __construct() {
        $this->id    = 'hubspot';
        $this->title = __( 'HubSpot', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-hubspot.svg';
    }
}

/**
 * SalesForce
 */
class WeForms_Pro_Integration_SalesForce extends WeForms_Pro_Integration_Prompt {
    public function __construct() {
        $this->id    = 'salesforce';
        $this->title = __( 'SalesForce', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-salesforce.svg';
    }
}

/**
 * Trello
 */
class WeForms_Pro_Integration_Trello extends WeForms_Pro_Integration_Prompt {
    public function __construct() {
        $this->id    = 'trello';
        $this->title = __( 'Trello', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-trello.svg';
    }
}

/**
 * Zapier
 */
class WeForms_Pro_Integration_Zapier extends WeForms_Pro_Integration_Prompt {
    public function __construct() {
        $this->id    = 'zapier';
        $this->title = __( 'Zapier', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-zapier.svg';
    }
}

/**
 * Zoho
 */
class WeForms_Pro_Integration_Zoho extends WeForms_Pro_Integration_Prompt {
    public function __construct() {
        $this->id    = 'zoho';
        $this->title = __( 'Zoho', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-zoho.svg';
    }
}
