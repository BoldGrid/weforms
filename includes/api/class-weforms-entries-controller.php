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
    protected $rest_base = 'entries';

    /**
     * Register all routes releated with forms
     *
     * @return void
     */
    public function register_routes() {

        register_rest_route(
            $this->namespace,
            '/'. $this->rest_base . '/(?P<entry_id>[\d]+)',
            array(
                array(
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => array( $this, 'delete_items' ),
                    'permission_callback' => array( $this, 'delete_items_permissions_check' ),
                    'args'                => array(
                        'entry_id'  => array(
                            'required'          => true,
                            'type'              => 'integer',
                            'sanitize_callback' => 'absint',
                            'description'       => __( 'Entry id', 'weforms' ),
                            'validate_callback' => array( $this, 'is_entry_exists' ),
                        ),
                    )
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            '/'. $this->rest_base,
            array(
                array(
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => array( $this, 'bulk_delete_items' ),
                    'permission_callback' => array( $this, 'delete_items_permissions_check' ),
                    'args'                => array(
                        'entry_id'  => array(
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
            '/'. $this->rest_base . '/(?P<entry_id>[\d]+)/restore',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'restore_items' ),
                    'permission_callback' => array( $this, 'update_items_permissions_check' ),
                    'args'                => array(
                        'entry_id'  => array(
                            'required'          => true,
                            'type'              => 'integer',
                            'sanitize_callback' => 'absint',
                            'description'       => __( 'Entry id', 'weforms' ),
                            'validate_callback' => array( $this, 'is_restore_exists' ),
                        )
                    )
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            '/'. $this->rest_base . '/restore',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'bulk_restore_items' ),
                    'permission_callback' => array( $this, 'delete_items_permissions_check' ),
                    'args'                => array(
                        'entry_id'  => array(
                            'required'          => true,
                            'type'              => 'object',
                            'description'       => __( 'Entry id', 'weforms' ),
                            'validate_callback' => array( $this, 'is_restore_exists' ),
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
        $entry_ids = isset( $request['entry_id'] ) ? array_map( 'absint', $request['entry_id'] ) : array();

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
     * Bulk Delele Item
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     **/
    public function bulk_delete_items( $request ) {
        $entry_ids = isset( $request['entry_id'] ) ? array_map( 'absint', $request['entry_id'] ) : array();
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

        // if( is_array( $param ) ) {
        if( is_array( $request['entry_id'] ) ) {
           $entry_id = implode( ",", $request['entry_id'] );
           $querystr = "
                SELECT $wpdb->weforms_entries.id
                FROM $wpdb->weforms_entries
                WHERE $wpdb->weforms_entries.ID  IN ( $entry_id )
            ";
        } else {
            // $entry_id = (int) $param;
            $entry_id = (int) $request['entry_id'];
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
     * Check  is entry in trash exist or not
     *
     * @since 1.4.2
     *
     * @param string $param
     * @param WP_REST_Request $request
     * @param string $key
     *
     * @return boolean
     */
    public function is_restore_exists( $param, $request, $key ) {
        global $wpdb;

        // if( is_array( $param ) ) {
        if( is_array( $request['entry_id'] ) ) {
           $entry_id = implode( ",", $param );
           $querystr = "
                SELECT $wpdb->weforms_entries.id
                FROM $wpdb->weforms_entries
                WHERE $wpdb->weforms_entries.ID  IN ( $entry_id )
                AND $wpdb->weforms_entries.status = \"trash\"
            ";
        } else {
            // $entry_id = (int) $param;
            $entry_id = (int) $request['entry_id'];
            $querystr = "
                SELECT $wpdb->weforms_entries.id
                FROM $wpdb->weforms_entries
                WHERE $wpdb->weforms_entries.ID = $entry_id
                AND $wpdb->weforms_entries.status = \"trash\"
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
     * Get the Form entries schema, conforming to JSON Schema
     *
     * @since 1.4.2
     *
     * @return array
     */
    public function get_item_schema() {
        $schema = array(
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => 'entries',
            'type'       => 'object',
            'properties' => array(
                'entry_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_entry_exists' ),
                    'context'     => array( 'embed', 'view', 'edit' ),
                    'required'          => true,
                    'readonly'    => true,
                ),
            ),
        );

        return $schema;
    }

    /**
     * prepare_item_for_response
     * @param  [type] $form    [description]
     * @param  [type] $request [description]
     * @return [type]          [description]
     */
    public function prepare_item_for_response( $form, $request ) {
        $response = rest_ensure_response( $form );
        $response = $this->add_links( $response, $form );
        return $response;
    }

    /**
     * Adds multiple links to the response.
     *
     * @since 1.4.2
     *
     * @param   object $response
     * @param   object $item
     *
     * @return  object $response
     */
    protected function add_links( $response, $item ) {
        $response->data['_links'] = $this->prepare_links( $item );

        return $response;
    }

    /**
     * Prepare links for the request.
     *
     *  @since 1.4.2
     *
     * @param  object $item
     *
     * @return array Links for the given user.
     */
    protected function prepare_links( $item ) {
        $links = [
            'self' => [
                'href' => rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $item['id'] ) ),
            ],
            'collection' => [
                'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
            ]
        ];

        return $links;
    }
}
