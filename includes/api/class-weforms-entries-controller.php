<?php

/**
 * Entry  manager class
 *
 * @since 1.4.2
 */

class Weforms_Entry_Controller extends WP_REST_Controller {

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
    protected $base = 'entries';

    /**
     * Register all routes releated with forms
     *
     * @return void
     */
    public function register_routes() {

        register_rest_route(
            $this->namespace,
            '/'. $this->base . '/(?P<entry_id>[\d]+)',
            array(
                array(
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => array( $this, 'delete_items' ),
                    'permission_callback' => array( $this, 'delete_items_permissions_check' ),
                    'args'                => array(
                        'entry_id'  => array(
                            'required'          => true,
                            'type'              => 'integer',
                            'description'       => __( 'Entry id', 'weforms' ),
                            'validate_callback' => array( $this, 'is_entry_exists' ),
                        ),
                    )
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            '/'. $this->base . '/bulkdelete',
            array(
                array(
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => array( $this, 'bulk_delete_items' ),
                    'permission_callback' => array( $this, 'delete_items_permissions_check' ),
                    'args'                => array(
                        'entry_ids'  => array(
                            'required'          => true,
                            'type'              => 'object',
                            'description'       => __( 'Entry id', 'weforms' ),
                            'validate_callback' => array( $this, 'is_entry_exists' ),
                        )
                    )
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            '/'. $this->base . '/(?P<entry_id>[\d]+)/restore',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'restore_items' ),
                    'permission_callback' => array( $this, 'update_items_permissions_check' ),
                    'args'                => array(
                        'entry_id'  => array(
                            'required'          => true,
                            'type'              => 'integer',
                            'description'       => __( 'Entry id', 'weforms' ),
                            'validate_callback' => array( $this, 'is_entry_exists' ),
                        )
                    )
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            '/'. $this->base . '/(?P<entry_id>[\d]+)/trash',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'trash_items' ),
                    'permission_callback' => array( $this, 'update_items_permissions_check' ),
                    'args'                => array(
                        'entry_id'  => array(
                            'required'          => true,
                            'type'              => 'integer',
                            'description'       => __( 'Entry id', 'weforms' ),
                            'validate_callback' => array( $this, 'is_entry_exists' ),
                        ),
                    )
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            '/'. $this->base . '/bulkrestore',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'bulk_restore_items' ),
                    'permission_callback' => array( $this, 'delete_items_permissions_check' ),
                    'args'                => array(
                        'entry_ids'  => array(
                            'required'          => true,
                            'type'              => 'object',
                            'description'       => __( 'Entry id', 'weforms' ),
                            'validate_callback' => array( $this, 'is_entry_exists' ),
                        ),
                    )
                ),
            )
        );

    }

    /**
     * Restore Items From Trash
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     **/
    public function bulk_restore_items( $request ) {
        $entry_ids = isset( $request['entry_ids'] ) ? array_map( 'absint', $request['entry_ids'] ) : array();

        if ( ! $entry_ids ) {
            return new WP_Error( 'rest_invalid_data',__( 'No entry ids provided!','weforms' ),array( 'status' => 404 ) );
        }

        foreach ( $entry_ids as $entry_id ) {
            $status = weforms_change_entry_status( $entry_id, 'publish' );

            if( $status ) {
                $response['id'][] = $entry_id;
                $response['message'][] = __( ' Entry bulk Restore successfully ', 'weforms' );
            } else {
                $response['id'][] = $entry_id;
                $response['message'][] = __( ' Entry bulk Restore failed ', 'weforms' );
            }
        }

        $response = $this->prepare_response_for_collection( $response );
        $response = rest_ensure_response( $response );

        return $response;
    }

    /**
     * Restore Item From Trash
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     **/
    public function restore_items( $request ) {
        $entry_id = isset( $request['entry_id'] ) ? intval( $request['entry_id'] ) : 0;

        weforms_change_entry_status( $entry_id, 'publish' );

        $response['id']             = $entry_id;
        $response['message']        = __( ' Entry Restore Successfully  successfully ', 'weforms' );
        $response['data']['status'] = 200;

        $response = $this->prepare_response_for_collection( $response );
        $response = rest_ensure_response( $response );

        return $response;
    }

    /**
     * Trash Item
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     **/
    public function trash_items( $request ) {
        $entry_id = isset( $request['entry_id'] ) ? intval( $request['entry_id'] ) : 0;

        weforms_change_entry_status( $entry_id, 'trash' );

        $response['id']             = $entry_id;
        $response['message']        = __( ' Entry Moved To Trash  successfully ', 'weforms' );
        $response['data']['status'] = 200;

        $response = $this->prepare_response_for_collection( $response );
        $response = rest_ensure_response( $response );

        return $response;
    }

    /**
     * Bulk Delele Item
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     **/
    public function bulk_delete_items( $request ) {
        $entry_ids = isset( $request['entry_ids'] ) ? array_map( 'absint', $request['entry_ids'] ) : array();
        $force     = isset( $request['force'] ) && ( $request['force'] ) ? true : false;

        if ( ! $entry_ids ) {
            return new WP_Error('rest_invalid_data',__( 'No entry ids provided!', 'weforms' ));
        }

        $response = array();

        foreach ( $entry_ids as $entry_id ) {

            if ( $force ) {

                $status =  weforms_delete_entry( $entry_id );

                if( $status ) {
                    $response['message'][] = __( ' Entry Deleted successfully ', 'weforms' );
                } else {
                    $response['message'][] = __( ' Entry Not found ', 'weforms' );
                }

                $response['id'][] = $entry_id;
            } else {

                $status = weforms_change_entry_status( $entry_id, 'trash' );

                if( $status ) {
                    $response['message'][] = __( ' Entry Move To Trash successfully ', 'weforms' );
                } else {
                    $response['message'][] = __( ' Entry Not found ', 'weforms' );
                }

                $response['id'][] = $entry_id;
            }
        }

        $response = $this->prepare_response_for_collection( $response );
        $response = rest_ensure_response( $response );

        return $response;
    }

    /**
     * Delele Item
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     **/
    public function delete_items( $request ) {
        $entry_id = isset( $request['entry_id'] ) ? intval( $request['entry_id'] ) : 0;
        $force = isset( $request['force'] ) && ( $request['force'] ) ? true : false;

        if ( $force ) {
            weforms_delete_entry( $entry_id );

            $entry_array['id']              = $entry_id;
            $entry_array['message']         = __( ' Entry Deleted successfully ', 'weforms' );
            $entry_array['data']['status']  = 200;
        } else {
            $status = weforms_change_entry_status( $entry_id, 'trash' );

            $entry_array['id']              = $entry_id;
            $entry_array['message']         = __( ' Entry Move To Trash successfully ', 'weforms' );
            $entry_array['data']['status']  = 200;
        }

        $response = $this->prepare_response_for_collection( $entry_array, $request );
        $response = rest_ensure_response( $response );

        return $response;
    }

    /**
     * Check Entries exist or not
     *
     * @since 1.4.2
     *
     * @param string $param
     * @param WP_REST_Request $request
     * @param string $key
     *
     * @return boolean
     */
    public function is_entry_exists( $param, $request, $key ) {
        global $wpdb;

        if( is_array( $param ) ) {
           $entry_id = implode( ",", $param );
           $querystr = "
                SELECT $wpdb->weforms_entries.id
                FROM $wpdb->weforms_entries
                WHERE $wpdb->weforms_entries.ID  IN ( $entry_id )
            ";
        } else {
            $entry_id = (int) $param;
            $querystr = "
                SELECT $wpdb->weforms_entries.id
                FROM $wpdb->weforms_entries
                WHERE $wpdb->weforms_entries.ID = $entry_id
            ";
        }

        $result = $wpdb->get_results( $querystr );

        if ( empty( $result ) ) {
            return false;
        } else {
            return true;
        }

        return true;
    }

    /**
     * Check form exist or not
     *
     * @since 1.4.2
     *
     * @param string $param
     * @param WP_REST_Request $request
     * @param string $key
     *
     * @return boolean
     */
    public function is_form_exists( $param, $request, $key ) {
        global $wpdb;

        $form_id = (int) $param;

        $querystr = "
            SELECT $wpdb->posts.id
            FROM $wpdb->posts
            WHERE $wpdb->posts.ID = $form_id
            AND $wpdb->posts.post_type = 'wpuf_contact_form'
        ";

        $result = $wpdb->get_var( $querystr );

        if( is_null( $result ) ) {
            return false;
        } else {
            return true;
        }

        $form = weforms()->form->get( (int) $param );

        return (bool) $form->id;
    }

    /**
     * Retrieves a collection of Entries.
     *
     * @since  1.3.9
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     **/
    public function get_items( $request ) {
        $form_id      = isset( $request['id'] ) ? intval( $request['id'] ) : 0;
        $status       = isset( $request['status'] ) ? $request['status'] : 'publish';
        $current_page = 1;
        $per_page     = 1;
        $offset       = ( $current_page - 1 ) * $per_page;

        if ( ! $form_id ) {
             return new WP_Error( 'rest_invalid_data', __( 'Please provide a form id', 'weforms'), array( 'status' => 404 ) );
        }

        $entries = weforms_get_form_entries(
            $form_id, array(
                'number' => $per_page,
                'status' => $status,
            )
        );

        $columns       = weforms_get_entry_columns( $form_id );
        $total_entries = weforms_count_form_entries( $form_id, $status );

        array_map(
            function( $entry ) use ( $columns ) {
                    $entry_id = $entry->id;
                    $entry->fields = array();
                foreach ( $columns as $meta_key => $label ) {
                    $value                    = weforms_get_entry_meta( $entry_id, $meta_key, true );
                    $entry->fields[ $meta_key ] = str_replace( WeForms::$field_separator, ' ', $value );
                }
            }, $entries
        );

        $entries = apply_filters( 'weforms_get_entries', $entries, $form_id );

        $response = array(
            'columns'    => $columns,
            'entries'    => $entries,
            'form_title' => get_post_field( 'post_title', $form_id ),
            'pagination' => array(
                'total'    => $total_entries,
                'per_page' => $per_page,
                'pages'    => ceil( $total_entries / $per_page ),
                'current'  => $current_page
            ),
            'meta' => array(
                'total'      => weforms_count_form_entries( $form_id ),
                'totalTrash' => weforms_count_form_entries( $form_id, 'trash' ),
            ),
        );

        $max_pages= ceil( $total_entries / $per_page );

        $response = $this->prepare_response_for_collection( $response );
        $response = rest_ensure_response( $response );

        $response->header( 'X-WP-Total', (int) $total_entries );
        $response->header( 'X-WP-TotalPages', (int) $max_pages );

        return $response;
    }

    /**
     * Checks if a given request has access to read Entries.
     *
     * @since 1.4.2
     *
     * @param  WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_item_permissions_check( $request ) {
        if ( ! current_user_can( weforms_form_access_capability() ) ) {
            return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to get  entries','weforms' ), array( 'status' => rest_authorization_required_code() ) );
        }

        return true;
    }

    /**
     * Checks if a given request has access to read Entries.
     *
     * @since 1.4.2
     *
     * @param  WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_items_permissions_check( $request ) {
        if ( ! current_user_can( weforms_form_access_capability() ) ) {
            return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to get the list of entries','weforms' ), array( 'status' => rest_authorization_required_code() ) );
        }

        return true;
    }

    /**
     * Checks if a given request has access to create a Entries.
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
     */
    public function create_item_permissions_check( $request ) {
        return true;
    }

    /**
     * Checks if a given request has access to Update a Entries.
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
     */
    public function update_items_permissions_check( $request ) {
        if ( ! current_user_can( weforms_form_access_capability() ) ) {
            return new WP_Error( 'rest_cannot_delete', __( 'Sorry, you are not allowed to delete this entries.','weforms' ), array( 'status' => rest_authorization_required_code() ) );
        }

        return true;
    }

    /**
     * Checks if a given request has access to delete a form.
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
     */
    public function delete_items_permissions_check( $request ) {
        if ( ! current_user_can( weforms_form_access_capability() ) ) {
            return new WP_Error( 'rest_cannot_delete', __( 'Sorry, you are not allowed to delete this entries.','weforms' ), array( 'status' => rest_authorization_required_code() ) );
        }

        return true;
    }

    /**
     * Check Weforms Pro Exist
     *
     * @return boolean
     **/
    public function pro_active() {
        if( class_exists( 'WeForms_Pro' ) ){
            return true;
        }

        return false;
    }
}
