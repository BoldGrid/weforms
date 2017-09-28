<?php

/**
 * Manage Import Export
 *
 * @since 1.1.0
 *
 * @package weForms
 */
class WeForms_Admin_Tools {

    /**
     * Import json file into database
     * @param  array $file
     * @return boolean
     */
    public static function import_json_file( $file ) {

        $encode_data = file_get_contents( $file );
        $options     = json_decode( $encode_data, true );

        foreach ( $options as $key => $value ) {

            $generate_post = array(
                'post_title'     => $value['post_data']['post_title'],
                'post_status'    => $value['post_data']['post_status'],
                'post_type'      => $value['post_data']['post_type'],
                'ping_status'    => $value['post_data']['ping_status'],
                'comment_status' => $value['post_data']['comment_status']
            );

            $post_id = wp_insert_post( $generate_post, true );

            if ( $post_id && !is_wp_error( $post_id ) ) {

                foreach ( $value['meta_data']['fields'] as $order => $field ) {
                    weforms_insert_form_field( $post_id, $field, false, $order );
                }

                update_post_meta( $post_id, 'wpuf_form_settings', $value['meta_data']['settings'] );
                update_post_meta( $post_id, 'notifications', $value['meta_data']['notifications'] );
            }
        }

        return true;
    }

    /**
     * Export into json file
     *
     * @param  string $post_type
     * @param  array  $post_ids
     */
    public static function export_to_json( $post_ids = array( ) ) {

        $formatted_data = array();
        $ids            = array();
        $blogname       = strtolower( str_replace( " ", "-", get_option( 'blogname' ) ) );
        $json_name      = $blogname . "-weforms-" . time(); // Namming the filename will be generated.

        if ( ! empty( $post_ids ) ) {
            foreach ( $post_ids as $key => $value ) {
                array_push( $ids, $value );
            }
        }

        $args = array(
            'post_status' => 'publish',
            'post_type'   => 'wpuf_contact_form',
            'post__in'    => (!empty( $ids ) ) ? $ids : ''
        );

        $query = new WP_Query( $args );

        foreach ( $query->posts as $post ) {
            $postdata = get_object_vars( $post );
            unset( $postdata['ID'] );

            $form = weforms()->form->get( $post );

            $data = array(
                'post_data' => $postdata,
                'meta_data' => array(
                    'fields'        => $form->get_fields(),
                    'settings'      => $form->get_settings(),
                    'notifications' => $form->get_notifications()
                )
            );

            array_push( $formatted_data, $data );
        }

        $json_file = json_encode( $formatted_data ); // Encode data into json data

        error_reporting(0);

        if ( ob_get_contents() ) {
            ob_clean();
        }
       
        header( "Content-Type: text/json; charset=" . get_option( 'blog_charset' ) );
        header( "Content-Disposition: attachment; filename=$json_name.json" );

        echo $json_file;

        exit();
    }

    /**
     * Formetted meta key value
     *
     * @param  array $array
     * @return array
     */
    function formetted_meta_key_value( $array ) {
        $result = array( );

        foreach ( $array as $key => $val ) {
            $result[$key] = $val[0];
        }

        return $result;
    }

}
