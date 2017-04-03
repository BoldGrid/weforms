<?php

/**
 * Contact form class
 */
class WPUF_Contact_Form_Builder {

    public function __construct() {
        add_action( 'init', array( $this, 'register_post_type' ) );
    }

    /**
     * Register form post types
     *
     * @return void
     */
    public function register_post_type() {
        $capability = wpuf_admin_role();

        register_post_type( 'wpuf_contact_form', array(
            'label'           => __( 'Contact Forms', 'wpuf-contact-form' ),
            'public'          => false,
            'show_ui'         => true,
            'show_in_menu'    => false,
            'capability_type' => 'post',
            'hierarchical'    => false,
            'query_var'       => false,
            'supports'        => array('title'),
            'capabilities' => array(
                'publish_posts'       => $capability,
                'edit_posts'          => $capability,
                'edit_others_posts'   => $capability,
                'delete_posts'        => $capability,
                'delete_others_posts' => $capability,
                'read_private_posts'  => $capability,
                'edit_post'           => $capability,
                'delete_post'         => $capability,
                'read_post'           => $capability,
            ),
            'labels' => array(
                'name'               => __( 'Forms', 'wpuf-contact-form' ),
                'singular_name'      => __( 'Form', 'wpuf-contact-form' ),
                'menu_name'          => __( 'Contact Forms', 'wpuf-contact-form' ),
                'add_new'            => __( 'Add Form', 'wpuf-contact-form' ),
                'add_new_item'       => __( 'Add New Form', 'wpuf-contact-form' ),
                'edit'               => __( 'Edit', 'wpuf-contact-form' ),
                'edit_item'          => __( 'Edit Form', 'wpuf-contact-form' ),
                'new_item'           => __( 'New Form', 'wpuf-contact-form' ),
                'view'               => __( 'View Form', 'wpuf-contact-form' ),
                'view_item'          => __( 'View Form', 'wpuf-contact-form' ),
                'search_items'       => __( 'Search Form', 'wpuf-contact-form' ),
                'not_found'          => __( 'No Form Found', 'wpuf-contact-form' ),
                'not_found_in_trash' => __( 'No Form Found in Trash', 'wpuf-contact-form' ),
                'parent'             => __( 'Parent Form', 'wpuf-contact-form' ),
            ),
        ) );
    }
}
