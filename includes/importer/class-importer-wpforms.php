<?php
/**
 * WPForms importer class
 */
class WeForms_Importer_WPForms extends WeForms_Importer_Abstract {

    function __construct() {
        $this->id        = 'wpforms';
        $this->title     = 'WPForms';
        $this->shortcode = 'wpforms';

        parent::__construct();
    }

    /**
     * Check if the plugin exists
     *
     * @return boolean
     */
    public function plugin_exists() {
        return function_exists( 'wpforms' );
    }

    /**
     * Get all the forms
     *
     * @return array
     */
    public function get_forms() {
        return wpforms()->form->get();
    }

    /**
     * Parse a form to array
     *
     * @param  string $form
     *
     * @return array
     */
    public function parse_form( $form ) {
        return json_decode( $form->post_content, true );
    }

    /**
     * Get the form name
     *
     * @param  string $form
     *
     * @return string
     */
    public function get_form_name( $form ) {
        return $form->post_title;
    }

    /**
     * Get the form id
     *
     * @param  mixed $form
     *
     * @return int
     */
    protected function get_form_id( $form ) {
        return $form->ID;
    }

    /**
     * Get form notifications of a form
     *
     * @param  mixed $form
     *
     * @return array
     */
    public function get_form_notifications( $form ) {
        $parsed       = $this->parse_form( $form );
        $notification = array_pop( $parsed['settings']['notifications'] );

        $form_notifications = array(
            array(
                'active'      => $settings['notification_enable'] ? true : false,
                'name'        => 'Admin Notification',
                'subject'     => $notification['subject'],
                'to'          => $notification['email'],
                'replyTo'     => $notification['replyto'],
                'message'     => $notification['message'],
                'fromName'    => $notification['sender_name'],
                'fromAddress' => $notification['sender_address'],
                'cc'          => '',
                'bcc'         => '',
            ),
        );

        return $form_notifications;
    }

    /**
     * Get all form fields of a form
     *
     * @param  mixed $form
     *
     * @return array
     */
    public function get_form_fields( $form ) {
        $form_fields = array();
        $parsed      = $this->parse_form( $form );

        if ( empty( $parsed['fields'] ) ) {
            return $form_fields;
        }

        foreach ( $parsed['fields'] as $menu_order => $field ) {

            // avoid empty meta_key
            $field['id'] = $field['type'] . '_' . $field['id'];

            switch ( $field['type'] ) {
                case 'text':
                case 'email':
                case 'textarea':

                    $form_fields[] = $this->get_form_field( $field['type'], array(
                        'required'    => ! empty( $field['required'] ) ? 'yes' : 'no',
                        'label'       => $field['label'],
                        'name'        => $field['id'],
                        'help'        => $field['description'],
                        'css_class'   => $field['css'],
                        'placeholder' => $field['placeholder'],
                        'default'     => ! empty( $field['default_value'] ) ? $field['default_value'] : '',
                    ) );

                    break;

                case 'select':
                case 'radio':
                case 'checkbox':

                    $form_fields[] = $this->get_form_field( $field['type'], array(
                        'required'    => ! empty( $field['required'] ) ? 'yes' : 'no',
                        'label'       => $field['label'],
                        'name'        => $field['id'],
                        'help'        => $field['description'],
                        'css_class'   => $field['css'],
                        'placeholder' => $field['placeholder'],
                        'selected'    => ! empty( $field['default_value'] ) ? $field['default_value'] : '',
                        'options'     => $this->get_options( $field ),
                    ) );
                    break;

                case 'name':

                    $form_fields[] = $this->get_form_field( $field['type'], array(
                        'required'   => ! empty( $field['required'] ) ? 'yes' : 'no',
                        'label'      => $field['label'],
                        'name'       => $field['id'],
                        'help'       => $field['description'],
                        'css_class'  => $field['css'],
                        'format'     => ( $field['format'] === 'first-last' ) ? 'first-last' : 'first-middle-last',
                        'hide_subs'  => ! empty( $field['sublabel_hide'] ) ? true : false,
                        'first_name' => array(
                            'placeholder' => $field['first_placeholder'],
                            'default'     => $field['first_default'],
                            'sub'         => __( 'First', 'weforms' ),
                        ),
                        'middle_name' => array(
                            'placeholder' => $field['middle_placeholder'],
                            'default'     => $field['middle_default'],
                            'sub'         => __( 'Middle', 'weforms' ),
                        ),
                        'last_name'   => array(
                            'placeholder' => $field['last_placeholder'],
                            'default'     => $field['last_default'],
                            'sub'         => __( 'Last', 'weforms' ),
                        ),
                    ) );
                    break;
            }
        }

        return $form_fields;
    }

    public function get_form_settings( $form ) {
        $default = $this->get_default_form_settings();
        $parsed  = $this->parse_form( $form );

        $settings = $parsed['settings'];

        // Settings mapping
        switch ( $settings['confirmation_type'] ) {
            case 'redirect':
                $redirect_to = 'url';
                break;

            case 'page':
                $redirect_to = 'page';
                break;

            case 'message':
            default:
                $redirect_to = 'same';
                break;
        }

        $form_settings = wp_parse_args( array(
            'message'     => $settings['confirmation_message'],
            'page_id'     => $settings['confirmation_page'],
            'url'         => $settings['confirmation_redirect'],
            'submit_text' => $settings['submit_text'],
            'redirect_to' => $redirect_to,
        ), $default );

        return $form_settings;
    }

    /**
     * Translate to wpuf field options array
     *
     * @param  object $field
     *
     * @return array
     */
    private function get_options( $field ) {
        $options = array();

        if ( ! $field['choices'] ) {
            return $options;
        }

        foreach ( $field['choices'] as $choice ) {
            $options[ $choice['label'] ] = $choice['label'];
        }

        return $options;
    }
}
