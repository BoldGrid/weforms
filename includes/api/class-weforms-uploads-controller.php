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
     * Size limit in KB for an upload field saved without `max_size`.
     *
     * Matches the form builder default for image fields. Uploads are also
     * capped by wp_max_upload_size(), so a missing value never means
     * "no limit".
     *
     * @since 1.6.30
     *
     * @var int
     */
    const DEFAULT_MAX_SIZE_KB = 1024;

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
                'permission_callback' => [ $this, 'delete_permissions_check' ],
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
     * @param WP_REST_Request $request Request.
     *
     * @return bool|\WP_Error True when the file matches one published upload field.
     */
    public function is_file_validate( $request ) {
        $files  = $request->get_file_params();
        $file   = ( isset( $files['file'] ) && \is_array( $files['file'] ) ) ? $files['file'] : [];
        $result = \weforms_authorize_upload(
            (int) $request->get_param( 'form_id' ),
            [
                'field_id' => \absint( $request->get_param( 'field_id' ) ),
            ],
            $file
        );

        if ( \is_wp_error( $result ) ) {
            return new \WP_Error( 'rest_weforms_upload', \__( 'Upload could not be accepted.', 'weforms' ), [ 'status' => 400 ] );
        }

        return true;
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

        $rejected = $this->reject_attachment_update_params( $request );

        if ( \is_wp_error( $rejected ) ) {
            return $rejected;
        }

        $files      = $request->get_file_params();
        $file       = ( isset( $files['file'] ) && \is_array( $files['file'] ) ) ? $files['file'] : [];
        $authorized = \weforms_authorize_upload(
            (int) $request->get_param( 'form_id' ),
            [
                'field_id' => \absint( $request->get_param( 'field_id' ) ),
            ],
            $file
        );

        if ( \is_wp_error( $authorized ) ) {
            return new \WP_Error( 'rest_weforms_upload', \__( 'Upload could not be accepted.', 'weforms' ), [ 'status' => 400 ] );
        }

        $endpoints             = $wp_rest_server->get_routes();
        $attachment_controller = $endpoints['/wp/v2/media'][0]['callback'][0];

        $GLOBALS['weforms_upload_form_id']    = \absint( $request->get_param( 'form_id' ) );
        $GLOBALS['weforms_upload_field_name'] = \sanitize_key( $authorized['name'] );

        WeForms_Upload_Protection::begin();

        try {
            $attachment_response = $attachment_controller->create_item( $this->request_for_new_attachment( $request ) );
        } finally {
            WeForms_Upload_Protection::end();
            unset( $GLOBALS['weforms_upload_form_id'], $GLOBALS['weforms_upload_field_name'] );
        }

        if ( \is_wp_error( $attachment_response ) ) {
            return $attachment_response;
        }

        if ( !empty( $attachment_response->data['id'] ) ) {
            $form_id  = $request->get_param( 'form_id' );

            WeForms_Upload_Protection::protect( $attachment_response->data['id'], (string) $files['file']['name'] );
            update_post_meta( $attachment_response->data['id'], 'attachment_form_id', $form_id );
            weforms_register_uploaded_attachment( $attachment_response->data['id'] );

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
        $permission = $this->delete_permissions_check( $request );

        if ( \is_wp_error( $permission ) ) {
            return $permission;
        }

        global $wp_rest_server;

        $endpoints             = $wp_rest_server->get_routes();
        $attachment_controller = $endpoints['/wp/v2/media'][0]['callback'][0];
        $attachment_response   = $attachment_controller->delete_item( $request );

        if ( \is_wp_error( $attachment_response ) || empty( $attachment_response->data['previous']['id'] ) ) {
            return new \WP_Error(
                'rest_weforms_cannot_delete',
                \__( 'Sorry, you are not allowed to delete this file.', 'weforms' ),
                [ 'status' => \rest_authorization_required_code() ]
            );
        }

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
        $html .= sprintf( '<div class="attachment-name"><img src="%s" alt="%s" /></div>', \esc_url( $image ), \esc_attr( $attachment->post_title ) );
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
            /**
             * A scalar value is one extension category. `ext` is a comma-separated
             * list, not a list of category records.
             */
            $category = \is_string( $field_extensions ) ? $field_extensions : '';

            if ( isset( $allowd_extensions[ $category ]['ext'] ) && \is_string( $allowd_extensions[ $category ]['ext'] ) ) {
                foreach ( \explode( ',', $allowd_extensions[ $category ]['ext'] ) as $extension ) {
                    $extension = \trim( $extension );

                    if ( '' !== $extension ) {
                        $allowd_ext_array[] = $extension;
                    }
                }
            }
        }

        return $allowd_ext_array;
    }

    /**
     * Published image or file upload field with this ID.
     *
     * Fields nested in columns are included. An unknown ID does not match.
     *
     * @since 1.6.30
     *
     * @param int $form_id  Form ID.
     * @param int $field_id Field ID.
     *
     * @return array|null
     */
    protected function find_upload_field( $form_id, $field_id ) {
        $form_id  = \absint( $form_id );
        $field_id = \absint( $field_id );

        if ( ! $form_id || ! $field_id ) {
            return null;
        }

        $fields = weforms()->form->get( $form_id )->get_fields();

        if ( ! \is_array( $fields ) ) {
            return null;
        }

        $found = null;

        $walk = function ( $field ) use ( &$walk, &$found, $field_id ) {
            if ( null !== $found || ! \is_array( $field ) ) {
                return;
            }

            $template = isset( $field['template'] ) ? $field['template'] : '';
            $id       = isset( $field['id'] ) ? \absint( $field['id'] ) : 0;

            if ( $id === $field_id && \in_array( $template, [ 'image_upload', 'file_upload' ], true ) ) {
                $found = $field;

                return;
            }

            if ( empty( $field['inner_fields'] ) || ! \is_array( $field['inner_fields'] ) ) {
                return;
            }

            foreach ( $field['inner_fields'] as $column_fields ) {
                if ( ! \is_array( $column_fields ) ) {
                    continue;
                }

                foreach ( $column_fields as $inner_field ) {
                    $walk( $inner_field );
                }
            }
        };

        foreach ( $fields as $field ) {
            $walk( $field );
        }

        return $found;
    }

    /**
     * Parameters that would edit an existing attachment.
     *
     * @since 1.6.30
     *
     * @return string[]
     */
    protected function attachment_update_params() {
        return [ 'id', 'post', 'author', 'status' ];
    }

    /**
     * Reject a create request that carries attachment-update parameters.
     *
     * @since 1.6.30
     *
     * @param WP_REST_Request $request Request.
     *
     * @return true|\WP_Error
     */
    public function reject_attachment_update_params( $request ) {
        foreach ( $this->attachment_update_params() as $key ) {
            if ( $request->has_param( $key ) ) {
                return new \WP_Error(
                    'rest_weforms_cannot_edit_attachment',
                    \__( 'Sorry, you are not allowed to edit an existing file.', 'weforms' ),
                    [ 'status' => 400 ]
                );
            }
        }

        return true;
    }

    /**
     * Media request that contains only the uploaded file.
     *
     * @since 1.6.30
     *
     * @param WP_REST_Request $request Upload request.
     *
     * @return WP_REST_Request
     */
    protected function request_for_new_attachment( $request ) {
        $clean = new \WP_REST_Request( 'POST' );
        $files = $request->get_file_params();
        $file  = ( isset( $files['file'] ) && \is_array( $files['file'] ) ) ? $files['file'] : [];

        $clean->set_file_params(
            [
                'file' => $file,
            ]
        );

        return $clean;
    }

    /**
     * Whether the current user may delete this form attachment.
     *
     * @since 1.6.30
     *
     * @param WP_REST_Request $request Request.
     *
     * @return true|\WP_Error
     */
    public function delete_permissions_check( $request ) {
        $id      = isset( $request['id'] ) ? \absint( $request['id'] ) : 0;
        $form_id = isset( $request['form_id'] ) ? \absint( $request['form_id'] ) : 0;
        $post    = $id ? \get_post( $id ) : null;

        if ( ! $post || 'attachment' !== $post->post_type || ! $form_id ) {
            return new \WP_Error(
                'rest_weforms_cannot_delete',
                \__( 'Sorry, you are not allowed to delete this file.', 'weforms' ),
                [ 'status' => \rest_authorization_required_code() ]
            );
        }

        $attach_form_id = \absint( \get_post_meta( $id, 'attachment_form_id', true ) );

        if ( $attach_form_id !== $form_id || ! \current_user_can( 'delete_post', $id ) ) {
            return new \WP_Error(
                'rest_weforms_cannot_delete',
                \__( 'Sorry, you are not allowed to delete this file.', 'weforms' ),
                [ 'status' => \rest_authorization_required_code() ]
            );
        }

        return true;
    }

    public function upload_permissions_check( $request ) {
        $rejected = $this->reject_attachment_update_params( $request );

        if ( \is_wp_error( $rejected ) ) {
            return $rejected;
        }

        if ( !is_weforms_api_allowed_guest_submission() ) {
            return new WP_Error( 'rest_weforms_cannot_upload', __( 'Sorry, you have no permission to upload File.', 'weforms' ), [ 'status' => rest_authorization_required_code() ] );
        }

        if ( ! \weforms_upload_form_is_open( (int) $request['form_id'] ) ) {
            return new WP_Error( 'rest_weforms_upload', __( 'Upload could not be accepted.', 'weforms' ), [ 'status' => 400 ] );
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
