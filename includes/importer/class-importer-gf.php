<?php

/**
 * Contact Form 7
 *
 * Import contact form 7 forms
 */
class WeForms_Importer_GF extends WeForms_Importer_Abstract {

    function __construct() {
        $this->id = 'gf';

        add_action( 'admin_notices', array( $this, 'maybe_show_notice' ) );

        // $forms = GFAPI::get_forms();
        // echo '<pre>';
        // print_r( $forms );
        // die();
        // add_action( 'wp_ajax_weforms_import_gf_dismiss', array( $this, 'dismiss_notice' ) );
        // add_action( 'wp_ajax_weforms_import_gf_forms', array( $this, 'import_forms' ) );
        // add_action( 'wp_ajax_weforms_gf_shortcode_replace', array( $this, 'replace_action' ) );
    }


    public function get_forms() {
        return GFAPI::get_forms();
    }

    public function get_form_name( $form ) {
        return $form['title'];
    }

    public function get_form_fields( $form ) {
        $form_fields = array();

        $form_tags  = $form['fields'];

        if ( ! $form_tags ) {
            return;
        }

        foreach ( $form_tags as $menu_order => $field ) {

            switch ( $field->type ) {

                case 'text':
                case 'email':
                case 'textarea':
                case 'date':
                case 'url':

                    $form_fields[] = $this->get_form_field( $field->type, array(
                        'required' => $field->isRequired ? 'yes' : 'no',
                        'label'    => $field->label,
                        'name'     => $this->get_meta_key( $field ),
                        'css'      => $field->cssClass,
                        'placeholder' => $field->placeholder
                    ) );
                    break;

            }
        }

    }

    public function get_form_settings( $form ) {

    }

    public function get_form_notifications( $form ) {

    }

    /**
    * Get unique meta key for field name
    *
    * @since 1.0.0
    *
    * @return void
    **/
    public function get_meta_key( $field ) {
        return str_replace( '-', '_', sanitize_title( $field->label . '-' . $field->id ) );
    }

    /**
     * Show notice if Contact From 7 found
     *
     * @return void
     */
    public function maybe_show_notice() {
        if ( ! class_exists( 'GFForms' ) ) {
            return;
        }

        if ( $this->is_dimissed() || ! current_user_can( 'manage_options' ) ) {
            return;
        }

        ?>
        <div class="notice notice-info">
            <p><strong><?php _e( 'Gravity Form Detected', 'weforms' ); ?></strong></p>
            <p><?php _e( 'Hey, looks like you have <strong>Gravity Form</strong> installed. Would you like to <strong>import</strong> the forms into weForms?', 'weforms' ); ?></p>

            <p>
                <a href="#" class="button button-primary weforms-import-gf" id="weforms-import-gf"><?php _e( 'Import Forms', 'weforms' ); ?></a>
                <a href="#" class="button weforms-import-gf" id="weforms-dimiss-gf"><?php _e( 'No Thanks', 'weforms' ); ?></a>
            </p>
        </div>

        <script type="text/javascript">
            jQuery(function($) {
                $('.notice').on('click', 'a#weforms-import-gf', function(e) {
                    e.preventDefault();

                    var self = $(this);
                    self.addClass('updating-message');

                    wp.ajax.send( 'weforms_import_gf_forms', {
                        data: {
                            type: self.data('type'),
                            _wpnonce: '<?php echo wp_create_nonce( 'weforms' ); ?>'
                        },

                        success: function(response) {
                            var html = '<p><strong>' + response.title + '</strong></p>' +
                                        '<p>' + response.message + '</p>' +
                                        '<p>' + response.action + '</p>';

                            html += '<ul>';
                            _.each(response.refs, function(el, index) {
                                html += '<li><a target="_blank" href="admin.php?page=weforms#/form/' + el.weforms_id + '/edit">' + el.title + '</a> - <a href="admin.php?page=weforms#/form/' + el.weforms_id + '/edit" target="_blank" class="button button-small"><span class="dashicons dashicons-external"></span> Edit</a></li>';
                            });

                            html += '</ul>';
                            html += '<p>' + '<a href="#" class="button button-primary weforms-gf-replace-action" data-type="replace"><?php _e( 'Replace Shortcodes', 'weforms' ); ?></a>&nbsp;' +
                                    '<a href="#" class="button weforms-gf-replace-action" data-type="skip"><?php _e( 'No Thanks', 'weforms' ); ?></a></p>';

                            self.closest('.notice').removeClass('notice-info').addClass('notice-success').html( html );
                        },

                        error: function(error) {
                            var html = '<p><strong>' + error.title + '</strong></p>' +
                                        '<p>' + error.message + '</p>';

                            self.closest('.notice').removeClass('notice-info').addClass('notice-error').html( html );
                        },

                        complete: function() {
                            self.removeClass('updating-message');
                        }
                    });
                });

                $('.notice').on('click', '#weforms-dimiss-gf', function(e) {
                    e.preventDefault();

                    $(this).closest('.notice').remove();
                    wp.ajax.send('weforms_import_gf_dismiss');
                });

                $('.notice').on('click', 'a.weforms-gf-replace-action', function(e) {
                    e.preventDefault();

                    var self = $(this);
                    var notice = self.closest('.notice');

                    self.addClass('updating-message');

                    wp.ajax.send( 'weforms_gf_shortcode_replace', {
                        data: {
                            type: self.data('type'),
                            _wpnonce: '<?php echo wp_create_nonce( 'weforms_gf_replace' ); ?>'
                        },

                        success: function(response) {
                            notice.remove();

                            if ( 'replace' === self.data('type') ) {
                                alert( response );
                            }
                        },

                        error: function(error) {
                            notice.remove();
                            alert( error );
                        },

                        complete: function() {
                            self.removeClass('updating-message');
                        }
                    });

                });
            });
        </script>
        <?php
    }

    // /**
    //  * Import forms
    //  *
    //  * @return void
    //  */
    // public function import_forms() {
    //     $forms = $this->get_forms();

    //     if ( $forms ) {
    //         foreach ($forms as $form) {
    //             $new_form      = $this->get_form( $form );
    //             $form_fields   = $this->get_form_fields( $form );
    //             $settings      = $this->get_form_settings( $form );
    //             $notifications = $this->get_form_notifications( $form );

    //             if ( $form_fields ) {
    //                 $form_id = $this->insert_form( $new_form );

    //                 foreach ($form_fields as $menu_order => $form_field) {
    //                     $this->insert_form_field( $form_id, $form_field, $menu_order );
    //                 }

    //                 update_post_meta( $form_id, 'wpuf_form_settings', $settings );
    //                 update_post_meta( $form_id, 'notifications', $notifications );
    //             }
    //         }
    //     }
    // }

        /**
     * Ajax import callback
     *
     * @return void
     */
    public function import_forms() {
        check_ajax_referer( 'weforms' );

        $this->check_caps();

        // check if plugin installed
        if ( ! class_exists( 'GFForms' ) ) {
            wp_send_json_error( array(
                'title' => __( 'Uh oh!', 'weforms' ),
                'message' => __( 'Gravity Form is not installed.', 'weforms' )
            ) );
        }

        $imported = 0;
        $refs     = array();
        $items    = WPCF7_ContactForm::find( array(
            'posts_per_page' => -1
        ) );

        if ( ! $items ) {
            wp_send_json_error( array(
                'title' => __( 'Uh oh!', 'weforms' ),
                'message' => __( 'No contact form found!', 'weforms' )
            ) );

            $this->dismiss_prompt();
        }

        foreach ($items as $form) {

            $properties = $form->get_properties();
            $form_tags  = $form->scan_form_tags();

            if ( ! $form_tags ) {
                continue;
            }

            $weforms_form = array(
                'post_title'  => '[CF7] ' . $form->title(),
                'post_type'   => 'wpuf_contact_form',
                'post_status' => 'publish',
                'post_author' => get_current_user_id()
            );

            $form_id = wp_insert_post( $weforms_form );

            if ( is_wp_error( $form_id ) ) {
                continue;
            }

            $submit_label = __( 'Submit Query', 'weforms' );
            $form_settings = array(
                'redirect_to'        => 'same',
                'message'            => $properties['messages']['mail_sent_ok'],
                'page_id'            => '',
                'url'                => '',
                'submit_text'        => __( 'Submit Query', 'weforms' ),
                'schedule_form'      => 'false',
                'schedule_start'     => '',
                'schedule_end'       => '',
                'sc_pending_message' => __( 'Form submission hasn\'t been started yet', 'weforms' ),
                'sc_expired_message' => __( 'Form submission is now closed.', 'weforms' ),
                'require_login'      => 'false',
                'req_login_message'  => __( 'You need to login to submit a query.', 'weforms' ),
                'limit_entries'      => 'false',
                'limit_number'       => '1000',
                'limit_message'      => __( 'Sorry, we have reached the maximum number of submissions.', 'weforms' ),
                'label_position'     => 'above',
            );

            $form_notifications = array(
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

            $sender_match = array();
            preg_match( '/([^<"]*)"?\s*<(\S*)>/', $properties['mail']['sender'], $sender_match );

            if ( isset( $sender_match[1] ) ) {
                $form_notifications[0]['fromName'] = $sender_match[1];
            }

            if ( isset( $sender_match[2] ) ) {
                $form_notifications[0]['fromAddress'] = $sender_match[2];
            }

            if ( $properties['mail_2']['active'] ) {
                $form_notifications[] = array(
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

            $sender2_match = array();
            preg_match( '/([^<"]*)"?\s*<(\S*)>/', $properties['mail_2']['sender'], $sender2_match );

            if ( isset( $sender2_match[1] ) ) {
                $form_notifications[1]['fromName'] = $sender2_match[1];
            }

            if ( isset( $sender2_match[2] ) ) {
                $form_notifications[1]['fromAddress'] = $sender2_match[2];
            }

            $conditional_config = array(
                'condition_status' => 'no',
                'cond_field'       => array(),
                'cond_operator'    => array( '=' ),
                'cond_option'      => array( '- select -' ),
                'cond_logic'       => 'all'
            );

            foreach ($form_tags as $menu_order => $cf_field) {
                $field_content = array();

                switch ( $cf_field->basetype ) {
                    case 'text':
                        $field_content = array(
                            'input_type'       => 'text',
                            'template'         => 'text_field',
                            'required'         => $cf_field->is_required() ? 'yes' : 'no',
                            'label'            => $this->find_label( $properties['form'], $cf_field->type, $cf_field->name ),
                            'name'             => $cf_field->name,
                            'is_meta'          => 'yes',
                            'help'             => '',
                            'css'              => $cf_field->get_class_option(),
                            'placeholder'      => '',
                            'default'          => '',
                            'size'             => 40,
                            'word_restriction' => '',
                            'wpuf_cond'        => $conditional_config
                        );
                        break;

                    case 'email':
                        $field_content = array(
                            'input_type'       => 'text',
                            'template'         => 'email_address',
                            'required'         => $cf_field->is_required() ? 'yes' : 'no',
                            'label'            => $this->find_label( $properties['form'], $cf_field->type, $cf_field->name ),
                            'name'             => $cf_field->name,
                            'is_meta'          => 'yes',
                            'help'             => '',
                            'css'              => $cf_field->get_class_option(),
                            'placeholder'      => '',
                            'default'          => '',
                            'size'             => 40,
                            'word_restriction' => '',
                            'wpuf_cond'        => $conditional_config
                        );
                        break;

                    case 'textarea':
                        $field_content = array(
                            'input_type'       => 'textarea',
                            'template'         => 'textarea_field',
                            'required'         => $cf_field->is_required() ? 'yes' : 'no',
                            'label'            => $this->find_label( $properties['form'], $cf_field->type, $cf_field->name ),
                            'name'             => $cf_field->name,
                            'is_meta'          => 'yes',
                            'help'             => '',
                            'css'              => $cf_field->get_class_option(),
                            'rows'             => 5,
                            'cols'             => 25,
                            'placeholder'      => '',
                            'default'          => '',
                            'rich'             => 'no',
                            'word_restriction' => '',
                            'wpuf_cond'        => $conditional_config
                        );
                        break;

                    case 'select':
                        $field_content = array(
                            'input_type' => 'select',
                            'template'   => 'dropdown_field',
                            'required'   => $cf_field->is_required() ? 'yes' : 'no',
                            'label'      => $this->find_label( $properties['form'], $cf_field->type, $cf_field->name ),
                            'name'       => $cf_field->name,
                            'is_meta'    => 'yes',
                            'help'       => '',
                            'css'        => $cf_field->get_class_option(),
                            'selected'   => '',
                            'inline'     => 'no',
                            'options'    => $this->get_options( $cf_field ),
                            'wpuf_cond'  => $conditional_config
                        );
                        break;

                    case 'date':
                        $field_content = array(
                            'input_type'      => 'date',
                            'template'        => 'date_field',
                            'required'        => $cf_field->is_required() ? 'yes' : 'no',
                            'label'           => $this->find_label( $properties['form'], $cf_field->type, $cf_field->name ),
                            'name'            => $cf_field->name,
                            'is_meta'         => 'yes',
                            'help'            => '',
                            'css'             => $cf_field->get_class_option(),
                            'format'          => 'dd/mm/yy',
                            'time'            => '',
                            'is_publish_time' => '',
                            'wpuf_cond'       => $conditional_config
                        );
                        break;

                    case 'range':
                    case 'number':

                        $field_content = array(
                            'input_type'       => 'numeric_text',
                            'template'         => 'numeric_text_field',
                            'required'         => $cf_field->is_required() ? 'yes' : 'no',
                            'label'            => $this->find_label( $properties['form'], $cf_field->type, $cf_field->name ),
                            'name'             => $cf_field->name,
                            'is_meta'          => 'yes',
                            'help'             => '',
                            'css'              => $cf_field->get_class_option(),
                            'placeholder'      => '',
                            'default'          => $value,
                            'size'             => 40,
                            'step_text_field' => $cf_field->get_option( 'step', 'int', true ),
                            'min_value_field' => $cf_field->get_option( 'min', 'signed_int', true ),
                            'max_value_field' => $cf_field->get_option( 'max', 'signed_int', true ),
                            'wpuf_cond'        => $conditional_config
                        );

                        if ( $cf_field->has_option( 'placeholder' ) || $cf_field->has_option( 'watermark' ) ) {
                            $field_content['placeholder'] = $value;
                            $value                        = '';
                        }

                        $value                  = $cf_field->get_default_option( $value );
                        $value                  = wpcf7_get_hangover( $cf_field->name, $value );
                        $field_content['value'] = $value;

                        break;

                    case 'url':
                        $field_content = array(
                            'input_type'       => 'text',
                            'template'         => 'website_url',
                            'required'         => $cf_field->is_required() ? 'yes' : 'no',
                            'label'            => $this->find_label( $properties['form'], $cf_field->type, $cf_field->name ),
                            'name'             => $cf_field->name,
                            'is_meta'          => 'yes',
                            'help'             => '',
                            'css'              => $cf_field->get_class_option(),
                            'placeholder'      => '',
                            'default'          => '',
                            'size'             => 40,
                            'word_restriction' => '',
                            'wpuf_cond'        => $conditional_config
                        );
                        break;

                    case 'range':
                        # code...
                        break;

                    case 'checkbox':
                        $field_content = array(
                            'input_type' => 'checkbox',
                            'template'   => 'checkbox_field',
                            'required'   => $cf_field->is_required() ? 'yes' : 'no',
                            'label'      => $this->find_label( $properties['form'], $cf_field->type, $cf_field->name ),
                            'name'       => $cf_field->name,
                            'is_meta'    => 'yes',
                            'help'       => '',
                            'css'        => $cf_field->get_class_option(),
                            'selected'   => '',
                            'inline'     => 'no',
                            'options'    => $this->get_options( $cf_field ),
                            'wpuf_cond'  => $conditional_config
                        );
                        break;

                    case 'radio':
                        $field_content = array(
                            'input_type' => 'radio',
                            'template'   => 'radio_field',
                            'required'   => $cf_field->is_required() ? 'yes' : 'no',
                            'label'      => $this->find_label( $properties['form'], $cf_field->type, $cf_field->name ),
                            'name'       => $cf_field->name,
                            'is_meta'    => 'yes',
                            'help'       => '',
                            'css'        => $cf_field->get_class_option(),
                            'selected'   => '',
                            'inline'     => 'no',
                            'options'    => $this->get_options( $cf_field ),
                            'wpuf_cond'  => $conditional_config
                        );
                        break;

                    case 'acceptance':
                        $field_content = array(
                            'input_type'       => 'toc',
                            'template'         => 'toc',
                            'required'         => $cf_field->is_required() ? 'yes' : 'no',
                            'name'             => $cf_field->name,
                            'description'      => $this->find_label( $properties['form'], $cf_field->type, $cf_field->name ),
                            'name'             => '',
                            'is_meta'          => 'yes',
                            'show_checkbox'    => true,
                            'wpuf_cond'        => $conditional_config
                        );
                        break;

                    case 'quiz':
                        # code...
                        break;

                    case 'recaptcha':
                        $field_content = array(
                            'input_type'       => 'recaptcha',
                            'template'         => 'recaptcha',
                            'required'         => $cf_field->is_required() ? 'yes' : 'no',
                            'label'            => $this->find_label( $properties['form'], $cf_field->type, $cf_field->name ),
                            'name'             => $cf_field->name,
                            'recaptcha_type'   => 'enable_no_captcha',
                            'is_meta'          => 'yes',
                            'help'             => '',
                            'css'              => $cf_field->get_class_option(),
                            'placeholder'      => '',
                            'default'          => '',
                            'size'             => 40,
                            'word_restriction' => '',
                            'wpuf_cond'        => $conditional_config
                        );
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

                        $field_content = array(
                            'input_type' => 'file_upload',
                            'template'   => 'file_upload',
                            'required'   => $cf_field->is_required() ? 'yes' : 'no',
                            'label'      => $this->find_label( $properties['form'], $cf_field->type, $cf_field->name ),
                            'name'       => $cf_field->name,
                            'is_meta'    => 'yes',
                            'help'       => '',
                            'css'        => $cf_field->get_class_option(),
                            'max_size'   => $allowed_size,
                            'count'      => '1',
                            'extension'  => $allowed_file_types,
                            'wpuf_cond'  => $conditional_config
                        );
                        break;

                    case 'submit':
                        $submit_label = isset( $cf_field->values[0] ) ? $cf_field->values[0] : '';
                        break;
                }


                if ( $field_content ) {
                    $form_field = array(
                        'post_type'    => 'wpuf_input',
                        'post_status'  => 'publish',
                        'post_content' => maybe_serialize( $field_content ),
                        'post_parent'  => $form_id,
                        'menu_order'   => $menu_order
                    );

                    wp_insert_post( $form_field );
                }
            }

            $form_settings['submit_text'] = $submit_label;

            update_post_meta( $form_id, 'wpuf_form_settings', $form_settings );
            update_post_meta( $form_id, 'notifications', $form_notifications );

            $imported++;

            $refs[$form->id] = array(
                'cf7_id'     => $form->id,
                'weforms_id' => $form_id,
                'title'      => '[CF7] ' . $form->title()
            );
        }

        $this->dismiss_prompt();
        update_option( 'weforms_cf7_imported_forms', $refs );

        wp_send_json_success( array(
            'title'   => sprintf( _n( '%s form imported', '%s forms imported', $imported ), $imported ),
            'message' => __( 'We have successfully imported these forms into weForms. You could check and edit in-case anything weird happended.', 'weforms' ),
            'action'  => __( 'Do you want to <strong>replace</strong> Contact From 7 shortcodes with weForms?', 'weforms' ),
            'refs'    => $refs
        ) );
    }

    /**
     * Dismiss the prompt
     *
     * @return void
     */
    public function dismiss_prompt() {
        update_option( 'weforms_dismiss_gf_notice', 'yes' );
    }

    /**
     * If the prompt is dismissed
     *
     * @return boolean
     */
    public function is_dimissed() {
        return 'yes' == get_option( 'weforms_dismiss_gf_notice' );
    }


}