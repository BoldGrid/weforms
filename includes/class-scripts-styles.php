<?php

/**
 * The scripts class
 *
 * @since 1.1.0
 */
class WeForms_Scripts_Styles {

    /**
     * The constructor
     */
    function __construct() {

        if ( is_admin() ) {
            add_action( 'admin_enqueue_scripts', array( $this, 'register_backend' ), 1800 );
            add_action( 'admin_enqueue_scripts', array( $this, 'no_conflict_mode' ), 1500 );
        } else {
            add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend' ) );
        }
    }

    /**
     * Helper function for No-Conflict Mode
     *
     * @return void
     */
    public function no_conflict_mode() {
        global $wp_scripts;

        $reg_arr = array();
        $required_objects = array (
            'utils',
            'common',
            'wp-ajax-response',
            'wp-api-request',
            'wp-pointer',
            'wp-auth-check',
            'wp-lists',
            'prototype',
            'jquery',
            'jquery-core',
            'jquery-migrate',
            'jquery-ui-core',
            'jquery-effects-core',
            'jquery-effects-blind',
            'jquery-effects-bounce',
            'jquery-effects-clip',
            'jquery-effects-drop',
            'jquery-effects-explode',
            'jquery-effects-fade',
            'jquery-effects-fold',
            'jquery-effects-highlight',
            'jquery-effects-puff',
            'jquery-effects-pulsate',
            'jquery-effects-scale',
            'jquery-effects-shake',
            'jquery-effects-size',
            'jquery-effects-slide',
            'jquery-effects-transfer',
            'jquery-ui-accordion',
            'jquery-ui-autocomplete',
            'jquery-ui-button',
            'jquery-ui-datepicker',
            'jquery-ui-dialog',
            'jquery-ui-draggable',
            'jquery-ui-droppable',
            'jquery-ui-menu',
            'jquery-ui-mouse',
            'jquery-ui-position',
            'jquery-ui-progressbar',
            'jquery-ui-resizable',
            'jquery-ui-selectable',
            'jquery-ui-selectmenu',
            'jquery-ui-slider',
            'jquery-ui-sortable',
            'jquery-ui-spinner',
            'jquery-ui-tabs',
            'jquery-ui-tooltip',
            'jquery-ui-widget',
            'jquery-form',
            'jquery-color',
            'schedule',
            'jquery-query',
            'jquery-serialize-object',
            'jquery-hotkeys',
            'jquery-table-hotkeys',
            'jquery-touch-punch',
            'thickbox',
            'json2',
            'underscore',
            'backbone',
            'wp-util',
            'wp-sanitize',
            'wp-backbone',
            'wp-embed',
            'wp-api',
            'postbox',
            'post',
            'link',
            'inline-edit-post',
            'inline-edit-tax',
            'iris',
            'wp-color-picker',
            'dashboard',
            'weforms-chart-js',
            'weforms-tiny-mce',
            'weforms-vendor',
            'weforms-form-builder-mixins',
            'weforms-form-builder-mixins-form',
            'weforms-form-builder-components',
            'weforms-int-payment-settings',
            'weforms-int-mailpoet',
            'weforms-int-aweber',
            'weforms-int-campaign-monitor',
            'weforms-int-constant-contact',
            'weforms-int-convertkit',
            'weforms-int-getresponse',
            'weforms-int-google-analytics',
            'weforms-int-google-sheets',
            'weforms-int-hubspot',
            'weforms-int-mailchimp',
            'weforms-int-salesforce',
            'weforms-int-trello',
            'weforms-int-zapier',
            'weforms-int-zoho',
            'weforms-mixins',
            'weforms-components',
            'weforms-app',
            'weforms-pro-wpuf-form-mixins',
            'weforms-pro-wpuf-form-components',
            'weforms-pro-components',
        );

        $wpuf_settings = weforms_get_settings();
        $screen        = get_current_screen();

        if ( isset( $wpuf_settings['no_conflict'] ) && $wpuf_settings['no_conflict'] && $screen->base == 'toplevel_page_weforms' ) {
            $registered_scripts = array_keys( $wp_scripts->registered );

            foreach ( $registered_scripts as $r_script ) {
                if ( !in_array( $r_script, $required_objects ) ) {
                    $reg_arr[] = $r_script;
                    wp_deregister_script( $r_script );
                }
            }

//            $this->enqueue_scripts( $this->get_admin_scripts() );

//            foreach ( $reg_arr as $reg_obj ) {
//                if ( !in_array( $reg_obj, $wp_scripts->queue ) ) {
//                    wp_enqueue_script( $reg_obj );
//                }
//            }
        }

    }

    /**
     * Register frontend scripts and styles
     *
     * @return void
     */
    public function register_frontend() {
        $this->register_styles( $this->get_frontend_styles() );
        $this->register_scripts( $this->get_frontend_scripts() );

        $this->get_frontend_localized();
    }

    /**
     * Register frontend scripts and styles
     *
     * @return void
     */
    public function register_backend() {
        // bail out if not weforms screen
        $screen = get_current_screen();
        if ( $screen->base != 'toplevel_page_weforms' ) {
            return;
        }

        $this->register_styles( $this->get_admin_styles() );
        $this->register_scripts( $this->get_admin_scripts() );

        $this->get_frontend_localized();
    }

    /**
     * Enqueue all the scripts and styles for frontend
     *
     * @return void
     */
    public function enqueue_frontend() {
        $this->enqueue_scripts( $this->get_frontend_scripts() );
        $this->enqueue_styles( $this->get_frontend_styles() );
    }

    /**
     * Enqueue all the scripts and styles for backend
     *
     * @return void
     */
    public function enqueue_backend() {
        $this->enqueue_scripts( $this->get_admin_scripts() );
        $this->enqueue_styles( $this->get_admin_styles() );
    }

    /**
     * Get file prefix
     *
     * @return string
     */
    public function get_prefix() {
        $prefix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

        return $prefix;
    }

    /**
     * Get all registered admin scripts
     *
     * @return array
     */
    public function get_admin_scripts() {
        $prefix = $this->get_prefix();

        $form_builder_js_deps = apply_filters( 'weforms-form-builder-js-deps', array(
            'jquery',
            'jquery-ui-sortable',
            'jquery-ui-draggable',
            'jquery-ui-datepicker',
            'weforms-tiny-mce',
            'underscore',
        ) );

        $builder_scripts = apply_filters( 'weforms_builder_scripts', array(
            'weforms-tiny-mce' => array(
                'src'       => site_url( '/wp-includes/js/tinymce/tinymce.min.js' ),
                'deps'      => array(),
                'in_footer' => true
            ),
            'weforms-vendor' => array(
                'src'       => WEFORMS_ASSET_URI . '/js/vendor' . $prefix . '.js',
                'deps'      => $form_builder_js_deps,
                'in_footer' => true
            ),
            'weforms-form-builder-mixins' => array(
                'src'       => WEFORMS_ASSET_URI . '/wpuf/js/wpuf-form-builder-mixins.js',
                'deps'      => array('weforms-vendor'),
                'in_footer' => true
            ),
            'weforms-form-builder-mixins-form' => array(
                'src'       => WEFORMS_ASSET_URI . '/js/wpuf-form-builder-contact-forms' . $prefix . '.js',
                'deps'      => array('weforms-vendor'),
                'in_footer' => true
            ),
            'weforms-form-builder-components' => array(
                'src'       => WEFORMS_ASSET_URI . '/wpuf/js/wpuf-form-builder-components' . $prefix . '.js',
                'deps'      => array( 'weforms-form-builder-mixins', 'weforms-form-builder-mixins-form' ),
                'in_footer' => true
            ),
        ) );

        $spa_scripts = array(
            'weforms-mixins' => array(
                'src'       => WEFORMS_ASSET_URI . '/js/spa-mixins' . $prefix . '.js',
                'deps'      => array( 'weforms-vendor', 'wp-util' ),
                'in_footer' => true
            ),
            'weforms-components' => array(
                'src'       => WEFORMS_ASSET_URI . '/js/form-builder-components' . $prefix . '.js',
                'deps'      => array( 'weforms-vendor', 'wp-util' ),
                'in_footer' => true
            ),
            'weforms-app' => array(
                'src'       => WEFORMS_ASSET_URI . '/js/spa-app' . $prefix . '.js',
                'deps'      => array( 'weforms-vendor', 'wp-util', 'weforms-form-builder-components' ),
                'in_footer' => true
            ),
        );

        $scripts = array_merge( $builder_scripts, $spa_scripts );

        return apply_filters( 'weforms_admin_scripts', $scripts );
    }

    /**
     * Get admin styles
     *
     * @return array
     */
    public function get_admin_styles() {
        $frontend_styles = $this->get_frontend_styles();

        $backend_styles = array(
            'weforms-font-awesome' => array(
                'src'  => WEFORMS_ASSET_URI . '/wpuf/vendor/font-awesome/css/font-awesome.min.css',
            ),
            'weforms-sweetalert2' => array(
                'src'  => WEFORMS_ASSET_URI . '/wpuf/vendor/sweetalert2/dist/sweetalert2.css',
            ),
            'weforms-selectize' => array(
                'src'  => WEFORMS_ASSET_URI . '/wpuf/vendor/selectize/css/selectize.default.css',
            ),
            'weforms-toastr' => array(
                'src'  => WEFORMS_ASSET_URI . '/wpuf/vendor/toastr/toastr.min.css',
            ),
            'weforms-tooltip' => array(
                'src'  => WEFORMS_ASSET_URI . '/wpuf/vendor/tooltip/tooltip.css',
            ),
            'weforms-form-builder' => array(
                'src'  => WEFORMS_ASSET_URI . '/wpuf/css/wpuf-form-builder.css',
                'deps' => array(
                    'weforms-css',
                    'weforms-font-awesome',
                    'weforms-sweetalert2',
                    'weforms-selectize',
                    'weforms-toastr',
                    'weforms-tooltip'
                )
            ),
            'weforms-style' => array(
                'src'  => WEFORMS_ASSET_URI . '/css/admin.css',
            ),
            'weforms-tiny-mce-css' => array(
                'src'  => site_url( '/wp-includes/css/editor.css' ),
                'deps' => array( 'wp-color-picker' )
            )

        );

        $styles = array_merge( $frontend_styles, $backend_styles );

        return apply_filters( 'weforms_admin_styles', $styles );
    }

    /**
     * Get all registered frontend scripts
     *
     * @return array
     */
    public function get_frontend_scripts() {

        $prefix = $this->get_prefix();

        $scripts = array(
            'wpuf-form' => array(
                'src'       => WEFORMS_ASSET_URI . '/wpuf/js/frontend-form' . $prefix . '.js',
                'deps'      => array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-slider' ),
                'in_footer' => false
            ),
            'wpuf-sweetalert2' => array(
                'src'       => WEFORMS_ASSET_URI . '/wpuf/vendor/sweetalert2/dist/sweetalert2' . $prefix . '.js',
                'in_footer' => false
            ),
            'jquery-ui-timepicker' => array(
                'src'       => WEFORMS_ASSET_URI . '/wpuf/js/jquery-ui-timepicker-addon' . $prefix . '.js',
                'deps'      => array( 'jquery-ui-datepicker' ),
                'in_footer' => false
            ),
            'wpuf-upload' => array(
                'src'       => WEFORMS_ASSET_URI . '/wpuf/js/upload' . $prefix . '.js',
                'deps'      => array( 'jquery', 'plupload-handlers', 'jquery-ui-sortable' ),
                'in_footer' => false
            )
        );

        return apply_filters( 'weforms_frontend_scripts', $scripts );
    }

    /**
     * Get all registered frontend styles
     *
     * @return array
     */
    public function get_frontend_styles() {
        $styles = array(
            'weforms-css' => array(
                'src'  => WEFORMS_ASSET_URI . '/wpuf/css/frontend-forms.css',
            ),
            'wpuf-sweetalert2' => array(
                'src'  => WEFORMS_ASSET_URI . '/wpuf/vendor/sweetalert2/dist/sweetalert2.css',
            ),
            'jquery-ui' => array(
                'src'  => WEFORMS_ASSET_URI . '/wpuf/css/jquery-ui-1.9.1.custom.css',
            )
        );

        return apply_filters( 'weforms_frontend_styles', $styles );
    }

    /**
     * Frontend localized scripts
     *
     * @return void
     */
    public function get_frontend_localized() {

        wp_localize_script( 'wpuf-form', 'wpuf_frontend', apply_filters( 'wpuf_frontend_js_data' , array(
            'ajaxurl'       => admin_url( 'admin-ajax.php' ),
            'error_message' => __( 'Please fix the errors to proceed', 'weforms' ),
            'nonce'         => wp_create_nonce( 'wpuf_nonce' ),
            'word_limit'    => __( 'Word limit reached', 'weforms' )
        ) ) );

        wp_localize_script( 'wpuf-form', 'error_str_obj', array(
            'required'   => __( 'is required', 'weforms' ),
            'mismatch'   => __( 'does not match', 'weforms' ),
            'validation' => __( 'is not valid', 'weforms' ),
            'duplicate'  => __( 'requires a unique entry and this value has already been used', 'weforms' ),
        ) );

        wp_localize_script( 'wpuf-upload', 'wpuf_frontend_upload', array(
            'confirmMsg' => __( 'Are you sure?', 'weforms' ),
            'delete_it'  => __( 'Yes, delete it', 'weforms' ),
            'cancel_it'  => __( 'No, cancel it', 'weforms' ),
            'nonce'      => wp_create_nonce( 'wpuf_nonce' ),
            'ajaxurl'    => admin_url( 'admin-ajax.php' ),
            'plupload'   => array(
                'url'              => admin_url( 'admin-ajax.php' ) . '?nonce=' . wp_create_nonce( 'wpuf-upload-nonce' ),
                'flash_swf_url'    => includes_url( 'js/plupload/plupload.flash.swf' ),
                'filters'          => array(
                    array(
                        'title' => __( 'Allowed Files', 'weforms' ),
                        'extensions' => '*'
                    )
                ),
                'multipart'        => true,
                'urlstream_upload' => true,
                'warning'          => __( 'Maximum number of files reached!', 'weforms' ),
                'size_error'       => __( 'The file you have uploaded exceeds the file size limit. Please try again.', 'weforms' ),
                'type_error'       => __( 'You have uploaded an incorrect file type. Please try again.', 'weforms' )
            )
        ) );
    }

    /**
     * Register scripts
     *
     * @param  array $scripts
     *
     * @return void
     */
    public function register_scripts( $scripts ) {
        foreach ( $scripts as $handle => $script ) {
            $deps      = isset( $script['deps'] ) ? $script['deps'] : false;
            $in_footer = isset( $script['in_footer'] ) ? $script['in_footer'] : false;

            wp_register_script( $handle, $script['src'], $deps, WEFORMS_VERSION, $in_footer );
        }
    }

    /**
     * Register styles
     *
     * @param  array $styles
     *
     * @return void
     */
    public function register_styles( $styles ) {
        foreach ( $styles as $handle => $style ) {
            $deps = isset( $style['deps'] ) ? $style['deps'] : false;

            wp_register_style( $handle, $style['src'], $deps, WEFORMS_VERSION );
        }
    }

    /**
     * Enqueue the scripts
     *
     * @param  array $scripts
     *
     * @return void
     */
    public function enqueue_scripts( $scripts ) {
        foreach ( $scripts as $handle => $script ) {
            wp_enqueue_script( $handle );
        }
    }

    /**
     * Enqueue styles
     *
     * @param  array $styles
     *
     * @return void
     */
    public function enqueue_styles( $styles ) {
        foreach ( $styles as $handle => $script ) {
            wp_enqueue_style( $handle );
        }
    }
}
