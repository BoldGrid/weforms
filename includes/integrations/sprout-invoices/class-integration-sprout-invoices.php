<?php

/**
 * WP SI Integration
 */
class WeForms_Integration_SI extends WeForms_Abstract_Integration
{

    /**
     * Initialize the plugin
     */
    public function __construct()
    {
        $this->id = 'sprout-invoices';
        $this->title = __( 'Sprout Invoices', 'weforms' );
        $this->icon = WEFORMS_ASSET_URI . '/images/icon-sprout-invoices.png';

        $this->settings_fields = [
            'enabled' => false,
            'group' => [],
            'stage' => 'subscriber',
            'fields' => [
                'subject' => '',
                'client_name' => '',
                'email' => '',
                'first_name' => '',
                'last_name' => '',
                'address' => '',
                'notes' => '',
                'duedate' => '',
                'number' => '',
                'vat' => '',
                'line_items' => '',
            ],
        ];

        add_action( 'weforms_entry_submission', [$this, 'create_doc'], 10, 3 );
        add_filter( 'weforms_entry_submission_response', [$this, 'modify_response_data_redirect'] );
    }


    /**
     * Check if the dependency met
     *
     * @return bool
     */
    public function has_dependency()
    {
        return function_exists( 'sprout_invoices_load' );
    }

    public function modify_response_data_redirect($response_data)
    {
        $si_redirect = weforms_get_entry_meta( $response_data['entry_id'], 'wpuf_si_redirect_url', true );
        if ('' !== $si_redirect) {
            $response_data['show_message'] = 0;
            $response_data['redirect_to'] = $si_redirect;
        }
        return $response_data;
    }

    /**
     * Subscribe a user when a form is submitted
     *
     * @param int $entry_id
     * @param int $form_id
     *
     * @return void
     */
    public function create_doc($entry_id, $form_id, $page_id)
    {
        if (!$this->has_dependency()) {
            return;
        }

        $integration = weforms_is_integration_active( $form_id, $this->id );

        if (false === $integration) {
            return;
        }

        $form_data = weforms_get_entry_data( $entry_id );
        if (false === $form_data) {
            return;
        }

        $payment_data = weforms_get_entry_payment( $entry_id );

        $address = self::get_value( $integration->fields->address, $entry_id, $form_id, $page_id );
        if (is_array( $address )) {
            $full_address = array(
                'street' => isset( $address['street_address'] ) ? $address['street_address'] . ' ' . $address['street_address2'] : '',
                'city' => isset( $address['city_name'] ) ? $address['city_name'] : '',
                'zone' => isset( $address['state'] ) ? $address['state'] : '',
                'postal_code' => isset( $address['zip'] ) ? $address['zip'] : '',
                'country' => isset( $address['country_select'] ) ? $address['country_select'] : '',
            );
        }

        preg_match_all("/(?<=:)\w+(?=\})/", $integration->fields->line_items, $matches );

        $line_item_data = array_key_exists( $matches[0][0], $form_data['data'] );

        // bail out if nothing found to be replaced
        if ( $line_item_data ) {

            $li = " ";

            foreach($matches[0] as $fieldSlug ){
                $form_fields = weforms()->form->get( $form_id )->get_fields();
                foreach ($form_fields as $key => $field) {
                    if ($field['name'] === $fieldSlug) {
                        $lineItemOptions = array_key_exists( 'options', $field ) ? $field['options'] : 'false';
                    }
                }
                $lineItemsSelected = array($form_data['data'][$fieldSlug]);
                if ($lineItemOptions != 'false') {
                    $li_items = array();
                    foreach ($lineItemsSelected as $item) {
                        $li_items[] = array(
                            'desc' => $item,
                            'rate' => array_search($item, $lineItemOptions),
                            'total' => array_search($item, $lineItemOptions),
                            'qty' => 1,
                        );
                    }
                } else {
                    $li_deposit[] = array(
                        'desc' => $lineItemsSelected[0]['product'],
                        'rate' => $lineItemsSelected[0]['price'],
                        'total' => $lineItemsSelected[0]['price'],
                        'qty' => $lineItemsSelected[0]['quantity'],
                    );
                }

            };
            if ( isset( $li_items ) && isset( $li_deposit ) ) {
                $li = array_merge( $li_items, $li_deposit );
            } elseif ( isset( $li_items ) ) {
                $li = $li_items;
            } else {
                $li = $li_deposit;
            }
        }

        //Setting up array to send user info to WordPress
        $submission = array(
            'subject'      => self::get_value( $integration->fields->subject, $entry_id, $form_id, $page_id ),
            'line_items'   => !empty( $li ) ? $li : array(),
            'full_address' => !empty( $full_address ) ? $full_address : array(),
            'client_name'  => self::get_value( $integration->fields->client_name, $entry_id, $form_id, $page_id ),
            'email'        => self::get_value( $integration->fields->email, $entry_id, $form_id, $page_id ),
            'first_name'   => self::get_value( $integration->fields->first_name, $entry_id, $form_id, $page_id ),
            'last_name'    => self::get_value( $integration->fields->last_name, $entry_id, $form_id, $page_id ),
            'notes'        => self::get_value( $integration->fields->notes, $entry_id, $form_id, $page_id ),
            'duedate'      => self::get_value( $integration->fields->duedate, $entry_id, $form_id, $page_id ),
            'number'       => self::get_value( $integration->fields->number, $entry_id, $form_id, $page_id ),
            'vat'          => self::get_value( $integration->fields->vat, $entry_id, $form_id, $page_id ),
            'entry_note'   => self::build_entry_note( $form_id, $entry_id ),
            'entry_id'     => $entry_id,
            'payment'      => !empty( $payment_data ) ? 'true' : 'false',
            'form_id'      => $form_id,
            'page_id'      => $page_id,
            'form_data'    => $form_data['data'],
        );
        $submission = apply_filters('wpf_si_submission_data_for_creation', $submission );

        $doc_id = 0;
        $doctype = $integration->doctype;
        $create_user_and_client = isset( $integration->create_user_and_client ) ? $integration->create_user_and_client : 'false';
        switch ($doctype) {
            case 'invoice':
                $doc_id = $this->create_invoice( $submission );
                if ( 'false' !== $create_user_and_client ) {
                    $this->create_client( $submission, $doc_id );
                }
                break;
            case 'estimate':
                $doc_id = $this->create_estimate( $submission );
                if ( 'false' !== $create_user_and_client ) {
                    $this->create_client( $submission, $doc_id );
                }
                break;
            case 'client':
                $this->create_client( $submission );
                break;
            default:
                // nada
                break;
        }

        // REDIRECT
        $redirect = isset( $integration->redirect ) ? $integration->redirect : 'false';
        if ( 'false' !== $redirect && $doc_id ) {
            $doc = si_get_doc_object( $doc_id );
            $doc->set_pending();
            $url = wp_get_referer();
            if (get_post_type( $doc_id ) == SI_Invoice::POST_TYPE) {
                $url = get_permalink( $doc_id );
            } elseif (get_post_type( $doc_id ) == SI_Estimate::POST_TYPE) {
                $url = get_permalink( $doc_id );
            }
            weforms_add_entry_meta( $entry_id, 'wpuf_si_redirect_url', $url );
        }


    }

    public function get_value($text, $entry_id, $form_id, $page_id)
    {

        // basic field
        $value = self::get_meta_value( $text, $entry_id );
        if (false != $value) {
            return $value;
        }

        // name field
        $value = self::maybe_name( $text, $entry_id );
        if (false != $value) {
            return $value;
        }

        $notification = new WeForms_Notification( [
            'form_id' => $form_id,
            'page_id' => $page_id,
            'entry_id' => $entry_id,
        ] );

        // merge field
        $value = $notification->get_merge_value( preg_replace( '/[{}]/', '', $text ) );

        return $value;
    }

    private static function get_meta_value($text, $entry_id)
    {
        $pattern = '/{field:(\w*)}/';

        preg_match_all( $pattern, $text, $matches );

        // bail out if nothing found to be replaced
        if (!$matches) {
            return false;
        }

        // returning the first value, can't really deal with more.
        foreach ($matches[1] as $index => $meta_key) {
            return weforms_get_entry_meta( $entry_id, $meta_key, true );
        }
    }

    public static function maybe_name($text, $entry_id)
    {
        $pattern = '/{name-(full|first|middle|last):(\w*)}/';

        preg_match_all( $pattern, $text, $matches );

        // bail out if nothing found to be replaced
        if (!$matches[0]) {
            return false;
        }

        list( $search, $fields, $meta_key ) = $matches;

        $meta_value = weforms_get_entry_meta( $entry_id, $meta_key[0], true );
        $replace = explode( WeForms::$field_separator, $meta_value );

        foreach ($search as $index => $search_key) {
            if ('first' == $fields[$index]) {
                $text = str_replace( $search_key, $replace[0], $text );
            } elseif ('middle' == $fields[$index]) {
                $text = str_replace( $search_key, $replace[1], $text );
            } elseif ('last' == $fields[$index]) {
                $text = str_replace( $search_key, $replace[2], $text );
            } else {
                $text = str_replace( $search_key, implode( ' ', $replace ), $text );
            }
        }

        return $text;
    }

    public static function build_entry_note($form_id, $entry_id)
    {

        $form = weforms()->form->get( $form_id );
        $entry = $form->entries()->get( $entry_id );
        $fields = $entry->get_fields();

        if (!$fields) {
            return '';
        }

        $table = ' ';
        $table .= '<div class="submission-values">';
        $table .= '<ul>';

        foreach ($fields as $key => $value) {
            $field_value = isset( $value['value'] ) ? $value['value'] : '';

            if (!$field_value) {
                continue; // let's skip empty fields
            }

            $table .= '<li>';

            $table .= '<span class="submission-label"><strong>' . $value['label'] . ':&nbsp;</strong></span>';

            $table .= '<span class="submission-value">';
            if (in_array( $value['type'], ['multiple_select', 'checkbox_field'] )) {
                $field_value = is_array( $field_value ) ? $field_value : [];

                if ($field_value) {
                    $table .= '<ul>';

                    foreach ($field_value as $value_key) {
                        $table .= '<li>' . $value_key . '</li>';
                    }
                    $table .= '</ul>';
                } else {
                    $table .= '&mdash;';
                }
            } else {
                $table .= $field_value;
            }
            $table .= '</span>';

            $table .= '</li>';

        }

        $table .= '</ul>';
        $table .= '</div>';


        $table .= '<div>';
        $edit_url = admin_url( sprintf( 'admin.php?page=weforms#/form/%s/entries/%s', $form_id, $entry_id ) );
        $table .= sprintf( __( 'Invoice Submitted: Form <a href="%s">#%s</a>.', 'weforms' ), $edit_url, $entry_id );
        $table .= '</div>';

        return $table;
    }

    protected function create_invoice( $submission = array() )
    {
        $invoice_args = array(
            'subject' => sprintf( apply_filters( 'si_form_submission_title_format', '%1$s (%2$s)', $submission ), $submission['subject'], $submission['client_name'] ),
            'fields' => $submission,
            'form' => $submission['form_data'],
        );

        do_action( 'si_doc_generation_start' );

        /**
         * Creates the invoice from the arguments
         */
        $invoice_id = SI_Invoice::create_invoice( $invoice_args );
        $invoice = SI_Invoice::get_instance( $invoice_id );

        $invoice->set_line_items( $submission['line_items'] );
        $invoice->set_calculated_total();
        if ( 'false' === $submission['payment'] ) {
            $invoice->set_pending();
        }

        // notes
        if (isset( $submission['notes'] ) && '' !== $submission['notes']) {
            SI_Internal_Records::new_record( $submission['notes'], SI_Controller::PRIVATE_NOTES_TYPE, $invoice_id, '', 0, false );
        }

        if (isset( $submission['number'] )) {
            $invoice->set_invoice_id( $submission['number'] );
        }

        if (isset( $submission['duedate'] )) {
            $invoice->set_due_date( $submission['duedate'] );
        }

        // Add Payment from weForms if deposit
        if ( 'true' === $submission['payment'] ) {
            SI_PAYMENT::new_payment();
        }

        // Finally associate the doc with the form submission
        add_post_meta( $invoice_id, 'wef_form_id', $submission['form_id'] );

        SI_Internal_Records::new_record( $submission['entry_note'], 'invoice_submission', $invoice_id, sprintf( __( 'Invoice Submitted: Form %s.', 'weforms' ), $submission['entry_id'] ), '', false );

        do_action( 'si_invoice_submitted_from_adv_form', $invoice, $invoice_args, $submission, $submission['form_data'] );

        return $invoice_id;

    }

    protected function create_client($submission = array(), $doc_id = 0)
    {

        $email = $submission['email'];
        $client_name = $submission['client_name'];
        $first_name = $submission['first_name'];
        $last_name = $submission['last_name'];

        /**
         * Attempt to create a user before creating a client.
         */
        $user_id = get_current_user_id();
        if (!$user_id) {
            if ('' !== $email) {
                // check to see if the user exists by email
                $user = get_user_by( 'email', $email );
                if ($user) {
                    $user_id = $user->ID;
                }
            }
        }

        // Create a user for the submission if an email is provided.
        if (!$user_id) {
            // email is critical
            if ('' !== $email) {
                $user_args = array(
                    'user_login' => esc_attr__( $email, 'weforms' ),
                    'display_name' => isset( $client_name ) ? esc_attr__( $client_name, 'weforms' ) : esc_attr__( $email, 'weforms' ),
                    'user_email' => esc_attr__( $email, 'weforms' ),
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'user_url' => '',
                );
                $user_id = SI_Clients::create_user( $user_args );
            }
        }

        // Make up the args in creating a client
        $args = array(
            'company_name' => $submission['client_name'],
            'website' => '',
            'address' => $submission['full_address'],
            'user_id' => $user_id,
        );
        $client_id = SI_Client::new_client( $args );
        $client = SI_Client::get_instance( $client_id );

        if (isset( $submission['vat'] )) {
            $client->save_post_meta( array('_iva' => $submission['vat']) );
            $client->save_post_meta( array('_vat' => $submission['vat']) );
        }

        if (!$doc_id) {
            return;
        }

        /**
         * After a client is created assign it to the estimate
         */
        $doc = si_get_doc_object( $doc_id );
        $doc->set_client_id( $client_id );

    }

    protected function create_estimate($submission = array())
    {
        $estimate_args = array(
            'subject' => sprintf( apply_filters( 'si_form_submission_title_format', '%1$s (%2$s)', $submission ), $submission['subject'], $submission['client_name'] ),
            'fields' => $submission,
            'form' => $submission['form_data'],
        );

        do_action( 'si_doc_generation_start' );

        /**
         * Creates the estimate from the arguments
         */
        $estimate_id = SI_Estimate::create_estimate( $estimate_args );
        $estimate = SI_Estimate::get_instance( $estimate_id );

        $estimate->set_line_items( $submission['line_items'] );
        // @todo Sprout Invoices should include the unsetting of these after the line items are set, IIRC there's a filter that handles this. FYI: Invoices do not have this issue.
        unset($estimate->subtotal);
        unset($estimate->calculated_total);
        $estimate->set_calculated_total();

        // notes
        if (isset( $submission['notes'] ) && '' !== $submission['notes']) {
            SI_Internal_Records::new_record( $submission['notes'], SI_Controller::PRIVATE_NOTES_TYPE, $estimate_id, '', 0, false );
        }

        if (isset( $submission['number'] )) {
            $estimate->set_estimate_id( $submission['number'] );
        }

        if (isset( $submission['duedate'] )) {
            $estimate->set_expiration_date( $submission['duedate'] );
        }

        // Finally associate the doc with the form submission
        add_post_meta( $estimate_id, 'wef_form_id', $submission['form_id'] );

        SI_Internal_Records::new_record( $submission['entry_note'], 'estimate_submission', $estimate_id, sprintf( __( 'Estimate Submitted: Form %s.', 'weforms' ), $submission['entry_id'] ), '', false );

        do_action( 'si_estimate_submitted_from_adv_form', $estimate, $estimate_args );

        return $estimate_id;
    }
}
