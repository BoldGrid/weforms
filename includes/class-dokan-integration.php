<?php

if ( ! class_exists( 'weForms_Dokan_Integration' ) ) :

/**
*
* Dokan Integration Class
*
* @since  1.2.9
*/
class weForms_Dokan_Integration{

    function __construct() {

        add_filter( 'dokan_settings_sections', array( $this, 'add_settings_sections' ) );
        add_filter( 'dokan_get_dashboard_nav', array( $this, 'add_dashboard_nav' ) );
        add_action( 'dokan_load_custom_template', array( $this, 'load_form_template' ) );
        add_filter( 'dokan_query_var_filter', array( $this, 'register_queryvar' ) );
        add_filter( 'dokan_settings_fields', array( $this, 'dokan_settings' ) );

    }

    public function add_settings_sections( $sections ) {
        $sections[] = array(
            'id'    => 'weforms_integration',
            'title' => __( 'Vendor Contact Form', 'weforms' ),
            'icon' => 'dashicons-admin-generic'
        );

        return $sections;
    }

    /**
     * Insert new URL's to the dashboard navigation bar
     *
     * @param  array  $urls
     *
     * @since  1.2.9
     *
     * @return array $urls
     */
    public function add_dashboard_nav( $urls ) {
        $access         = dokan_get_option( 'allow_vendor_contact_form', 'weforms_integration' );
        $section_label  = dokan_get_option( 'vendor_contact_section_label', 'weforms_integration', __( 'Contact Admin', 'weforms' ) );

        if ( $access == 'on' ) {
            $urls['contact'] = array(
                'title' => $section_label,
                'icon'  => '<i class="fa fa-envelope-open"></i>',
                'url'   => dokan_get_navigation_url( 'contact' ),
                'pos'   => 67
            );
        }

        return $urls;
    }

    /**
     * Load template for contact section
     *
     * @param  array  $query_vars
     *
     * @since  1.2.9
     *
     * @return void
     */
    public function load_form_template( $query_vars ) {
        $access = dokan_get_option( 'allow_vendor_contact_form', 'weforms_integration' );

        if ( isset( $query_vars['contact'] ) && $access == 'on' ) {
            require_once WEFORMS_ROOT . '/includes/templates/dokan/dashboard-contact-section.php';
        }

    }

    /**
     * Register query var
     *
     * @param  array  $query_vars
     *
     * @since  1.2.9
     *
     * @return array $query_vars
     */
    public function register_queryvar( $query_vars ) {
        $query_vars[] = 'contact';

        return $query_vars;
    }

    /**
     * Dokan settings for weForms integration
     *
     * @param  array  $settings_fields
     *
     * @since  1.2.9
     *
     * @return array  $settings_fields
     */
    public function dokan_settings( $settings_fields ) {
        $settings_fields['weforms_integration']['allow_vendor_contact_form'] = array(
            'name'    => 'allow_vendor_contact_form',
            'label'   => __( 'Vendor Can Contact', 'weforms' ),
            'desc'    => __( 'Allow Vendors to contact admin from the dashbaord area', 'weforms' ),
            'type'    => 'checkbox',
            'default' => 'off'
        );

        $settings_fields['weforms_integration']['vendor_contact_section_label'] = array(
            'name'    => 'vendor_contact_section_label',
            'label'   => __( 'Contact Section Label', 'weforms' ),
            'desc'    => __( 'Label of contact section to show on vendor dashboard', 'weforms' ),
            'type'    => 'text',
            'default' => __( 'Contact Admin', 'weforms' )
        );

        $settings_fields['weforms_integration']['vendor_contact_form'] = array(
            'name'    => 'vendor_contact_form',
            'label'   => __( 'Select Contact Form', 'weforms' ),
            'desc'    => __( 'Select a contact form that will show on the vendor dashboard.', 'weforms' ),
            'type'    => 'select',
            'options' => $this->get_forms_dropdown_list(),
        );

        return $settings_fields;
    }

    /**
     * Get all the contact forms
     *
     * @since  1.2.9
     *
     * @return array  $post_forms
     */
    public function get_forms_dropdown_list() {
        $list   = array();
        $forms  = $this->get_all_forms();

        foreach ( $forms as $form ) {
            $list[$form->id] = $form->name;
        }
        return $list;
    }

    public function get_all_forms() {
        $forms_data = weforms()->form->all();

        return $forms_data['forms'];
    }

}

endif;