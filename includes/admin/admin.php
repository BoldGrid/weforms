<?php

/**
 * The admin page handler class
 */
class WPUF_Contact_Form_Admin {

    public function __construct() {
        add_action( 'wpuf_admin_menu_top', array( $this, 'register_admin_menu' ) );
    }

    public function register_admin_menu() {
        $capability = wpuf_admin_role();

        add_submenu_page( 'wp-user-frontend', __( 'Contact Forms', 'wpuf-contact-form' ), __( 'Contact Forms', 'wpuf-contact-form' ), $capability, 'wpuf-contact-forms', array( $this, 'contact_form_page') );
    }

    public function contact_form_page() {
        $action = isset( $_GET['action'] ) ? $_GET['action'] : null;
        $add_new_page_url = admin_url( 'admin.php?page=wpuf-contact-forms&action=add-new' );

        switch ( $action ) {
            case 'edit':
                require_once dirname( __FILE__ ) . '/views/contact-form.php';
                break;

            case 'add-new':
                require_once dirname( __FILE__ ) . '/views/contact-form.php';
                break;

            default:
                require_once dirname( __FILE__ ) . '/class-forms-list-table.php';
                require_once dirname( __FILE__ ) . '/views/forms-list-table.php';
                break;
        }
    }
}
