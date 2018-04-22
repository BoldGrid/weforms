<?php

/**
 * The Form Manager Class
 *
 * @since 1.1.0
 */
class WeForms_Form_Manager {

    /**
     * Get all the forms
     *
     * @return array
     */
    public function all() {
        return $this->get_forms( array( 'posts_per_page' => -1 ) );
    }

    /**
     * Get forms
     *
     * @param  array $args
     *
     * @return array
     */
    public function get_forms( $args = array() ) {
        $forms_array = array(
            'forms' => array(),
            'meta'  => array(
                'total' => 0,
                'pages' => 0
            )
        );
        $defaults  = array(
            'post_type'   => 'wpuf_contact_form',
            'post_status' => array( 'publish', 'draft', 'pending' )
        );

        $args  = wp_parse_args( $args, $defaults );

        $query = new WP_Query( $args );
        $forms = $query->get_posts();

        if ( $forms ) {
            foreach ($forms as $form) {
                $forms_array['forms'][] = new WeForms_Form( $form );
            }
        }

        $forms_array['meta']['total'] = (int) $query->found_posts;
        $forms_array['meta']['pages'] = (int) $query->max_num_pages;

        return $forms_array;
    }

    /**
     * Get a single form
     *
     * @param  integer|WP_Post $form
     *
     * @return \WeForms_Form
     */
    public function get( $form ) {
        return new WeForms_Form( $form );
    }

    /**
     * Create a form
     *
     * @param  string $form_name
     * @param  array  $fields
     *
     * @return integer|WP_Error
     */
    public function create( $form_name, $fields = array() ) {
        $form_id = wp_insert_post( array(
            'post_title'  => $form_name,
            'post_type'   => 'wpuf_contact_form',
            'post_status' => 'publish'
        ) );

        if ( is_wp_error( $form_id ) ) {
            return $form_id;
        }

        if ( $fields ) {
            foreach ($fields as $order => $field) {
                $args = array(
                    'post_type'    => 'wpuf_input',
                    'post_parent'  => $form_id,
                    'post_status'  => 'publish',
                    'post_content' => maybe_serialize( wp_unslash( $field ) ),
                    'menu_order'   => $order
                );

                wp_insert_post( $args );
            }
        }

        return $form_id;
    }

    /**
     * Delete a form with it's input fields
     *
     * @param  integer  $form_id
     * @param  boolean $force
     *
     * @return void
     */
    public function delete( $form_id, $force = true ) {
        global $wpdb;

        wp_delete_post( $form_id, $force );

        // delete form inputs as WP doesn't know the relationship
        $wpdb->delete( $wpdb->posts,
            array(
                'post_parent' => $form_id,
                'post_type'   => 'wpuf_input'
            )
        );
    }

    /**
     * Save and existing form
     *
     * @since 1.1.0
     *
     * @param array $data Contains form_fields, form_settings, form_settings_key data
     *
     * @return boolean
     */
    public function save( $data ) {
        $saved_wpuf_inputs = array();

        wp_update_post( array( 'ID' => $data['form_id'], 'post_status' => 'publish', 'post_title' => $data['post_title'] ) );

        $existing_wpuf_input_ids = get_children( array(
            'post_parent' => $data['form_id'],
            'post_status' => 'publish',
            'post_type'   => 'wpuf_input',
            'numberposts' => '-1',
            'orderby'     => 'menu_order',
            'order'       => 'ASC',
            'fields'      => 'ids'
        ) );

        $new_wpuf_input_ids = array();

        if ( ! empty( $data['form_fields'] ) ) {

            foreach ( $data['form_fields'] as $order => $field ) {
                if ( ! empty( $field['is_new'] ) ) {
                    unset( $field['is_new'] );
                    unset( $field['id'] );

                    $field_id = 0;

                } else {
                    $field_id = $field['id'];
                }

                $field_id = weforms_insert_form_field( $data['form_id'], $field, $field_id, $order );

                $new_wpuf_input_ids[] = $field_id;

                $field['id'] = $field_id;

                $saved_wpuf_inputs[] = $field;
            }

        }

        $inputs_to_delete = array_diff( $existing_wpuf_input_ids, $new_wpuf_input_ids );

        if ( ! empty( $inputs_to_delete ) ) {
            foreach ( $inputs_to_delete as $delete_id ) {
                wp_delete_post( $delete_id , true );
            }
        }

        update_post_meta( $data['form_id'], $data['form_settings_key'], $data['form_settings'] );
        update_post_meta( $data['form_id'], 'notifications', $data['notifications'] );
        update_post_meta( $data['form_id'], 'integrations', $data['integrations'] );
        update_post_meta( $data['form_id'], '_weforms_version', WEFORMS_VERSION );

        return $saved_wpuf_inputs;
    }

    /**
     * API to duplicate a form
     *
     * @param int $_form_id
     *
     * @return int New duplicated form id
     */
    function duplicate( $_form_id ) {

        $form = $this->get( $_form_id );

        if ( empty( $form ) ) {
            return;
        }
        
        $form_id = $this->create( $form->name, $form->get_fields());

        $data = array(
            'form_id'           => absint( $form_id ),
            'post_title'        => sanitize_text_field( $form->name ) . ' (#' . $form_id . ')',
            'form_fields'       => $this->get( $form_id )->get_fields(), // already imported just proxy
            'form_settings'     => $form->get_settings(),
            'form_settings_key' => 'wpuf_form_settings',
            'notifications'     => $form->get_notifications(),
            'integrations'      => $form->get_integrations()
        );

        $form_fields = $this->save( $data );

        return $this->get( $form_id );
    }
}
