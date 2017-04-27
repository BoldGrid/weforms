<?php

/**
 * The admin page handler class
 */
class WPUF_Contact_Form_Admin {

    public function __construct() {
        add_action( 'init', array( $this, 'register_post_type' ) );

        add_action( 'wpuf_admin_menu_top', array( $this, 'register_admin_menu' ) );
        add_action( 'admin_footer', array( $this, 'include_vue_templates' ) );
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

    public function register_admin_menu() {
        $capability = wpuf_admin_role();

        $hook = add_submenu_page( 'wp-user-frontend', __( 'Contact Forms', 'wpuf-contact-form' ), __( 'Contact Forms', 'wpuf-contact-form' ), $capability, 'wpuf-contact-forms', array( $this, 'contact_form_page') );

        add_action( 'load-'. $hook, array( $this, 'enqueue_scripts' ) );
    }

    /**
     * Check if the page is contact page
     *
     * @return boolean
     */
    public function is_contact_page() {
        return ( get_current_screen()->id == 'user-frontend_page_wpuf-contact-forms' );
    }

    public function enqueue_scripts() {
        $prefix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

        if ( isset( $_GET['action'] ) ) {
            return;
        }

        wp_enqueue_script( 'wpuf-vue', WPUF_ASSET_URI . '/vendor/vue/vue' . $prefix . '.js', array(), WPUF_VERSION, true );
        wp_enqueue_script( 'wpuf-vuex', WPUF_ASSET_URI . '/vendor/vuex/vuex' . $prefix . '.js', array( 'wpuf-vue' ), WPUF_VERSION, true );
        wp_enqueue_script( 'wpuf-vue-router', WPUF_CONTACT_FORM_ASSET_URI . '/js/vendor/vue-router.js', array( 'jquery', 'wpuf-vue', 'wpuf-vuex' ), false, true );
        wp_enqueue_script( 'nprogress', WPUF_CONTACT_FORM_ASSET_URI . '/js/vendor/nprogress.js', array( 'jquery' ), false, true );
        wp_enqueue_script( 'wpuf-cf-spa', WPUF_CONTACT_FORM_ASSET_URI . '/js/spa.js', array( 'wpuf-vue-router', 'wp-util' ), false, true );
        wp_localize_script( 'wpuf-cf-spa', 'wpufContactForm', array(
            'nonce'   => wp_create_nonce( 'wpuf-contact-form' ),
            'confirm' => __( 'Are you sure?', 'wpuf-contact-form' )
        ) );
    }

    public function contact_form_page() {

        $action = isset( $_GET['action'] ) ? $_GET['action'] : null;

        switch ( $action ) {
            case 'edit':
                require_once WPUF_ROOT . '/views/post-form.php';
                break;

            default:
                require_once dirname( __FILE__ ) . '/views/vue-index.php';
                break;
        }
    }

    public function include_vue_templates() {
        if ( ! $this->is_contact_page() ) {
            return;
        }

        $templates = array(
            'form-list-table',
            'home-page',
            'create-page',
            'form-editor',
            'form-entries',
            'form-entry-single',
            'component-table',
            'form-notification',
            'merge-tags',
        );

        $template_path = dirname( __FILE__ ) . '/views/vue';

        foreach ($templates as $template_id) {
            self::include_js_template( $template_id, $template_path );
        }
    }

    /**
     * Embed a Vue.js component template
     *
     * @param string $template
     * @param string $file_path
     *
     * @return void
     */
    public static function include_js_template( $template, $file_path = '' ) {
        $file_path = $file_path . DIRECTORY_SEPARATOR . $template . '.php';

        if ( file_exists( $file_path ) ) {
            echo '<script type="text/x-template" id="tmpl-wpuf-' . $template . '">' . "\n";
            include apply_filters( 'wpuf-contact-form-builder-js-template-path', $file_path, $template );
            echo "\n" . '</script>' . "\n";
        }
    }
}
