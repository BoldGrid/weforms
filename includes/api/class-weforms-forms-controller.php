<?php

class Weforms_Forms_Controller extends Weforms_REST_Controller {

    /**
     * Endpoint namespace
     *
     * @var string
     */
    protected $namespace = 'weforms/v1';

    /**
     * Route name
     *
     * @var string
     */
    protected $rest_base = 'forms';

    /**
     * Register all routes releated with forms
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_items' ],
                    'args'                => $this->get_collection_params(),
                    'permission_callback' => [ $this, 'get_items_permissions_check' ],
                ],
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [ $this, 'create_item' ],
                    'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
                    'permission_callback' => [ $this, 'create_item_permissions_check' ],
                ],
                [
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => [ $this, 'bulk_delete_form' ],
                    'permission_callback' => [ $this, 'delete_item_permissions_check' ],
                    'args'                => [
                        'ids' => [
                            'description' => __( 'Unique identifier for the object', 'weforms' ),
                            'type'        => 'array',
                            'items'       => [
                                'type' => 'integer',
                            ],
                            'validate_callback' => [ $this, 'is_bulk_delete_form_exists' ],
                            'required'          => true,
                        ],
                    ],
                ],
            ]
          );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>\d+)', [
            'args' => [
                'form_id' => [
                    'description'         => __( 'Unique identifier for the object', 'weforms' ),
                    'type'                => 'integer',
                    'sanitize_callback'   => 'absint',
                    'validate_callback'   => [ $this, 'is_form_exists' ],
                    'required'            => true,
                ],
            ],
            [
                'methods'  => WP_REST_Server::READABLE,
                'callback' => [ $this, 'get_item' ],
                'args'     => [
                    'context' => $this->get_context_param( [ 'default' => 'view' ] ),
                ],
                'permission_callback' => [ $this, 'get_item_permissions_check' ],
            ],
            [
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => [ $this, 'delete_item' ],
                'args'                => [
                    'force' => [
                        'type'        => 'boolean',
                        'default'     => false,
                        'description' => __( 'Whether to bypass trash and force deletion.', 'weforms' ),
                    ],
                ],
                'permission_callback' => [ $this, 'delete_item_permissions_check' ],
            ],
            [
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => [ $this, 'update_item' ],
                'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
                'permission_callback' => [ $this, 'update_item_permissions_check' ],
            ],
        ] );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)' . '/entries/',
            [
                'args' => [
                    'form_id' => [
                        'description'       => __( 'Unique identifier for the object', 'weforms' ),
                        'type'              => 'integer',
                        'sanitize_callback' => 'absint',
                        'validate_callback' => [ $this, 'is_form_exists' ],
                        'required'          => true,
                    ],
                ],

                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [ $this, 'save_entry' ],
                    'permission_callback' => [ $this, 'submit_permissions_check' ],
                ],
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_entry' ],
                    'permission_callback' => [ $this, 'get_item_permissions_check' ],
                    'args'                => $this->get_collection_params(),
                ],
            ]
          );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)' . '/duplicate/', [
            'args' => [
                'form_id' => [
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'key'               => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => [ $this, 'is_form_exists' ],
                    'required'          => true,
                ],
            ],
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'duplicate_form' ],
                'permission_callback' => [ $this, 'create_item_permissions_check' ],
            ],
        ] );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)' . '/export_entries/', [
            'args' => [
                'form_id' => [
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => [ $this, 'is_form_exists' ],
                    'required'          => true,
                ],
            ],

            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [ $this, 'export_form_entries' ],
                'permission_callback' => [ $this, 'get_items_permissions_check' ],
            ],
        ] );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)' . '/entries/(?P<entry_id>[\d]+)',
            [
                'args' => [
                    'form_id' => [
                        'description'       => __( 'Unique identifier for the object', 'weforms' ),
                        'type'              => 'integer',
                        'sanitize_callback' => 'absint',
                        'validate_callback' => [ $this, 'is_form_exists' ],
                        'required'          => true,
                    ],
                    'entry_id' => [
                        'description'       => __( 'Unique identifier for the object', 'weforms' ),
                        'type'              => 'integer',
                        'sanitize_callback' => 'absint',
                        'validate_callback' => [ $this, 'is_entry_exists' ],
                        'required'          => true,
                    ],
                    'context' => $this->get_context_param( [ 'default' => 'view' ] ),
                ],
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_entry_details' ],
                    'permission_callback' => [ $this, 'get_item_permissions_check' ],
                ],
            ]
          );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/import', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'import_forms' ],
                'permission_callback' => [ $this, 'create_item_permissions_check' ],
            ],
        ] );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/export', [
            'args' => [
                'ids' => [
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'array',
                    'validate_callback' => [ $this, 'is_bulk_delete_form_exists' ],
                    'required'          => false,
                ],
            ],
             [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [ $this, 'export_forms' ],
                'permission_callback' => [ $this, 'get_item_permissions_check' ],
            ],
        ] );
    }

    /**
     * Check Template Exist
     *
     * @param string          $param
     * @param WP_REST_Request $request
     * @param string          $key
     *
     * @return bool
     **/
    public function is_template_exists( $param, $request, $key ) {
        $templates = weforms()->templates->get_templates();

        return (bool) array_key_exists( $param, $templates );
    }

    public function is_field_exists( $param, $request, $key ) {
        $fields = $request['field_id'];
        $fields = array_filter( $fields, 'is_numeric' );

        if ( empty( $fields ) ) {
            return false;
        }

        return true;
    }

    public function save_check( $request ) {
        $form          = weforms()->form->get( (int) $request['form_id'] );
        $form_error    = new WP_Error();
        $form_settings = $form->get_settings();
        $form_fields   = $form->get_fields();
        $form_entries  = weforms_get_form_entries( $form->id, [ 'number'  => '', 'offset'  => '' ] );

        if ( !$form_fields ) {
            $form_error->add( 'rest_weforms_form_fields', __( 'No form fields found!', 'weforms' ), [ 'status' => 404 ] );
        } else {
            if ( $form->has_field( 'recaptcha' ) ) {
                $validate_Captcha =  $this->validate_reCaptcha( $request );

                if ( $validate_Captcha == false ) {
                    $form_error->add( 'rest_weforms_form', __( 'reCAPTCHA validation failed', 'weforms' ), [ 'status' => 404 ] );
                }
            }

            $entry_fields = [];

            foreach ( $form_fields as $key => $field ) {
                if ( $field['wpuf_cond']['condition_status'] == 'yes' ) {
                    $logic          = [];
                    $cond_fields    = $field['wpuf_cond']['cond_field'];
                    $cond_operators = $field['wpuf_cond']['cond_operator'];
                    $cond_options   = $field['wpuf_cond']['cond_option'];

                    $required = false;

                    foreach ( $cond_fields as $cond_key => $value ) {
                        $operator       = $cond_operators[$cond_key];
                        $selected_value = $request[$value];
                        $logic_value    = $cond_options[$cond_key];

                        if ( $field['wpuf_cond']['cond_logic'] == 'all' ) {
                            if ( ( $operator == '=' && $selected_value == $logic_value ) || ( $operator == '!=' && $selected_value != $logic_value ) ) {
                                $required = true;
                            } else {
                                $required = false;
                                break;
                            }
                        }

                        if ( $field['wpuf_cond']['cond_logic'] == 'any' ) {
                            if ( ( $operator == '=' && $selected_value == $logic_value ) || ( $operator == '!=' && $selected_value != $logic_value ) ) {
                                $required = true;
                                break;
                            } else {
                                $required = false;
                            }
                        }
                    }

                    if ( $required  ) {
                        if ( isset( $field['required'] ) && $field['required'] == 'yes' && empty( $request->get_param( $field['name'] ) ) ) {
                            $form_error->add( 'rest_weforms_form_required_field', __( $field['name'] . ' Required Field Missing', 'weforms' ), [ 'status' => 404 ] );
                        }
                    }
                } elseif ( ( isset( $field['required'] ) && $field['required'] == 'yes' && empty( $request->get_param( $field['name']  ) ) ) ) {
                    $form_error->add( 'rest_weforms_form_required_field', __( $field['name'] . ' Required Field Missing', 'weforms' ), [ 'status' => 404 ] );
                }

                $ignore_list  = apply_filters( 'wefroms_entry_ignore_list', [
                    'recaptcha', 'step_start', 'section_break',
                ] );

                if ( in_array( $field['template'], $ignore_list ) ) {
                    continue;
                }

                if ( !empty( $field['name'] ) ) {
                    $entry_fields[ $field['name'] ] = $request->get_param( $field['name'] );
                }

                if ( !array_key_exists( $field['template'], $form_fields ) ) {
                    continue;
                }
            }

            foreach ( $entry_fields as $field_key => $field_value ) {
                $duplicate_check = false;
                $field_label     = 'This';

                foreach ( $form_fields as $form_field ) {
                    if ( in_array( $form_field['template'], [ 'text_field', 'website_url', 'numeric_text_field', 'email_address' ] ) && $form_field['name'] == $field_key && isset( $form_field['duplicate'] ) && 'no' == $form_field['duplicate'] ) {
                        $duplicate_check = true;
                        $field_label     = $form_field['label'];
                    }
                }

                if ( $duplicate_check ) {
                    foreach ( $form_entries as $entry ) {
                        $existing = weforms_get_entry_meta( $entry->id, $field_key, true );

                        if ( $existing && $field_value == $existing ) {
                            $form_error->add( 'rest_weforms_form_unique_entry', __( 'field requires a unique entry and  has already been used.', 'weforms' ), [ 'status' => 404 ] );
                        }
                    }
                }
            }

            foreach ( $form_fields as $key => $field ) {
                //skip custom html field as it is not saved
                if ( 'custom_html' == $field['template'] ) {
                    continue;
                }

                //skip recaptcha field as it is not saved
                if ( 'recaptcha' == $field['template'] ) {
                    continue;
                }

                if ( !empty( $field['name'] ) ) {
                    $value = $request[ $field['name'] ];
                }

                if ( 'file_upload' === $field['template'] ) {
                    $file     = $request->get_param( 'wpuf_files' );
                    $file_ids = $file[ $field['name'] ];

                    foreach ( $file_ids as $key => $file_id ) {
                        if ( !wp_get_attachment_url( $file_id ) ) {
                            $form_error->add( 'rest_weforms_form_file', __( 'File Not Found', 'weforms' ), [ 'status' => 404 ] );
                        }
                    }
                }

                if ( 'image_upload' === $field['template'] ) {
                    $image     = $request->get_param( 'wpuf_files' );
                    $image_ids = $image[ $field['name'] ];

                    foreach ( $image_ids as $key => $image_id ) {
                        if ( !wp_get_attachment_url( $image_id ) ) {
                            $form_error->add( 'rest_weforms_form_image', __( 'Image Not Found', 'weforms' ), [ 'status' => 404 ] );
                        }
                    }
                }

                if ( 'date_field' === $field['template'] ) {
                    $date_format = $field['format'];

                    $possible_date= [
                          'dd-mm-yy' => 'd-m-y',
                          'yy-mm-dd' => 'y-m-d',
                          'mm-dd-yy' => 'm-d-y',
                          'dd/mm/yy' => 'd/m/y',
                          'yy/mm/dd' => 'y/m/d',
                          'mm/dd/yy' => 'm/d/y',
                          'dd.mm.yy' => 'd/m/y',
                          'yy.mm.dd' => 'y/m/d',
                          'mm.dd.yy' => 'm/d/y',
                    ];

                    foreach ( $possible_date as $key => $date ) {
                        if ( strcmp( $key, $date_format ) == 0 ) {
                            $date_format = $date;
                            break;
                        }
                    }

                    $d = DateTime::createFromFormat( $date_format, $value );

                    if ( !( $d && ( $d->format( $date_format ) == $value ) ) ) {
                        $form_error->add( 'rest_weforms_form_date_field', __( 'Date Field Is not Valid', 'weforms' ), [ 'status' => 404 ] );
                    }
                }

                if ( 'email_address' === $field['template'] ) {
                    if ( !$this->email_validation( $value ) ) {
                        $form_error->add( 'rest_weforms_form_email', __( 'Invalid Email Address', 'weforms' ), [ 'status' => 404 ] );
                    }
                }

                if ( 'website_url' === $field['template'] ) {
                    if ( !filter_var( $value, FILTER_VALIDATE_URL ) ) {
                        $form_error->add( 'rest_weforms_form_url', __( 'Invalid Url Address', 'weforms' ), [ 'status' => 404 ] );
                    }
                }

                if ( 'numeric_text_field' === $field['template'] ) {
                    if ( !is_numeric( $value ) ) {
                        $form_error->add( 'rest_weforms_form_number', __( 'Number Field Should be numberic value ', 'weforms' ), [ 'status' => 404 ] );
                    }
                }

                if ( 'single_product' === $field['template'] ) {
                    if ( !$value ) {
                        $value = [];
                    }

                    $value['price']    = isset( $value['price'] ) ? floatval( $value['price'] ) : 0;
                    $value['quantity'] = isset( $value['quantity'] ) ? floatval( $value['quantity'] ) : 0;
                    $quantity          = isset( $field['quantity'] ) ? $field['quantity'] : [];
                    $price             = isset( $field['price'] ) ? $field['price'] : [];

                    if ( isset( $price['is_flexible'] ) && $price['is_flexible'] ) {
                        $min = isset( $price['min'] ) ? floatval( $price['min'] ) : 0;
                        $max = isset( $price['max'] ) ? floatval( $price['max'] ) : 0;

                        if ( $value['price'] < $min ) {
                            return new WP_Error( 'rest_weforms_form_price', sprintf( __( '%s price must be equal or greater than %s',
                                    $field['weforms'],
                                    $min,
                                    'weforms'
                                  )
                              ), [ 'status' => 404 ] );
                        }

                        if ( $max && $value['price'] > $max ) {
                            $form_error->add( 'rest_weforms_form_price', sprintf( __( '%s price must be equal or less than %s',
                                    $field['weforms'],
                                    $max,
                                    'weforms'
                                  )
                              ), [ 'status' => 404 ] );
                        }
                    }

                    if ( isset( $quantity['status'] ) && $quantity['status'] ) {
                        $min = isset( $quantity['min'] ) ? floatval( $quantity['min'] ) : 0;
                        $max = isset( $quantity['max'] ) ? floatval( $quantity['max'] ) : 0;

                        if ( $value['quantity'] < $min ) {
                            $form_error->add( 'rest_weforms_form_quantity', sprintf( __(
                                    '%s quantity must be equal or greater than %s',
                                    $field['weforms'],
                                    $min,
                                    'weforms'
                                  )
                              ), [ 'status' => 404 ] );
                        }

                        if ( $max && $value['quantity'] > $max ) {
                            $form_error->add( 'rest_weforms_form_quantity', sprintf( __(
                                    '%s quantity must be equal or less than %s',
                                    $field['weforms'],
                                    $max,
                                    'weforms'
                                  )
                              ), [ 'status' => 404 ] );
                        }
                    }
                }
            }
        }

        if ( count( $form_error->get_error_messages() ) > 0 ) {
            return $form_error;
        } else {
            return true;
        }
    }

    /**
     * reCaptcha Validation
     *
     * @since 1.4.2
     *
     * @return void
     */
    public function validate_reCaptcha( $request ) {
        if ( class_exists( 'WPUF_ReCaptcha' ) ) {
            $recaptcha_class = 'WPUF_ReCaptcha';
        } else {
            if ( !function_exists( 'recaptcha_get_html' ) ) {
                require_once WEFORMS_INCLUDES . '/library/reCaptcha/recaptchalib.php';
            }

            require_once WEFORMS_INCLUDES . '/library/reCaptcha/recaptchalib_noCaptcha.php';

            $recaptcha_class = 'Weforms_ReCaptcha';
        }

        $invisible = isset( $request['g-recaptcha-response'] ) ? false : true;

        $recaptcha_settings = weforms_get_settings( 'recaptcha' );
        $secret             = isset( $recaptcha_settings->secret ) ? $recaptcha_settings->secret : '';

        if ( ! $invisible ) {
            $response           = null;
            $reCaptcha          = new $recaptcha_class( $secret );
            $remote_ADDR        = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
            $recaptcha_response = isset( $_SERVER['g-recaptcha-response'] ) ?  sanitize_text_field( wp_unslash( $request['g-recaptcha-response'] ) ) : '';

            $resp = $reCaptcha->verifyResponse(
                $remote_ADDR,
                $recaptcha_response
            );

            if ( !$resp->success ) {
                return false;
            }
        } else {
            $recap_challenge = isset( $request['recaptcha_challenge_field'] ) ? $request['recaptcha_challenge_field'] : '';
            $recap_response  = isset( $request['recaptcha_response_field'] ) ? $request['recaptcha_response_field'] : '';
            $resp            = recaptcha_check_answer( $secret, $remote_ADDR, $recap_challenge, $recap_response );

            if ( !$resp->is_valid ) {
                ob_clean();

                return false;
            }
        }

        return true;
    }

    public function weforms_api_insert_entry( $args, $fields = [] ) {
        global $wpdb;

        $browser = weforms_get_browser();

        $defaults = [
            'form_id'     => 0,
            'user_id'     => get_current_user_id(),
            'user_ip'     => ip2long( weforms_get_client_ip() ),
            'user_device' => $browser['name'] . '/' . $browser['platform'],
            'created_at'  => current_time( 'mysql' ),
        ];

        $r = wp_parse_args( $args, $defaults );

        if ( !$r['form_id'] ) {
            return new WP_Error( 'no-form-id', __( 'No form ID was found.', 'weforms' ) );
        }

        if ( !$fields ) {
            return new WP_Error( 'no-fields', __( 'No form fields were found.', 'weforms' ) );
        }

        $success = $wpdb->insert( $wpdb->weforms_entries, $r );

        if ( is_wp_error( $success ) || !$success ) {
            return new WP_Error( 'could-not-create', __( 'Could not create an entry', 'weforms' ), [ 'status' => 404 ] );
        }

        $entry_id = $wpdb->insert_id;

        foreach ( $fields as $key => $value ) {
            weforms_add_entry_meta( $entry_id, $key, $value );
        }

        return $entry_id;
    }

    /**
     * Save and existing form
     *
     * @since 1.4.2
     *
     * @param array $data Contains form_fields, form_settings, form_settings_key data
     *
     * @return bool
     */
    public function weforms_api_save( $data ) {
        $saved_wpuf_inputs = [];

        wp_update_post( [ 'ID' => $data['form_id'], 'post_status' => 'publish', 'post_title' => $data['post_title'] ] );

        $existing_wpuf_input_ids = get_children( [
            'post_parent' => $data['form_id'],
            'post_status' => 'publish',
            'post_type'   => 'wpuf_input',
            'numberposts' => '-1',
            'orderby'     => 'menu_order',
            'order'       => 'ASC',
            'fields'      => 'ids',
        ] );

        $new_wpuf_input_ids = [];

        if ( !empty( $data['form_fields'] ) ) {
            foreach ( $data['form_fields'] as $order => $field ) {
                if ( !empty( $field['is_new'] ) ) {
                    unset( $field['is_new'] );
                    unset( $field['id'] );

                    $field_id = 0;
                } else {
                    $field_id = $field['id'];
                }

                $field_id = weforms_insert_form_field( $data['form_id'], $field, $field_id, $order );

                $new_wpuf_input_ids[] = $field_id;

                $field['id'] = $field_id;

                $saved_wpuf_inputs[] = $field;
            }
        }

        $form                   = weforms()->form->get( $data['form_id'] );
        $new_form_settings      = array_merge( $data['form_settings'], array_diff_key( $form->get_settings(), $data['form_settings'] ) );
        $new_form_notifications = array_merge( $data['notifications'], array_diff_key( $form->get_notifications(), $data['notifications'] ) );
        $new_form_integrations  = array_merge( $data['integrations'], array_diff_key( $form->get_integrations(), $data['integrations'] ) );

        update_post_meta( $data['form_id'], $data['form_settings_key'], $new_form_settings );
        update_post_meta( $data['form_id'], 'notifications', $new_form_notifications );
        update_post_meta( $data['form_id'], 'integrations', $new_form_integrations );
        update_post_meta( $data['form_id'], '_weforms_version', WEFORMS_VERSION );

        return $saved_wpuf_inputs;
    }

    /**
     * Bulk Delete Form Exist
     *
     * @since 1.4.2
     *
     * @param array           $param
     * @param WP_REST_Request $request Full details about the request
     * @param string          $key
     *
     * @return bool
     **/
    public function is_bulk_delete_form_exists( $param, $request, $key ) {
        global $wpdb;
        $forms = $request['ids'];

        if ( is_array( $forms ) ) {
            $forms = array_filter( $forms, 'is_numeric' );

            if ( empty( $forms ) ) {
                return false;
            }

            $form_id  = implode( ',', $forms );
            $querystr = " SELECT $wpdb->posts.id
                FROM $wpdb->posts
                WHERE $wpdb->posts.post_type = 'wpuf_contact_form'
                AND $wpdb->posts.ID IN ( $form_id )
            ";
        } else {
            $form_id  = (int) $forms;
            $querystr = " SELECT $wpdb->posts.id
                FROM $wpdb->posts
                WHERE $wpdb->posts.post_type = 'wpuf_contact_form'
                AND $wpdb->posts.ID = $form_id
            ";
        }

        $result = $wpdb->get_results( $querystr );

        if ( empty( $result ) ) {
            return false;
        } else {
            return true;
        }
    }

    public function email_validation( $value ) {
        return ( !preg_match( "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $value ) ) ? false : true;
    }

    public function get_items( $request ) {
        $args = [
            'posts_per_page' => isset( $request['per_page'] ) ? intval( $request['per_page'] ) : 10,
            'paged'          => isset( $request['page'] ) ? absint( $request['page'] ) : 1,
            'order'          => isset( $request['order'] ) ? $request['order'] : 'DESC',
            'orderby'        => isset( $request['orderby'] ) ? $request['orderby'] : 'post_date',
            'post_status'    => isset( $request['status'] ) ? $request['status'] : 'any',
            's'              => isset( $request['search'] ) ? $request['search'] : '',
        ];

        $forms_array  = [];

        $defaults     = [
            'post_type'   => 'wpuf_contact_form',
            'post_status' => [ 'publish', 'draft', 'pending' ],
        ];

        $args            = wp_parse_args( $args, $defaults );
        $query           = new WP_Query( $args );
        $forms           = $query->get_posts();
        $formatted_items = [];

        if ( $forms ) {
            foreach ( $forms as $form_obj ) {
                $form                = new WeForms_Form( $form_obj );
                $form_author         = $form->data->post_author;
                $form_status         = $form->is_api_form_submission_open();
                $data                = [];
                $data['id']          = $form->id;
                $data['name']        = $form->name;
                $data['created_by']  = get_the_author_meta( 'user_nicename', $form_author );
                $data['created_on']  = $form->data->post_date;
                $data['status']      = $form_status['type'];
                $data['status_info'] = isset( $form_status['message'] ) ? $form_status['message'] : '';
                $data['fields']      = count( $form->get_fields() );
                $data['entries']     = $form->num_form_entries();
                $data['views']       = $form->num_form_views();
                $data['payments']    = $form->num_form_payments();
                $data                = $this->prepare_item_for_response( (array) $data, $request );
                $formatted_items[]   = $this->prepare_response_for_collection( $data );
            }
        }

        $contact_forms = apply_filters( 'weforms_rest_get_contact_forms', $formatted_items );
        $response      = rest_ensure_response( $contact_forms );
        $response      = $this->format_collection_response( $response, $request, (int) $query->found_posts  );

        return $response;
    }

    public function get_item( $request ) {
        $form_id     = absint( $request['form_id'] );
        $form        = weforms()->form->get( $form_id );
        $form_status = $form->is_api_form_submission_open();
        $form_author = $form->data->post_author;

        $data = [
            'id'            => $form->data->ID,
            'name'          => $form->name,
            'created_by'    => get_the_author_meta( 'user_nicename', $form_author ),
            'created_on'    => $form->data->post_date,
            'status'        => $form_status['type'],
            'status_info'   => isset( $form_status['message'] ) ? $form_status['message'] : '',
            'fields'        => $form->get_fields(),
            'settings'      => $form->get_settings(),
            'notifications' => $form->get_notifications(),
            'integrations'  => $form->get_integrations(),
        ];

        $data     = $this->prepare_item_for_response( $data, $request );
        $response = rest_ensure_response( $data );

        return $response;
    }

    public function create_item( $request ) {
        $template      = isset( $request['template'] ) ? sanitize_text_field( $request['template'] ) : '';
        $name          = isset( $request['name'] ) ? sanitize_text_field( $request['name'] ) : '';
        $setting       = isset( $request['settings'] ) ? $request['settings'] : [];
        $notifications = isset( $request['notifications'] ) ? $request['notifications'] : [];
        $fields        = isset( $request['fields'] ) ? $request['fields'] : [];
        $integrations  = isset( $request['integrations'] ) ? $request['integrations'] : [];

        $default_form_settings     =  weforms_get_default_form_settings();
        $default_form_notification =  [ weforms_get_default_form_notification() ];
        $integration_list          =  weforms()->integrations->get_integration_js_settings();
        $field_list                =  weforms()->fields->get_fields();

        if ( isset( $template  ) && !empty( $template ) ) {
            $form_id = weforms()->templates->create( $template );
        } elseif ( !empty( $name ) ) {
            if ( !empty( $fields ) ) {
                foreach ( $fields as $key => $field ) {
                    $f  = in_array(  $field['template'], array_keys( $field_list ) );

                    if ( empty( $f ) ) {
                        unset( $fields[ $key ] );
                    }
                }
            }

            $fields  =  array_intersect_key(  $fields, $field_list );
            $form_id = weforms()->form->create( $name, $fields );

            if ( is_wp_error( $form_id ) ) {
                return new WP_Error( 'rest_invalid_data', __( 'Could not create the form', 'weforms' ), [ 'status' => 404 ] );
            }

            $new_form_settings     =  array_diff_key( $default_form_settings, $setting );
            $new_form_notification =  array_diff_key( $default_form_notification, $notifications );
            $new_form_integrations =  array_intersect_key( $integrations, $integration_list );

            if ( !class_exists( 'WeForms_Pro' ) ) {
                $new_form_integrations = array_udiff_assoc( $new_form_integrations, $integration_list,
                    function ( $item, $item_list ) {
                        if ( isset( $item_list['pro'] ) && $item_list['pro'] == true ) {
                            return 0;
                        }

                        return $item;
                    }
                  );
            }

            update_post_meta( $form_id, 'wpuf_form_settings', $new_form_settings );
            update_post_meta( $form_id, 'notifications', $new_form_notification );
            update_post_meta( $form_id, 'integrations', $new_form_integrations );
        }

        if ( is_wp_error( $form_id ) || is_null( $form_id ) ) {
            return new WP_Error( 'rest_weforms_invalid_data', __( 'Could not create the form', 'weforms' ), [ 'status' => 404 ] );
        }

        $form = weforms()->form->get( $form_id );

        $data = [
            'id'            => $form->data->ID,
            'name'          => $form->name,
            'fields'        => $form->get_fields(),
            'settings'      => $form->get_settings(),
            'notifications' => $form->get_notifications(),
            'integrations'  => $form->get_integrations(),
        ];

        $response = $this->prepare_item_for_response( $data, $request );
        $response = rest_ensure_response( $response );
        $response->set_status( 200 );
        $response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $form->id ) ) );

        return $response;
    }

    public function update_item( $request ) {
        $data                      = [];
        $form_id                   = $request['form_id'];
        $data['form_id']           = $form_id;
        $data['form_settings_key'] = 'wpuf_form_settings';

        if ( isset( $request['name'] ) && !empty( $request['name'] ) ) {
            $data['post_title']    = $request['name'];
        }

        if ( isset( $request['settings'] ) && !empty( $request['settings'] ) ) {
            $data['form_settings'] = $request['settings'];
        }

        if ( isset( $request['fields'] ) && !empty( $request['fields'] ) ) {
            $data['form_fields']   = $request['fields'];
        }

        if ( isset( $request['notifications'] ) && !empty( $request['notifications'] ) ) {
            $data['notifications'] = $request['notifications'];
        }

        if ( isset( $request['integrations'] ) && !empty( $request['integrations'] ) ) {
            $data['integrations']  = $request['integrations'];
        }

        $this->weforms_api_update( $data );

        $form = weforms()->form->get( $form_id );

        $response_data = [
            'id'            => $form->data->ID,
            'name'          => $form->name,
            'fields'        => $form->get_fields(),
            'settings'      => $form->get_settings(),
            'notifications' => $form->get_notifications(),
            'integrations'  => $form->get_integrations(),
        ];

        $response = $this->prepare_item_for_response( $response_data, $request );
        $response = rest_ensure_response( $response );
        $response->set_status( 201 );
        $response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $form->id ) ) );

        return $response;
    }

    public function delete_item( $request ) {
        $form_array = [];
        $form_id    = $request['form_id'];
        $form       = $this->delete( $form_id );

        if ( is_wp_error( $form ) ) {
            return new WP_Error( 'error', __( 'Could not delete the form', 'weforms' ), [ 'status' => 404 ] );
        }

        $form_array['id']             = $form_id;
        $form_array['message']        = __( 'form  deleted successfully', 'weforms' );
        $form_array['data']['status'] = 200;

        $response = $this->prepare_response_for_collection( $form_array, $request );
        $response = rest_ensure_response( $response );

        return $response;
    }

    /**
     * Duplicate Form
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request full details about the request
     *
     * @return WP_REST_Response|WP_Error response object on success, or WP_Error object on failure
     **/
    public function duplicate_form( $request ) {
        $form_id = $request['form_id'];
        $form    = weforms()->form->get( $form_id );

        if ( empty( $form ) ) {
            return new WP_Error( 'error', __( 'Could not duplicate the form', 'weforms' ), [ 'status' => 404 ] );
        }

        $form_id = weforms()->form->create( $form->name, $form->get_fields() );

        $data = [
            'form_id'           => absint( $form_id ),
            'post_title'        => sanitize_text_field( $form->name ) . ' (#' . $form_id . ')',
            'form_fields'       => weforms()->form->get( $form_id )->get_fields(),
            'form_settings'     => $form->get_settings(),
            'form_settings_key' => 'wpuf_form_settings',
            'notifications'     => $form->get_notifications(),
            'integrations'      => $form->get_integrations(),
        ];

        $form_fields    = weforms()->form->save( $data );
        $form           = weforms()->form->get( $form_id );
        $form->settings = $form->get_settings();

        $response_data = [
            'id'            => $form->data->ID,
            'name'          => $form->name,
            'fields'        => $form->get_fields(),
            'settings'      => $form->get_settings(),
            'notifications' => $form->get_notifications(),
            'integrations'  => $form->get_integrations(),
        ];

        $response = $this->prepare_item_for_response( $response_data, $request );
        $response = rest_ensure_response( $response );
        $response->set_status( 201 );
        $response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $form->id ) ) );

        return $response;
    }

    /**
     * Prepare a single user output for response
     *
     * @param object          $item
     * @param WP_REST_Request $request           request object
     * @param array           $additional_fields (optional)
     *
     * @return WP_REST_Response $response response data
     */
    public function prepare_item_for_response( $item, $request, $additional_fields = [] ) {
        $response = rest_ensure_response( $item );
        $response = $this->add_links( $response, $item );

        return $response;
    }

    /**
     * Get the Form's schema, conforming to JSON Schema
     *
     * @return array
     */
    public function get_item_schema() {
        $schema = [
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => 'forms',
            'type'       => 'object',
            'properties' => [
                'form_id' => [
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => [ $this, 'is_form_exists' ],
                    'context'           => [ 'embed', 'view', 'edit' ],
                    'required'          => true,
                    'readonly'          => true,
                ],
                'name' => [
                    'description'       => __( '', 'weforms' ),
                    'type'              => 'string',
                    'context'           => [ 'edit', 'view'],
                    'sanitize_callback' => 'sanitize_text_field',
                    'required'          => false,
                ],
                'template' => [
                    'required'          => false,
                    'type'              => 'string',
                    'description'       => 'template name',
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => [ $this, 'is_template_exists' ],
                ],
                'settings' => [
                    'description' => __( '', 'weforms' ),
                    'type'        => 'object',
                    'context'     => [ 'edit', 'view'],
                    'required'    => false,
                ],
                'notifications' => [
                    'description' => __( '', 'weforms' ),
                    'type'        => 'object',
                    'context'     => [ 'edit', 'view' ],
                    'required'    => false,
                ],
                'fields' => [
                    'description' => __( '', 'weforms' ),
                    'type'        => 'object',
                    'context'     => [ 'edit', 'view' ],
                    'required'    => false,
                ],
                'integrations' => [
                    'description' => __( '', 'weforms' ),
                    'context'     => [ 'edit', 'view' ],
                    'type'        => 'object',
                    'required'    => false,
                ],
            ],
        ];

        return $schema;
    }

    public function get_collection_params() {
        $query_params                       = parent::get_collection_params();
        $query_params['context']['default'] = 'view';

        $schema            = $this->get_item_schema();
        $schema_properties = $schema['properties'];

        return $query_params;
    }

    /**
     * Delete a form with it's input fields
     *
     * @since 1.4.2
     *
     * @param int  $form_id
     * @param bool $force
     *
     * @return void
     */
    public function delete( $form_id, $force = true ) {
        global $wpdb;

        $form = weforms()->form->get( $form_id );

        if ( !$form->id ) {
            return new WP_Error( 'form_not_found', __( 'Form not found', 'weforms' ) );
        }

        $wp_post = wp_delete_post( $form_id, $force );

        if ( !$wp_post ) {
            return new WP_Error( 'unable_to_delete', __( 'Unable to delete form', 'weforms' ) );
        }

        // delete form inputs as WP doesn't know the relationship
        return $wpdb->delete( $wpdb->posts,
            [
                'post_parent' => $form_id,
                'post_type'   => 'wpuf_input',
            ]
          );

        return $form;
    }

    /**
     * Bulk Delete Form
     *
     * @since  1.3.9
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error response object on success, or WP_Error object on failure
     */
    public function bulk_delete_form( $request ) {
        $form_ids = $request['ids'];

        if ( empty( $form_ids  ) ) {
            return new WP_Error( 'error', __( 'No form ids provided!', 'weforms' ), [ 'status' => 404 ] );
        }

        $response = [];

        foreach ( $form_ids as $form_id ) {
            $status =  $this->delete( $form_id );

            if ( is_wp_error( $status ) ) {
                $response[$form_id] = $status->get_error_message();
            } else {
                $response[$form_id] = __( 'form  deleted successfully ', 'weforms' );
            }
        }

        $response = $this->prepare_response_for_collection( $response );
        $response = rest_ensure_response( $response );

        return $response;
    }

    /**
     * Update and existing form
     *
     * @since 1.4.2
     *
     * @param array $data Contains form_fields, form_settings, form_settings_key data
     *
     * @return bool
     */
    public function weforms_api_update( $data ) {
        $saved_wpuf_inputs = [];

        if ( isset( $data['post_title'] ) && !empty( $data['post_title'] ) ) {
            wp_update_post(
                [
                    'ID'          => $data['form_id'],
                    'post_status' => 'publish',
                    'post_title'  => $data['post_title'],
                ]
              );
        }

        $existing_wpuf_input_ids = get_children( [
            'post_parent' => $data['form_id'],
            'post_status' => 'publish',
            'post_type'   => 'wpuf_input',
            'numberposts' => '-1',
            'orderby'     => 'menu_order',
            'order'       => 'ASC',
            'fields'      => 'ids',
        ] );

        $new_wpuf_input_ids = [];

        if ( !empty( $data['form_fields'] ) ) {
            foreach ( $data['form_fields'] as $order => $field ) {
                if ( !empty( $field['is_new'] ) ) {
                    unset( $field['is_new'] );
                    unset( $field['id'] );

                    $field_id = 0;
                } else {
                    $field_id = $field['id'];
                }

                $field_id = weforms_insert_form_field( $data['form_id'], $field, $field_id, $order );

                $new_wpuf_input_ids[] = $field_id;

                $field['id'] = $field_id;

                $saved_wpuf_inputs[] = $field;
            }
        }

        $form = weforms()->form->get( $data['form_id'] );

        if ( isset( $data['form_settings'] ) && !empty( $data['form_settings'] ) ) {
            $new_form_settings = array_merge( $data['form_settings'], array_diff_key( $form->get_settings(), $data['form_settings'] ) );
            update_post_meta( $data['form_id'], 'wpuf_form_settings', $new_form_settings );
        }

        if ( isset( $data['notifications'] ) && !empty( $data['notifications'] ) ) {
            $existing_notifications = $form->get_notifications();

            foreach ( $existing_notifications as $key => $notification ) {
                if ( array_key_exists( $key, $data['notifications'] ) ) {
                    $existing_notifications[ $key ] = $data['notifications'][$key];
                }
            }

            update_post_meta( $data['form_id'], 'notifications', $existing_notifications );
        }

        if ( isset( $data['integrations'] ) && !empty( $data['integrations'] ) ) {
            $integration_list = weforms()->integrations->get_integration_js_settings();
            $form_integration = $form->get_integrations();
            $integrations     = $data['integrations'];
            $integrations     =  array_intersect_key(  $integrations, $integration_list );

            if ( !class_exists( 'WeForms_Pro' ) ) {
                $integrations = array_udiff_assoc( $integrations, $integration_list,
                    function ( $item, $item_list ) {
                        if ( isset( $item_list['pro'] ) && $item_list['pro'] == true ) {
                            return 0;
                        }

                        return $item;
                    }
                  );
            }

            $new_form_integrations = array_merge( $integrations, array_diff_key( $form->get_integrations(), $integrations ) );

            update_post_meta( $data['form_id'], 'integrations', $new_form_integrations );
        }

        update_post_meta( $data['form_id'], '_weforms_version', WEFORMS_VERSION );

        return $saved_wpuf_inputs;
    }

    /**
     * Import Forms
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error response object on success, or WP_Error object on failure
     **/
    public function import_forms( $request ) {
        $files   = $request->get_file_params();

        if ( !$files ) {
            return new WP_Error( 'rest_invalid_file', __( 'No file found to import.', 'weforms' ), [ 'status' => 404] );
        }

        $file_ext  = pathinfo( $files['file']['name'], PATHINFO_EXTENSION );

        if ( !class_exists( 'WeForms_Admin_Tools' ) ) {
            require_once WEFORMS_INCLUDES . '/admin/class-admin-tools.php';
        }

        if ( ( $file_ext == 'json' ) && ( $files['file']['size'] < 500000 ) ) {
            $status = WeForms_Admin_Tools::import_json_file( $files['file']['tmp_name'] );

            if ( $status ) {
                $response_data             = [];
                $response_data['message']  = __( 'The forms have been imported successfully!', 'weforms' );
                $response_data['status']   = 200;

                $response = $this->prepare_response_for_collection( $response_data );
                $response = rest_ensure_response( $response );

                return $response;
            } else {
                return new WP_Error( 'rest_invalid_file', __( 'Something went wrong importing the file.', 'weforms' ), [ 'status' => 404 ] );
            }
        } else {
            return new WP_Error( 'rest_invalid_file', __( 'Invalid file or file size too big.', 'weforms' ), [ 'status' => 404 ] );
        }
    }

    /**
     * Save Form Entry from Frontend
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request full details about the request
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure
     **/
    public function save_entry( $request ) {
        $form_id       = $request['form_id'];
        $page_id       = isset( $request['page_id'] ) ? intval( $request['page_id'] ) : 0;
        $form          = weforms()->form->get( $form_id );
        $form_settings = $form->get_settings();
        $form_fields   = $form->get_fields();
        $entry_fields  = $form->prepare_entries( $request );
        $entry_fields  = apply_filters( 'weforms_before_entry_submission', $entry_fields, $form, $form_settings, $form_fields );

        $entry_id = $this->weforms_api_insert_entry( [
            'form_id' => $form_id,
        ], $entry_fields );

        if ( is_wp_error( $entry_id ) ) {
            return new WP_Error( 'rest_invalid_data', $entry_id->get_error_message(), [ 'status' => 404 ] );
        }

        // redirect URL
        $show_message = false;
        $redirect_to  = false;

        if ( $form_settings['redirect_to'] == 'page' ) {
            $redirect_to = get_permalink( $form_settings['page_id'] );
        } elseif ( $form_settings['redirect_to'] == 'url' ) {
            $redirect_to = $form_settings['url'];
        } elseif ( $form_settings['redirect_to'] == 'same' ) {
            $show_message = true;
        } else {
            $redirect_to = get_permalink( $post_id );
        }

        // Fire a hook for integration
        do_action( 'weforms_entry_submission', $entry_id, $form_id, $page_id, $form_settings );

        $field_search = $field_replace = [];

        foreach ( $form_fields as $r_field ) {
            $field_search[] = '{' . $r_field['name'] . '}';

            if ( $r_field['template'] == 'name_field' ) {
                $field_replace[] = implode( ' ', explode( '|', $entry_fields[$r_field['name']] ) );
            } else {
                $field_replace[] = isset( $entry_fields[$r_field['name']] ) ? $entry_fields[$r_field['name']] : '';
            }
        }

        $message = str_replace( $field_search, $field_replace, $form_settings['message'] );

        $notification = new WeForms_Notification( [
            'form_id'  => $form_id,
            'page_id'  => $page_id,
            'entry_id' => $entry_id,
        ] );

        $notification->send_notifications();

        $entry_data = weforms_get_entry_data( $entry_id );

        $response_data                 = [];
        $response_data['id']           =  $entry_id;
        $response_data['message']      =  'Entries Created Successfully';
        $response_data['success']      = true;
        $response_data['redirect_to']  = $redirect_to;
        $response_data['show_message'] = $show_message;
        $response_data['message']      = $message;
        $response_data['data']         = $entry_data['data'];
        $response_data['form_id']      = $form_id;
        $response_data['entry_id']     = $entry_id;

        $response = apply_filters( 'weforms_entry_submission_response', $response_data );
        $response = $this->prepare_response_for_collection( $response_data );
        $response = rest_ensure_response( $response );

        return $response;
    }

    /**
     * Export Form Entries
     *
     **/
    public function export_form_entries( $request ) {
        $form_id = $request['form_id'];

        if ( !$form_id ) {
            return new WP_Error( 'rest_weforms_invalid_form', __( 'Form id not exists', 'weforms' ) );
        }

        $entry_array   = [];
        $columns       = weforms_get_entry_columns( $form_id, false );
        $total_entries = weforms_count_form_entries( $form_id );
        $entries       = weforms_get_form_entries( $form_id, [
            'number'       => $total_entries,
            'offset'       => 0,
        ] );
        $extra_columns =  [
            'ip_address' => __( 'IP Address', 'weforms' ),
            'created_at' => __( 'Date', 'weforms' ),
        ];

        $columns = array_merge( [ 'id' => 'Entry ID' ], $columns, $extra_columns );

        foreach ( $entries as $entry ) {
            $temp = [];

            foreach ( $columns as $column_id => $label ) {
                switch ( $column_id ) {
                    case 'id':
                        $temp[ $column_id ] = $entry->id;
                        break;

                    case 'ip_address':
                        $temp[ $column_id ] = $entry->ip_address;
                        break;

                    case 'created_at':
                        $temp[ $column_id ] = $entry->created_at;
                        break;

                    default:
                        $value              = weforms_get_entry_meta( $entry->id, $column_id, true );
                        $value              = weforms_get_pain_text( $value );
                        $temp[ $column_id ] = str_replace( WeForms::$field_separator, ' ', $value );
                        break;
                }
            }

            $entry_array[] = $temp;
        }

        error_reporting( 0 );

        if ( ob_get_contents() ) {
            ob_clean();
        }

        $blogname  = sanitize_title( strtolower( str_replace( ' ', '-', get_option( 'blogname' ) ) ) );
        $file_name = $blogname . '-weforms-entries-' . time() . '.csv';

        // force download
        header( 'Content-Type: application/force-download' );
        header( 'Content-Type: application/octet-stream' );
        header( 'Content-Type: application/download' );

        // disposition / encoding on response body
        header( "Content-Disposition: attachment;filename={$file_name}" );
        header( 'Content-Transfer-Encoding: binary' );

        $handle = fopen( 'php://output', 'w' );

        //handle UTF-8 chars conversion for CSV
        fprintf( $handle, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );

        // put the column headers
        fputcsv( $handle, array_values( $columns ) );

        // put the entry values
        foreach ( $entry_array as $row ) {
            fputcsv( $handle, $row );
        }

        fclose( $handle );

        exit;
    }

    public function submit_permissions_check( $request ) {
        if ( !is_weforms_api_allowed_guest_submission() ) {
            return new WP_Error( 'rest_cannot_submit_entry', __( 'Sorry, you have no permission to submit this form.', 'weforms' ), [ 'status' => rest_authorization_required_code() ] );
        }

        $form         = weforms()->form->get( (int) $request['form_id'] );
        $form_is_open = $form->is_submission_open();

        if ( is_wp_error( $form_is_open ) ) {
            return new WP_Error( 'rest_weforms_form_permission', $form_is_open->get_error_message(), [ 'status' => 404 ] );
        }

        $form_validations = $this->save_check( $request );

        if ( is_wp_error( $form_validations  ) ) {
            $form_message = [];

            foreach ( $form_validations->get_error_messages() as $error ) {
                $form_message[] = $error;
            }

            return new WP_Error( 'error', $form_message, [ 'status' => 404 ] );
        }

        return true;
    }

    /**
     * Get Form Entries
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error response object on success, or WP_Error object on failure
     */
    public function get_entry( $request ) {
        $form_id      = isset( $request['form_id'] ) ? intval( $request['form_id'] ) : 0;
        $current_page = isset( $request['page'] ) ? intval( $request['page'] ) : 1;
        $per_page     = isset( $request['per_page'] ) ? intval( $request['per_page'] ) : 10;
        $status       = isset( $request['status'] ) ? $request['status'] : 'publish';
        $offset       = ( $current_page - 1 ) * $per_page;

        if ( !$form_id ) {
            return new WP_Error( 'rest_invalid_form', __( 'Please provide a form id', 'weforms' ), [ 'status' => 404 ] );
        }

        $entries = weforms_get_form_entries(
            $form_id, [
                'number' => $per_page,
                'offset' => $offset,
                'status' => $status,
            ]
          );

        $columns       = weforms_get_entry_columns( $form_id );
        $total_entries = weforms_count_form_entries( $form_id, $status );

        array_map(
            function ( $entry ) use ( $columns,$form_id ) {
                $entry_id = $entry->id;
                $entry->fields = [];
                $entry->links = rest_url( sprintf( '%s/%s/%d/%s/%d', $this->namespace, $this->rest_base, $form_id, 'entries', $entry->id ) );

                foreach ( $columns as $meta_key => $label ) {
                    $value                    = weforms_get_entry_meta( $entry_id, $meta_key, true );
                    $entry->fields[ $meta_key ] = str_replace( WeForms::$field_separator, ' ', $value );
                }
            }, $entries
          );

        $entries = apply_filters( 'weforms_get_entries', $entries, $form_id );

        $response  = $entries;
        $max_pages = ceil( $total_entries / $per_page );
        $response  = $this->prepare_response_for_collection( $response );
        $response  = rest_ensure_response( $response );

        $response->header( 'X-WP-Total', (int) $total_entries );
        $response->header( 'X-WP-TotalPages', (int) $max_pages );

        return $response;
    }

    /**
     * Get Single Entry Details
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request full details about the request
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure
     **/
    public function get_entry_details( $request ) {
        $form_id       = $request['form_id'];
        $entry_id      = $request['entry_id'];
        $form          = weforms()->form->get( $form_id );
        $form_settings = $form->get_settings();
        $entry         = $form->entries()->get( $entry_id );
        $fields        = $entry->get_fields();

        $fields_data =  array_map( function ( $field ) {
            return [
                'name'  => $field['name'],
                'label' => $field['label'],
                'value' => $field['value'],
            ];
        }, $fields );

        $metadata      = $entry->get_metadata();
        $payment       = $entry->get_payment_data();

        if ( isset( $payment->payment_data ) && is_serialized( $payment->payment_data ) ) {
            $payment->payment_data = unserialize( $payment->payment_data );
        }

        $has_empty          = false;
        $answers            = [];
        $respondentPoints   = isset( $form_settings['total_points'] ) ? floatval( $form_settings['total_points'] ) : 0;

        foreach ( $fields as $key => $field ) {
            if ( $form_settings['quiz_form'] == 'yes' ) {
                $selectedAnswers    = isset( $field['selected_answers'] ) ? $field['selected_answers'] : '';
                $givenAnswer        = isset( $field['value'] ) ? $field['value'] : '';
                $options            = isset( $field['options'] ) ? $field['options'] : '';
                $template           = $field['template'];
                $fieldPoints        = isset( $field['points'] ) ? floatval( $field['points'] ) : 0;

                if ( $template == 'radio_field' || $template == 'dropdown_field' ) {
                    $answers[$field['name']] = true;

                    if ( empty( $givenAnswer ) ) {
                        $answers[$field['name']] = false;
                        $respondentPoints -= $fieldPoints;
                    } else {
                        foreach ( $options as $key => $value ) {
                            if ( $givenAnswer == $value ) {
                                if ( $key != $selectedAnswers ) {
                                    $answers[$field['name']] = false;
                                    $respondentPoints -= $fieldPoints;
                                }
                            }
                        }
                    }
                } elseif ( $template == 'checkbox_field' || $template == 'multiple_select' ) {
                    $answers[$field['name']] = true;
                    $userAnswer              = [];

                    foreach ( $options as $key => $value ) {
                        foreach ( $givenAnswer as $answer ) {
                            if ( $value == $answer ) {
                                $userAnswer[] = $key;
                            }
                        }
                    }

                    $userAnswer   = implode( '|', $userAnswer );
                    $rightAnswers = implode( '|', $selectedAnswers );

                    if ( $userAnswer != $rightAnswers || empty( $userAnswer ) ) {
                        $answers[$field['name']] = false;
                        $respondentPoints -= $fieldPoints;
                    }
                }
            } elseif ( empty( $field['value'] ) ) {
                $has_empty      = true;
                break;
            }
        }

        $response = [
            'fields'            => $fields_data,
            'meta_data'         => $metadata,
            'payment_data'      => $payment,
            'has_empty'         => $has_empty,
            'respondent_points' => $respondentPoints,
            'answers'           => $answers,
        ];

        $response = $this->prepare_response_for_collection( $response );
        $response = rest_ensure_response( $response );
        $response->header( 'Location', rest_url( sprintf( '/%s/%s/%d/%s/%d', $this->namespace, $this->rest_base, $form->id, 'entries', $entry->id  ) ) );

        return $response;
    }

    /**
     * Export forms to JSON
     *
     * @param WP_REST_Request $request
     *
     * @return json
     **/
    public function export_forms( $request ) {
        $export_type = isset( $request['type'] ) ? $request['type'] : 'selected';
        $selected    = isset( $request['ids'] ) ? array_map( 'absint', $request['ids'] ) : [];

        if ( !class_exists( 'WeForms_Admin_Tools' ) ) {
            require_once WEFORMS_INCLUDES . '/admin/class-admin-tools.php';
        }

        switch ( $export_type ) {
            case 'all':
                WeForms_Admin_Tools::export_to_json();
                break;

            case 'selected':
                WeForms_Admin_Tools::export_to_json( $selected );
                break;
        }
    }
}
