<?php

/**
 * Settings  manager class
 *
 * @since 1.4.2
 */
class Weforms_Setting_Controller extends Weforms_REST_Controller {

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
    protected $rest_base = 'settings';

    /**
     * @var array
     */
    protected $defaults = [
        'email_gateway' => '',
        'gateways'      => '',
        'recaptcha'     => '',
        'gmap_api'      => '',
        'credit'        => '',
    ];

    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->rest_base, [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [ $this, 'get_items' ],
                'permission_callback' => [ $this, 'get_item_permissions_check' ],
                'args'                => [
                    'context' => $this->get_context_param( [ 'default' => 'view' ] ),
                ],
            ],
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'create_item' ],
                'permission_callback' => [ $this, 'create_item_permissions_check' ],
                'args'                => [
                    'credit' => [
                        'required'    => false,
                        'type'        => 'boolean',
                        'description' => __( 'Weforms Setting', 'weforms' ),
                    ],
                ],
            ],
        ] );
    }

    /**
     * Retrieves a collection of forms.
     *
     * @since  1.4.2
     *
     * @param WP_REST_Request $request full details about the request
     *
     * @return WP_REST_Response|WP_Error response object on success, or WP_Error object on failure
     **/
    public function get_items( $request ) {
        $settings = weforms_get_settings();

        // checking to prevent js error, will be removed in future
        if ( !isset( $settings['credit'] ) ) {
            $settings['credit'] = false;
        }

        if ( !isset( $settings['permission'] ) ) {
            $settings['permission'] = 'manage_options';
        }

        $settings = $this->prepare_response_for_collection( $settings );
        $response = rest_ensure_response( $settings );

        return $response;
    }

    /**
     * Creates a single form
     *
     * @param WP_REST_Request $request full details about the request
     *
     * @since 1.4.2
     *
     * @return WP_REST_Response|WP_Error response object on success, or WP_Error object on failure
     **/
    public function create_item( $request ) {
        $req_settings = $request->get_params();
        $settings     = get_option( 'weforms_settings' );

        foreach ( $settings as $key => $setting ) {
            if ( array_key_exists( $key, $req_settings ) ) {
                $settings[$key] = $req_settings[$key];
            }
        }

        $requires_wpuf_update = false;
        $wpuf_update_array    = [];

        update_option( 'weforms_settings', $settings );

        // wpuf settings sync
        if ( !empty( $settings['gmap_api'] ) ) {
            $requires_wpuf_update              = true;
            $wpuf_update_array['gmap_api_key'] = $settings['gmap_api'];
        }

        if ( !empty( $settings['recaptcha'] ) ) {
            $requires_wpuf_update                   = true;
            $wpuf_update_array['recaptcha_public']  = $settings['recaptcha']->key;
            $wpuf_update_array['recaptcha_private'] = $settings['recaptcha']->secret;
        }

        if ( !empty( $settings['no_conflict'] ) ) {
            $requires_wpuf_update              = true;
            $wpuf_update_array['no_conflict']  = $settings['no_conflict'];
        }

        if ( !empty( $settings['email_footer'] ) ) {
            $requires_wpuf_update              = true;
            $wpuf_update_array['email_footer'] = $settings['email_footer'];
        }

        if ( $requires_wpuf_update ) {
            $wpuf_settings = get_option( 'wpuf_general', [] );
            $wpuf_settings = array_merge( $wpuf_settings, $wpuf_update_array );

            update_option( 'wpuf_general', $wpuf_settings );
        }

        do_action( 'weforms_save_settings', $settings );

        $settings = apply_filters( 'weforms_after_save_settings', $settings );

        $request->set_param( 'context', 'edit' );

        $response = $this->prepare_response_for_collection( $settings, $request );
        $response = rest_ensure_response( $response );

        return $response;
    }
}
