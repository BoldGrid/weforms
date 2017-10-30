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

        add_filter( 'weforms_integrations', array( $this, 'register_pro_integrations' ) );

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
    public function register_pro_integrations( $integrations ) {
        require_once WEFORMS_INCLUDES . '/admin/class-pro-upgrade-integrations.php';

        $pro = array(
            'WeForms_Pro_Integration_MailChimp',
            'WeForms_Pro_Integration_CM',
            'WeForms_Pro_Integration_CC',
            'WeForms_Pro_Integration_AWeber',
            'WeForms_Pro_Integration_ConvertKit'
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

        $fields['repeat_field']         = new WeForms_Form_Field_Repeat();
        $fields['file_upload']          = new WeForms_Form_Field_File();
        $fields['country_list_field']   = new WeForms_Form_Field_Country();
        $fields['numeric_text_field']   = new WeForms_Form_Field_Numeric();
        $fields['address_field']        = new WeForms_Form_Field_Address();
        $fields['google_map']           = new WeForms_Form_Field_GMap();
        $fields['shortcode']            = new WeForms_Form_Field_Shortcode();
        $fields['action_hook']          = new WeForms_Form_Field_Hook();
        $fields['toc']                  = new WeForms_Form_Field_Toc();
        $fields['ratings']              = new WeForms_Form_Field_Rating();
        $fields['linear_scale']         = new WeForms_Form_Field_Linear_Scale();
        $fields['checkbox_grid']        = new WeForms_Form_Field_Checkbox_Grid();
        $fields['multiple_choice_grid'] = new WeForms_Form_Field_Multiple_Choice_Grid();
        $fields['step_start']           = new WeForms_Form_Field_Step();

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
            'shortcode', 'action_hook', 'toc', 'ratings', 'linear_scale', 'checkbox_grid', 'multiple_choice_grid'
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
