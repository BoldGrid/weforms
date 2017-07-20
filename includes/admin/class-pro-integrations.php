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
            'constant-contact' => array(
                'id'    => 'constant-contact',
                'title' => __( 'Constant Contact', 'textdomain' ),
                'icon'  => WEFORMS_ASSET_URI . '/images/icon-constant-contact.png',
                'pro'   => true
            ),
            'campaign-monitor' => array(
                'id'    => 'campaign-monitor',
                'title' => __( 'Campaign Monitor', 'textdomain' ),
                'icon'  => WEFORMS_ASSET_URI . '/images/icon-campaign-monitor.png',
                'pro'   => true
            ),
            'mailpoet' => array(
                'id'    => 'mailpoet',
                'title' => __( 'MailPoet', 'textdomain' ),
                'icon'  => WEFORMS_ASSET_URI . '/images/icon-mailpoet.png',
                'pro'   => true
            ),
        );

        return array_merge( $integrations, $pro );
    }
}
