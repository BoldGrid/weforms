<?php

/**
 * The admin page handler class
 */
class WPUF_Contact_Form_Admin {

    public function __construct() {
        add_action( 'wpuf_admin_menu_top', array( $this, 'register_admin_menu' ) );
        add_action( 'admin_footer', array( $this, 'include_vue_templates' ) );
        add_action( 'admin_footer', array( $this, 'render_form_templates' ) );
    }

    public function register_admin_menu() {
        $capability = wpuf_admin_role();

        $hook = add_submenu_page( 'wp-user-frontend', __( 'Contact Forms', 'wpuf-contact-form' ), __( 'Contact Forms', 'wpuf-contact-form' ), $capability, 'wpuf-contact-forms', array( $this, 'contact_form_page') );

        add_action( 'load-'. $hook, array( $this, 'form_builder_init' ) );
        add_action( 'load-'. $hook, array( $this, 'builder_enqueue_scripts' ) );
    }

    /**
     * Check if the page is contact page
     *
     * @return boolean
     */
    public function is_contact_page() {
        return ( get_current_screen()->id == 'user-frontend_page_wpuf-contact-forms' );
    }

    public function builder_enqueue_scripts() {
        $prefix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

        wp_enqueue_style( 'wpuf-formbuilder', WPUF_ASSET_URI . '/css/formbuilder.css' );

        if ( isset( $_GET['action'] ) ) {
            return;
        }

        wp_enqueue_script( 'wpuf-vue', WPUF_ASSET_URI . '/vendor/vue/vue' . $prefix . '.js', array(), WPUF_VERSION, true );
        wp_enqueue_script( 'wpuf-vuex', WPUF_ASSET_URI . '/vendor/vuex/vuex' . $prefix . '.js', array( 'wpuf-vue' ), WPUF_VERSION, true );
        wp_enqueue_script( 'wpuf-vue-router', WPUF_CONTACT_FORM_ASSET_URI . '/js/vue-router.js', array( 'jquery', 'wpuf-vue', 'wpuf-vuex' ), false, true );
        wp_enqueue_script( 'wpuf-cf-admin', WPUF_CONTACT_FORM_ASSET_URI . '/js/vue-script.js', array( 'wpuf-vue-router', 'wp-util' ), false, true );
    }

    /**
     * Add dependencies to form builder script
     *
     * @since 2.5
     *
     * @param array $deps
     *
     * @return array
     */
    public function js_dependencies( $deps ) {
        array_push( $deps, 'wpuf-contact-form-builder-mixin' );

        return $deps;
    }

    /**
     * Add mixins to form builder builder stage component
     *
     * @since 2.5
     *
     * @param array $mixins
     *
     * @return array
     */
    public function js_builder_stage_mixins( $mixins ) {
        array_push( $mixins , 'wpuf_forms_mixin_builder_stage' );

        return $mixins;
    }

    /**
     * Admin script form wpuf_forms form builder
     *
     * @since 2.5
     *
     * @return void
     */
    public function admin_enqueue_scripts() {
        wp_enqueue_script( 'wpuf-contact-form-builder-mixin', WPUF_CONTACT_FORM_ASSET_URI . '/js/wpuf-form-builder-contact-forms.js', array( 'jquery', 'underscore', 'wpuf-vue', 'wpuf-vuex' ), WPUF_CONTACT_FORM_VERSION, true );
    }

    public function contact_form_page() {
        require_once dirname( __FILE__ ) . '/views/vue-index.php';

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

    public function form_builder_init() {
        $form_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

        $settings = array(
            'form_type'         => 'contact_form',
            'post_type'         => 'wpuf_contact_form',
            'post_id'           => $form_id,
            'form_settings_key' => 'wpuf_form_settings',
            'shortcodes'        => array( array( 'name' => 'wpuf_contact_form' ) )
        );

        // add_filter( 'wpuf-form-builder-fields-common-properties', array( $this, 'add_fields_common_properties' ) );

        new WPUF_Admin_Form_Builder( $settings );

        add_filter( 'wpuf-form-builder-js-deps', array( $this, 'js_dependencies' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

        add_action( 'wpuf-form-builder-js-builder-stage-mixins', array( $this, 'js_builder_stage_mixins' ) );
        add_action( 'wpuf-form-builder-template-builder-stage-submit-area', array( $this, 'add_form_submit_area' ) );

        add_filter( 'wpuf-form-builder-fields-custom-fields', function( $fields ) {

            $search_key = 'custom_hidden_field';

            if ( in_array( $search_key, $fields ) ) {
                $key = array_search( $search_key, $fields );
                unset( $fields[ $key ] );

                // re-index the array to preserve sequential keys
                // otherwise JS converts this into object insetead of array
                $fields = array_values( $fields );
            }

            return $fields;
        } );

        // add_filter( 'wpuf-form-builder-field-settings', function( $settings ) {
        //     if ( array_key_exists( 'custom_hidden_field', $settings ) ) {
        //         // unset( $settings[ 'custom_hidden_field' ] );
        //     }

        //     return $settings;
        // });
    }

    /**
     * Add buttons in form submit area
     *
     * @return void
     */
    public function add_form_submit_area() {
        ?>
            <input @click.prevent="" type="submit" name="submit" :value="form_settings.submit_text">
        <?php
    }

    /**
     * Render the forms in the modal
     *
     * @return void
     */
    public function render_form_templates() {
        if ( ! $this->is_contact_page() ) {
            return;
        }

        $registry       = wpuf_cf_get_form_templates();
        $blank_form_url = admin_url( 'admin.php?page=wpuf-contact-forms&action=add-new' );
        $action_name    = 'wpuf_contact_form_template';

        if ( ! $registry ) {
            // return;
        }

        include WPUF_ROOT . '/admin/html/modal.php';
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
            'component-table'
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
