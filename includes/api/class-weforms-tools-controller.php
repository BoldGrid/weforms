<?php

/**
 * Tools  manager class
 *
 * @since 1.4.2
 */

class Weforms_Tools_Controller extends WP_REST_Controller {

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
    protected $base = 'tools';

    /**
     * Register all routes releated with forms
     *
     * @return void
     */
    public function register_routes() {

        register_rest_route( $this->namespace, '/' . $this->base, array(
            array(
                'methods'              => WP_REST_Server::READABLE,
                'callback'             => array( $this, 'get_items' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' )
            ),
            array(
                'methods'              => WP_REST_Server::CREATABLE,
                'callback'             => array( $this, 'update_item' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),
                'args' => array(
                    'settings' => array(
                        'required'    =>  true,
                        'type'        => 'object',
                        'description' => __( 'Weforms Setting', 'weforms' ),
                    )
                )

            ),
        ) );

    }

    public function get_items( $request ) {
        $response = array();
        $response = $this->prepare_response_for_collection( $response );
        $response = rest_ensure_response( $response );

        return $response;
    }

    public function update_item( $request ) {
        $response = array();
        $response = $this->prepare_response_for_collection( $response );
        $response = rest_ensure_response( $response );

        return $response;
    }

    public function get_item_permissions_check( $request ) {
        if ( ! current_user_can( weforms_form_access_capability() ) ) {
            return false;
        }

        return true;
    }

    public function create_item_permissions_check( $request ) {
        if ( ! current_user_can( weforms_form_access_capability() ) ) {
            return false;
        }

        return true;
    }
}
