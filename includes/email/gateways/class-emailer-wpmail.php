<?php

/**
 * WP Mail
 */
class WeForms_Emailer_WPMail implements WeForms_Mailer_Contract {

    /**
     * Send email via wp_mail
     *
     * @param  string $to       Email addresses to send message
     * @param  string $subject  Email subject
     * @param  string $body     Message contents
     * @param  array $headers  Optional. Files to attach.
     *
     * @return bool
     */
    public function send( $to, $subject, $body, $headers ) {
        $_headers = array();

        if ( isset( $headers['from'] ) ) {
            $_headers[] = sprintf( 'From: %s <%s>', $headers['from']['name'], $headers['from']['email'] );
        }

        if ( isset( $headers['cc'] ) ) {
            $_headers[] = sprintf( 'CC: %s', $headers['cc'] );
        }

        if ( isset( $headers['bcc'] ) ) {
            $_headers[] = sprintf( 'BCC: %s', $headers['bcc'] );
        }

        if ( isset( $headers['replyto'] ) ) {
            $_headers[] = sprintf( 'Reply-To: %s', $headers['replyto'] );
        }

        $_headers[] = 'Content-Type: text/html; charset=UTF-8';

        return wp_mail( $to, $subject, $body, $_headers );
    }
}