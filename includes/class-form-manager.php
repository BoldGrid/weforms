<?php

/**
 * The Form Manager Class
 *
 * @since 1.0.5
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
    public function get_forms( $args ) {
        $all_forms = array();
        $defaults  = array(
            'post_type'   => 'wpuf_contact_form',
            'post_status' => array( 'publish', 'draft', 'pending' )
        );

        $args  = wp_parse_args( $args, $defaults );

        $query = new WP_Query( $args );
        $forms = $query->get_posts();

        if ( $forms ) {
            foreach ($forms as $form) {
                $all_forms[] = new WeForms_Form( $form );
            }
        }

        return $all_forms;
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
}
