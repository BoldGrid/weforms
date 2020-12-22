<?php

/**
 * The Integration Loader
 */
class WeForms_Integration_Manager {

    /**
     * The integration instances
     *
     * @var array
     */
    public $integrations = [];

    /**
     * Return loaded integrations.
     *
     * @return array
     */
    public function get_integrations() {
        if ( $this->integrations ) {
            return $this->integrations;
        }

        require_once WEFORMS_INCLUDES . '/integrations/slack/class-integration-slack.php';
        require_once WEFORMS_INCLUDES . '/integrations/erp/class-integration-erp.php';
        require_once WEFORMS_INCLUDES . '/integrations/mailpoet/class-integration-mailpoet.php';
        require_once WEFORMS_INCLUDES . '/integrations/sprout-invoices/class-integration-sprout-invoices.php';

        $integrations = apply_filters( 'weforms_integrations', [
            'WeForms_Integration_Slack', 'WeForms_Integration_ERP', 'WeForms_Integration_MailPoet_Free', 'WeForms_Integration_SI',
        ] );

        // Load integration classes
        foreach ( $integrations as $integration ) {
            $integration_instance = new $integration();

            $this->integrations[ $integration_instance->id ] = $integration_instance;
        }

        return $this->integrations;
    }

    public function get_integration_js_settings() {
        $settings = [];

        foreach ( $this->get_integrations() as $integration_id => $integration ) {
            $settings[ $integration_id ] = $integration->get_js_settings();
        }

        return $settings;
    }
}