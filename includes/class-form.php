<?php

/**
 * The Form Class
 *
 * @since 1.1.0
 */
class WeForms_Form {

    /**
     * The form id
     *
     * @var integer
     */
    public $id = 0;

    /**
     * The form title
     *
     * @var string
     */
    public $name = '';

    /**
     * Holds the post data object
     *
     * @var null|WP_post
     */
    public $data = null;

    /**
     * The Constructor
     *
     * @param int|WP_Post $form
     */
    public function __construct( $form = null ) {

        if ( is_numeric( $form ) ) {

            $the_post = get_post( $form );

            if ( $the_post ) {
                $this->id   = $the_post->ID;
                $this->name = $the_post->post_title;
                $this->data = $the_post;
            }

        } elseif ( is_a( $form, 'WP_Post' ) ) {
            $this->id   = $form->ID;
            $this->name = $form->post_title;
            $this->data = $form;
        }
    }

    /**
     * Get the form ID
     *
     * @return integer
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Get the form name
     *
     * @return string
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Get all form fields of this form
     *
     * @return array
     */
    public function get_fields() {
        $fields = get_children(array(
            'post_parent' => $this->id,
            'post_status' => 'publish',
            'post_type'   => 'wpuf_input',
            'numberposts' => '-1',
            'orderby'     => 'menu_order',
            'order'       => 'ASC',
        ));

        $form_fields = array();

        foreach ( $fields as $key => $content ) {

            $field = maybe_unserialize( $content->post_content );

            $field['id'] = $content->ID;

            // Add inline property for radio and checkbox fields
            $inline_supported_fields = array( 'radio_field', 'checkbox_field' );
            if ( in_array( $field['template'] , $inline_supported_fields ) ) {
                if ( ! isset( $field['inline'] ) ) {
                    $field['inline'] = 'no';
                }
            }

            // Add 'selected' property
            $option_based_fields = array( 'dropdown_field', 'multiple_select', 'radio_field', 'checkbox_field' );
            if ( in_array( $field['template'] , $option_based_fields ) ) {
                if ( ! isset( $field['selected'] ) ) {

                    if ( 'dropdown_field' === $field['template'] || 'radio_field' === $field['template'] ) {
                        $field['selected'] = '';
                    } else {
                        $field['selected'] = array();
                    }

                }
            }

            // Add 'multiple' key for template:repeat
            if ( 'repeat_field' === $field['template'] && ! isset( $field['multiple'] ) ) {
                $field['multiple'] = '';
            }

            if ( 'recaptcha' === $field['template'] ) {
                $field['name'] = 'recaptcha';
                $field['enable_no_captcha'] = isset( $field['enable_no_captcha'] ) ? $field['enable_no_captcha'] : '';

            }

            $form_fields[] = apply_filters( 'weforms-get-form-fields', $field );
        }

        return $form_fields;
    }

    /**
     * Get form settings
     *
     * @return array
     */
    public function get_settings() {
        return wpuf_get_form_settings( $this->id );
    }

    /**
     * Get form notifications
     *
     * @return array
     */
    public function get_notifications() {
        return wpuf_get_form_notifications( $this->id );
    }

    /**
     * Get all the integrations
     *
     * @return array
     */
    public function get_integrations() {
        return wpuf_get_form_integrations( $this->id );
    }

    /**
     * Get entries of this form
     *
     * @return \WeForms_Form_Entry_Manager
     */
    public function entries() {
        return new WeForms_Form_Entry_Manager( $this->id );
    }

    /**
     * Get number of form entries
     *
     * @return integer
     */
    public function num_form_entries() {
        return weforms_count_form_entries( $this->id );
    }

    /**
     * Get the number of form views
     *
     * @return integer
     */
    public function num_form_views() {
        return weforms_get_form_views( $this->id );
    }
}
