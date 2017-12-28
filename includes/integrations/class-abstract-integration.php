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
     * The settings fields for this integrations
     *
     * @var array
     */
    protected $template = null;


    /**
     * The settings settings_template
     *
     * @var array
     */
    protected $settings_template = null;


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


    /**
     * Check if it's the forms page
     *
     * @return boolean
     */
    public function is_weforms_page() {
        if ( get_current_screen()->base != 'toplevel_page_weforms' ) {
            return false;
        }

        return true;
    }

    /**
     * Load the individual template file if exists
     *
     * @return void
     */
    public function load_template() {
        if ( ! $this->is_weforms_page() ) {
            return;
        }

        if ( ! $this->template ) {
            return;
        }

        echo '<script type="text/x-template" id="tmpl-wpuf-integration-' . $this->id . '">';
        include $this->template;
        echo '</script>';
    }

    /**
     * Load settings
     *
     * @return void
     */
    public function load_settings( $priority = 10 ) {

        if ( ! $this->settings_template ) {
            return;
        }

        add_action( 'weforms_settings_tabs', array( $this, 'settings_tabs' ), $priority );
        add_action( 'weforms_settings_tab_content_' . $this->id, array( $this, 'settings_panel' ), $priority );
    }


    /**
     * Render the settings panel
     *
     * @return void
     */
    public function settings_panel() {
        if( file_exists( $this->settings_template )) {
            include $this->settings_template;
        }
    }

    /**
     * Add new tab at settings
     *
     * @return void
     */
    public function settings_tabs( $tabs ) {

        $tabs[ $this->id ] = array(
            'label' => $this->title,
            'icon' => $this->icon,
        );

        return $tabs;
    }

    /**
     * Get file prefix
     *
     * @return string
     */
    public function get_prefix() {
        $prefix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

        return $prefix;
    }

    /**
     * Get default componenet url
     *
     * @return string
     */
    public function module_component_file( $plugin_file ) {

        $prefix = $this->get_prefix();

        return plugins_url( 'component/index' . $prefix . '.js', $plugin_file );
    }
}
