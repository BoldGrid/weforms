<?php
/**
 * Class WeForms_Privacy
 *
 * Add Exporters and Erasers to WP data exporter
 *
 * @since 1.2.9
 */
Class WeForms_Privacy {

    private $name = "weForms";

    public function __construct(){
        add_action( 'admin_init', array( $this, 'add_privacy_message' ) );

        $payment_export = weforms_get_settings( 'privacy_payment_export' );
        $payment_erase  = weforms_get_settings( 'privacy_payment_erase' );

        if ( isset( $payment_export ) && $payment_export ) {
            add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporters' ), 10 );
        }
        if ( isset( $payment_erase ) && $payment_erase ) {
            add_filter('wp_privacy_personal_data_erasers', array($this, 'register_erasers'), 10);
        }
    }

    function add_privacy_message(){
        if ( function_exists( 'wp_add_privacy_policy_content' ) ) {
            $content = $this->get_privacy_message();
            wp_add_privacy_policy_content( $this->name, $content );
        }
    }

    /**
     * Add privacy policy content for the privacy policy page.
     */
    function get_privacy_message() {
        $content = '
			<div contenteditable="false">' .
            '<p class="wp-policy-help">' .
            __( 'This sample policy includes the basics around what personal data you may be collecting, storing and sharing, as well as who may have access to that data. Depending on what settings are enabled and which additional plugins are used, the specific information shared by your form will vary. We recommend consulting with a lawyer when deciding what information to disclose on your privacy policy.', 'weforms' ) .
            '</p>' .
            '</div>' .
            '<p>' . __( 'We collect information about you during the form submission process on our WordPress website.', 'weforms' ) . '</p>' .
            '<h2>' . __( 'What we collect and store', 'weforms' ) . '</h2>' .
            '<p>' . __( 'While you visit our site, we’ll track:', 'weforms' ) . '</p>' .
            '<ul>' .
            '<li>' . __( 'Form Fields Data: Forms Fields data includes the available field types when creating a form. We’ll use this to, for example, collect informations like Name, Email and other available fields.', 'weforms' ) . '</li>' .
            '<li>' . __( 'Location, IP address and browser type: we’ll use this for purposes like estimating taxes and shipping. Also, for reducing fraudulent activities and prevent identity theft while placing orders', 'weforms' ) . '</li>' .
            '<li>' . __( 'Transaction Details: we’ll ask you to enter this so we can, for instance, provide & regulate subscription packs that you bought and keep track of your payment details for subscription packs!', 'weforms' ) . '</li>' .
            '</ul>' .
            '<p>' . __( 'We’ll also use cookies to keep track of form elements while you’re browsing our site.', 'weforms' ) . '</p>' .
            '<div contenteditable="false">' .
            '<p class="wp-policy-help">' . __( 'Note: you may want to further detail your cookie policy, and link to that section from here.', 'weforms' ) . '</p>' .
            '</div>' .
            '<p>' . __( 'When you fill up a form, we’ll ask you to provide information including your name, billing address, shipping address, email address, phone number, credit card/payment details and optional account information like username and password and any other form fields found in the form building options. We’ll use this information for purposes, such as, to:', 'weforms' ) . '</p>' .
            '<ul>' .
            '<li>' . __( 'Send you information about your account and order', 'weforms' ) . '</li>' .
            '<li>' . __( 'Respond to your requests, including transaction details and complaints', 'weforms' ) . '</li>' .
            '<li>' . __( 'Process payments and prevent fraud', 'weforms' ) . '</li>' .
            '<li>' . __( 'Set up your account', 'weforms' ) . '</li>' .
            '<li>' . __( 'Comply with any legal obligations we have, such as calculating taxes', 'weforms' ) . '</li>' .
            '<li>' . __( 'Improve our form offerings', 'weforms' ) . '</li>' .
            '<li>' . __( 'Send you marketing messages, if you choose to receive them', 'weforms' ) . '</li>' .
            '<li>' . __( 'Or any other service the built form was created to comply with and it’s necessary information', 'weforms' ) . '</li>' .
            '</ul>' .
            '<p>' . __( 'If you create an account, we will store your name, address, email and phone number, which will be used to populate the form fields for future submissions.', 'weforms' ) . '</p>' .
            '<p>' . __( 'We generally store information about you for as long as we need the information for the purposes for which we collect and use it, and we are not legally required to continue keeping it. For example, we will store form submission information for XXX years for tax, accounting and marketing purposes. This includes your name, email address and billing and shipping addresses.', 'weforms' ) . '</p>' .
            '<h2>' . __( 'Who on our team has access', 'weforms' ) . '</h2>' .
            '<p>' . __( 'Members of our team have access to the information you provide us. For example, Administrators and Editors and any body else who has permission can access:', 'weforms' ) . '</p>' .
            '<ul>' .
            '<li>' . __( 'Form submission information and other details related to it', 'weforms' ) . '</li>' .
            '<li>' . __( 'Customer information like your name, email address, and billing and shipping information.', 'weforms' ) . '</li>' .
            '</ul>' .
            '<p>' . __( 'Our team members have access to this information to help fulfill transactions and support you.', 'weforms' ) . '</p>' .
            '<h2>' . __( 'What we share with others', 'weforms' ) . '</h2>' .
            '<div contenteditable="false">' .
            '<p class="wp-policy-help">' . __( 'In this section you should list who you’re sharing data with, and for what purpose. This could include, but may not be limited to, analytics, marketing, payment gateways, shipping providers, and third party embeds.', 'weforms' ) . '</p>' .
            '</div>' .
            '<p>' . __( 'We share information with third parties who help us provide our orders and store services to you; for example --', 'weforms' ) . '</p>' .
            '<h3>' . __( 'Payments', 'weforms' ) . '</h3>' .
            '<div contenteditable="false">' .
            '<p class="wp-policy-help">' . __( 'In this subsection you should list which third party payment processors you’re using to take payments on your site since these may handle customer data. We’ve included PayPal as an example, but you should remove this if you’re not using PayPal.', 'weforms' ) . '</p>' .
            '</div>' .
            '<p>' . __( 'We accept payments through PayPal. When processing payments, some of your data will be passed to PayPal, including information required to process or support the payment, such as the purchase total and billing information.', 'weforms' ) . '</p>' .
            '<p>' . __( 'Please see the <a href="https://www.paypal.com/us/webapps/mpp/ua/privacy-full">PayPal Privacy Policy</a> for more details.', 'weforms' ) . '</p>'.
            '<p>' . __( 'Also, we accept payments through Stripe. When processing payments, some of your data will be passed to Stripe, including information required to process or support the payment, such as the purchase total and billing information.', 'weforms' ) . '</p>' .
            '<p>' . __( 'Please see the <a href="https://stripe.com/us/privacy">Stripe Privacy Policy</a> for more details.', 'weforms' ) . '</p>'.
            '<h3>' . __( 'Available Modules', 'weforms' ) . '</h3>' .
            '<p>' . __( 'In this subsection you should list which third party modules you’re using to increase the functionality of your created forms using weForms since these may handle customer data.', 'weforms' ) . '</p>' .
            '<p>' . __( 'weForms Pro comes with support for modules like HubSpot, Constant Contact, Salesforce, PayPal, Stripe, Google Analytics, ConvertKit, Zapier, Campaign Monitor, Aweber, MailChimp, Zoho, Trello, SMS Notification(Using Twilio, Nexmo and other popular gateways), Google Sheets, GetResponse. Data sent to those platforms will be handled by their own privacy policy.', 'weforms' ) . '</p>' .
            '<p>' . __( 'Please note any future modules that will be added will have some data transferred to their own platform which falls in their own data policy.', 'weforms' ) . '</p>' .
            '<p>' . __( 'As an example while using MailChimp for your marketing email automation service by integrating it with weForms, some of your data will be passed to MailChimp, including information required to process or support the email marketing services, such as name, email address and any other information that you intend to pass or collect including all collected information through subscription. ', 'weforms' ) . '</p>' .
            '<p>' . __( 'Please see the <a href="https://mailchimp.com/legal/privacy/">MailChimp Privacy Policy</a> for more details.', 'weforms' ) . '</p>';

        return apply_filters( 'weforms_privacy_policy_content', $content );
    }

    /**
     * Register WeForms Exporter to export data
     *
     * @param $exporters
     *
     * @return array
     */
    function register_exporters( $exporters ) {
        $exporters['weforms-transaction-data-export'] = array(
            'exporter_friendly_name' => __( 'weForms Transaction Data', 'weforms' ),
            'callback'               => array( 'WeForms_Privacy', 'export_payment_data'),
        );

        return apply_filters( 'weforms_privacy_register_exporters', $exporters );
    }

    /**
     * Register WeForms Eraser to delete data
     *
     * @param $erasers
     *
     * @return array
     */
    function register_erasers( $erasers ) {
        $erasers['weforms-transaction-data-export'] = array(
            'eraser_friendly_name' => __( 'weForms Transaction Data', 'weforms' ),
            'callback'             => array( 'WeForms_Privacy', 'erase_payment_data'),
        );

        return apply_filters( 'weforms_privacy_register_erasers', $erasers );
    }

    /**
     * Get current user id
     *
     */
    public static function get_user_id() {
        if ( is_user_logged_in() ) {
            $user = get_current_user_id();
            return $user;
        }
        return false;
    }

    /**
     * Finds and exports payment data
     *
     *
     * @return array An array of data in name value pairs
     */
    public static function export_payment_data(){

        $data_to_export = array();
        $weforms_user = self::get_user_id();

        $payment_data = self::get_form_payments( $weforms_user );

        $idx = 0;

        if ( !empty( $payment_data ) ) {
            foreach ( $payment_data as $pay_data ) {
                $data_to_export[] = array(
                    'group_id'    => 'weforms-payment-data',
                    'group_label' => __( 'weForms Payment Data', 'weforms' ),
                    'item_id'     => "weforms-payment-{$idx}",
                    'data'        => self::process_payment_data( $pay_data ),
                );
                $idx++;
            }
        }

        /**
         * Filters the export data array
         *
         * @param array
         */
        $data_to_export = apply_filters( 'weforms_privacy_export_data', $data_to_export, $weforms_user );

        return array(
            'data' => $data_to_export,
            'done' => true
        );
    }

    /**
     * Anonymize payment data of a user
     *
     * @return array
     */
    public static function erase_payment_data(){
        global $wpdb;

        $weforms_user = self::get_user_id();

        $query = "Update " . $wpdb->prefix . 'weforms_payments' . " Set `user_id` = 0 WHERE `user_id` = {$weforms_user}";
        $wpdb->query( $query );

        $erased = apply_filters( 'weforms_erase_payment_data', array(
                'items_removed'  => true,
                'items_retained' => false,
                'messages'       => array(),
                'done'           => true,
            ), $weforms_user
        );

        return $erased;

    }

    /**
     * Get payments by a user_id
     *
     * @param int $form_id
     * @param array $args
     *
     * @return Object
     */
    public static function get_form_payments( $user_id ) {
        global $wpdb;

        $query = 'SELECT * FROM ' . $wpdb->prefix . 'weforms_payments' .
            ' WHERE user_id = ' . $user_id ;

        $results = $wpdb->get_results( $query );

        return $results;
    }

    public static function process_payment_data( $payment_data ) {

        $field_value = unserialize( $payment_data->payment_data );

        $serialized_value = array(); $transaction_data = array();

        $user_data = get_userdata( $payment_data->user_id );

        if ( is_array( $field_value ) ) {

            foreach ( $field_value as $key => $sfv ) {
                $sfv = str_replace( array( '_', '-' ), ' ', $key ) . ': ' . $sfv;
                $sfv = ucwords( $sfv );
                $serialized_value[] = $sfv;
            }

            $payment_data->payment_data = implode( '<br> ', $serialized_value );
        }
        if ( !empty( $user_data ) && !empty( $payment_data ) ) {
            $transaction_data = array(
                array(
                    'name'  => __( 'User', 'weforms' ),
                    'value' => $user_data->user_login,
                ),
                array(
                    'name'  => __( 'Total', 'weforms' ),
                    'value' => $payment_data->total,
                ),
                array(
                    'name'  => __( 'Gateway', 'weforms' ),
                    'value' => $payment_data->gateway,
                ),
                array(
                    'name'  => __( 'Transaction ID', 'weforms' ),
                    'value' => $payment_data->transaction_id,
                ),
                array(
                    'name'  => __( 'Payment Status', 'weforms' ),
                    'value' => $payment_data->status,
                ),
                array(
                    'name'  => __( 'Payment Data', 'weforms' ),
                    'value' => $payment_data->payment_data,
                ),
                array(
                    'name'  => __( 'Transaction Time', 'weforms' ),
                    'value' => $payment_data->created_at,
                ),
            );
        }

        return apply_filters( 'weforms_export_payment_data', $transaction_data );
    }

}
