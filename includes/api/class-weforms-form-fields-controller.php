<?php

/**
 * Settings  manager class
 *
 * @since 1.4.2
 */
class Weforms_Form_Fields_Controller extends Weforms_REST_Controller {

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
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/fields', [
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
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => [ $this, 'update_item_fields' ],
                    'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
                    'permission_callback' => [ $this, 'get_item_permissions_check' ],
                ],
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_item_fields' ],
                    'args'                => [
                            'context' => $this->get_context_param( [ 'default' => 'view' ] ),
                    ],
                    'permission_callback' => [ $this, 'get_item_permissions_check' ],
                ],

                [
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => [ $this, 'delete_item_fields' ],
                    'args'                => [
                        'field_id' => [
                            'description'       => __( '', 'weforms' ),
                            'type'              => 'array',
                            'validate_callback' => [ $this, 'is_field_exists' ],
                            'required'          => true,
                        ],
                    ],
                    'permission_callback' => [ $this, 'get_item_permissions_check' ],
                ],
            ]
          );
    }

    /**
     * [update_item_settingss description]
     *
     * @param [type] $request [description]
     *
     * @return [type] [description]
     */
    public function update_item_fields( $request ) {
        $form_id     = $request->get_param( 'form_id' );
        $form_fields = $request->get_param( 'fields' );

        $data = [
            'form_id'           => $form_id,
            'form_fields'       => $form_fields,
        ];

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

        $form = weforms()->form->get( $form_id );

        $response_data = [
            'id'            => $form->data->ID,
            'fields'        => $form->get_fields(),
        ];

        $request->set_param( 'context', 'edit' );
        $response = $this->prepare_item_for_response( $response_data, $request );
        $response = rest_ensure_response( $response );
        $response->set_status( 201 );
        $response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $form->id ) ) );

        return $response;
    }

    public function get_item_fields( $request ) {
        $form_id = $request->get_param( 'form_id' );
        $form    = weforms()->form->get( $form_id );
        $data    = $form->get_fields();

        $response = $this->prepare_response_for_collection( $data, $request );
        $response = rest_ensure_response( $response );
        $response->header( 'X-WP-Total', (int) count( $data )  );

        return $response;
    }

    public function delete_item_fields( $request ) {
        $form_id   = $request->get_param( 'form_id' );
        $field_ids = $request->get_param( 'field_id' );

        if ( empty( $field_ids ) ) {
            return new WP_Error( 'error', __( 'Fields id not provided', 'weforms' ), [ 'status' => 404 ] );
        }

        $fields = get_children( [
            'post_parent' => $form_id,
            'post_status' => 'publish',
            'post_type'   => 'wpuf_input',
            'numberposts' => '-1',
            'orderby'     => 'menu_order',
            'order'       => 'ASC',
            'fields'      => 'ids',
        ] );

        $deleted_fields = [];

        foreach ( $field_ids as $field_id ) {
            if ( in_array( $field_id, $fields ) ) {
                $deleted_fields[] = wp_delete_post( $field_id, true );
            }
        }

        if ( empty( $deleted_fields )  ) {
            return new WP_Error( 'error', __( 'Fields not exist or deleted before.', 'weforms' ), [ 'status' => 404 ] );
        }

        $data  = [
            'message' => __( 'Fields  deleted successfully ', 'weforms' ),
        ];

        $response = $this->prepare_response_for_collection( $data, $request );
        $response = rest_ensure_response( $response );

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
                'fields' => [
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
