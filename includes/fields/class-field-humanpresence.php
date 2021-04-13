<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_HumanPresence extends WeForms_Field_Contract {

    public function __construct() {
        $this->name       = __( 'HP Anti-Spam', 'weforms' );
        $this->input_type = 'humanpresence';
        $this->icon       = 'humanpresence';
    }

    /**
     * Render the field
     *
     * @param array $field_settings
     * @param int   $form_id
     *
     * @return void
     */
    public function render( $field_settings, $form_id ) {

    }

    /**
     * Custom validator
     *
     * @return array
     */
    public function get_validator() {
        return [
            'callback'      => 'has_humanpresence_installed',
            'button_class'  => 'button-faded',
            'msg_title'     => __( 'Human Presence Anti-Spam Required', 'weforms' ),
            'msg'           => apply_filters( 'wpuf-form-builder-humanpresence-validation-msg', sprintf(
                __( 'To enable Human Presence Anti-Spam on weForms, <a href="%s" target="_blank">Install and activate the plugin</a> on your site. Once installed, toggle on the weForms form you want to protect.', 'weforms' ),
                // admin_url( 'admin.php?page=weforms#/settings' ),
                'http://www.humanpresence.io/weforms-signup'
              ) ),
        ];
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        $settings = [
        ];

        return $settings;
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $props = [
            'template'              => $this->get_type(),
            'label'                 => '',
            'is_meta'               => 'yes',
            'id'                    => 0,
            'is_new'                => true,
            'wpuf_cond'             => null,
        ];

        return $props;
    }

}
