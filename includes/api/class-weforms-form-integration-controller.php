<?php

/**
 * Settings  manager class
 *
 * @since 1.4.2
 */
class Weforms_Form_Integration_Controller extends Weforms_REST_Controller {

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
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/integrations',
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
                    'callback'            => [ $this, 'get_item_integrations' ],
                    'args'                => [
                            'context' => $this->get_context_param( [ 'default' => 'view' ] ),
                    ],
                    'permission_callback' => [ $this, 'get_item_permissions_check' ],
                ],
                [
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => [ $this, 'update_item_integrations' ],
                    'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
                    'permission_callback' => [ $this, 'get_item_permissions_check' ],
                ],
            ]
          );
    }

    /**
     * get item integrations
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response|WP_Error response object on success, or WP_Error object on failure
     */
    public function get_item_integrations( $request ) {
        $form_id = $request->get_param( 'form_id' );
        $form    = weforms()->form->get( $form_id );
        $data    = $form->get_integrations();

        $response  = $this->prepare_response_for_collection( $data, $request );
        $response  = rest_ensure_response( $response );
        $response->header( 'X-WP-Total', (int) count( $data['integrations'] )  );

        return $response;
    }

    /**
     * [update_item_integrations description]
     *
     * @param [type] $request [description]
     *
     * @return [type] [description]
     */
    public function update_item_integrations( $request ) {
        $wpuf_form_id      = $request->get_param( 'form_id' );
        $integrations      = $request->get_param( 'integrations' );

        $integration_list = weforms()->integrations->get_integration_js_settings();

        $form             = weforms()->form->get( $wpuf_form_id );
        $form_integration = $form->get_integrations();
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

        $data = [
            'form_id'           => $wpuf_form_id,
            'integrations'      => $integrations,
        ];

        $new_form_integrations = array_merge( $data['integrations'], array_diff_key( $form->get_integrations(), $data['integrations'] ) );

        update_post_meta( $data['form_id'], 'integrations', $new_form_integrations );

        $response_data = [
            'id'            => $form->data->ID,
            'integrations'  => $form->get_integrations(),
        ];

        $response = rest_ensure_response( $response_data );
        $response->set_status( 201 );
        $response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $form->id ) ) );

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
                'integrations' => [
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
