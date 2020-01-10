<?php

/**
 * Emailer Class
 */
class WeForms_Emailer {

    /**
     * The default gateway to send
     *
     * @var WeForms_Mailer_Contract|null
     */
    private $gateway = null;

    public function __construct() {
        require_once WEFORMS_INCLUDES . '/email/gateways/interface-mailer.php';

        $this->gateway = $this->get_sending_gateway();
    }

    /**
     * Get available gateways
     *
     * @return array
     */
    public function get_available_gateways() {
        require_once WEFORMS_INCLUDES . '/email/gateways/class-emailer-wpmail.php';

        $gateways = apply_filters( 'weforms_email_gateways', [
            'wordpress' => new WeForms_Emailer_WPMail(),
        ] );

        return $gateways;
    }

    /**
     * Get the sending gateway class
     *
     * @return WeForms_Mailer_Contract
     */
    public function get_sending_gateway() {
        $gateway            = weforms_get_settings( 'email_gateway', 'WordPress' );
        $available_gateways = $this->get_available_gateways();

        if ( array_key_exists( $gateway, $available_gateways ) ) {
            return $available_gateways[ $gateway ];
        }

        return $available_gateways['wordpress'];
    }

    /**
     * Send email via the gateway
     *
     * @param string $to      Email addresses to send message
     * @param string $subject Email subject
     * @param string $body    Message contents
     * @param string $headers Optional. Files to attach.
     *
     * @return bool
     */
    public function send( $to, $subject, $body, $headers ) {
        return $this->gateway->send( $to, $subject, $body, $headers );
    }
}
