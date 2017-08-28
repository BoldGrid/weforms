<?php
/**
 * WPForms importer class
 */
class WeForms_Importer_WPForms extends WeForms_Importer_Abstract {

    function __construct() {
        add_action( 'admin_notices', array( $this, 'maybe_show_notice' ) );
        add_action( 'wp_ajax_weforms_import_wpforms_dismiss', array( $this, 'dismiss_notice' ) );
        add_action( 'wp_ajax_weforms_import_wpforms_forms', array( $this, 'import_forms' ) );
    }

    /**
     * Show notice if WPForms found
     *
     * @return void
     */
    public function maybe_show_notice() {
        if ( ! class_exists( 'WPForms' ) ) {
            return;
        }

        if ( $this->is_dimissed() || ! current_user_can( 'manage_options' ) ) {
            return;
        }

        ?>
        <div class="notice notice-info">
            <p><strong><?php _e( 'WPForms Detected', 'weforms' ); ?></strong></p>
            <p><?php _e( 'Hey, looks like you have <strong>WPForms</strong> installed. Would you like to <strong>import</strong> the forms into weForms?', 'weforms' ); ?></p>

            <p>
                <a href="#" class="button button-primary weforms-import-wpforms" id="weforms-import-wpforms"><?php _e( 'Import Forms', 'weforms' ); ?></a>
                <a href="#" class="button weforms-import-wpforms" id="weforms-dimiss-wpforms"><?php _e( 'No Thanks', 'weforms' ); ?></a>
            </p>
        </div>

        <script type="text/javascript">
            jQuery(function($) {
                $('.notice').on('click', 'a#weforms-import-wpforms', function(e) {
                    e.preventDefault();

                    var self = $(this);
                    self.addClass('updating-message');

                    wp.ajax.send( 'weforms_import_wpforms_forms', {
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
                            html += '<p>' + '<a href="#" class="button button-primary weforms-wpforms-replace-action" data-type="replace"><?php _e( 'Replace Shortcodes', 'weforms' ); ?></a>&nbsp;' +
                                    '<a href="#" class="button weforms-wpforms-replace-action" data-type="skip"><?php _e( 'No Thanks', 'weforms' ); ?></a></p>';

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

                $('.notice').on('click', '#weforms-dimiss-wpforms', function(e) {
                    e.preventDefault();

                    $(this).closest('.notice').remove();
                    wp.ajax.send('weforms_import_wpforms_dismiss');
                });

                $('.notice').on('click', 'a.weforms-wpforms-replace-action', function(e) {
                    e.preventDefault();

                    var self = $(this);
                    var notice = self.closest('.notice');

                    self.addClass('updating-message');

                    wp.ajax.send( 'weforms_wpforms_shortcode_replace', {
                        data: {
                            type: self.data('type'),
                            _wpnonce: '<?php echo wp_create_nonce( 'weforms_wpforms_replace' ); ?>'
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

    /**
     * Ajax import callback
     *
     * @return void
     */
    public function import_forms() {
        check_ajax_referer( 'weforms' );

        $this->check_caps();

        // check if plugin installed
        if ( ! function_exists( 'wpforms' ) ) {
            wp_send_json_error( array(
                'title' => __( 'Uh oh!', 'weforms' ),
                'message' => __( 'WPForms is not installed.', 'weforms' )
            ) );
        }

        $imported = 0;
        $refs     = array();
        $items    = wpforms()->form->get();

        if ( ! $items ) {
            wp_send_json_error( array(
                'title' => __( 'Uh oh!', 'weforms' ),
                'message' => __( 'No contact form found!', 'weforms' )
            ) );

            $this->dismiss_prompt();
        }

        foreach ( $items as $item ) {
            $form = json_decode( $item->post_content, true );

            $fields   = $form['fields'];
            $settings = $form['settings'];
            $meta     = $form['meta'];

            if ( empty( $fields ) ) {
                return;
            }

            $weforms_form = array(
                'post_title'  => '[WPForms] ' . $settings['form_title'],
                'post_type'   => 'wpuf_contact_form',
                'post_status' => 'publish',
                'post_author' => get_current_user_id()
            );

            $form_id = wp_insert_post( $weforms_form );

            if ( is_wp_error( $form_id ) ) {
                continue;
            }

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

            $form_settings = array(
                'redirect_to'        => $redirect_to,
                'message'            => $settings['confirmation_message'],
                'page_id'            => $settings['confirmation_page'],
                'url'                => $settings['confirmation_redirect'],
                'submit_text'        => $settings['submit_text'],
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

            $notification = array_pop( $settings['notifications'] );

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

            $conditional_config = array(
                'condition_status' => 'no',
                'cond_field'       => array(),
                'cond_operator'    => array( '=' ),
                'cond_option'      => array( '- select -' ),
                'cond_logic'       => 'all'
            );

            // Fields mapping
            foreach ( $fields as $menu_order => $field ) {
                $field_content = array();

                switch ( $field['type'] ) {
                    case 'text':
                        $field_content = array(
                            'input_type'       => 'text',
                            'template'         => 'text_field',
                            'required'         => ! empty( $field['required'] ) ? 'yes' : 'no',
                            'label'            => $field['label'],
                            'name'             => 'wpforms_field_' . $field['id'],
                            'is_meta'          => 'yes',
                            'help'             => $field['description'],
                            'css'              => $field['css'],
                            'placeholder'      => $field['placeholder'],
                            'default'          => ! empty( $field['default_value'] ) ? $field['default_value'] : '',
                            'size'             => 40,
                            'word_restriction' => '',
                            'wpuf_cond'        => $conditional_config
                        );
                        break;

                    case 'email':
                        $field_content = array(
                            'input_type'       => 'text',
                            'template'         => 'email_address',
                            'required'         => ! empty( $field['required'] ) ? 'yes' : 'no',
                            'label'            => $field['label'],
                            'name'             => 'wpforms_field_' . $field['id'],
                            'is_meta'          => 'yes',
                            'help'             => $field['description'],
                            'css'              => $field['css'],
                            'placeholder'      => $field['placeholder'],
                            'default'          => ! empty( $field['default_value'] ) ? $field['default_value'] : '',
                            'size'             => 40,
                            'word_restriction' => '',
                            'wpuf_cond'        => $conditional_config
                        );
                        break;

                    case 'textarea':
                        $field_content = array(
                            'input_type'       => 'textarea',
                            'template'         => 'textarea_field',
                            'required'         => ! empty( $field['required'] ) ? 'yes' : 'no',
                            'label'            => $field['label'],
                            'name'             => 'wpforms_field_' . $field['id'],
                            'is_meta'          => 'yes',
                            'help'             => $field['description'],
                            'css'              => $field['css'],
                            'placeholder'      => $field['placeholder'],
                            'default'          => ! empty( $field['default_value'] ) ? $field['default_value'] : '',
                            'rows'             => 5,
                            'cols'             => 25,
                            'rich'             => 'no',
                            'word_restriction' => '',
                            'wpuf_cond'        => $conditional_config
                        );
                        break;

                    case 'select':
                        $field_content = array(
                            'input_type'       => 'select',
                            'template'         => 'dropdown_field',
                            'required'         => ! empty( $field['required'] ) ? 'yes' : 'no',
                            'label'            => $field['label'],
                            'name'             => 'wpforms_field_' . $field['id'],
                            'is_meta'          => 'yes',
                            'help'             => $field['description'],
                            'css'              => $field['css'],
                            'selected'         => ! empty( $field['default_value'] ) ? $field['default_value'] : '',
                            'inline'           => 'no',
                            'options'          => $this->get_options( $field ),
                            'wpuf_cond'        => $conditional_config
                        );
                        break;

                    case 'radio':
                        $field_content = array(
                            'input_type'       => 'radio',
                            'template'         => 'radio_field',
                            'required'         => ! empty( $field['required'] ) ? 'yes' : 'no',
                            'label'            => $field['label'],
                            'name'             => 'wpforms_field_' . $field['id'],
                            'is_meta'          => 'yes',
                            'help'             => $field['description'],
                            'css'              => $field['css'],
                            'selected'         => ! empty( $field['default_value'] ) ? $field['default_value'] : '',
                            'inline'           => 'no',
                            'options'          => $this->get_options( $field ),
                            'wpuf_cond'        => $conditional_config
                        );
                        break;

                    case 'checkbox':
                        $field_content = array(
                            'input_type'       => 'checkbox',
                            'template'         => 'checkbox_field',
                            'required'         => ! empty( $field['required'] ) ? 'yes' : 'no',
                            'label'            => $field['label'],
                            'name'             => 'wpforms_field_' . $field['id'],
                            'is_meta'          => 'yes',
                            'help'             => $field['description'],
                            'css'              => $field['css'],
                            'selected'         => ! empty( $field['default_value'] ) ? $field['default_value'] : '',
                            'inline'           => 'no',
                            'options'          => $this->get_options( $field ),
                            'wpuf_cond'        => $conditional_config
                        );
                        break;

                    case 'number':
                        $field_content = array(
                            'input_type'       => 'numeric_text',
                            'template'         => 'numeric_text_field',
                            'required'         => ! empty( $field['required'] ) ? 'yes' : 'no',
                            'label'            => $field['label'],
                            'name'             => 'wpforms_field_' . $field['id'],
                            'is_meta'          => 'yes',
                            'help'             => $field['description'],
                            'css'              => $field['css'],
                            'placeholder'      => $field['placeholder'],
                            'default'          => ! empty( $field['default_value'] ) ? $field['default_value'] : '',
                            'size'             => 40,
                            'step_text_field'  => 0,
                            'min_value_field'  => 0,
                            'max_value_field'  => 0,
                            'wpuf_cond'        => $conditional_config
                        );
                        break;

                    case 'name':
                        $field_content = array(
                            'input_type'       => 'name',
                            'template'         => 'name_field',
                            'required'         => ! empty( $field['required'] ) ? 'yes' : 'no',
                            'label'            => $field['label'],
                            'name'             => 'wpforms_field_' . $field['id'],
                            'is_meta'          => 'yes',
                            'help'             => $field['description'],
                            'css'              => $field['css'],
                            'format'           => ( $field['format'] === 'first-last' ) ? 'first-last' : 'first-middle-last',
                            'first_name'       => array(
                                'placeholder'      => $field['first_placeholder'],
                                'default'          => $field['first_default'],
                                'sub'              => __( 'First', 'weforms' ),
                            ),
                            'middle_name'      => array(
                                'placeholder'      => $field['middle_placeholder'],
                                'default'          => $field['middle_default'],
                                'sub'              => __( 'Middle', 'weforms' ),
                            ),
                            'last_name'        => array(
                                'placeholder'      => $field['last_placeholder'],
                                'default'          => $field['last_default'],
                                'sub'              => __( 'Last', 'weforms' ),
                            ),
                            'hide_subs'        => ! empty( $field['sublabel_hide'] ) ? true : false,
                            'wpuf_cond'        => $conditional_config
                        );
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

                update_post_meta( $form_id, 'wpuf_form_settings', $form_settings );
                update_post_meta( $form_id, 'notifications', $form_notifications );

                $imported++;

                $refs[ $form['id'] ] = array(
                    'wpforms_id'     =>  $form['id'] ,
                    'weforms_id'      => $form_id,
                    'title'           => '[WPForms] ' . $settings['form_title']
                );
            }
        }

        $this->dismiss_prompt();
        update_option( 'weforms_wpforms_imported_forms', $refs );

        wp_send_json_success( array(
            'title'   => sprintf( _n( '%s form imported', '%s forms imported', $imported ), $imported ),
            'message' => __( 'We have successfully imported these forms into weForms. You could check and edit in-case anything weird happended.', 'weforms' ),
            'action'  => __( 'Do you want to <strong>replace</strong> WPForms shortcodes with weForms?', 'weforms' ),
            'refs'    => $refs
        ) );
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
