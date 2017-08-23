<?php

/**
 * The Pro Integrations
 */
class WeForms_Pro_Integrations {

    /**
     * Initialize
     */
    function __construct() {

        if ( class_exists( 'WeForms_Pro' ) ) {
            return;
        }

        add_filter( 'weforms_form_builder_integrations', array( $this, 'register_integrations' ) );
    }

    /**
     * Register the pro integrations
     *
     * @param  array $integrations
     *
     * @return array
     */
    public function register_integrations( $integrations ) {
        $pro = array(
            'mailchimp' => array(
                'id'       => 'mailchimp',
                'title'    => __( 'MailChimp', 'textdomain' ),
                'icon'     => WEFORMS_ASSET_URI . '/images/icon-mailchimp.png',
                'pro'      => true,
                'settings' => array(
                    'enabled' => false,
                    'list'    => '',
                    'double'  => false,
                    'fields'  => array(
                        'email'      => '',
                        'first_name' => '',
                        'last_name'  => ''
                    )
                )
            ),
            'campaign-monitor' => array(
                'id'    => 'campaign-monitor',
                'title' => __( 'Campaign Monitor', 'weforms' ),
                'icon'  => WEFORMS_ASSET_URI . '/images/icon-campaign-monitor.png',
                'pro'   => true
            ),
            'constant-contact' => array(
                'id'    => 'constant-contact',
                'title' => __( 'Constant Contact', 'weforms' ),
                'icon'  => WEFORMS_ASSET_URI . '/images/icon-constant-contact.png',
                'pro'   => true
            ),
            'mailpoet' => array(
                'id'    => 'mailpoet',
                'title' => __( 'MailPoet', 'weforms' ),
                'icon'  => WEFORMS_ASSET_URI . '/images/icon-mailpoet.png',
                'pro'   => true
            ),
            'aweber' => array(
                'id'    => 'aweber',
                'title' => __( 'AWeber', 'weforms' ),
                'icon'  => WEFORMS_ASSET_URI . '/images/icon-aweber.png',
                'pro'   => true
            ),
            'getresponse' => array(
                'id'    => 'getresponse',
                'title' => __( 'Get Response', 'weforms' ),
                'icon'  => WEFORMS_ASSET_URI . '/images/icon-getresponse.png',
                'pro'   => true
            ),
            'convertkit' => array(
                'id'    => 'convertkit',
                'title' => __( 'ConvertKit', 'weforms' ),
                'icon'  => WEFORMS_ASSET_URI . '/images/icon-convertkit.png',
                'pro'   => true
            ),
        );

        return array_merge( $integrations, $pro );
    }
}
