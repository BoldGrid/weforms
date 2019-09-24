<?php

/**
 * Entry  manager class
 *
 * @since 1.4.2
 */

class Weforms_Entry_Controller extends Weforms_REST_Controller {

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
                    'permission_callback' => array( $this, 'delete_item_permissions_check' ),
                    'args'                => array(
                        'entry_id'  => array(
                            'required'          => true,
                            'type'              => 'integer',
                            'sanitize_callback' => 'absint',
                            'description'       => __( 'Entry id', 'weforms' ),
                            'validate_callback' => array( $this, 'is_entry_exists' ),
                        ),
                        'force' => array(
                            'type'        => 'boolean',
                            'default'     => false,
                            'description' => __( 'Whether to bypass trash and force deletion.', 'weforms' ),
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
                    'permission_callback' => array( $this, 'delete_item_permissions_check' ),
                    'args'                => array(
                        'entry_id'  => array(
                            'required'          => true,
                            'type'              => 'object',
                            'description'       => __( 'Entry id', 'weforms' ),
                            'validate_callback' => array( $this, 'is_entry_exists' ),
                        ),
                        'force' => array(
                            'type'        => 'boolean',
                            'default'     => false,
                            'description' => __( 'Whether to bypass trash and force deletion.', 'weforms' ),
                        ),
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
                    'permission_callback' => array( $this, 'update_item_permissions_check' ),
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
                    'permission_callback' => array( $this, 'delete_item_permissions_check' ),
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
            return new WP_Error( 'rest_weforms_invalid_entry',__( 'No entry ids provided!','weforms' ),array( 'status' => 404 ) );
        }

        foreach ( $entry_ids as $entry_id ) {
            $status = weforms_change_entry_status( $entry_id, 'publish' );

            if( $status ) {
                $response['message'][$entry_id] = __( 'Entry Restore successfully ', 'weforms' );
            } else {
                $response['message'][$entry_id] = __( 'Entry Restore failed ', 'weforms' );
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
        $response['message']        = __( 'Entry Restore Successfully  successfully ', 'weforms' );
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
        $force     = (bool) $request['force'];

        if ( ! $entry_ids ) {
            return new WP_Error('rest_weforms_invalid_entry',__( 'No entry ids provided!', 'weforms' ));
        }

        $response = array();

        foreach ( $entry_ids as $entry_id ) {

            if ( $force ) {

                $status =  weforms_delete_entry( $entry_id );

                if( $status ) {
                    $response['message'][$entry_id] = __( 'Entry Deleted successfully ', 'weforms' );
                } else {
                    $response['message'][$entry_id] = __( 'Entry Not found ', 'weforms' );
                }
            } else {
                $status = weforms_change_entry_status( $entry_id, 'trash' );

                if( $status ) {
                    $response['message'][$entry_id] = __( 'Entry Move To Trash successfully ', 'weforms' );
                } else {
                    $response['message'][$entry_id] = __( 'Entry Not found ', 'weforms' );
                }
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
        $entry_id = $request['entry_id'];
        $force    = (bool) $request['force'];

        if ( $force ) {
            weforms_delete_entry( $entry_id );

            $entry_array['id']              = $entry_id;
            $entry_array['message']         = __( 'Entry Deleted successfully', 'weforms' );
            $entry_array['data']['status']  = 200;
        } else {
            $status = weforms_change_entry_status( $entry_id, 'trash' );

            $entry_array['id']              = $entry_id;
            $entry_array['message']         = __( 'Entry Move To Trash successfully', 'weforms' );
            $entry_array['data']['status']  = 200;
        }

        $response = $this->prepare_response_for_collection( $entry_array, $request );
        $response = rest_ensure_response( $response );

        return $response;
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
}
