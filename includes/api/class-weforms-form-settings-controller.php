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
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/settings', array(
            'args' => array(
                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'required'          => true,
                ),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_item_settings' ),
                'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form_id>[\d]+)/settings', array(
            'args' => array(
                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'required'          => true,
                ),
            ),
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_item_settings' ),
                'args'     => array(
                    'context' => $this->get_context_param( [ 'default' => 'view' ] )
                ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
            ),
        ) );
    }

    public function get_item_settings( $request ) {

    }

    public function update_item_settings( $request ) {

    }

    /**
     * Get the Form's schema, conforming to JSON Schema
     *
     * @return array
     */
    public function get_item_schema() {
        $schema = array(
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => 'forms',
            'type'       => 'object',
            'properties' => array(
                'form_id' => array(
                    'description'       => __( 'Unique identifier for the object', 'weforms' ),
                    'type'              => 'integer',
                    'sanitize_callback' => 'absint',
                    'validate_callback' => array( $this, 'is_form_exists' ),
                    'context'           => array( 'embed', 'view', 'edit' ),
                    'required'          => true,
                    'readonly'          => true,
                ),
                "settings" => array(
                    'description' => __( '', 'weforms' ),
                    'type'        => 'object',
                    'context'     => [ 'edit' ,'view'],
                    'required'    => false,
                ),
            ),
        );

        return $schema;
    }
}
