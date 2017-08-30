<?php

/**
 * Import caldera forms
 *
 * Class WeForms_Importer_Caldera_Forms
 */
class WeForms_Importer_Caldera_Forms extends WeForms_Importer_Abstract {

    function __construct() {
        $this->id        = 'caldera-forms';
        $this->title     = 'Caldera Forms';
        $this->shortcode = 'caldera_form';

        parent::__construct();
    }

    /**
     * See if the plugin exists
     *
     * @return boolean
     */
    public function plugin_exists() {
        return class_exists( 'Caldera_Forms' );
    }

    /**
     * Get all the forms
     *
     * @return array
     */
    public function get_forms() {
        $forms = array();

        $items = Caldera_Forms_Forms::get_forms();

        foreach ( $items as $item ) {
            $forms[] = Caldera_Forms_Forms::get_form( $item );
        }

        return $forms;
    }

    /**
     * Get form name
     *
     * @param  object $form
     *
     * @return string
     */
    public function get_form_name( $form ) {
        return $form['name'];
    }

    /**
     * Get the form id
     *
     * @param  mixed $form
     *
     * @return int
     */
    protected function get_form_id( $form ) {
        return $form['ID'];
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
        $fields      = Caldera_Forms_Forms::get_fields( $form );
        foreach ( $fields as $name => $field ) {
            switch ( $field['type'] ) {
                case 'text':
                case 'email':
                case 'textarea':
                case 'date':
                case 'url':
                    $form_fields[] = $this->get_form_field( $field['type'], array(
                        'required' => isset( $field['required'] ) ? 'yes' : 'no',
                        'label'    => $field['label'],
                        'name'     => $field['slug'],
                        'css'      => $field['config']['custom_class'],
                    ) );
                    break;

                case 'select':
                case 'radio':
                case 'checkbox':
                    $form_fields[] = $this->get_form_field( $field['type'], array(
                        'required' => isset( $field['required'] ) ? 'yes' : 'no',
                        'label'    => $field['label'],
                        'name'     => $field['slug'],
                        'css'      => $field['config']['custom_class'],
                        'options'  => $field['config']['option'],
                    ) );
                    break;

                case 'range':
                case 'number':
                    $field_content[] = $this->get_form_field( $field['type'], array(
                        'required'        => isset( $field['required'] ) ? 'yes' : 'no',
                        'label'           => $field['label'],
                        'name'            => $field['slug'],
                        'css'             => $field['config']['custom_class'],
                        'step_text_field' => $field['config']['step'],
                        'min_value_field' => $field['config']['min'],
                        'max_value_field' => $field['config']['max'],
                    ) );

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
        $default  = $this->get_default_form_settings();
        $settings = wp_parse_args( array(
            'message' => $form['success'],
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

        $notifications = array(
            array(
                'active'      => $form['mailer']['on_insert'] ? 'true' : 'false',
                'name'        => 'Admin Notification',
                'subject'     => isset( $form['mailer']['email_subject'] ) ? $form['mailer']['email_subject'] : 'Admin Notification',
                'to'          => isset( $form['mailer']['recipients'] ) ? $form['mailer']['recipients'] : '{admin_email}',
                'replyTo'     => '{field:your-email}',
                'message'     => '{all_fields}',
                'fromName'    => '{site_name}',
                'fromAddress' => '{admin_email}',
                'cc'          => '',
                'bcc'         => '',
            ),
        );

//        $processors = array();
//        if(isset($form['processors'])){
//            $processors = $form['processors'];
//        }
//
//        foreach ($processors as $key => $processor){
//          if($processor['type'] == 'auto_responder'){
//              $config = $processor['config'];
//              $notifications[] = array(
//                  'active'      => true,
//                  'name'        => 'Admin Notification',
//                  'subject'     => $config['subject'],
//                  'to'          => $config['recipient_email'],
//                  'replyTo'     => '{field:your-email}',
//                  'message'     => '{all_fields}',
//                  'fromName'    => '{site_name}',
//                  'fromAddress' => '{admin_email}',
//                  'cc'          => '',
//                  'bcc'         => '',
//              );
//          }
//        }

        return $notifications;
    }

}
