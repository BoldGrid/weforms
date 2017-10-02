<?php

/**
 * The Integration abstract class
 *
 * @since 1.1.0
 */
abstract class WeForms_Abstract_Integration {

    /**
     * The integration id
     *
     * @var boolean
     */
    public $id;

    /**
     * If the integration is enabled
     *
     * @var boolean
     */
    public $enabled;

    /**
     * Integration title
     *
     * @var string
     */
    public $title;

    /**
     * URL to the integration icon
     *
     * @var string
     */
    public $icon;

    /**
     * The settings fields for this integrations
     *
     * @var array
     */
    public $settings_fields = array();

    /**
     * Get the integration title
     *
     * @return string
     */
    public function get_title() {
        return apply_filters( 'weforms_integration_title', $this->title, $this );
    }

    /**
     * Get the integration id
     *
     * @return string
     */
    public function get_id() {
        return apply_filters( 'weforms_integration_title', $this->id, $this );
    }

    /**
     * Get intgration icon
     *
     * @return string
     */
    public function get_icon() {
        return apply_filters( 'weforms_integration_icon', $this->icon, $this );
    }

    /**
     * Check if the integration is enabled
     *
     * @return boolean
     */
    public function is_enabled() {
        return $this->enabled == true;
    }

    /**
     * Check if it's a pro field
     *
     * @return boolean
     */
    public function is_pro() {
        return false;
    }

    /**
     * Get the settings fields
     *
     * @return array
     */
    public function get_settings_fields() {
        return $this->settings_fields;
    }

    /**
     * Get the integration settings for the component
     *
     * @return array
     */
    public function get_js_settings() {
        return array(
            'id'       => $this->get_id(),
            'title'    => $this->get_title(),
            'icon'     => $this->get_icon(),
            'settings' => $this->get_settings_fields(),
            'pro'      => $this->is_pro()
        );
    }

}
