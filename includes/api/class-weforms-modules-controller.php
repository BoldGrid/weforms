<?php

/**
 * Modules  manager class
 *
 * @since 1.4.2
 */

class Weforms_Modules_Controller extends WP_REST_Controller {

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
    protected $base = 'modules';



    /**
     * Register all routes releated with forms
     *
     * @return void
     */
    public function register_routes() {

        if( $this->pro_active( ) ) {
            register_rest_route( $this->namespace, '/'. $this->base, array(
                    array(
                        'methods'             => WP_REST_Server::READABLE,
                        'callback'            => array( $this, 'get_items' ),
                        'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    ),
                )
            );

            register_rest_route( $this->namespace, '/'. $this->base . '/toggle', array(
                    array(
                        'methods'             => WP_REST_Server::EDITABLE,
                        'callback'            => array( $this, 'toggle_modules' ),
                        'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    ),
                )
            );
        }
    }

    /**
     * Retrieves a collection of Modules.
     *
     * @since  1.3.9
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     **/
    public function  get_items( $request ) {
        $modules = array(
            'all'    => weforms_pro_get_modules(),
            'active' => weforms_pro_get_active_modules()
        );

        $response = $this->prepare_response_for_collection( $modules);
        $response = rest_ensure_response( $response );

        return $response;
    }

    /**
     * Toggle Module  function
     *
     * @since 1.4.2
     *
     * @param  WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     **/
    public function toggle_modules( $request ) {
        $module = isset( $_POST['module'] ) ? sanitize_text_field( $_POST['module'] ) : '';
        $type   = isset( $_POST['type'] ) ? $_POST['type'] : '';

        if ( ! $module ) {
            return new WP_Error( 'rest_invalid_request_type', __( 'Invalid module provided', 'weforms' ), array('status' => 404 ) );
        }

        if ( ! in_array( $type, array( 'activate', 'deactivate' ) ) ) {
            return new WP_Error( 'rest_invalid_request_type', __( 'Invalid request type', 'weforms' ), array( 'status' => 404 ) );
        }

        if ( 'all' === $module ) {
            $modules = weforms_pro_get_modules();
        } else {
            $modules = array( $module => weforms_pro_get_module( $module ) );
        }

        if ( 'activate' == $type ) {
            foreach ($modules as $module_file => $data) {
                weforms_pro_activate_module( $module_file );
            }

            $message = __( 'Activated', 'weforms-pro' );
        } else {
            foreach ($modules as $module_file => $data) {
                weforms_pro_deactivate_module( $module_file );
            }

            $message = __( 'Deactivated', 'weforms-pro' );
        }

        $data  = array(
            'message' => $module . ' ' . $message,
            'code' => ''
        );

        $response = $this->prepare_response_for_collection( $data);
        $response = rest_ensure_response( $response );

        return $response;
    }


    /**
     * Checks if a given request has access to read moduels.
     *
     * @since 1.4.2
     *
     * @param  WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_items_permissions_check( $request ) {
        if ( ! current_user_can( weforms_form_access_capability() ) ) {
            return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to get the list of Modules','weforms' ), array( 'status' => rest_authorization_required_code() ) );
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


