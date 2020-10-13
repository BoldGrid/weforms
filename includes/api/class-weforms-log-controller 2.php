<?php

/**
 * Log  manager class
 *
 * @since 1.4.2
 */
class Weforms_Log_Controller extends Weforms_REST_Controller {

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
        register_rest_route( $this->namespace, '/' . $this->rest_base, [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_items' ],
                    'permission_callback' => [ $this, 'get_items_permissions_check' ],
                    'args'                => [
                        'context' => $this->get_context_param( [ 'default' => 'view' ] ),
                    ],
                ],
                [
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => [ $this, 'delete_item' ],
                    'permission_callback' => [ $this, 'delete_item_permissions_check' ],
                ],
            ]
          );
    }

    /**
     * Retrieves a collection of log.
     *
     * @since  1.3.9
     *
     * @param WP_REST_Request $request full details about the request
     *
     * @return WP_REST_Response|WP_Error response object on success, or WP_Error object on failure
     **/
    public function get_items( $request ) {
        $file = weforms_log_file_path();

        if ( !file_exists( $file ) ) {
            return new WP_Error( 'rest_weforms_invalid_data', __( 'file not exist', 'weforms' ), [ 'status' => 404 ] );
        }

        $data = file_get_contents( $file );
        $data = explode( "\n", $data );
        $data = array_reverse( $data );
        $data = array_filter( $data );

        if ( empty( $data ) ) {
            return new WP_Error( 'rest_weforms_invalid_data', __( 'data not exist', 'weforms' ), [ 'status' => 404 ] );
        }

        $logs = [];

        foreach ( $data as $key => $row ) {
            preg_match( '/\[(?<time>.+?)\]\[(?<type>.+?)\](?<message>.+)/im', $row, $log );

            if ( empty( $log['message'] ) ) {
                $log            = [];
                $log['message'] = !empty( $log['message'] ) ? $log['message'] : $row;
            }

            if ( !empty( $log['time'] ) ) {
                $human_time  = human_time_diff( strtotime( $log['time'] ), current_time( 'timestamp' ) );
                $log['time'] = $human_time ? $human_time . ' ' . __( 'ago', 'weforms' ) : $log['time'];
            }

            $logs[] = $log;
        }

        $data     = [];
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
    public function delete_item( $request ) {
        $file = weforms_log_file_path();

        if ( !file_exists( $file ) ) {
            return new WP_Error( 'rest_weforms_invalid_data', __( 'file not exist', 'weforms' ), [ 'status' => 404 ] );
        }

        @unlink( $file );

        $data     = [ 'message' => 'log file deleted' ];
        $response = $this->prepare_response_for_collection( $data, $request );
        $response = rest_ensure_response( $response );

        return $response;
    }
}
