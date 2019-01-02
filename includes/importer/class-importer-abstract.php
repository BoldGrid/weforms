<?php

/**
 * Importer Abstract Class
 */
abstract class WeForms_Importer_Abstract {

    /**
     * The importer ID
     *
     * @var string
     */
    public $id = '';

    /**
     * The importer title
     *
     * @var string
     */
    public $title = '';

    /**
     * The shortcode (without bracket)
     *
     * @var string
     */
    public $shortcode = '';

    /**
     * The ajax action string
     *
     * @var string
     */
    protected $action = '';

    /**
     * Conditional stub
     *
     * @var array
     */
    public $conditionals = array(
        'condition_status' => 'no',
        'cond_field'       => array(),
        'cond_operator'    => array( '=' ),
        'cond_option'      => array( '- select -' ),
        'cond_logic'       => 'all'
    );

    public function __construct() {
        add_action( 'admin_notices', array( $this, 'maybe_show_notice' ) );

        add_action( 'wp_ajax_weforms_import_xdismiss_' . $this->id, array( $this, 'dismiss_notice' ) );
        add_action( 'wp_ajax_weforms_import_xforms_' . $this->id, array( $this, 'import_forms' ) );
        add_action( 'wp_ajax_weforms_import_xreplace_' . $this->id, array( $this, 'replace_action' ) );
    }

    /**
     * Get the importer name
     *
     * @return string
     */
    public function get_importer_name() {
        return $this->title;
    }

    /**
     * Get the shortcode ready for regular expression
     *
     * @return string
     */
    public function get_shortcode( $regex = false ) {

        if ( $regex ) {
            return preg_quote( $this->shortcode );
        }

        return $this->shortcode;
    }

    /**
     * Get all the forms
     *
     * @return array
     */
    abstract protected function get_forms();

    /**
     * Get the form name
     *
     * @param  mixed $form
     *
     * @return string
     */
    abstract protected function get_form_name( $form );

    /**
     * Get the form id
     *
     * @param  mixed $form
     *
     * @return int
     */
    abstract protected function get_form_id( $form );

    /**
     * Get all form fields of a form
     *
     * @param  mixed $form
     *
     * @return array
     */
    abstract protected function get_form_fields( $form );

    /**
     * Get form settings of a form
     *
     * @param  mixed $form
     *
     * @return array
     */
    abstract protected function get_form_settings( $form );

    /**
     * Get form notifications of a form
     *
     * @param  mixed $form
     *
     * @return array
     */
    abstract protected function get_form_notifications( $form );

    /**
     * Check if the plugin exists
     *
     * @return boolean
     */
    abstract protected function plugin_exists();

    /**
     * Show notice if the plugin found
     *
     * @return void
     */
    public function maybe_show_notice() {
        if ( ! $this->plugin_exists() ) {
            return;
        }

        if ( $this->is_dimissed() || !current_user_can( 'manage_options' ) ) {
            return;
        }

        ?>
        <div class="notice notice-info">
            <p><strong><?php printf( __( '%s Detected', 'weforms' ), $this->get_importer_name() ); ?></strong></p>
            <p><?php printf( __( 'Hey, looks like you have <strong>%s</strong> installed. Would you like to <strong>import</strong> the forms into weForms?', 'weforms' ), $this->get_importer_name() ); ?></p>

            <p>
                <a href="#" class="button button-primary weforms-import-<?php echo $this->id ;?>" id="weforms-import-<?php echo $this->id ;?>"><?php _e( 'Import Forms', 'weforms' ); ?></a>
                <a href="#" class="button weforms-import-<?php echo $this->id ;?>" id="weforms-dimiss-<?php echo $this->id ;?>"><?php _e( 'No Thanks', 'weforms' ); ?></a>
            </p>
        </div>

        <script type="text/javascript">
            jQuery(function($) {
                $('.notice').on('click', 'a#weforms-import-<?php echo $this->id ;?>', function(e) {
                    e.preventDefault();

                    var self = $(this);
                    self.addClass('updating-message');

                    wp.ajax.send( 'weforms_import_xforms_<?php echo $this->id ;?>', {
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
                            html += '<p>' + '<a href="#" class="button button-primary weforms-<?php echo $this->id ;?>-replace-action" data-type="replace"><?php _e( 'Replace Shortcodes', 'weforms' ); ?></a>&nbsp;' +
                                    '<a href="#" class="button weforms-<?php echo $this->id ;?>-replace-action" data-type="skip"><?php _e( 'No Thanks', 'weforms' ); ?></a></p>';

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

                $('.notice').on('click', '#weforms-dimiss-<?php echo $this->id ;?>', function(e) {
                    e.preventDefault();

                    $(this).closest('.notice').remove();
                    wp.ajax.send('weforms_import_xdismiss_<?php echo $this->id ;?>');
                });

                $('.notice').on('click', 'a.weforms-<?php echo $this->id ;?>-replace-action', function(e) {
                    e.preventDefault();

                    var self = $(this);
                    var notice = self.closest('.notice');

                    self.addClass('updating-message');

                    wp.ajax.send( 'weforms_import_xreplace_<?php echo $this->id ;?>', {
                        data: {
                            type: self.data('type'),
                            _wpnonce: '<?php echo wp_create_nonce( 'weforms' ); ?>'
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
     * Import forms
     *
     * @return void
     */
    public function import_forms() {
        check_ajax_referer( 'weforms' );

        $this->check_caps();

        // check if plugin installed
        if ( ! $this->plugin_exists() ) {
            wp_send_json_error( array(
                'title'   => __( 'Uh oh!', 'weforms' ),
                'message' => sprintf( __( '%s is not installed.', 'weforms' ), $this->get_importer_name() )
            ) );
        }

        $imported = 0;
        $refs     = array();
        $forms    = $this->get_forms();

        if ( ! $forms ) {
            wp_send_json_error( array(
                'title'   => __( 'Uh oh!', 'weforms' ),
                'message' => __( 'No forms found!', 'weforms' )
            ) );

            $this->dismiss_prompt();
        }

        if ( $forms ) {
            foreach ($forms as $form) {
                $form_name     = $this->get_form_name( $form );
                $form_fields   = $this->get_form_fields( $form );
                $settings      = $this->get_form_settings( $form );
                $notifications = $this->get_form_notifications( $form );

                if ( $form_fields ) {
                    $form_id = $this->insert_form( $form_name );

                    if ( is_wp_error( $form_id ) ) {
                        continue;
                    }

                    foreach ($form_fields as $menu_order => $form_field) {
                        $this->insert_form_field( $form_field, $form_id, $menu_order );
                    }

                    $this->update_settings( $form_id, $settings );
                    $this->update_notification( $form_id, $notifications );

                    $imported++;

                    $refs[ $this->get_form_id( $form ) ] = array(
                        'imported_id' => $this->get_form_id( $form ),
                        'weforms_id'  => $form_id,
                        'title'       => $form_name
                    );
                }
            }
        }

        $this->dismiss_prompt();
        $this->update_refs( $refs );

        wp_send_json_success( array(
            'title'   => sprintf( _n( '%s form imported', '%s forms imported', $imported, 'weforms' ), $imported ),
            'message' => __( 'We have successfully imported these forms into weForms. You could check and edit in-case anything weird happended.', 'weforms' ),
            'action'  => sprintf( __( 'Do you want to <strong>replace</strong> %s shortcodes with weForms?', 'weforms' ), $this->get_importer_name() ),
            'refs'    => $refs
        ) );
    }

    /**
     * Replace contact form 7 shortcodes
     *
     * @return void
     */
    public function replace_action() {
        check_ajax_referer( 'weforms' );

        $this->check_caps();

        if ( ! in_array( $_POST['type'], array( 'skip', 'replace' ) ) ) {
            wp_send_json_error( __( 'Not a valid action type.', 'weforms' ) );
        }

        if ( 'skip' == $_POST['type'] ) {
            wp_send_json_success();
        }

        $pages_query = new WP_Query( array(
            'post_type'      => 'page',
            'posts_per_page' => -1,
            's'              => '[' . $this->get_shortcode()
        ) );

        if ( ! $pages_query->found_posts ) {
            wp_send_json_error( __( 'No pages found with shortcode. Skipped!', 'weforms' ) );
        }

        $count = 0;
        $refs  = $this->get_refs();
        $pages = $pages_query->get_posts();

        foreach ($pages as $page) {
            preg_match_all( '/\[(\[?)(' . $this->get_shortcode( true ) . '|)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/s', $page->post_content, $matches, PREG_SET_ORDER );

            if ( empty( $matches ) ) {
                continue;
            }

            foreach ( $matches as $shortcode ) {
                $atts = shortcode_parse_atts( $shortcode[0] );

                if ( isset( $atts['id'] ) && array_key_exists( $atts['id'], $refs ) ) {
                    $replace = sprintf( '[weforms id="%d"]', $refs[ $atts['id'] ]['weforms_id'] );

                    $post_content = str_replace( $shortcode[0], $replace, $page->post_content );

                    wp_update_post( array(
                        'ID'           => $page->ID,
                        'post_content' => $post_content
                    ) );

                    $count++;
                }
            }
        }

        wp_send_json_success( sprintf( _n( 'Replaced %d form', 'Replaced %d forms', $count, 'weforms' ), $count ) );
    }

    /**
     * Check capability if able to process
     *
     * @return void
     */
    public function check_caps() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You are not allowed.', 'weforms' ) );
        }
    }

    /**
     * If the prompt is dismissed
     *
     * @return boolean
     */
    public function is_dimissed() {
        return 'yes' == get_option( 'weforms_dismiss_xnotice_' . $this->id );
    }

    /**
     * Dismiss the prompt
     *
     * @return void
     */
    public function dismiss_prompt() {
        update_option( 'weforms_dismiss_xnotice_' . $this->id, 'yes' );
    }

    /**
     * Dismiss the notice
     *
     * @return void
     */
    public function dismiss_notice() {
        $this->dismiss_prompt();

        wp_send_json_success();
    }

    /**
     * Get the imported form refs
     *
     * @return array
     */
    public function get_refs() {
        return get_option( 'weforms_imported_xforms_' . $this->id, array() );
    }

    /**
     * Update the imported reference forms
     *
     * @param  array $refs
     *
     * @return void
     */
    public function update_refs( $refs ) {
        update_option( 'weforms_imported_xforms_' . $this->id, $refs );
    }

    /**
     * Get form field
     *
     * @param  string $type
     * @param  array  $args [description]
     *
     * @return array
     */
    public function get_form_field( $type, $args = array() ) {
        $defaults = array(
             'required'           => 'no',
             'label'              => '',
             'name'               => '',
             'help'               => '',
             'css_class'          => '',
             'placeholder'        => '',
             'value'              => '',
             'default'            => '',
             'options'            => array(),
             'step'               => '',
             'min'                => '',
             'max'                => '',
             'extension'          => '',
             'max_size'           => '', // file size
             'size'               => '', // file size
             'first_placeholder'  => '',
             'first_default'      => '',
             'middle_placeholder' => '',
             'middle_default'     => '',
             'last_placeholder'   => '',
             'last_default'       => '',
             'first_name'         => '',
             'middle_name'        => '',
             'last_name'          => '',
        );

        $args = wp_parse_args( $args, $defaults );

        switch ( $type ) {
            case 'text':
                $field_content = array(
                    'input_type'       => 'text',
                    'template'         => 'text_field',
                    'required'         => $args['required'],
                    'label'            => $args['label'],
                    'name'             => $args['name'],
                    'is_meta'          => 'yes',
                    'help'             => $args['help'],
                    'css'              => $args['css_class'],
                    'placeholder'      => $args['placeholder'],
                    'default'          => $args['default'],
                    'size'             => $args['size'],
                    'word_restriction' => '',
                    'wpuf_cond'        => $this->conditionals
                );
                break;

            case 'email':
                $field_content = array(
                    'input_type'       => 'email',
                    'template'         => 'email_address',
                    'required'         => $args['required'],
                    'label'            => $args['label'],
                    'name'             => $args['name'],
                    'is_meta'          => 'yes',
                    'help'             => $args['help'],
                    'css'              => $args['css_class'],
                    'placeholder'      => $args['placeholder'],
                    'default'          => $args['default'],
                    'size'             => $args['size'],
                    'word_restriction' => '',
                    'wpuf_cond'        => $this->conditionals
                );
                break;

            case 'textarea':
                $field_content = array(
                    'input_type'       => 'textarea',
                    'template'         => 'textarea_field',
                    'required'         => $args['required'],
                    'label'            => $args['label'],
                    'name'             => $args['name'],
                    'is_meta'          => 'yes',
                    'help'             => $args['help'],
                    'css'              => $args['css_class'],
                    'rows'             => 5,
                    'cols'             => 25,
                    'placeholder'      => $args['placeholder'],
                    'default'          => $args['default'],
                    'rich'             => 'no',
                    'word_restriction' => '',
                    'wpuf_cond'        => $this->conditionals
                );
                break;

            case 'select':
                $field_content = array(
                    'input_type' => 'select',
                    'template'   => 'dropdown_field',
                    'required'   => $args['required'],
                    'label'      => $args['label'],
                    'name'       => $args['name'],
                    'is_meta'    => 'yes',
                    'help'       => '',
                    'css'        => $args['css_class'],
                    'selected'   => '',
                    'inline'     => 'no',
                    'options'    => $args['options'],
                    'wpuf_cond'  => $this->conditionals
                );
                break;

            case 'multiselect':
                $field_content = array(
                    'input_type' => 'multiselect',
                    'template'   => 'multiple_select',
                    'required'   => $args['required'],
                    'label'      => $args['label'],
                    'name'       => $args['name'],
                    'is_meta'    => 'yes',
                    'help'       => '',
                    'css'        => $args['css_class'],
                    'selected'   => '',
                    'first'      => __( '- select -', 'weforms' ),
                    'options'    => $args['options'],
                    'wpuf_cond'  => $this->conditionals
                );
                break;

            case 'date':
                $field_content = array(
                    'input_type'      => 'date',
                    'template'        => 'date_field',
                    'required'         => $args['required'],
                    'label'            => $args['label'],
                    'name'             => $args['name'],
                    'is_meta'         => 'yes',
                    'help'            => '',
                    'css'             => $args['css_class'],
                    'format'          => 'dd/mm/yy',
                    'time'            => '',
                    'is_publish_time' => '',
                    'wpuf_cond'       => $this->conditionals
                );
                break;

            case 'range':
            case 'number':

                $field_content = array(
                    'input_type'      => 'numeric_text',
                    'template'        => 'numeric_text_field',
                    'required'        => $args['required'],
                    'label'           => $args['label'],
                    'name'            => $args['name'],
                    'is_meta'         => 'yes',
                    'help'            => '',
                    'css'             => $args['css_class'],
                    'placeholder'     => $args['placeholder'],
                    'default'         => $args['value'],
                    'size'            => 40,
                    'step_text_field' => $args['step'],
                    'min_value_field' => $args['min'],
                    'max_value_field' => $args['max'],
                    'wpuf_cond'       => $this->conditionals
                );

                break;

            case 'url':
                $field_content = array(
                    'input_type'       => 'url',
                    'template'         => 'website_url',
                    'required'         => $args['required'],
                    'label'            => $args['label'],
                    'name'             => $args['name'],
                    'is_meta'          => 'yes',
                    'help'             => '',
                    'css'              => $args['css_class'],
                    'placeholder'      => '',
                    'default'          => '',
                    'size'             => 40,
                    'word_restriction' => '',
                    'wpuf_cond'        => $this->conditionals
                );

                break;

            case 'checkbox':
                $field_content = array(
                    'input_type' => 'checkbox',
                    'template'   => 'checkbox_field',
                    'required'   => $args['required'],
                    'label'      => $args['label'],
                    'name'       => $args['name'],
                    'is_meta'    => 'yes',
                    'help'       => '',
                    'css'        => $args['css_class'],
                    'selected'   => '',
                    'inline'     => 'no',
                    'options'    => $args['options'],
                    'wpuf_cond'  => $this->conditionals
                );
                break;

            case 'radio':
                $field_content = array(
                    'input_type' => 'radio',
                    'template'   => 'radio_field',
                    'required'   => $args['required'],
                    'label'      => $args['label'],
                    'name'       => $args['name'],
                    'is_meta'    => 'yes',
                    'help'       => '',
                    'css'        => $args['css_class'],
                    'selected'   => '',
                    'inline'     => 'no',
                    'options'    => $args['options'],
                    'wpuf_cond'  => $this->conditionals
                );
                break;

            case 'hidden':
                $field_content = array(
                    'input_type'       => 'hidden',
                    'template'         => 'custom_hidden_field',
                    'label'            => $args['label'],
                    'name'             => $args['name'],
                    'is_meta'          => 'yes',
                    'wpuf_cond'        => $this->conditionals
                );
                break;

            case 'section_break':
                $field_content = array(
                    'input_type'       => 'section_break',
                    'template'         => 'section_break',
                    'label'            => $args['label'],
                    'name'             => $args['name'],
                    'wpuf_cond'        => $this->conditionals
                );
                break;

            case 'html':
                $field_content = array(
                    'input_type'       => 'html',
                    'template'         => 'custom_html',
                    'label'            => $args['label'],
                    'name'             => $args['name'],
                    'html'             => $args['default'],
                    'wpuf_cond'        => $this->conditionals
                );
                break;

            case 'toc':
                $field_content = array(
                    'input_type'       => 'toc',
                    'template'         => 'toc',
                    'required'         => $args['required'],
                    'name'             => $args['name'],
                    'description'      => $args['label'],
                    'is_meta'          => 'yes',
                    'show_checkbox'    => true,
                    'wpuf_cond'        => $this->conditionals
                );
                break;

            case 'recaptcha':
                $field_content = array(
                    'input_type'       => 'recaptcha',
                    'template'         => 'recaptcha',
                    'required'         => $args['required'],
                    'label'            => $args['label'],
                    'name'             => $args['name'],
                    'recaptcha_type'   => 'enable_no_captcha',
                    'is_meta'          => 'yes',
                    'help'             => '',
                    'css'             => $args['css_class'],
                    'placeholder'      => '',
                    'default'          => '',
                    'size'             => 40,
                    'word_restriction' => '',
                    'wpuf_cond'        => $this->conditionals
                );
                break;

            case 'file':
                $field_content = array(
                    'input_type' => 'file_upload',
                    'template'   => 'file_upload',
                    'required'   => $args['required'],
                    'label'      => $args['label'],
                    'name'       => $args['name'],
                    'is_meta'    => 'yes',
                    'help'       => $args['help'],
                    'css'        => $args['css_class'],
                    'max_size'   => $args['max_size'],
                    'count'      => '1',
                    'extension'  => $args['extension'],
                    'wpuf_cond'  => $this->conditionals
                );
                break;

            case 'name':
                $field_content = array(
                    'input_type'  => 'name',
                    'template'    => 'name_field',
                    'required'    => $args['required'],
                    'label'       => $args['label'],
                    'name'        => $args['name'],
                    'is_meta'     => 'yes',
                    'format'      => $args['format'],
                    'first_name'  => $args['first_name'],
                    'middle_name' => $args['middle_name'],
                    'last_name'   => $args['last_name'],
                    'hide_subs'   => false,
                    'help'        => $args['help'],
                    'css'         => $args['css_class'],
                    'wpuf_cond'   => $this->conditionals,
                );
                break;

            case 'ratings':
                $field_content = array(
                    'input_type' => 'ratings',
                    'template'   => 'ratings',
                    'required'   => $args['required'],
                    'label'      => $args['label'],
                    'name'       => $args['name'],
                    'is_meta'    => 'yes',
                    'help'       => '',
                    'css'        => $args['css_class'],
                    'options'    => $args['options'],
                    'wpuf_cond'  => $this->conditionals
                );
                break;

            case 'linear_scale':
                $field_content = array(
                    'input_type' => 'linear_scale',
                    'template'   => 'linear_scale',
                    'required'   => $args['required'],
                    'label'      => $args['label'],
                    'name'       => $args['name'],
                    'is_meta'    => 'yes',
                    'help'       => '',
                    'css'        => $args['css_class'],
                    'options'    => $args['options'],
                    'wpuf_cond'  => $this->conditionals
                );
                break;

            case 'checkbox_grid':
                $field_content = array(
                    'input_type' => 'checkbox_grid',
                    'template'   => 'checkbox_grid',
                    'required'   => $args['required'],
                    'label'      => $args['label'],
                    'name'       => $args['name'],
                    'is_meta'    => 'yes',
                    'help'       => '',
                    'css'        => $args['css_class'],
                    'options'    => $args['options'],
                    'wpuf_cond'  => $this->conditionals
                );
                break;

            case 'multiple_choice_grid':
                $field_content = array(
                    'input_type' => 'multiple_choice_grid',
                    'template'   => 'multiple_choice_grid',
                    'required'   => $args['required'],
                    'label'      => $args['label'],
                    'name'       => $args['name'],
                    'is_meta'    => 'yes',
                    'help'       => '',
                    'css'        => $args['css_class'],
                    'options'    => $args['options'],
                    'wpuf_cond'  => $this->conditionals
                );
                break;

            case 'single_product':
                $field_content = array(
                    'input_type' => 'single_product',
                    'template'   => 'single_product',
                    'required'   => $args['required'],
                    'label'      => $args['label'],
                    'name'       => $args['name'],
                    'is_meta'    => 'yes',
                    'help'       => '',
                    'css'        => $args['css_class'],
                    'options'    => $args['options'],
                    'wpuf_cond'  => $this->conditionals
                );
                break;

            case 'multiple_product':
                $field_content = array(
                    'input_type' => 'multiple_product',
                    'template'   => 'multiple_product',
                    'required'   => $args['required'],
                    'label'      => $args['label'],
                    'name'       => $args['name'],
                    'is_meta'    => 'yes',
                    'help'       => '',
                    'css'        => $args['css_class'],
                    'options'    => $args['options'],
                    'wpuf_cond'  => $this->conditionals
                );
                break;

            case 'payment_method':
                $field_content = array(
                    'input_type' => 'payment_method',
                    'template'   => 'payment_method',
                    'required'   => $args['required'],
                    'label'      => $args['label'],
                    'name'       => $args['name'],
                    'is_meta'    => 'yes',
                    'help'       => '',
                    'css'        => $args['css_class'],
                    'options'    => $args['options'],
                    'wpuf_cond'  => $this->conditionals
                );
                break;

            case 'total':
                $field_content = array(
                    'input_type' => 'total',
                    'template'   => 'total',
                    'required'   => $args['required'],
                    'label'      => $args['label'],
                    'name'       => $args['name'],
                    'is_meta'    => 'yes',
                    'help'       => '',
                    'css'        => $args['css_class'],
                    'options'    => $args['options'],
                    'wpuf_cond'  => $this->conditionals
                );
                break;
        }

        return $field_content;
    }

    /**
     * Default form settings
     *
     * @return array
     */
    public function get_default_form_settings() {
        $form_settings = array(
            'redirect_to'        => 'same',
            'message'            => __( 'Thanks for contacting us! We will get in touch with you shortly.', 'weforms' ),
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

        return $form_settings;
    }

    /**
     * Insert a form
     *
     * @param  string $form_name
     *
     * @return ID|WP_Error
     */
    public function insert_form( $form_name ) {
        $weforms_form = array(
            'post_title'  => sprintf( '[%s] %s', strtoupper( $this->id ), $form_name ),
            'post_type'   => 'wpuf_contact_form',
            'post_status' => 'publish',
            'post_author' => get_current_user_id()
        );

        return wp_insert_post( $weforms_form );
    }

    /**
     * Insert a form field
     *
     * @param  array $field
     * @param  int $form_id
     * @param  int $menu_order
     *
     * @return int|WP_Error
     */
    public function insert_form_field( $field, $form_id, $menu_order ) {
        $form_field = array(
            'post_type'    => 'wpuf_input',
            'post_status'  => 'publish',
            'post_content' => maybe_serialize( $field ),
            'post_parent'  => $form_id,
            'menu_order'   => $menu_order
        );

        return wp_insert_post( $form_field );
    }

    /**
     * Update form settings
     *
     * @param  int $form_id
     * @param  array $settings
     *
     * @return void
     */
    public function update_settings( $form_id, $settings ) {
        update_post_meta( $form_id, 'wpuf_form_settings', $settings );
    }

    /**
     * Update Notification
     *
     * @param  int $form_id
     * @param  array $notification
     *
     * @return void
     */
    public function update_notification( $form_id, $notifications ) {
        update_post_meta( $form_id, 'notifications', $notifications );
    }
}
