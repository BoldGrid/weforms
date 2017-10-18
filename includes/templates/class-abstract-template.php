<?php

/**
 * Form template abstract class
 *
 * @since 1.1.0
 */
abstract class WeForms_Form_Template {

    /**
     * If the form is enabled
     *
     * @var boolean
     */
    public $enabled = true;

    /**
     * Template title
     *
     * @var string
     */
    public $title;

    /**
     * Template description
     *
     * @var string
     */
    public $description;

    /**
     * Conditional logic
     *
     * @var array
     */
    protected $conditionals;

    /**
     * Form fields
     *
     * @var array
     */
    protected $form_fields;

    /**
     * Form settings
     *
     * @var array
     */
    protected $form_settings;

    /**
     * Form Template Category
     *
     * @var array
     */
    public $category = 'default';


    /**
     * Form Template Image
     *
     * @var array
     */
    public $image;

    /**
     * Form notifications
     *
     * @since 2.5.2
     *
     * @var array
     */
    protected $form_notifications;

    public function __construct() {
        $this->conditionals = array(
            'condition_status' => 'no',
            'cond_field'       => array(),
            'cond_operator'    => array( '=' ),
            'cond_option'      => array( '- select -' ),
            'cond_logic'       => 'all'
        );
    }

    /**
     * Get the template title
     *
     * @return string
     */
    public function get_title() {
        return $this->title;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function get_description() {
        return $this->description;
    }

    /**
     * Get the form fields
     *
     * @return array
     */
    public function get_form_fields() {
        return $this->form_fields;
    }

    /**
     * Get all available fields
     *
     * @return array
     */
    public function get_available_fields() {
        return weforms()->fields->get_fields();
    }

    /**
     * Get the form settings
     *
     * @return array
     */
    public function get_form_settings() {
        return $this->get_default_settings();
    }

    /**
     * Get form notifications
     *
     * @since 2.5.2
     *
     * @return array
     */
    public function get_form_notifications() {
        return $this->get_default_notification();
    }

    /**
     * Check if the template is enabled
     *
     * @return boolean
     */
    public function is_enabled() {
        return $this->enabled;
    }

    /**
     * Get default settings
     *
     * @return array
     */
    public function get_default_settings() {
        return weforms_get_default_form_settings();
    }

    /**
     * Get notifications of contact form template
     *
     * @return array
     */
    function get_default_notification() {
        return array( weforms_get_default_form_notification() );
    }

}

