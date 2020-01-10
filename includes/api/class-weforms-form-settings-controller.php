<?php

/**
 * Settings  manager class
 *
 * @since 1.4.2
 */
class Weforms_Form_Setting_Controller extends Weforms_REST_Controller {

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
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/settings', [
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
                'callback'            => [ $this, 'update_item_settings' ],
                'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
                'permission_callback' => [ $this, 'get_item_permissions_check' ],
            ],
        ] );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/settings', [
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
                'callback'            => [ $this, 'get_item_settings' ],
                'args'                => [
                    'context' => $this->get_context_param( [ 'default' => 'view' ] ),
                ],
                'permission_callback' => [ $this, 'get_item_permissions_check' ],
            ],
        ] );
    }

    public function get_item_settings( $request ) {
        $form_id = $request->get_param( 'form_id' );
        $form    = weforms()->form->get( $form_id );
        $data    = $form->get_settings();

        $response = $this->prepare_response_for_collection( $data, $request );
        $response = rest_ensure_response( $response );
        $response->header( 'X-WP-Total', (int) count( $data ) );

        return $response;
    }

    public function update_item_settings( $request ) {
        $form_id       = $request->get_param( 'form_id' );
        $settings      = $request['settings'];
        $form          = weforms()->form->get( $form_id );
        $form_settings = $form->get_settings();

        if ( isset( $settings ) && !empty( $settings ) ) {
            $new_form_settings = array_merge( $settings, array_diff_key( $form->get_settings(), $settings ) );
            update_post_meta( $form_id, 'wpuf_form_settings', $new_form_settings );
        }

        $response_data = [
            'id'       => $form->data->ID,
            'settings' => $form->get_settings(),
        ];

        $response = rest_ensure_response( $response_data );
        $response->set_status( 201 );

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
                'settings' => [
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
