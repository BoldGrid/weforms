<?php

/**
 * Mailpoet Integration
 */
class WeForms_Integration_MailPoet_Free extends WeForms_Abstract_Integration {

    function __construct() {
        $this->id              = 'mailpoet';
        $this->title           = __( 'MailPoet', 'weforms' );
        $this->icon            = WEFORMS_ASSET_URI . '/images/icon-mailpoet.png';
        $this->template        = dirname( __FILE__ ) . '/component/template.php';

        $this->settings_fields = array(
            'enabled' => false,
            'list'    => '',
            'double'  => false,
            'fields'  => array(
                'email'      => '',
                'first_name' => '',
                'last_name'  => ''
            )
        );

        add_filter( 'admin_footer', array( $this, 'load_template' ) );

        add_action( 'wp_ajax_wpuf_mailpoet_fetch_lists', array( $this, 'fetch_lists' ) );

        add_filter( 'weforms_builder_scripts', array( $this, 'enqueue_mixin' ) );

        add_action( 'weforms_entry_submission', array( $this, 'subscribe_user' ), 10, 4 );
    }

    /**
     * Enqueue the mixin
     *
     * @param $scritps
     *
     * @return array
     */
    public function enqueue_mixin( $scripts ) {

        $scripts['weforms-int-mailpoet'] = array(
            'src' => plugins_url( 'component/index.js', __FILE__ ),
            'deps' => array( 'weforms-form-builder-components' )
        );

        return $scripts;
    }

    /**
     * Fetch Mailpoets saved list from server
     *
     * @return array
     */
    public function fetch_lists() {
        if ( class_exists( 'WYSIJA' ) ) {
            $mail_poet_lists = WYSIJA::get( 'list', 'model' );
            $lists = $mail_poet_lists->get( array( 'name', 'list_id' ), array( 'is_enabled' => 1 ) );
            wp_send_json_success( $lists );
        }
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
    public function subscribe_user( $entry_id, $form_id, $page_id, $form_settings ) {

        if ( !class_exists( 'WYSIJA' ) ) {
            return;
        }

        $integration = weforms_is_integration_active( $form_id, $this->id );
        if ( false === $integration ) {
            return;
        }

        if ( empty( $integration->list ) || empty( $integration->fields->email ) ) {
            return;
        }

        $email = WeForms_Notification::replace_field_tags( $integration->fields->email, $entry_id );

        if ( empty( $email ) ) {
            return;
        }

        $first_name = WeForms_Notification::replace_name_tag( $integration->fields->first_name, $entry_id );
        $last_name  = WeForms_Notification::replace_name_tag( $integration->fields->last_name, $entry_id );

        // Populate data submitted.
        if ( $first_name && 'false' !== $first_name ) {
            $userData = array( 'email' => $email, 'firstname' => $first_name, 'lastname' => $last_name );
        }
        else {
            $userData = array( 'email' => $user->user_email );
        }

        $data = array(
          'user'      => $userData,
          'user_list' => array( 'list_ids' => array( $integration->list ) )
        );

        // Add subscriber to MailPoet.
        $weHelper = WYSIJA::get( 'user', 'helper' );
        $weHelper->addSubscriber( $data );
    }
}

