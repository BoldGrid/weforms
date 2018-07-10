<?php

/**
 * The admin page handler class
 */
class WeForms_Admin {

    public function __construct() {

        add_action( 'init', array( $this, 'register_post_type' ) );

        add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );

        add_action( 'pre_update_option_wpuf_general', array( $this, 'watch_wpuf_settings' ) );

        add_filter( 'admin_post_weforms_export_forms', array( $this, 'export_forms' ) );
        add_filter( 'admin_post_weforms_export_form_entries', array( $this, 'export_form_entries' ) );

        // load default settings tabs
        add_filter( 'weforms_settings_tabs', array( $this, 'set_default_settings' ), 5 );
        add_action( 'weforms_settings_tab_content_general', array( $this, 'settings_tab_general' ) );
        add_action( 'weforms_settings_tab_content_recaptcha', array( $this, 'settings_tab_recaptcha' ) );
        add_action( 'weforms_settings_tab_content_privacy', array( $this, 'settings_tab_privacy' ) );
    }

    /**
     * Register form post types
     *
     * @return void
     */
    public function register_post_type() {
        $capability = weforms_form_access_capability();

        register_post_type( 'wpuf_contact_form', array(
            'label'           => __( 'Contact Forms', 'weforms' ),
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
                'name'               => __( 'Forms', 'weforms' ),
                'singular_name'      => __( 'Form', 'weforms' ),
                'menu_name'          => __( 'Contact Forms', 'weforms' ),
                'add_new'            => __( 'Add Form', 'weforms' ),
                'add_new_item'       => __( 'Add New Form', 'weforms' ),
                'edit'               => __( 'Edit', 'weforms' ),
                'edit_item'          => __( 'Edit Form', 'weforms' ),
                'new_item'           => __( 'New Form', 'weforms' ),
                'view'               => __( 'View Form', 'weforms' ),
                'view_item'          => __( 'View Form', 'weforms' ),
                'search_items'       => __( 'Search Form', 'weforms' ),
                'not_found'          => __( 'No Form Found', 'weforms' ),
                'not_found_in_trash' => __( 'No Form Found in Trash', 'weforms' ),
                'parent'             => __( 'Parent Form', 'weforms' ),
            ),
        ) );
    }

    /**
     * Register the admin menu
     *
     * @return void
     */
    public function register_admin_menu() {
        global $submenu;

        $capability = weforms_form_access_capability();

        $hook = add_menu_page( __( 'weForms - The Best Contact Form', 'weforms' ), 'weForms', $capability, 'weforms', array( $this, 'contact_form_page'), 'data:image/svg+xml;base64,' . base64_encode( '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 300 300" style="enable-background:new 0 0 300 300;" xml:space="preserve"><g fill="#9ea3a8"><path d="M285.1,24.1C273.1,9.4,254.8,0,234.3,0H65.7C46.6,0,29.3,8.2,17.3,21.2C6.6,32.9,0,48.5,0,65.7v60.5v108.1 C0,270.6,29.4,300,65.7,300h140h28.6c15.3,0,29.5-5.3,40.7-14.1c15.2-12,25-30.7,25-51.6V65.7C300,49.9,294.4,35.4,285.1,24.1z M212.7,187L104.6,233c-11.1,4.8-24-0.3-28.7-11.4c-4.8-11.1,0.3-24,11.4-28.7l108.1-46.1c11.1-4.8,24,0.3,28.7,11.4 C228.9,169.3,223.8,182.2,212.7,187z M217.4,107.1L99.9,157.8c-11.1,4.8-24-0.3-28.7-11.4c-4.8-11.1,0.3-24,11.4-28.7L200.1,67 c11.1-4.8,24,0.3,28.7,11.4v0C233.6,89.5,228.5,102.3,217.4,107.1z"/></g></svg>' ), 56 );

        if ( current_user_can( $capability ) ) {
            $submenu['weforms'][] = array( __( 'All Forms', 'weforms' ), $capability, 'admin.php?page=weforms#/' );
            $submenu['weforms'][] = array( __( 'Entries', 'weforms' ), $capability, 'admin.php?page=weforms#/entries' );
            $submenu['weforms'][] = array( __( 'Tools', 'weforms' ), $capability, 'admin.php?page=weforms#/tools' );

            if ( class_exists( 'WeForms_Pro' ) ) {
                if ( current_user_can( 'manage_options' ) ) {
                    $submenu['weforms'][] = array( __( 'Modules', 'weforms' ), $capability, 'admin.php?page=weforms#/modules' );
                }
            } else {
                $submenu['weforms'][] = array( __( 'Premium', 'weforms' ), $capability, 'admin.php?page=weforms#/premium' );
            }

            do_action( 'weforms-admin-menu', $hook, $capability );

            $submenu['weforms'][] = array( __( 'Help', 'weforms' ), $capability, 'admin.php?page=weforms#/help' );
        }

        // only admins should see the settings page
        if ( current_user_can( 'manage_options' ) ) {
            $submenu['weforms'][] = array( __( 'Settings', 'weforms' ), 'manage_options', 'admin.php?page=weforms#/settings' );
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
        new WeForms_Form_Builder_Assets();
    }

    /**
     * The contact form page handler
     *
     * @return void
     */
    public function contact_form_page() {
        require_once dirname( __FILE__ ) . '/views/vue-index.php';
    }

    /**
     * Export forms to JSON
     *
     * @return void
     */
    public function export_forms() {

        check_admin_referer( 'weforms-export-forms' );

        if ( ! isset( $_REQUEST['weforms_export_forms'] ) ) {
            return;
        }

        $export_type = isset( $_POST['export_type'] ) ? $_POST['export_type'] : 'all';
        $selected    = isset( $_POST['selected_forms'] ) ? array_map( 'absint', $_POST['selected_forms'] ) : array();

        if ( ! class_exists( 'WeForms_Admin_Tools' ) ) {
            require_once dirname( __FILE__ ) . '/class-admin-tools.php';
        }

        switch ($export_type) {
            case 'all':
                WeForms_Admin_Tools::export_to_json();
                return;

            case 'selected':
                WeForms_Admin_Tools::export_to_json( $selected );
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
        $columns       = weforms_get_entry_columns( $form_id, false );
        $total_entries = weforms_count_form_entries( $form_id );
        $entries       = weforms_get_form_entries( $form_id, array(
            'number'       => $total_entries,
            'offset'       => 0
        ) );
        $extra_columns =  array(
            'ip_address' => __( 'IP Address', 'weforms' ),
            'created_at' => __( 'Date', 'weforms' )
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

                        $value              = weforms_get_entry_meta( $entry->id, $column_id, true );
                        $value              = weforms_get_pain_text( $value );
                        $temp[ $column_id ] = str_replace( WeForms::$field_separator, ' ', $value );

                        break;
                }
            }

            $entry_array[] = $temp;
        }


        error_reporting(0);

        if ( ob_get_contents() ) {
            ob_clean();
        }

        $blogname  = strtolower( str_replace( " ", "-", get_option( 'blogname' ) ) );
        $file_name = $blogname . "-weforms-entries-" . time() . '.csv';

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$file_name}");
        header("Content-Transfer-Encoding: binary");

        $handle = fopen("php://output", 'w');

        //handle UTF-8 chars conversion for CSV
        fprintf( $handle, chr(0xEF).chr(0xBB).chr(0xBF) );

        // put the column headers
        fputcsv( $handle, array_values( $columns ) );

        // put the entry values
        foreach ( $entry_array as $row ) {
            fputcsv( $handle, $row );
        }

        fclose( $handle );

        exit;
    }

    /**
     * Watch the wpuf general settings and sync with weforms settings
     *
     * @param  array $value
     *
     * @return array
     */
    public function watch_wpuf_settings( $settings ) {
        $merge_array = array();

        if ( isset( $settings['gmap_api_key'] ) ) {
            $merge_array['gmap_api'] = $settings['gmap_api_key'];
        }

        if ( isset( $settings['recaptcha_public'] ) ) {
            $recaptcha         =  new StdClass;
            $recaptcha->key    = $settings['recaptcha_public'];
            $recaptcha->secret = $settings['recaptcha_private'];

            $merge_array['recaptcha'] = $recaptcha;
        }

        if ( $merge_array ) {
            $weforms_settings = get_option( 'weforms_settings', array() );
            $weforms_settings = array_merge( $weforms_settings, $merge_array );

            update_option( 'weforms_settings', $weforms_settings );
        }

        return $settings;
    }

    /**
     * Set default settings tabs
     *
     * @param array $tabs
     *
     * @return array
     */
    function set_default_settings( $tabs = array() ) {

        $tabs['general']   = array(
            'label' => __('General Settings', 'weforms'),
            'icon'  => WEFORMS_ASSET_URI . '/images/integrations/general-setting.svg',
        );

        $tabs['recaptcha'] = array(
            'label' => __('reCaptcha', 'weforms'),
            'icon'  => WEFORMS_ASSET_URI . '/images/integrations/reCaptcha.svg',
        );

        /* TODO:  Refactor this block when more options are added in privacy settings*/
        if ( class_exists( 'WeForms_Pro' ) ) {
            $tabs['privacy'] = array(
                'label' => __('Privacy', 'weforms'),
                'icon'  => WEFORMS_ASSET_URI . '/images/privacy.svg',
            );
        }

        return $tabs;
    }

    /**
     * General tab content
     *
     * @param array $tab
     *
     * @return void
     */
    function settings_tab_general( $tab ) {
        include dirname( __FILE__ ) . '/views/weforms-settings-general.php';
    }

    /**
     * recaptcha tab content
     *
     * @param array $tab
     *
     * @return void
     */
    function settings_tab_recaptcha( $tab ) {
        include dirname( __FILE__ ) . '/views/weforms-settings-recaptcha.php';
    }

    /**
     * Privacy tab content
     *
     * @param array $tab
     *
     * @return void
     */
    function settings_tab_privacy( $tab ) {
        include dirname( __FILE__ ) . '/views/weforms-settings-privacy.php';
    }
}
