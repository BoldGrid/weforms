<?php

/**
 * Ninja Form
 *
 * Import Ninja form forms
 */
class WeForms_Importer_Ninja_Forms extends WeForms_Importer_Abstract {

    /**
     * Will store submit button label
     *
     * @var string
     */
    private $submit_text;

    function __construct() {
        $this->id        = 'nf';
        $this->title     = 'Ninja Forms';
        $this->shortcode = 'ninja_form';

        parent::__construct();
    }

    /**
     * See if the plugin exists
     *
     * @return boolean
     */
    public function plugin_exists() {
        return class_exists( 'Ninja_Forms' );
    }

    /**
     * Show notice if Ninja From found
     *
     * @return void
     */
    public function ninja_form_field($form) {

        $data = array();
        foreach( Ninja_Forms()->form( $form->get_id() )->get_fields() as $field ){
            $data[$field->get_settings( 'order' )] = array(
                'type'      => $field->get_setting( 'type' ),
                'key'       => $field->get_setting( 'key' ),
                'label'     => $field->get_setting( 'label' ),
                'default'     => $field->get_setting( 'default' ),
                'required'  => $field->get_setting( 'required' ) ? $field->get_setting( 'required' ) : 0
            );

            if (in_array($field->get_setting('type'), array('listselect', 'listradio', 'listcheckbox', 'listmultiselect')) ) {
                foreach ($field->get_setting('options') as $option) {
                    $data[$field->get_settings( 'order' )]['options'][] = array(
                        'label'     => $option['label'],
                        'value'     => $option['value'],
                    );
                }
            }
        }
        return $data;
    }


    /**
     * Get all the forms
     *
     * @return array
     */
    public function get_forms() {
        $items    = Ninja_Forms()->form()->get_forms();

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
        return $form->get_setting( 'title' );
    }

    /**
     * Get the form id
     *
     * @param  mixed $form
     *
     * @return int
     */
    protected function get_form_id( $form ) {
        return $form->get_id();
    }

    /**
     * Get the form fields
     *
     * @param  object $form
     *
     * @return array
     */
    public function get_form_fields( $form ) {

        $ninja_form_fields = $this->ninja_form_field($form);


        // echo "<pre>";
        // print_r($ninja_form_fields);
        // echo "</pre>";

        // die;

        $form_fields = array();

        foreach ($ninja_form_fields as $menu_order => $field) {

            $field_content = array();

            switch ( $field['type'] ) {
                case 'textbox':
                case 'text':
                case 'email':
                case 'textarea':
                case 'date':
                case 'url':
                case 'firstname':
                case 'lastname':
                case 'zip':
                case 'product':
                case 'city':
                case 'quiz':
                case 'address':
                case 'spam':

                    if($field['type'] == 'firstname' || $field['type'] == 'lastname' || $field['type'] == 'zip' || $field['type'] == 'product' || $field['type'] == 'city' || $field['type'] == 'quiz' || $field['type'] == 'address' || $field['type'] == 'spam' || $field['type'] == 'textbox') {
                        $field['type'] = 'text';
                    }

                    $form_fields[] = $this->get_form_field( $field['type'], array(
                        'required' => $field['required'] ? 'yes' : 'no',
                        'label'    => $this->find_label( $field['label'], $field['type'], $field['key'] ),
                        'name'     => $field['key'],
                    ) );
                    break;

                case 'checkbox':
                case 'listcheckbox':
                    if($field['type'] == 'listcheckbox') {
                        $field['type'] = 'checkbox';
                    }

                    $form_fields[] = $this->get_form_field( $field['type'], array(
                        'required' => $field['required'] ? 'yes' : 'no',
                        'label'    => $this->find_label( $field['label'], $field['type'], $field['key'] ),
                        'name'     => $field['key'],
                        'options'  => $this->get_options( $field['options'] ) ? $this->get_options( $field['options'] ) : array( 'label' => $field['label'], 'value' => $field['key'] ),
                    ) );

                    break;

                case 'select':
                case 'radio':
                case 'listmultiselect':
                case 'listradio':
                case 'listselect':
                case 'listcountry':
                case 'starrating':
                    if($field['type'] == 'listcountry' || $field['type'] == 'starrating') {
                        $field['type'] = 'select';
                    } else if($field['type'] == 'listmultiselect' || $field['type'] == 'listselect') {
                        $field['type'] = 'multiselect';
                    } else if($field['type'] == 'listradio') {
                        $field['type'] = 'radio';
                    }


                    $form_fields[] = $this->get_form_field( $field['type'], array(
                        'required' => $field['required'] ? 'yes' : 'no',
                        'label'    => $this->find_label( $field['label'], $field['type'], $field['key'] ),
                        'name'     => $field['key'],
                        'options'  => $this->get_options( $field['options'] ),
                    ) );
                    break;

                case 'range':
                case 'number':
                case 'phone':
                case 'quantity':
                case 'total':
                case 'shipping':
                    if($field['type'] == 'phone' || $field['type'] == 'quantity' || $field['type'] == 'total' || $field['type'] == 'shipping') {
                        $field['type'] = 'number';
                    }

                    $form_fields[] = $this->get_form_field( $field['type'], array(
                        'required'        => $field['required'] ? 'yes' : 'no',
                        'label'           => $this->find_label( $field['label'], $field['type'], $field['key'] ),
                        'name'            => $field['key'],
                    ) );

                    break;

                case 'hr':
                case 'hidden':
                case 'html':

                    if($field['type'] == 'hr') {
                        $field['type'] = 'section_break';
                    }

                    $form_fields[] = $this->get_form_field( $field['type'], array(
                        'label'    => $this->find_label( $field['label'], $field['type'], $field['key'] ),
                        'name'     => $field['key'],
                        'default'  => $field['default'],
                    ) );

                    break;

                case 'acceptance':

                    $form_fields[] = $this->get_form_field( 'toc', array(
                        'required'    => $field['required'] ? 'yes' : 'no',
                        'description' => $this->find_label( $field['label'], $field['type'], $field['key'] ),
                        'name'        => $field['key'],
                    ) );
                    break;

                case 'recaptcha':

                    $form_fields[] = $this->get_form_field( $field['type'], array(
                        'required' => $field['required'] ? 'yes' : 'no',
                        'label'    => $this->find_label( $field['label'], $field['type'], $field['key'] ),
                        'name'     => $field['key'],
                    ) );
                    break;

                case 'submit':

                    $this->submit_text = $field['label'];

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
        $all_settings = get_option( 'nf_form_' . $form->get_id(), true );
        foreach ($all_settings['actions'] as $actions) {
            if('successmessage' == $actions['settings']['type']){
                $message = $actions['settings']['message'];
            }
        }
        $message    = str_replace(' {field:name}', '', $message);
        $default    = $this->get_default_form_settings();
        $settings   = wp_parse_args( array(
            'message' => $message,
            ), $default );

        if ( $this->submit_text ) {
            $settings['submit_text'] = $this->submit_text;
        }

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
        $all_settings = get_option( 'nf_form_' . $form->get_id(), true );
        foreach ($all_settings['actions'] as $actions) {
            if('Email Notification' == $actions['settings']['label']){
                $action_settings = $actions['settings'];
            }
            else if('Email Confirmation' == $actions['settings']['label']){
                $action_settings2 = $actions['settings'];
            }
        }

        $sub    = str_replace('{field:name}', '{site_name}', $action_settings['email_subject']);

        $notifications = array(
            array(
                'active'      => $action_settings['active'] ? 'true' : 'false',
                'name'        => 'Admin Notification',
                'subject'     => str_replace( '[your-subject]', '{field:your-subject}', $sub ),
                'to'          => '{field:your-email}',
                'replyTo'     => '{field:your-email}',
                'message'     => '{all_fields}',
                'fromName'    => '{site_name}',
                'fromAddress' => '{admin_email}',
                'cc'          => '',
                'bcc'         => '',
            ),
        );

        $sender_match = $this->get_notification_sender_match( get_option( 'admin_email' ) );

        if ( !empty( $sender_match['fromName'] ) ) {
            $form_notifications[0]['fromName'] = $sender_match['fromName'];
        }

        if ( isset( $sender_match['fromAddress'] ) ) {
            $form_notifications[0]['fromAddress'] = $sender_match['fromAddress'];
        }

        if ( $action_settings2['active'] ) {
            $notifications[] = array(
                'active'      => $action_settings2['active'] ? 'true' : 'false',
                'name'        => 'Admin Notification',
                'subject'     => str_replace( '[your-subject]', $action_settings2['subject'], $action_settings2['subject'] ),
                'to'          => '{field:your-email}',
                'replyTo'     => '{field:your-email}',
                'message'     => '{all_fields}',
                'fromName'    => '{site_name}',
                'fromAddress' => '{admin_email}',
                'cc'          => '',
                'bcc'         => '',
            );
        }

        $sender_match = $this->get_notification_sender_match( get_option( 'admin_email' ) );

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

        if ( !isset( $mail['sender'] ) ) {
            return;
        }
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

        return $content; // i guess, we don't need this :/ will remove later

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

        if ( !is_array( $field ) ) {
            return $options;
        }

        foreach ($field as $value) {
            $options[ $value['value'] ] = $value['label'];
        }

        return $options;
    }
}
