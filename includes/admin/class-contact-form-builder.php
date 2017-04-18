<?php

/**
 * Contact form class
 */
class WPUF_Contact_Form_Builder {

    private $form_type = 'contact_form';
    private $hook = 'user-frontend_page_wpuf-contact-forms';

    public function __construct() {

        add_action( 'load-'. $this->hook, array( $this, 'form_builder_init' ) );
        add_action( 'load-'. $this->hook, array( $this, 'builder_enqueue_scripts' ) );

        add_action( 'wpuf-form-builder-tabs-' . $this->form_type, array( $this, 'add_primary_tabs' ) );
        add_action( 'wpuf-form-builder-tab-contents-' . $this->form_type, array( $this, 'add_primary_tab_contents' ) );

        add_action( 'wpuf-form-builder-settings-tabs-' . $this->form_type, array( $this, 'add_settings_tabs' ) );
        add_action( 'wpuf-form-builder-settings-tab-contents-' . $this->form_type, array( $this, 'add_settings_tab_contents' ) );
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

        wp_enqueue_script( 'wpuf-cf-comp-notification', WPUF_CONTACT_FORM_ASSET_URI . '/js/components-notification.js', array( 'wpuf-vue', 'wpuf-vuex' ), false, true );

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

    public function builder_enqueue_scripts() {
        wp_enqueue_style( 'jquery-ui', WPUF_ASSET_URI . '/css/jquery-ui-1.9.1.custom.css' );
        wp_enqueue_style( 'wpuf-formbuilder', WPUF_ASSET_URI . '/css/formbuilder.css' );
        wp_enqueue_style( 'wpuf-cf-admin', WPUF_CONTACT_FORM_ASSET_URI . '/css/admin.css' );

        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_script( 'jquery-ui-timepicker', WPUF_ASSET_URI . '/js/jquery-ui-timepicker-addon.js', array('jquery-ui-datepicker') );
    }

    /**
     * Add dependencies to form builder script
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
     * @return void
     */
    public function admin_enqueue_scripts() {
        wp_enqueue_script( 'wpuf-contact-form-builder-mixin', WPUF_CONTACT_FORM_ASSET_URI . '/js/wpuf-form-builder-contact-forms.js', array( 'jquery', 'underscore', 'wpuf-vue', 'wpuf-vuex' ), WPUF_CONTACT_FORM_VERSION, true );
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
     * Additional primary tabs
     *
     * @return void
     */
    public function add_primary_tabs() {
        $tabs = apply_filters( 'wpuf_contact_form_editor_tabs', array(
            'notification' => __( 'Notifications', 'wpuf-contact-form' ),
            'integration'  => __( 'Integrations', 'wpuf-contact-form' )
        ) );

        foreach ($tabs as $key => $label) {
            ?>
            <a href="#wpuf-form-builder-tab-<?php echo $key; ?>" class="nav-tab"><?php echo $label; ?></a>
            <?php
        }
    }

    public function add_primary_tab_contents() {
        include dirname( __FILE__ ) . '/views/builder-tabs.php';
    }

    /**
     * Add settings tabs
     *
     * @return void
     */
    public function add_settings_tabs() {
        ?>

            <a href="#wpuf-metabox-settings" class="nav-tab"><?php _e( 'Form Settings', 'wpuf-contact-form' ); ?></a>
            <a href="#wpuf-metabox-settings-restriction" class="nav-tab"><?php _e( 'Submission Restriction', 'wpuf-contact-form' ); ?></a>

        <?php
    }

    /**
     * Add settings tabs
     *
     * @return void
     */
    public function add_settings_tab_contents() {
        ?>

            <div id="wpuf-metabox-settings" class="group">
                <?php include_once dirname( __FILE__ ) . '/views/form-settings.php'; ?>
            </div>

            <div id="wpuf-metabox-settings-restriction" class="group">
                <?php include_once dirname( __FILE__ ) . '/views/submission-restriction.php'; ?>
            </div>

            <?php do_action( 'wpuf_contact_form_settings_tab_content' ); ?>

        <?php
    }

}
