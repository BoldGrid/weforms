<?php

/**
 * WP ERP Integration
 *
 * @package weForms\Integrations
 */
class WeForms_Integration_ERP extends WeForms_Abstract_Integration {

    /**
     * Initialize the plugin
     */
    function __construct() {
        $this->id    = 'erp';
        $this->title = __( 'WP ERP', 'weforms' );
        $this->icon  = WEFORMS_ASSET_URI . '/images/icon-weforms.png';

        $this->settings_fields = array(
            'enabled' => false,
            'group'   => [],
            'stage'   => 'subscriber',
            'fields'  => array(
                'email'      => '',
                'first_name' => '',
                'last_name'  => ''
            )
        );

        add_action( 'weforms_entry_submission', array( $this, 'subscribe_user' ), 10, 4 );
    }

    /**
     * Check if the dependency met
     *
     * @return boolean
     */
    public function has_dependency() {
        return function_exists( 'erp_crm_get_contact_groups' );
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

        if ( ! $this->has_dependency() ) {
            return;
        }

        $integration = weforms_is_integration_active( $form_id, $this->id );

        if ( false === $integration ) {
            return;
        }

        if ( empty( $integration->fields->email ) ) {
            return;
        }

        $email = WeForms_Notification::replace_field_tags( $integration->fields->email, $entry_id );

        if ( empty( $email ) ) {
            return;
        }

        $first_name = WeForms_Notification::replace_name_tag( $integration->fields->first_name, $entry_id );
        $last_name  = WeForms_Notification::replace_name_tag( $integration->fields->last_name, $entry_id );

        $contact_id = erp_insert_people( array(
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'email'      => $email,
            'type'       => 'contact',
        ) );

        if ( is_wp_error( $contact_id ) ) {
            return;
        }

        $contact = new \WeDevs\ERP\CRM\Contact( absint( $contact_id ), 'contact' );
        $contact->update_meta( 'life_stage', $integration->stage );

        if ( $integration->group ) {
            foreach ($integration->group as $group_id) {

                $hash = sha1( microtime() . 'erp-subscription-form' . $group_id . $contact_id );

                erp_crm_create_new_contact_subscriber( array(
                    'group_id' => $group_id,
                    'user_id'  => $contact_id,
                    'status'   => 'subscribe',
                    'hash'     => $hash
                ) );
            }
        }
    }

}