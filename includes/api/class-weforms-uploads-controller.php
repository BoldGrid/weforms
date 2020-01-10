<?php

/**
 * Settings  manager class
 *
 * @since 1.4.2
 */
class Weforms_Upload_Controller extends Weforms_REST_Controller {

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
    protected $rest_base = 'uploads';

    /**
     * Register all routes releated with forms
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/files', [
                'args'   => [
                    'form_id' => [
                        'required'            => true,
                        'description'         => __( 'Unique identifier for the object.', 'weforms' ),
                        'sanitize_callback'   => 'absint',
                        'type'                => 'integer',
                        'validate_callback'   => [ $this, 'is_form_exists' ],
                    ],
                    'field_id' => [
                        'required'            => true,
                        'description'         => __( 'Unique identifier for the object.', 'weforms' ),
                        'sanitize_callback'   => 'absint',
                        'type'                => 'integer',
                    ],
                ],
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [ $this, 'upload_file' ],
                    'permission_callback' => [ $this, 'upload_permissions_check' ],
                ],
        ] );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/files/(?P<id>[\d]+)/', [
            'args' => [
                'form_id' => [
                    'description'       => __( 'Unique identifier for the object.', 'weforms' ),
                    'validate_callback' => [ $this, 'is_form_exists' ],
                    'required'          => true,
                    'sanitize_callback' => 'absint',
                    'type'              => 'integer',
                ],
                'id' => [
                    'description'       => __( 'Unique identifier for the object.', 'weforms' ),
                    'validate_callback' => [ $this, 'is_form_attach_exist' ],
                    'required'          => true,
                    'sanitize_callback' => 'absint',
                    'type'              => 'integer',
                ],
                'force' => [
                    'required'    => true,
                    'type'        => 'boolean',
                    'description' => __( '', 'weforms' ),
                    'default'     => true,
                ],
            ],

            [
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => [ $this, 'delete_file' ],
                'permission_callback' => [ $this, 'upload_permissions_check' ],
            ],
        ] );
    }

    /**
     *  Check form Attach exists
     *
     * @since 1.4.2
     *
     * @param string          $param
     * @param WP_REST_Request $request
     * @param string          $key
     *
     * @return bool
     */
    public function is_form_attach_exist( $param, $request, $key ) {
        $form_id        = $request->get_param( 'form_id' );
        $attach_id      = (int) $param;
        $attach_form_id = get_post_meta( $attach_id, 'attachment_form_id', true );

        if ( $attach_form_id == $form_id  ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *  Check Form Field Exist
     *
     * @since 1.4.2
     *
     * @param string          $param
     * @param WP_REST_Request $request
     * @param string          $key
     *
     * @return bool
     */
    public function is_file_validate( $request ) {
        $file_error    = new WP_Error();
        $form          = weforms()->form->get( (int) $request['form_id'] );
        $form_settings = $form->get_settings();
        $form_fields   = $form->get_fields();
        $files         = $request->get_file_params();
        $headers       = $request->get_headers();
        $field_id      = $request->get_param( 'field_id' );

        if ( !empty( $files ) ) {
            foreach ( $form_fields as $field ) {
                if ( 'image_upload' === $field['template'] && $field['id'] == $field_id ) {
                    $allowed_extension = weforms_allowed_extensions();
                    $allowed_file_type = explode( ',', $allowed_extension['images']['ext'] );
                    $file_type         =  wp_check_filetype( $files['file']['name'], $mimes = null );

                    if ( in_array( $file_type['ext'], $allowed_file_type ) ) {
                        if ( $files['file']['size'] <= ( $field['max_size'] * 1024 ) ) {
                            return true;
                        } else {
                            $file_error->add( 'rest_weforms_invalid_file_size', __( 'File Size exceeds limit!', 'weforms' ), [ 'status' => 404 ] );
                        }
                    }
                }

                if ( 'file_upload' === $field['template'] && $field['id'] == $field_id ) {
                    $file_type =  wp_check_filetype( $files['file']['name'], $mimes = null );

                    if ( $this->is_allow_file_type( $field['extension'], $file_type['ext'] ) ) {
                        if ( $files['file']['size'] <= ( $field['max_size'] * 1024 ) ) {
                            return true;
                        } else {
                            $file_error->add( 'rest_weforms_invalid_file_size', __( 'File Size exceeds limit!', 'weforms' ), [ 'status' => 404 ] );
                        }
                    }
                }
            }
        }

        if ( count( $file_error->get_error_messages() ) > 0 ) {
            return $file_error;
        } else {
            return true;
        }
    }

    /**
     * Upload File
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error response object on success, or WP_Error object on failure
     */
    public function upload_file( $request ) {
        global $wp_rest_server;

        $endpoints             = $wp_rest_server->get_routes();
        $attachment_controller = $endpoints['/wp/v2/media'][0]['callback'][0];
        $files                 = $request->get_file_params();
        $headers               = $request->get_headers();
        $attachment_response   = $attachment_controller->create_item( $request );

        if ( !empty( $attachment_response->data['id'] ) ) {
            $form_id  = $request->get_param( 'form_id' );

            update_post_meta( $attachment_response->data['id'], 'attachment_form_id', $form_id );

            if ( wp_attachment_is_image( $attachment_response->data['id'] ) ) {
                $image = wp_get_attachment_image_src( $attachment_response->data['id'], 'thumbnail' );
                $image = $image[0];
            } else {
                $image = wp_mime_type_icon( $attachment_response->data['id'] );
            }

            $data = [
                'form_id'   => $form_id,
                'attach_id' => $attachment_response->data['id'],
                'link'      => $image,
            ];

            $response          = $this->prepare_response_for_collection( $data, $request );
            $response['html '] = $this->attach_html( $attachment_response->data['id'] );
            $response          = rest_ensure_response( $response );

            return $response;
        } else {
            return new WP_Error( 'rest_weforms', __( 'could not upload file', 'weforms' ), [ 'status' => 404 ] );
        }
    }

    /**
     * Delete File
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error response object on success, or WP_Error object on failure
     */
    public function delete_file( $request ) {
        global $wp_rest_server;

        $endpoints              = $wp_rest_server->get_routes();
        $attachment_controller  = $endpoints['/wp/v2/media'][0]['callback'][0];
        $attachment_response    = $attachment_controller->delete_item( $request );
        $data                   = [];
        $data['id']             = $attachment_response->data['previous']['id'];
        $data['message']        = 'File deleted successfully';
        $data['data']['status'] = 200;
        $response               = $this->prepare_response_for_collection( $data );
        $response               = rest_ensure_response( $response );

        return $response;
    }

    /**
     * Image attachment response
     *
     * @since 1.4.2
     *
     * @param int    $attach_id
     * @param string $type
     *
     * @return string
     */
    public static function attach_html( $attach_id, $type = NULL ) {
        if ( ! $type ) {
            $type = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : 'image';
        }

        $attachment = get_post( $attach_id );

        if ( !$attachment ) {
            return;
        }

        if ( wp_attachment_is_image( $attach_id ) ) {
            $image = wp_get_attachment_image_src( $attach_id, 'thumbnail' );
            $image = $image[0];
        } else {
            $image = wp_mime_type_icon( $attach_id );
        }

        $html = '<li class="ui-state-default wpuf-image-wrap thumbnail">';
        $html .= sprintf( '<div class="attachment-name"><img src="%s" alt="%s" /></div>', $image, esc_attr( $attachment->post_title ) );
        $html .= sprintf( '<input type="hidden" name="wpuf_files[%s][]" value="%d">', $type, $attach_id );
        $html .= '<div class="caption">';
        $html .= sprintf( '<a href="#" class="attachment-delete" data-attach_id="%d"> <img src="%s" /></a>', $attach_id, WEFORMS_ASSET_URI . '/images/del-img.png' );
        $html .= sprintf( '<span class="wpuf-drag-file"> <img src="%s" /></span>', WEFORMS_ASSET_URI . '/images/move-img.png' );
        $html .= '</div>';
        $html .= '</li>';

        return $html;
    }

    /**
     * Compare Two Array Return One Array
     *
     * @since 1.4.2
     *
     * @return array
     **/
    public function compare_array( $field_extensions, $allowd_extensions ) {
        $array_new = [];

        foreach ( $field_extensions as $key => $value ) {
            if ( array_key_exists( $value, $allowd_extensions ) ) {
                $array_new[$key] = $allowd_extensions[$value];
            }
        }

        return $array_new;
    }

    /**
     * Get Allowed Extension
     *
     * @since 1.4.2
     *
     * @param string $field_extension
     * @param string $file_extension
     *
     * @return bool
     */
    public function is_allow_file_type( $field_extension, $file_extension ) {
        $allowed_extensions       = weforms_allowed_extensions();
        $field_allowed_extensions = $this->get_allowed_extension( $allowed_extensions, $field_extension );

        if ( in_array( $file_extension, $field_allowed_extensions ) ) {
            return true;
        }

        return false;
    }

    /**
     * Get Allowed Extension Type
     *
     * @since 1.4.2
     *
     * @param array $allowd_extensions
     * @param array $field_extensions
     *
     * @return array
     */
    public function get_allowed_extension( $allowd_extensions, $field_extensions ) {
        $allowd_ext_array = [];

        if ( is_array( $field_extensions ) ) {
            $allowd_extensions = $this->compare_array( $field_extensions, $allowd_extensions );

            foreach ( $allowd_extensions as $key => $allowd_extension ) {
                $extensions = explode( ',', $allowd_extension['ext'] );

                foreach ( $extensions as $key => $extension ) {
                    array_push( $allowd_ext_array, $extension );
                }
            }
        } else {
            if ( array_key_exists( $field_extensions, $allowd_extensions ) ) {
                foreach ( $allowd_extensions[ $field_extensions ][ 'ext' ] as $key => $allowd_extension ) {
                    $extensions = explode( ',', $allowd_extension['ext'] );

                    foreach ( $extensions as $key => $extension ) {
                        array_push( $allowd_ext_array, $extension );
                    }
                }
            }
        }

        return $allowd_ext_array;
    }

    public function upload_permissions_check( $request ) {
        if ( !is_weforms_api_allowed_guest_submission() ) {
            return new WP_Error( 'rest_weforms_cannot_upload', __( 'Sorry, you have no permission to upload File.', 'weforms' ), [ 'status' => rest_authorization_required_code() ] );
        }

        $form         = weforms()->form->get( (int) $request['form_id'] );
        $form_is_open = $form->is_submission_open();

        if ( is_wp_error( $form_is_open ) ) {
            return new WP_Error( 'rest_weforms_form_permission', $form_is_open->get_error_message(), [ 'status' => 404 ] );
        }

        $file_validations = $this->is_file_validate( $request );

        if ( is_wp_error( $file_validations  ) ) {
            $file_message = [];

            foreach ( $file_validations->get_error_messages() as $error ) {
                $file_message[] = $error;
            }

            return new WP_Error( 'error', $file_message, [ 'status' => 404 ] );
        }

        return true;
    }
}
