<?php

/**
 * Attachment Uploader class
 *
 * @since 1.1.0
 */
class WeForms_Ajax_Upload {

    public function __construct() {

        // let WPUF handle the upload if installed
        if ( class_exists( 'WPUF_Upload' ) ) {
            return;
        }

        add_action( 'wp_ajax_wpuf_upload_file', [$this, 'upload_file'] );
        add_action( 'wp_ajax_nopriv_wpuf_upload_file', [$this, 'upload_file'] );

        add_action( 'wp_ajax_wpuf_file_del', [$this, 'delete_file'] );
        add_action( 'wp_ajax_nopriv_wpuf_file_del', [$this, 'delete_file'] );
    }

    /**
     * Validate if it's coming from WordPress with a valid nonce
     *
     * @return void
     */
    function validate_nonce() {
        $nonce = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';

        if ( !wp_verify_nonce( $nonce, 'wpuf-upload-nonce' ) ) {
            die( 'error' );
        }
    }

    /**
     * Upload a file
     *
     * @param bool $image_only
     *
     * @return string
     */
    public function upload_file( $image_only = false ) {
        $this->validate_nonce();
        $nonce = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'wpuf-upload-nonce' ) ) {
            die( 'error' );
        }

        // a valid request will have a form ID
        $form_id = isset( $_POST['form_id'] ) ? intval( sanitize_text_field( wp_unslash( $_POST['form_id'] ) ) ) : false;

        if ( !$form_id ) {
            die( 'error' );
        }


        $file = isset( $_FILES['wpuf_file'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_FILES['wpuf_file'] ) ) : [];

        $upload = array(
             'name'     => isset( $file['name'] ) ? $file['name'] : '',
             'type'     => isset( $file['type'] ) ? $file['type'] : '',
             'tmp_name' => $_FILES['wpuf_file']['tmp_name'],
             'error'    => isset( $file['error'] ) ? $file['error'] : '',
             'size'     => isset( $file['size'] ) ? $file['size'] : '',
         );

        // $upload = array(
        //     'name'     => isset( $_FILES['wpuf_file']['name'] ) ? sanitize_file_name( wp_unslash( $_FILES['wpuf_file']['name'] ) ) : '',
        //     'type'     => isset( $_FILES['wpuf_file']['type'] ) ? sanitize_mime_type( wp_unslash( $_FILES['wpuf_file']['type'] ) ) : '',
        //     'tmp_name' => $_FILES['wpuf_file']['tmp_name'],
        //     'error'    => isset( $_FILES['wpuf_file']['error'] ) ? sanitize_text_field( wp_unslash( $_FILES['wpuf_file']['error'] ) ) : '',
        //     'size'     => isset( $_FILES['wpuf_file']['size'] ) ? sanitize_text_field( wp_unslash( $_FILES['wpuf_file']['size'] ) ) : ''
        // );

        header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );

        $attach = $this->handle_upload( $upload );

        if ( $attach['success'] ) {
            $response         = [ 'success' => true ];
            $response['html'] = $this->attach_html( $attach['attach_id'] );

            echo wp_kses( $response['html'], [
                'li' =>  [
                    'class' => []
                ],
                'div'   => [
                    'class' => []
                ],
                'img' => [
                    'src' => [],
                    'alt' => []
                ],

                'input' => [
                    'type'  => [],
                    'name'  => [],
                    'value' => []
                ],
                'a' => [
                    'data-attach_id' => [],
                    'href'           => [],
                    'class'          => []
                ],
                'span' => [
                    'class' => []
                ]
            ]);
        } else {
            echo 'error';
        }

        exit;
    }

    /**
     * Generic function to upload a file
     *
     * @param string $field_name file input field name
     *
     * @return bool|int attachment id on success, bool false instead
     */
    public function handle_upload( $upload_data ) {
        $uploaded_file = wp_handle_upload( $upload_data, ['test_form' => false] );

        // If the wp_handle_upload call returned a local path for the image
        if ( isset( $uploaded_file['file'] ) ) {
            $file_loc  = $uploaded_file['file'];
            $file_name = basename( $upload_data['name'] );
            $file_type = wp_check_filetype( $file_name );

            $attachment = [
                'post_mime_type' => $file_type['type'],
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_name ) ),
                'post_content'   => '',
                'post_status'    => 'inherit',
            ];

            $attach_id   = wp_insert_attachment( $attachment, $file_loc );
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file_loc );

            wp_update_attachment_metadata( $attach_id, $attach_data );

            // Store a unique deletion token for security (prevents IDOR attacks)
            $delete_token = wp_generate_password( 32, false );
            update_post_meta( $attach_id, '_wpuf_delete_token', $delete_token );

            return ['success' => true, 'attach_id' => $attach_id];
        }

        return ['success' => false, 'error' => $uploaded_file['error']];
    }

    /**
     * Image attachment response
     *
     * @param int    $attach_id
     * @param string $type
     *
     * @return string
     */
    public static function attach_html( $attach_id, $type = NULL ) {
        if ( ! $type ) {
            $type = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : 'image';
        }

        $attachment = get_post( $attach_id );

        if ( !$attachment ) {
            return;
        }

        if ( wp_attachment_is_image( $attach_id ) ) {
            $image = wp_get_attachment_image_src( $attach_id, 'thumbnail' );
            $image = $image[0];
        } else {
            $image = wp_mime_type_icon( $attach_id );
        }

        // Get deletion token for security (prevents IDOR attacks)
        $delete_token = get_post_meta( $attach_id, '_wpuf_delete_token', true );
        // If no token exists (legacy files), generate one now
        if ( empty( $delete_token ) ) {
            $delete_token = wp_generate_password( 32, false );
            update_post_meta( $attach_id, '_wpuf_delete_token', $delete_token );
        }

        $html = '<li class="ui-state-default wpuf-image-wrap thumbnail">';
        $html .= sprintf( '<div class="attachment-name"><img src="%s" alt="%s" /></div>', $image, esc_attr( $attachment->post_title ) );

        $html .= sprintf( '<input type="hidden" name="wpuf_files[%s][]" value="%d">', $type, $attach_id );
        $html .= '<div class="caption">';
        $html .= sprintf( '<a href="#" class="attachment-delete" data-attach_id="%d" data-delete-token="%s"> <img src="%s" /></a>', $attach_id, esc_attr( $delete_token ), WEFORMS_ASSET_URI . '/images/del-img.png' );
        $html .= sprintf( '<span class="wpuf-drag-file"> <img src="%s" /></span>', WEFORMS_ASSET_URI . '/images/move-img.png' );
        $html .= '</div>';
        $html .= '</li>';

        return $html;
    }

    /**
     * Delete a file
     *
     * @return void
     */
    public function delete_file() {
        check_ajax_referer( 'wpuf_nonce', 'nonce' );

        $attach_id  = isset( $_POST['attach_id'] ) ? intval( $_POST['attach_id'] ) : 0;
        $attachment = get_post( $attach_id );

        // Validate attachment exists
        if ( ! $attachment || 'attachment' !== $attachment->post_type ) {
            echo 'error';
            exit;
        }

        $current_user_id = get_current_user_id();
        $is_authenticated = $current_user_id > 0;
        $can_delete = false;

        if ( $is_authenticated ) {
            // For authenticated users: must own the file OR have admin/editor capabilities
            if ( $current_user_id == $attachment->post_author || current_user_can( 'delete_private_pages' ) ) {
                $can_delete = true;
            }
        } else {
            // For unauthenticated users: must provide the correct deletion token
            // This prevents IDOR attacks where 0 == 0 would allow deletion of any guest upload
            $delete_token = isset( $_POST['delete_token'] ) ? sanitize_text_field( wp_unslash( $_POST['delete_token'] ) ) : '';
            $stored_token = get_post_meta( $attach_id, '_wpuf_delete_token', true );

            // Only allow deletion if token matches AND file was uploaded by guest (post_author == 0)
            if ( ! empty( $delete_token ) && ! empty( $stored_token ) &&
                 hash_equals( $stored_token, $delete_token ) &&
                 $attachment->post_author == 0 ) {
                $can_delete = true;
            }
        }

        if ( $can_delete ) {
            wp_delete_attachment( $attach_id, true );
            echo 'success';
        } else {
            echo 'error';
        }

        exit;
    }
}
