<?php

/**
 * The Pro Integrations
 */
class WeForms_Pro_Upgrades {

    /**
     * Initialize
     */
    function __construct() {

        if ( class_exists( 'WeForms_Pro' ) ) {
            return;
        }

        add_filter( 'weforms_form_builder_integrations', array( $this, 'register_integrations' ) );

        // form fields
        add_filter( 'weforms_field_get_js_settings', array( $this, 'add_conditional_field_prompt' ) );
        add_filter( 'weforms_form_fields', array( $this, 'register_pro_fields' ) );
        add_filter( 'weforms_field_groups_custom', array( $this, 'add_to_custom_fields' ) );
        add_filter( 'weforms_field_groups_others', array( $this, 'add_to_others_fields' ) );
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
            'campaign-monitor' => array(
                'id'    => 'campaign-monitor',
                'title' => __( 'Campaign Monitor', 'weforms' ),
                'icon'  => WEFORMS_ASSET_URI . '/images/icon-campaign-monitor.png',
                'pro'   => true
            ),
            'constant-contact' => array(
                'id'    => 'constant-contact',
                'title' => __( 'Constant Contact', 'weforms' ),
                'icon'  => WEFORMS_ASSET_URI . '/images/icon-constant-contact.png',
                'pro'   => true
            ),
            'mailpoet' => array(
                'id'    => 'mailpoet',
                'title' => __( 'MailPoet', 'weforms' ),
                'icon'  => WEFORMS_ASSET_URI . '/images/icon-mailpoet.png',
                'pro'   => true
            ),
            'aweber' => array(
                'id'    => 'aweber',
                'title' => __( 'AWeber', 'weforms' ),
                'icon'  => WEFORMS_ASSET_URI . '/images/icon-aweber.png',
                'pro'   => true
            ),
            'getresponse' => array(
                'id'    => 'getresponse',
                'title' => __( 'Get Response', 'weforms' ),
                'icon'  => WEFORMS_ASSET_URI . '/images/icon-getresponse.png',
                'pro'   => true
            ),
            'convertkit' => array(
                'id'    => 'convertkit',
                'title' => __( 'ConvertKit', 'weforms' ),
                'icon'  => WEFORMS_ASSET_URI . '/images/icon-convertkit.png',
                'pro'   => true
            ),
        );

        return array_merge( $integrations, $pro );
    }

    /**
     * Register pro fields
     *
     * @param  array $fields
     *
     * @return array
     */
    public function register_pro_fields( $fields ) {
        if ( ! class_exists( 'WeForms_Form_Field_Pro' ) ) {
            require_once WEFORMS_INCLUDES . '/fields/class-fields-pro.php';
        }

        require_once WEFORMS_INCLUDES . '/admin/class-pro-upgrade-fields.php';

        $fields['repeat_field']       = new WeForms_Form_Field_Repeat();
        $fields['date_field']         = new WeForms_Form_Field_Date();
        $fields['file_upload']        = new WeForms_Form_Field_File();
        $fields['country_list_field'] = new WeForms_Form_Field_Country();
        $fields['numeric_text_field'] = new WeForms_Form_Field_Numeric();
        $fields['address_field']      = new WeForms_Form_Field_Address();
        $fields['google_map']         = new WeForms_Form_Field_GMap();
        $fields['shortcode']          = new WeForms_Form_Field_Shortcode();
        $fields['action_hook']        = new WeForms_Form_Field_Hook();
        $fields['toc']                = new WeForms_Form_Field_Toc();
        $fields['ratings']            = new WeForms_Form_Field_Rating();
        $fields['step_start']         = new WeForms_Form_Field_Step();

        return $fields;
    }

    /**
     * Register fields to custom field section
     *
     * @param array $fields
     */
    public function add_to_custom_fields( $fields ) {
        $pro_fields = array(
            'repeat_field', 'date_field', 'file_upload', 'country_list_field',
            'numeric_text_field', 'address_field', 'google_map', 'step_start'
        );

        return array_merge( $fields, $pro_fields );
    }

    /**
     * Register fields to others field section
     *
     * @param array $fields
     */
    public function add_to_others_fields( $fields ) {
        $pro_fields = array(
            'shortcode', 'action_hook', 'toc', 'ratings'
        );

        return array_merge( $fields, $pro_fields );
    }

    /**
     * Add conditional logic prompt
     *
     * @param array $settings
     */
    public function add_conditional_field_prompt( $settings ) {

        $settings['settings'][] = array(
            'name'           => 'wpuf_cond',
            'title'          => __( 'Conditional Logic', 'weforms' ),
            'type'           => 'option-pro-feature-alert',
            'section'        => 'advanced',
            'priority'       => 30,
            'help_text'      => '',
            'is_pro_feature' => true
        );

        return $settings;
    }
}
