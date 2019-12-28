<?php

abstract class Weforms_REST_Controller extends WP_REST_Controller {

    public function get_item_permissions_check( $request ) {
        return $this->get_items_permissions_check( $request );
    }

    public function get_items_permissions_check( $request ) {
        if ( !current_user_can( weforms_form_access_capability() ) ) {
            return new WP_Error( 'rest_weforms_forbidden_context', __( 'Sorry, you are not allowed', 'weforms' ), [ 'status' => rest_authorization_required_code() ] );
        }

        return true;
    }

    /**
     * Check if a given request has access to create items
     *
     * @param WP_REST_Request $request full data about the request
     *
     * @return WP_Error|bool
     */
    public function create_item_permissions_check( $request ) {
        return $this->get_items_permissions_check( $request );
    }

    /**
     * Check if a given request has access to update a specific item
     *
     * @param WP_REST_Request $request full data about the request
     *
     * @return WP_Error|bool
     */
    public function update_item_permissions_check( $request ) {
        return $this->get_items_permissions_check( $request );
    }

    /**
     * Check if a given request has access to delete a specific item
     *
     * @param WP_REST_Request $request full data about the request
     *
     * @return WP_Error|bool
     */
    public function delete_item_permissions_check( $request ) {
        return $this->create_item_permissions_check( $request );
    }

    /**
     * Prepare a single user output for response
     *
     * @param object          $item
     * @param WP_REST_Request $request           request object
     * @param array           $additional_fields (optional)
     *
     * @return WP_REST_Response $response response data
     */
    public function prepare_item_for_response( $item, $request, $additional_fields = [] ) {
        $response = rest_ensure_response( $item );
        $response = $this->add_links( $response, $item );

        return $response;
    }

    /**
     * Adds multiple links to the response.
     *
     * @since 1.4.2
     *
     * @param object $response
     * @param object $item
     *
     * @return object $response
     */
    protected function add_links( $response, $item ) {
        $response->data['_links'] = $this->prepare_links( $item );

        return $response;
    }

    /**
     * Prepare links for the request.
     *
     *  @since 1.4.2
     *
     * @param object $item
     *
     * @return array links for the given user
     */
    protected function prepare_links( $item ) {
        $links = [
            'self' => [
                'href' => rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $item['id'] ) ),
            ],
            'collection' => [
                'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
            ],
        ];

        return $links;
    }

    /**
     * Format item's collection for response
     *
     * @since 1.4.2
     *
     * @param object $response
     * @param object $request
     * @param array  $items
     * @param int    $total_items
     *
     * @return object
     */
    public function format_collection_response( $response, $request, $total_items ) {
        if ( $total_items === 0 ) {
            return $response;
        }

        // Store pagation values for headers then unset for count query.
        $per_page = (int) ( !empty( $request['per_page'] ) ? $request['per_page'] : 20 );
        $page     = (int) ( !empty( $request['page'] ) ? $request['page'] : 1 );

        $response->header( 'X-WP-Total', (int) $total_items );

        $max_pages = ceil( $total_items / $per_page );

        $response->header( 'X-WP-TotalPages', (int) $max_pages );
        $base = add_query_arg( $request->get_query_params(), rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );

        if ( $page > 1 ) {
            $prev_page = $page - 1;

            if ( $prev_page > $max_pages ) {
                $prev_page = $max_pages;
            }
            $prev_link = add_query_arg( 'page', $prev_page, $base );
            $response->link_header( 'prev', $prev_link );
        }

        if ( $max_pages > $page ) {
            $next_page = $page + 1;
            $next_link = add_query_arg( 'page', $next_page, $base );
            $response->link_header( 'next', $next_link );
        }

        return $response;
    }

    /**
     * Check form exist or not
     *
     * @since 1.4.2
     *
     * @param string          $param
     * @param WP_REST_Request $request
     * @param string          $key
     *
     * @return bool
     */
    public function is_form_exists( $param, $request, $key ) {
        global $wpdb;

        $form_id = (int) $request['form_id'];

        $querystr = "
            SELECT $wpdb->posts.id
            FROM $wpdb->posts
            WHERE $wpdb->posts.ID = $form_id
            AND $wpdb->posts.post_type = 'wpuf_contact_form'
         ";

        $result = $wpdb->get_var( $querystr );

        if ( empty( $result ) ) {
            return new WP_Error( 'rest_weforms_form', __( 'Form Not Exist', 'weforms' ), [ 'status' => 404 ] );
        } else {
            return true;
        }
    }

    /**
     * Check Entries exist or not
     *
     * @since 1.4.2
     *
     * @param string          $param
     * @param WP_REST_Request $request
     * @param string          $key
     *
     * @return bool
     */
    public function is_entry_exists( $param, $request, $key ) {
        global $wpdb;

        if ( is_array( $request['entry_id'] ) ) {
            $entry_id = implode( ',', $request['entry_id'] );
            $querystr = "
                SELECT $wpdb->weforms_entries.id
                FROM $wpdb->weforms_entries
                WHERE $wpdb->weforms_entries.ID  IN ( $entry_id )
            ";
        } else {
            $entry_id = (int) $request['entry_id'];
            $querystr = "
                SELECT $wpdb->weforms_entries.id
                FROM $wpdb->weforms_entries
                WHERE $wpdb->weforms_entries.ID = $entry_id
            ";
        }

        $result = $wpdb->get_results( $querystr );

        if ( empty( $result ) ) {
            return false;
        } else {
            return true;
        }

        return true;
    }
}
