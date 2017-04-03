<?php

/**
 * The admin page handler class
 */
class WPUF_Contact_Form_Admin {

    public function __construct() {
        add_action( 'wpuf_admin_menu_top', array( $this, 'register_admin_menu' ) );
        add_action( 'admin_footer', array( $this, 'include_vue_templates' ) );
    }

    public function register_admin_menu() {
        $capability = wpuf_admin_role();

        $hook = add_submenu_page( 'wp-user-frontend', __( 'Contact Forms', 'wpuf-contact-form' ), __( 'Contact Forms', 'wpuf-contact-form' ), $capability, 'wpuf-contact-forms', array( $this, 'contact_form_page') );

        add_action( 'load-'. $hook, array( $this, 'scripts' ) );
    }

    public function scripts() {
        $prefix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

        wp_enqueue_script( 'wpuf-vue', WPUF_ASSET_URI . '/vendor/vue/vue' . $prefix . '.js', array(), WPUF_VERSION, true );
        wp_enqueue_script( 'wpuf-vuex', WPUF_ASSET_URI . '/vendor/vuex/vuex' . $prefix . '.js', array( 'wpuf-vue' ), WPUF_VERSION, true );
        wp_enqueue_script( 'wpuf-vue-router', WPUF_CONTACT_FORM_ASSET_URI . '/js/vue-router.js', array( 'jquery', 'wpuf-vue', 'wpuf-vuex' ), false, true );
        wp_enqueue_script( 'wpuf-contact-form-admin', WPUF_CONTACT_FORM_ASSET_URI . '/js/vue-script.js', array( 'jquery', 'wpuf-vue', 'wpuf-vuex', 'wpuf-vue-router', 'wp-util' ), false, true );
    }

    public function contact_form_page() {
        require_once dirname( __FILE__ ) . '/views/vue-index.php';
    }

    public function include_vue_templates() {
        if ( get_current_screen()->id != 'user-frontend_page_wpuf-contact-forms' ) {
            return;
        }

        $templates = array(
            'form-list-table',
            'home-page',
            'create-page',
            'form-editor',
            'form-entries'
        );

        $template_path = dirname( __FILE__ ) . '/views/vue';

        foreach ($templates as $template_id) {
            $this->include_js_template( $template_id, $template_path );
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
    public function include_js_template( $template, $file_path = '' ) {
        $file_path = $file_path . DIRECTORY_SEPARATOR . $template . '.php';

        if ( file_exists( $file_path ) ) {
            echo '<script type="text/x-template" id="tmpl-wpuf-' . $template . '">' . "\n";
            include apply_filters( 'wpuf-contact-form-builder-js-template-path', $file_path, $template );
            echo "\n" . '</script>' . "\n";
        }
    }
}
