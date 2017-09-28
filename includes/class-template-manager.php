<?php

/**
 * Template Manager Class
 *
 * @since 1.1.0
 */
class WeForms_Template_Manager {

    /**
     * The templates
     *
     * @var array
     */
    private $templates = array();

    /**
     * Get all the registered fields
     *
     * @return array
     */
    public function get_templates() {

        if ( ! empty( $this->templates ) ) {
            return $this->templates;
        }

        $this->register_templates();

        return $this->templates;
    }

    /**
     * Get all the templates
     *
     * @return array
     */
    public function register_templates() {

        require_once WEFORMS_INCLUDES . '/templates/class-abstract-template.php';

        require_once WEFORMS_INCLUDES . '/templates/class-template-blank.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-contact.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-event-registration.php';
        require_once WEFORMS_INCLUDES . '/templates/class-template-support.php';

        $templates = array(
            'blank'              => new WeForms_Template_Blank(),
            'contact'            => new WeForms_Template_Contact(),
            'event_registration' => new WeForms_Template_Event_Registration(),
            'support'            => new WeForms_Template_Support()
        );

        $this->templates = apply_filters( 'weforms_get_templates', $templates );
    }

    /**
     * Check if a template exists
     *
     * @param  string $name
     *
     * @return boolean
     */
    public function exists( $name ) {
        if ( array_key_exists( $name, $this->get_templates() ) ) {
            return $this->templates[ $name ];
        }

        return false;
    }

    /**
     * Create a form from a template
     *
     * @param  string $name
     *
     * @return integer
     */
    public function create( $name ) {
        if ( ! $template = $this->exists( $name ) ) {
            return;
        }

        $form_id = weforms()->form->create( $template->get_title(), $template->get_form_fields() );

        if ( is_wp_error( $form_id ) ) {
            return $form_id;
        }

        $meta_updates = array(
            'wpuf_form_settings' => $template->get_form_settings(),
            'notifications'      => $template->get_form_notifications(),
            'integrations'       => array()
        );

        foreach ($meta_updates as $meta_key => $meta_value) {
            update_post_meta( $form_id, $meta_key, $meta_value );
        }

        return $form_id;
    }
}
