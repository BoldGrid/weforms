<?php

/**
 * Settings  manager class
 *
 * @since 1.4.2
 */
class Weforms_Form_Notification_Controller extends Weforms_REST_Controller {

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

    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/notifications',
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
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_item_notification' ],
                    'args'                => [
                            'context' => $this->get_context_param( [ 'default' => 'view' ] ),
                    ],
                    'permission_callback' => [ $this, 'get_item_permissions_check' ],
                ],
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [ $this, 'add_item_notification' ],
                    'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
                    'permission_callback' => [ $this, 'get_item_permissions_check' ],
                ],
                [
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => [ $this, 'update_item_notification' ],
                    'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
                    'permission_callback' => [ $this, 'get_item_permissions_check' ],
                ],
            ]
          );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/notifications',
            [
                'args' => [
                    'form_id' => [
                        'description'       => __( 'Unique identifier for the object', 'weforms' ),
                        'type'              => 'integer',
                        'sanitize_callback' => 'absint',
                        'validate_callback' => [ $this, 'is_form_exists' ],
                        'required'          => true,
                    ],
                    'index' => [
                        'description'       => __( 'Unique identifier for the object', 'weforms' ),
                        'type'              => 'array',
                        'items'             => [
                            'type' => 'integer',
                        ],
                        'required'          => true,
                    ],
                ],
                [
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => [ $this, 'delete_item_notification' ],
                    // 'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::DELETABLE ),
                    'permission_callback' => [ $this, 'delete_item_permissions_check' ],
                ],
            ]
          );
    }

    /**
     * get item notification
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error response object on success, or WP_Error object on failure
     */
    public function get_item_notification( $request ) {
        $form_id = $request->get_param( 'form_id' );
        $form    = weforms()->form->get( $form_id );
        $data    = $form->get_notifications();

        $response = $this->prepare_response_for_collection( $data, $request );
        $response = rest_ensure_response( $response );
        $response->header( 'X-WP-Total', (int) count( $data ) );

        return $response;
    }

    /**
     * add item notification
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error response object on success, or WP_Error object on failure
     */
    public function add_item_notification( $request ) {
        $form_id       = $request->get_param( 'form_id' );
        $notifications = $request->get_param( 'notifications' );

        $data = [
            'form_id'       => $form_id,
            'notifications' => $notifications,
        ];

        $form                  = weforms()->form->get( $form_id );
        $new_form_notification = array_merge( $form->get_notifications(), $data['notifications'] );

        update_post_meta( $data['form_id'], 'notifications', $new_form_notification );

        $form = weforms()->form->get( $form_id );

        $response_data = [
            'id'            => $form->data->ID,
            'notifications' => $form->get_notifications(),
        ];

        $response = rest_ensure_response( $response_data );
        $response->set_status( 201 );

        return $response;
    }

    /**
     * update item integrations
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error response object on success, or WP_Error object on failure
     */
    public function update_item_notification( $request ) {
        $wpuf_form_id  = $request->get_param( 'form_id' );
        $notifications = $request->get_param( 'notifications' );

        $data = [
            'form_id'       => $wpuf_form_id,
            'notifications' => $notifications,
        ];

        $form                   = weforms()->form->get( $wpuf_form_id );
        $existing_notifications = $form->get_notifications();

        foreach ( $existing_notifications as $key => $notification ) {
            if ( array_key_exists( $key, $data['notifications'] ) ) {
                $existing_notifications[ $key ] = $data['notifications'][$key];
            }
        }

        update_post_meta( $data['form_id'], 'notifications', $existing_notifications );

        $form = weforms()->form->get( $wpuf_form_id );

        $response_data = [
            'id'            => $form->data->ID,
            'notifications' => $form->get_notifications(),
        ];

        $response = rest_ensure_response( $response_data );
        $response->set_status( 201 );
        $response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $form->id ) ) );

        return $response;
    }

    /**
     * delete item notification
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error response object on success, or WP_Error object on failure
     */
    public function delete_item_notification( $request ) {
        $form_id           = $request->get_param( 'form_id' );
        $notification_ids  = $request->get_param( 'index' );
        $form              = weforms()->form->get( $form_id );
        $form_notification = $form->get_notifications();

        foreach ( $notification_ids as $notification_id ) {
            unset( $form_notification[ $notification_id ] );
        }

        update_post_meta( $form_id, 'notifications', array_values( $form_notification ) );

        $form = weforms()->form->get( $form_id );

        $data = [
            'id'            => $form->data->ID,
            'notifications' => $form->get_notifications(),
        ];

        $response = $this->prepare_response_for_collection( $data, $request );
        $response = rest_ensure_response( $response );
        $response->header( 'X-WP-Total', (int) count( $data['notifications'] )  );

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
                'notifications' => [
                    'description' => __( '', 'weforms' ),
                    'type'        => 'object',
                    'context'     => [ 'edit', 'view'],
                    'required'    => false,
                ],
            ],
        ];

        return $schema;
    }
}
