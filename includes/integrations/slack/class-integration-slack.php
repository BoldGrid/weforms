<?php

/**
 * Slack Integrations
 *
 * @package weForms\Integrations
 */
class WeForms_Integration_Slack extends WPUF_Abstract_Integration {

    /**
     * Initialize the plugin
     */
    function __construct() {
        $this->id    = 'slack';
        $this->title = __( 'Slack', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-slack.png';

        $this->settings_fields = array(
            'enabled' => false,
            'message' => '',
            'url'     => '',
        );

        add_filter( 'weforms_form_builder_integrations', array( $this, 'register_integration_settings' ) );
    }

}
