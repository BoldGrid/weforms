<?php

if ( ! class_exists( 'WPUF_Post_Form_Template' ) ) :

/**
 * Post form template
 *
 * @since 2.4
 */
abstract class WPUF_Post_Form_Template {

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
        return apply_filters( 'wpuf_post_form_template_title', $this->title, $this );
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function get_description() {
        return apply_filters( 'wpuf_post_form_template_description', $this->description, $this );
    }

    /**
     * Get the form fields
     *
     * @return array
     */
    public function get_form_fields() {
        return apply_filters( 'wpuf_post_form_template_form_fields', $this->form_fields, $this );
    }

    /**
     * Get the form settings
     *
     * @return array
     */
    public function get_form_settings() {
        return apply_filters( 'wpuf_post_form_template_form_settings', $this->form_settings, $this );
    }

    /**
     * Get form notifications
     *
     * @since 2.5.2
     *
     * @return array
     */
    public function get_form_notifications() {
        return apply_filters( 'wpuf_post_form_template_form_notifications', $this->form_notifications, $this );
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
     * Run necessary processing after new post insert
     *
     * @param  int   $post_id
     * @param  int   $form_id
     * @param  array $form_settings
     *
     * @return void
     */
    public function after_insert( $post_id, $form_id, $form_settings ) {}

    /**
     * Run necessary processing after editing a post
     *
     * @param  int   $post_id
     * @param  int   $form_id
     * @param  array $form_settings
     *
     * @return void
     */
    public function after_update( $post_id, $form_id, $form_settings ) {}
}

endif;