<?php

/**
 * Contact Form 7
 *
 * Import contact form 7 forms
 */
class WeForms_Importer_CF7 extends WeForms_Importer_Abstract {

    function __construct() {
        $this->id        = 'cf7';
        $this->title     = 'Contact Form 7';
        $this->shortcode = 'contact-form-7';

        parent::__construct();
    }

    /**
     * See if the plugin exists
     *
     * @return boolean
     */
    public function plugin_exists() {
        return class_exists( 'WPCF7' );
    }

    /**
     * Get all the forms
     *
     * @return array
     */
    public function get_forms() {
        $items    = WPCF7_ContactForm::find( array(
            'posts_per_page' => -1
        ) );

        return $items;
    }

    /**
     * Get form name
     *
     * @param  object $form
     *
     * @return string
     */
    public function get_form_name( $form ) {
        return $form->title();
    }

    /**
     * Get the form id
     *
     * @param  mixed $form
     *
     * @return int
     */
    protected function get_form_id( $form ) {
        return $form->id;
    }

    /**
     * Get the form fields
     *
     * @param  object $form
     *
     * @return array
     */
    public function get_form_fields( $form ) {
        $form_fields = array();
        $form_tags   = $form->scan_form_tags();
        $properties  = $form->get_properties();

        if ( ! $form_tags ) {
            return $form_fields;
        }

        foreach ($form_tags as $menu_order => $cf_field) {
            $field_content = array();

            switch ( $cf_field->basetype ) {
                case 'text':
                case 'email':
                case 'textarea':
                case 'date':
                case 'url':

                    $form_fields[] = $this->get_form_field( $cf_field->basetype, array(
                        'required'  => $cf_field->is_required() ? 'yes' : 'no',
                        'label'     => $this->find_label( $properties['form'], $cf_field->type, $cf_field->name ),
                        'name'      => $cf_field->name,
                        'css_class' => $cf_field->get_class_option(),
                    ) );
                    break;

                case 'select':
                case 'radio':
                case 'checkbox':
                    $form_fields[] = $this->get_form_field( $cf_field->basetype, array(
                        'required'  => $cf_field->is_required() ? 'yes' : 'no',
                        'label'     => $this->find_label( $properties['form'], $cf_field->type, $cf_field->name ),
                        'name'      => $cf_field->name,
                        'css_class' => $cf_field->get_class_option(),
                        'options'   => $this->get_options( $cf_field ),
                    ) );

                    break;

                case 'range':
                case 'number':

                    $field_content = $this->get_form_field( $cf_field->basetype, array(
                        'required'        => $cf_field->is_required() ? 'yes' : 'no',
                        'label'           => $this->find_label( $properties['form'], $cf_field->type, $cf_field->name ),
                        'name'            => $cf_field->name,
                        'css_class'       => $cf_field->get_class_option(),
                        'step_text_field' => $cf_field->get_option( 'step', 'int', true ),
                        'min_value_field' => $cf_field->get_option( 'min', 'signed_int', true ),
                        'max_value_field' => $cf_field->get_option( 'max', 'signed_int', true ),
                    ) );

                    if ( $cf_field->has_option( 'placeholder' ) || $cf_field->has_option( 'watermark' ) ) {
                        $field_content['placeholder'] = $value;
                        $value                        = '';
                    }

                    $value                  = $cf_field->get_default_option( $value );
                    $value                  = wpcf7_get_hangover( $cf_field->name, $value );
                    $field_content['value'] = $value;

                    $form_fields[] = $field_content;

                    break;

                case 'range':
                case 'quiz':
                    # code...
                    break;

                case 'acceptance':
                    $form_fields[] = $this->get_form_field( 'toc', array(
                        'required'    => $cf_field->is_required() ? 'yes' : 'no',
                        'description' => $this->find_label( $properties['form'], $cf_field->type, $cf_field->name ),
                        'name'        => $cf_field->name,
                    ) );
                    break;

                case 'recaptcha':
                    $form_fields[] = $this->get_form_field( $cf_field->basetype, array(
                        'required'  => $cf_field->is_required() ? 'yes' : 'no',
                        'label'     => $this->find_label( $properties['form'], $cf_field->type, $cf_field->name ),
                        'name'      => $cf_field->name,
                        'css_class' => $cf_field->get_class_option(),
                    ) );
                    break;

                case 'file':

                    $allowed_size       = 1024; // default size 1 MB
                    $allowed_file_types = array();

                    if ( $file_size_a = $cf_field->get_option( 'limit' ) ) {
                        $limit_pattern = '/^([1-9][0-9]*)([kKmM]?[bB])?$/';

                        foreach ( $file_size_a as $file_size ) {
                            if ( preg_match( $limit_pattern, $file_size, $matches ) ) {
                                $allowed_size = (int) $matches[1];

                                if ( ! empty( $matches[2] ) ) {
                                    $kbmb = strtolower( $matches[2] );

                                    if ( 'kb' == $kbmb ) {
                                        $allowed_size *= 1;
                                    } elseif ( 'mb' == $kbmb ) {
                                        $allowed_size *=  1024;
                                    }
                                }

                                break;
                            }
                        }
                    }


                    if ( $file_types_a = $cf_field->get_option( 'filetypes' ) ) {
                        foreach ( $file_types_a as $file_types ) {
                            $file_types = explode( '|', $file_types );

                            foreach ( $file_types as $file_type ) {
                                $file_type = trim( $file_type, '.' );
                                $file_type = str_replace( array( '.', '+', '*', '?' ), array( '\.', '\+', '\*', '\?' ), $file_type );

                                $_type = $this->get_file_type( $file_type );

                                if ( ! in_array( $_type, $allowed_file_types ) ) {
                                    $allowed_file_types[] = $_type;
                                }
                            }
                        }
                    }

                    $form_fields[] = $this->get_form_field( $cf_field->basetype, array(
                        'required'  => $cf_field->is_required() ? 'yes' : 'no',
                        'label'     => $this->find_label( $properties['form'], $cf_field->type, $cf_field->name ),
                        'name'      => $cf_field->name,
                        'css_class' => $cf_field->get_class_option(),
                        'max_size'  => $allowed_size,
                        'extension' => $allowed_file_types,
                    ) );
                    break;
            }
        }

        return $form_fields;
    }

    /**
     * Get form settings
     *
     * @param  object $form
     *
     * @return array
     */
    public function get_form_settings( $form ) {
        $default    = $this->get_default_form_settings();
        $properties = $form->get_properties();

        $settings = wp_parse_args( array(
            'message' => $properties['messages']['mail_sent_ok'],
        ), $default );

        return $settings;
    }

    /**
     * Get form notifications
     *
     * @param  object $form
     *
     * @return array
     */
    public function get_form_notifications( $form ) {
        $notifications = array();
        $properties    = $form->get_properties();

        $notifications = array(
            array(
                'active'      => $properties['mail']['active'] ? 'true' : 'false',
                'name'        => 'Admin Notification',
                'subject'     => str_replace( '[your-subject]', '{field:your-subject}', $properties['mail']['subject'] ),
                'to'          => $properties['mail']['recipient'],
                'replyTo'     => '{field:your-email}',
                'message'     => '{all_fields}',
                'fromName'    => '{site_name}',
                'fromAddress' => '{admin_email}',
                'cc'          => '',
                'bcc'         => '',
            ),
        );

        $sender_match = $this->get_notification_sender_match( $properties['mail'] );

        if ( !empty( $sender_match['fromName'] ) ) {
            $form_notifications[0]['fromName'] = $sender_match['fromName'];
        }

        if ( isset( $sender_match['fromAddress'] ) ) {
            $form_notifications[0]['fromAddress'] = $sender_match['fromAddress'];
        }

        if ( $properties['mail_2']['active'] ) {
            $notifications[] = array(
                'active'      => $properties['mail_2']['active'] ? 'true' : 'false',
                'name'        => 'Admin Notification',
                'subject'     => str_replace( '[your-subject]', '{field:your-subject}', $properties['mail_2']['subject'] ),
                'to'          => $properties['mail_2']['recipient'],
                'replyTo'     => '{field:your-email}',
                'message'     => '{all_fields}',
                'fromName'    => '{site_name}',
                'fromAddress' => '{admin_email}',
                'cc'          => '',
                'bcc'         => '',
            );
        }

        $sender_match = $this->get_notification_sender_match( $properties['mail2'] );

        if ( !empty( $sender_match['fromName'] ) ) {
            $form_notifications[1]['fromName'] = $sender_match['fromName'];
        }

        if ( isset( $sender_match['fromAddress'] ) ) {
            $form_notifications[1]['fromAddress'] = $sender_match['fromAddress'];
        }

        return $notifications;
    }

    /**
     * Match the sender
     *
     * @param  array $mail
     *
     * @return array
     */
    public function get_notification_sender_match( $mail ) {
        $sender       = array( 'fromName' => '', 'fromAddress' => '' );
        $sender_match = array();

        preg_match( '/([^<"]*)"?\s*<(\S*)>/', $mail['sender'], $sender_match );

        if ( isset( $sender_match[1] ) ) {
            $sender['fromName'] = $sender_match[1];
        }

        if ( isset( $sender_match[2] ) ) {
            $sender['fromAddress'] = $sender_match[2];
        }

        return $sender;
    }

    /**
     * Try to find out the input label
     *
     * Loop through all the label tags and try to find out
     * if the field is inside that tag. Then strip out the field and find out label
     *
     * @param  string $content
     * @param  string $type
     * @param  string $fieldname
     *
     * @return string
     */
    private function find_label( $content, $type, $fieldname ) {

        // find all enclosing label fields
        $pattern = '/<label>([ \w\S\r\n\t]+?)<\/label>/';
        preg_match_all( $pattern, $content, $matches );

        foreach ($matches[1] as $key => $match) {
            $match = trim( str_replace( "\n", '', $match ) );

            preg_match( '/\[(?:' . preg_quote( $type ) . ') ' . $fieldname . '(?:[ ](.*?))?(?:[\r\n\t ](\/))?\]/', $match, $input_match );

            if ( $input_match ) {
                $label = strip_tags( str_replace( $input_match[0], '', $match ) );
                return trim( $label );
            }
        }

        return $fieldname;
    }

    /**
     * Get file type for upload files
     *
     * @param  string $extension
     *
     * @return boolean|string
     */
    private function get_file_type( $extension ) {
        $allowed_extensions = weforms_allowed_extensions();

        foreach ($allowed_extensions as $type => $extensions) {
            $_extensions = explode( ',', $extensions['ext'] );

            if ( in_array( $extension, $_extensions ) ) {
                return $type;
            }
        }

        return false;
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

        if ( ! $field->raw_values ) {
            return $options;
        }

        foreach ($field->raw_values as $key => $value) {
            $options[ $value ] = $field->values[ $key ];
        }

        return $options;
    }

}
