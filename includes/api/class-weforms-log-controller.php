<?php

/**
 * Log  manager class
 *
 * @since 1.4.2
 */

class Weforms_Log_Controller extends WP_REST_Controller {

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
    protected $rest_base = 'logs';



    /**
     * Register all routes releated with forms
     *
     * @return void
     */
    public function register_routes() {

        register_rest_route( $this->namespace, '/'. $this->rest_base, array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_items' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                ),
                array(
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => array( $this, 'delete_item' ),
                    'permission_callback' => array( $this, 'delete_item_permissions_check' ),
                )
            )
        );

    }

    /**
     * Retrieves a collection of log.
     *
     * @since  1.3.9
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     **/
    public function  get_items( $request ) {
        $file = weforms_log_file_path();

        if ( ! file_exists( $file ) ) {
            return new WP_Error( 'rest_invalid_data', __( 'file not exist', 'weforms') , array( 'status' => 404 ) );
        }

        $data = file_get_contents( $file );
        $data = explode( "\n", $data );
        $data = array_reverse( $data );
        $data = array_filter( $data );

        if ( empty( $data ) ) {
            return new WP_Error( 'rest_invalid_data', __( 'data not exist', 'weforms') , array( 'status' => 404 ) );
        }

        $logs = array();

        foreach ( $data as $key => $row ) {
            preg_match( '/\[(?<time>.+?)\]\[(?<type>.+?)\](?<message>.+)/im', $row, $log );

            if ( empty( $log['message'] ) ) {
                $log = array();
                $log['message'] = ! empty( $log['message'] ) ? $log['message'] : $row;
            }

            if ( ! empty( $log['time'] ) ) {
                $human_time = human_time_diff( strtotime( $log['time'] ) , current_time( 'timestamp' ) );
                $log['time'] = $human_time ? $human_time . ' ' . __( 'ago', 'weforms' ) : $log['time'];
            }

            $logs[] = $log;
        }

        $data     = array();
        $response = $this->prepare_response_for_collection( $logs, $request );
        $response = rest_ensure_response( $response );

        return $response;
    }


    /**
     * Delete Log file
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request
     *
     * @return json
     **/
    function delete_item( $request ) {
        $file = weforms_log_file_path();

        if ( ! file_exists( $file ) ) {
            return new WP_Error( 'rest_invalid_data', __( 'file not exist', 'weforms') , array( 'status' => 404 ) );
        }

        @unlink( $file );

        $data     = array( 'message' => 'log file deleted' );
        $response = $this->prepare_response_for_collection( $data, $request );
        $response = rest_ensure_response( $response );

        return $response;
    }

    /**
     * Checks if a given request has access to read log.
     *
     * @since 1.4.2
     *
     * @param  WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_items_permissions_check( $request ) {
        if ( ! current_user_can( weforms_form_access_capability() ) ) {
            return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to get the list of forms log','weforms' ), array( 'status' => rest_authorization_required_code() ) );
        }

        return true;
    }

    /**
     * Checks if a given request has access to delete a log.
     *
     * @since 1.4.2
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
     */
    public function delete_item_permissions_check( $request ) {
        if ( ! current_user_can( weforms_form_access_capability() ) ) {
            return new WP_Error( 'rest_cannot_delete', __( 'Sorry, you are not allowed to delete this forms log.','weforms' ), array( 'status' => rest_authorization_required_code() ) );
        }

        return true;
    }

}


