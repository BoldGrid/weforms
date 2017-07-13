<?php

/**
 * The admin page handler class
 */
class WPUF_Contact_Form_Admin {

    public function __construct() {

        add_action( 'init', array( $this, 'register_post_type' ) );

        add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );

        add_filter( 'admin_post_bcf_export_forms', array( $this, 'export_forms' ) );
        add_filter( 'admin_post_bcf_export_form_entries', array( $this, 'export_form_entries' ) );
    }

    /**
     * Register form post types
     *
     * @return void
     */
    public function register_post_type() {
        $capability = wpuf_admin_role();

        register_post_type( 'wpuf_contact_form', array(
            'label'           => __( 'Contact Forms', 'best-contact-form' ),
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
                'name'               => __( 'Forms', 'best-contact-form' ),
                'singular_name'      => __( 'Form', 'best-contact-form' ),
                'menu_name'          => __( 'Contact Forms', 'best-contact-form' ),
                'add_new'            => __( 'Add Form', 'best-contact-form' ),
                'add_new_item'       => __( 'Add New Form', 'best-contact-form' ),
                'edit'               => __( 'Edit', 'best-contact-form' ),
                'edit_item'          => __( 'Edit Form', 'best-contact-form' ),
                'new_item'           => __( 'New Form', 'best-contact-form' ),
                'view'               => __( 'View Form', 'best-contact-form' ),
                'view_item'          => __( 'View Form', 'best-contact-form' ),
                'search_items'       => __( 'Search Form', 'best-contact-form' ),
                'not_found'          => __( 'No Form Found', 'best-contact-form' ),
                'not_found_in_trash' => __( 'No Form Found in Trash', 'best-contact-form' ),
                'parent'             => __( 'Parent Form', 'best-contact-form' ),
            ),
        ) );
    }

    public function register_admin_menu() {
        global $submenu;

        $capability = wpuf_admin_role();

        $hook = add_menu_page( __( 'Best Contact Forms', 'best-contact-form' ), __( 'Contact Forms', 'best-contact-form' ), $capability, 'best-contact-forms', array( $this, 'contact_form_page'), 'data:image/svg+xml;base64,' . base64_encode( '<svg viewBox="0 0 110 64" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Page-1" stroke="none" stroke-width="1" fill="#9ea3a8" fill-rule="evenodd"><g id="logo" fill="#9ea3a8"><g id="Shape"><path d="M101.16,-8.8817842e-16 L57.5,-8.8817842e-16 L57.5,16.69 L109.17,16.69 L109.17,8 C109.170002,5.87653433 108.325774,3.8401861 106.82332,2.33960728 C105.320865,0.839028462 103.283464,-0.00265433208 101.16,-8.8817842e-16 Z" fill-rule="nonzero"></path><polygon fill-rule="nonzero" points="57.5 24.21 57.5 40.9 57.5 63.27 74.53 63.27 74.53 40.9 79.45 40.9 98.73 40.9 98.73 24.21 79.45 24.21 74.53 24.21"></polygon><path d="M3.75,16.69 C1.2859816,21.287726 -0.00228984973,26.4236348 2.62234667e-15,31.64 C-4.1933494e-07,40.0305305 3.33380912,48.0772606 9.26774777,54.0093238 C15.2016864,59.941387 23.2494699,63.2726527 31.64,63.27 L50.92,63.27 L50.92,46.58 L31.64,46.58 C26.302452,46.5800001 21.3703543,43.7324522 18.7015803,39.1100001 C16.0328063,34.4875479 16.0328063,28.7924521 18.7015803,24.1699999 C21.3703543,19.5475478 26.302452,16.6999999 31.64,16.7 L50.92,16.7 L50.92,1.18248108e-15 L31.64,1.18248108e-15 C19.9773109,-0.00395864835 9.25871213,6.41029065 3.75,16.69 Z" fill-rule="nonzero"></path></g></g></g></svg>' ), 56 );

        if ( current_user_can( $capability ) ) {
            $submenu['best-contact-forms'][] = array( __( 'Contact Forms', 'best-contact-form' ), $capability, 'admin.php?page=best-contact-forms#/' );
            $submenu['best-contact-forms'][] = array( __( 'Tools', 'best-contact-form' ), $capability, 'admin.php?page=best-contact-forms#/tools' );
            $submenu['best-contact-forms'][] = array( __( 'Add-ons', 'best-contact-form' ), $capability, 'admin.php?page=best-contact-forms#/extensions' );
        }

        add_action( 'load-'. $hook, array( $this, 'load_assets' ) );
    }

    /**
     * Load the asset libraries
     *
     * @return void
     */
    public function load_assets() {
        require_once dirname( __FILE__ ) . '/class-form-builder-assets.php';
        new WPUF_Contact_Form_Builder_Assets();
    }

    public function admin_enqueue_scripts() {
        $prefix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

        /**
         * Load all the styles
         */
        wp_enqueue_style( 'wpuf-css', WPUF_ASSET_URI . '/css/frontend-forms.css' );
        wp_enqueue_style( 'wpuf-font-awesome', WPUF_ASSET_URI . '/vendor/font-awesome/css/font-awesome.min.css', array(), WPUF_VERSION );
        wp_enqueue_style( 'wpuf-sweetalert2', WPUF_ASSET_URI . '/vendor/sweetalert2/dist/sweetalert2.css', array(), WPUF_VERSION );
        wp_enqueue_style( 'wpuf-selectize', WPUF_ASSET_URI . '/vendor/selectize/css/selectize.default.css', array(), WPUF_VERSION );
        wp_enqueue_style( 'wpuf-toastr', WPUF_ASSET_URI . '/vendor/toastr/toastr.min.css', array(), WPUF_VERSION );
        wp_enqueue_style( 'wpuf-tooltip', WPUF_ASSET_URI . '/vendor/tooltip/tooltip.css', array(), WPUF_VERSION );

        $form_builder_css_deps = apply_filters( 'wpuf-form-builder-css-deps', array(
            'wpuf-css', 'wpuf-font-awesome', 'wpuf-sweetalert2', 'wpuf-selectize', 'wpuf-toastr', 'wpuf-tooltip'
        ) );

        wp_enqueue_style( 'wpuf-form-builder', WPUF_ASSET_URI . '/css/wpuf-form-builder.css', $form_builder_css_deps, WPUF_VERSION );
        wp_enqueue_style( 'wpuf-cf-style', WPUF_CONTACT_FORM_ASSET_URI . '/css/admin.css', false );


        /**
         * Load all the scripts
         */
        wp_enqueue_script( 'wpuf-vue', WPUF_ASSET_URI . '/vendor/vue/vue' . $prefix . '.js', array(), WPUF_VERSION, true );
        wp_enqueue_script( 'wpuf-vuex', WPUF_ASSET_URI . '/vendor/vuex/vuex' . $prefix . '.js', array( 'wpuf-vue' ), WPUF_VERSION, true );
        wp_enqueue_script( 'wpuf-vue-router', WPUF_CONTACT_FORM_ASSET_URI . '/js/vendor/vue-router.js', array( 'jquery', 'wpuf-vue', 'wpuf-vuex' ), false, true );
        wp_enqueue_script( 'nprogress', WPUF_CONTACT_FORM_ASSET_URI . '/js/vendor/nprogress.js', array( 'jquery' ), false, true );
        wp_enqueue_script( 'wpuf-sweetalert2', WPUF_ASSET_URI . '/vendor/sweetalert2/dist/sweetalert2.js', array(), WPUF_VERSION, true );
        wp_enqueue_script( 'wpuf-jquery-scrollTo', WPUF_ASSET_URI . '/vendor/jquery.scrollTo/jquery.scrollTo' . $prefix . '.js', array( 'jquery' ), WPUF_VERSION, true );
        wp_enqueue_script( 'wpuf-selectize', WPUF_ASSET_URI . '/vendor/selectize/js/standalone/selectize' . $prefix . '.js', array( 'jquery' ), WPUF_VERSION, true );
        wp_enqueue_script( 'wpuf-toastr', WPUF_ASSET_URI . '/vendor/toastr/toastr' . $prefix . '.js', array(), WPUF_VERSION, true );
        wp_enqueue_script( 'wpuf-clipboard', WPUF_ASSET_URI . '/vendor/clipboard/clipboard' . $prefix . '.js', array(), WPUF_VERSION, true );
        wp_enqueue_script( 'wpuf-tooltip', WPUF_ASSET_URI . '/vendor/tooltip/tooltip' . $prefix . '.js', array(), WPUF_VERSION, true );

        $form_builder_js_deps = apply_filters( 'wpuf-form-builder-js-deps', array(
            'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'underscore',
            'wpuf-vue', 'wpuf-vuex', 'wpuf-sweetalert2', 'wpuf-jquery-scrollTo',
            'wpuf-selectize', 'wpuf-toastr', 'wpuf-clipboard', 'wpuf-tooltip'
        ) );

        wp_enqueue_script( 'wpuf-form-builder-mixins', WPUF_ASSET_URI . '/js/wpuf-form-builder-mixins.js', $form_builder_js_deps, WPUF_VERSION, true );
        wp_enqueue_script( 'wpuf-form-builder-mixins-form', WPUF_CONTACT_FORM_ASSET_URI . '/js/wpuf-form-builder-contact-forms.js', $form_builder_js_deps, WPUF_VERSION, true );

        // mixins
        $wpuf_mixins = array(
            'root'           => apply_filters( 'wpuf-form-builder-js-root-mixins', array() ),
            'builder_stage'  => apply_filters( 'wpuf-form-builder-js-builder-stage-mixins', array() ),
            'form_fields'    => apply_filters( 'wpuf-form-builder-js-form-fields-mixins', array() ),
            'field_options'  => apply_filters( 'wpuf-form-builder-js-field-options-mixins', array() ),
        );

        wp_localize_script( 'wpuf-form-builder-mixins', 'wpuf_mixins', $wpuf_mixins );

        /*
         * Data required for building the form
         */
        require_once WPUF_ROOT . '/admin/form-builder/class-wpuf-form-builder-field-settings.php';
        require_once WPUF_ROOT . '/includes/free/prompt.php';

        $wpuf_form_builder = apply_filters( 'wpuf-form-builder-localize-script', array(
            'i18n'              => $this->i18n(),
            'panel_sections'    => $this->get_panel_sections(),
            'field_settings'    => WPUF_Form_Builder_Field_Settings::get_field_settings(),
            'pro_link'          => WPUF_Pro_Prompt::get_pro_url(),
            'site_url'          => site_url('/')
        ) );

        wp_localize_script( 'wpuf-form-builder-mixins', 'wpuf_form_builder', $wpuf_form_builder );

        wp_enqueue_script( 'wpuf-form-builder-components', WPUF_ASSET_URI . '/js/wpuf-form-builder-components.js', array( 'wpuf-form-builder-mixins' ), WPUF_VERSION, true );
        wp_enqueue_script( 'wpuf-form-builder', WPUF_ASSET_URI . '/js/wpuf-form-builder.js', array( 'wpuf-form-builder-components' ), WPUF_VERSION, true );

        wp_enqueue_script( 'wpuf-cf-spa', WPUF_CONTACT_FORM_ASSET_URI . '/js/spa-app.js', array( 'wpuf-vue-router', 'wp-util' ), false, true );
        wp_localize_script( 'wpuf-cf-spa', 'wpufContactForm', array(
            'nonce'   => wp_create_nonce( 'best-contact-form' ),
            'confirm' => __( 'Are you sure?', 'best-contact-form' )
        ) );
    }

    /**
     * The contact form page handler
     *
     * @return void
     */
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

    /**
     * Export forms to JSON
     *
     * @return void
     */
    public function export_forms() {

        check_admin_referer( 'bcf-export-forms' );

        if ( ! isset( $_REQUEST['bcf_export_forms'] ) ) {
            return;
        }

        $export_type = isset( $_POST['export_type'] ) ? $_POST['export_type'] : 'all';
        $selected    = isset( $_POST['selected_forms'] ) ? array_map( 'absint', $_POST['selected_forms'] ) : array();

        if ( ! class_exists( 'WPUF_Admin_Tools' ) ) {
            require_once WPUF_ROOT . '/admin/class-tools.php';
        }

        switch ($export_type) {
            case 'all':
                WPUF_Admin_Tools::export_to_json( 'wpuf_contact_form' );
                return;

            case 'selected':
                WPUF_Admin_Tools::export_to_json( 'wpuf_contact_form', $selected );
                return;
        }

        exit;
    }

    /**
     * Export form entries to CSV
     *
     * @return void
     */
    public function export_form_entries() {

        $form_id = isset( $_REQUEST['selected_forms'] ) ? absint( $_REQUEST['selected_forms'] ) : 0;

        if ( ! $form_id ) {
            return;
        }

        $entry_array   = [];
        $columns       = wpuf_cf_get_entry_columns( $form_id, false );
        $total_entries = wpuf_cf_count_form_entries( $form_id );
        $entries       = wpuf_cf_get_form_entries( $form_id, array(
            'number'       => $total_entries,
            'offset'       => 0
        ) );
        $extra_columns =  array(
            'ip_address' => __( 'IP Address', 'best-contact-form' ),
            'created_at' => __( 'Date', 'best-contact-form' )
        );

        $columns = array_merge( array( 'id' => 'Entry ID' ), $columns, $extra_columns );

        foreach ($entries as $entry) {
            $temp = array();

            foreach ($columns as $column_id => $label) {
                switch ( $column_id ) {
                    case 'id':
                        $temp[ $column_id ] = $entry->id;
                        break;

                    case 'ip_address':
                        $temp[ $column_id ] = $entry->ip_address;
                        break;

                    case 'created_at':
                        $temp[ $column_id ] = $entry->created_at;
                        break;

                    default:
                        $value              = wpuf_cf_get_entry_meta( $entry->id, $column_id, true );
                        $temp[ $column_id ] = str_replace( WPUF_Render_Form::$separator, ' ', $value );
                        break;
                }
            }

            $entry_array[] = $temp;
        }

        $handle = fopen("php://output", 'w');

        // put the column headers
        fputcsv( $handle, array_values( $columns ) );

        // put the entry values
        foreach ($entry_array as $row) {
            fputcsv( $handle, $row );
        }

        fclose( $handle );

        $blogname  = strtolower( str_replace( " ", "-", get_option( 'blogname' ) ) );
        $file_name = $blogname . "-bcf-entries-" . time() . '.csv';

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$file_name}");
        header("Content-Transfer-Encoding: binary");
        exit;
    }
}
