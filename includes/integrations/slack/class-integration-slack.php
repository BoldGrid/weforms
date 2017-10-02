<?php

/**
 * Slack Integrations
 *
 * @package weForms\Integrations
 */
class WeForms_Integration_Slack extends WeForms_Abstract_Integration {

    /**
     * Initialize the plugin
     */
    function __construct() {
        $this->id    = 'slack';
        $this->title = __( 'Slack', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-slack.png';

        $this->settings_fields = array(
            'enabled' => false,
            'url'     => '',
        );

        add_action( 'weforms_entry_submission', array( $this, 'send_notification' ), 10, 4 );
    }

    /**
     * Subscribe a user when a form is submitted
     *
     * @param  int $entry_id
     * @param  int $form_id
     * @param  int $page_id
     * @param  array $form_settings
     *
     * @return void
     */
    public function send_notification( $entry_id, $form_id, $page_id, $form_settings ) {

        $integration = weforms_is_integration_active( $form_id, $this->id );

        if ( false === $integration ) {
            return;
        }

        if ( empty( $integration->url ) ) {
            return;
        }

        $attachment_fields = array();
        $title             = sprintf( __( "#%d New submission on %s", 'weforms' ), $entry_id, get_post_field( 'post_title', $form_id ) );
        $form_data         = weforms_get_entry_data( $entry_id );

        if ( false === $form_data ) {
            return;
        }

        // prepare the attachment data
        foreach ( $form_data['fields'] as $meta_key => $value ) {
            $is_short = true;
            $_value   = $form_data['data'][ $meta_key ];

            if ( 'textarea' == $value['type'] ) {
                $is_short = false;
                $_value   = html_entity_decode( $_value );

                // replace paragrphs and breaks
                $_value = str_replace( '<p>', '', $_value );
                $_value = str_replace( '<br />', '', $_value );
                $_value = str_replace( '</p>', "\n", $_value );
            }

            $attachment_fields[] = array(
                'title' => $value['label'],
                'value' => $_value,
                'short' => $is_short
            );
        }

        $data = array(
            'payload' => json_encode( array(
            'username'    => 'weForms',
            'icon_url'    => '',
            'attachments' => array(
                array(
                    'fallback'    => $title,
                    'color'       => '#36a64f',
                    'title'       => $title,
                    'title_link'  => sprintf( '%s#/form/%d/entries/%d', admin_url( 'admin.php?page=weforms' ), $form_id, $entry_id ),
                    'fields'      => $attachment_fields,
                    'footer'      => 'weForms',
                    'ts'          => current_time( 'timestamp' )
                )
            )
        ) ) );

        $posting_to_slack = wp_remote_post( $integration->url, array(
            'method'      => 'POST',
            'timeout'     => 30,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => false,
            'headers'     => array(),
            'body'        => $data,
            'cookies'     => array()
        ) );

    }

}
