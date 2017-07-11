<?php

/**
 * Contact form class
 */
class WPUF_Contact_Form_Builder {

    private $form_type = 'contact_form';
    private $hook = 'toplevel_page_best-contact-forms';

    public function __construct() {

        add_action( 'load-'. $this->hook, array( $this, 'form_builder_init' ) );
        add_action( 'load-'. $this->hook, array( $this, 'builder_enqueue_scripts' ) );
    }

    /**
     * Init the form builder
     *
     * @return void
     */
    public function form_builder_init() {
        $form_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

        if ( ! $form_id ) {
            return;
        }

        add_action( 'wpuf-form-builder-tabs-' . $this->form_type, array( $this, 'add_primary_tabs' ) );
        add_action( 'wpuf-form-builder-tab-contents-' . $this->form_type, array( $this, 'add_primary_tab_contents' ) );

        add_action( 'wpuf-form-builder-settings-tabs-' . $this->form_type, array( $this, 'add_settings_tabs' ) );
        add_action( 'wpuf-form-builder-settings-tab-contents-' . $this->form_type, array( $this, 'add_settings_tab_contents' ) );

        add_action( 'wpuf-form-builder-js-builder-stage-mixins', array( $this, 'js_builder_stage_mixins' ) );
        add_action( 'wpuf-form-builder-template-builder-stage-submit-area', array( $this, 'add_form_submit_area' ) );

        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        add_filter( 'wpuf-form-builder-js-deps', array( $this, 'js_dependencies' ) );

        add_action( 'wpuf-form-builder-enqueue-after-components', array( $this, 'admin_enqueue_scripts_components' ) );
        add_filter( 'wpuf-form-builder-field-settings', array( $this, 'add_field_settings' ) );
        add_action( 'wpuf-form-builder-add-js-templates', array( $this, 'add_form_components' ) );
        add_filter( 'wpuf-form-builder-fields-custom-fields', array( $this, 'add_custom_fields' ) );

        do_action( 'wpuf-form-builder-init-type-wpuf_forms' );

        $settings = array(
            'form_type'         => 'contact_form',
            'post_type'         => 'wpuf_contact_form',
            'post_id'           => $form_id,
            'form_settings_key' => 'wpuf_form_settings',
            'shortcodes'        => array( array( 'name' => 'wpuf_contact_form' ) )
        );

        new WPUF_Admin_Form_Builder( $settings );
    }

    /**
     * Enqueue scripts and styles
     *
     * @return void
     */
    public function builder_enqueue_scripts() {
        wp_enqueue_style( 'jquery-ui', WPUF_ASSET_URI . '/css/jquery-ui-1.9.1.custom.css' );
        wp_enqueue_style( 'wpuf-formbuilder', WPUF_ASSET_URI . '/css/formbuilder.css' );
        wp_enqueue_style( 'wpuf-cf-admin', WPUF_CONTACT_FORM_ASSET_URI . '/css/admin.css' );

        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_script( 'jquery-ui-timepicker', WPUF_ASSET_URI . '/js/jquery-ui-timepicker-addon.js', array('jquery-ui-datepicker') );
        wp_enqueue_script( 'wpuf-cf-comp-notification', WPUF_CONTACT_FORM_ASSET_URI . '/js/components-notification.js', array( 'wpuf-vue', 'wpuf-vuex' ), false, true );
        wp_localize_script( 'wpuf-cf-comp-notification', 'wpufCFBuilderNotification', array(
            'defaultNotification' => array(
                'active'      => 'true',
                'name'        => 'Admin Notification',
                'subject'     => '[{from_name}] New Form Submission #{entry_id}',
                'to'          => '{admin_email}',
                'replyTo'     => '',
                'message'     => '{all_fields}',
                'fromName'    => '',
                'fromAddress' => '{admin_email}',
                'cc'          => '',
                'bcc'         => ''
            )
        ) );
    }

    /**
     * Enqueue form builder components
     *
     * @return void
     */
    public function admin_enqueue_scripts_components() {
        wp_enqueue_script( 'wpuf-cf-form-builder-components', WPUF_CONTACT_FORM_ASSET_URI . '/js/form-builder-components.js', array( 'wpuf-form-builder-components' ), WPUF_CONTACT_FORM_VERSION, true );
    }

    /**
     * Field settings for custom components
     *
     * @param array $settings
     */
    public function add_field_settings( $settings ) {
        require_once dirname( __FILE__ ) . '/class-contact-form-builder-settings.php';

        $settings = array_merge( WPUF_Contact_Form_Builder_Field_Settings::get_field_settings(), $settings );

        return $settings;
    }

    /**
     * Add custom fields
     *
     * @param array $fields
     */
    public function add_custom_fields( $fields ) {
        $new_fields = array( 'name_field' );
        $fields     = array_merge( $new_fields, $fields );

        return $fields;
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
            'notification' => __( 'Notifications', 'best-contact-form' ),
            'integration'  => __( 'Integrations', 'best-contact-form' )
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

            <a href="#wpuf-metabox-settings" class="nav-tab"><?php _e( 'Form Settings', 'best-contact-form' ); ?></a>
            <a href="#wpuf-metabox-settings-restriction" class="nav-tab"><?php _e( 'Submission Restriction', 'best-contact-form' ); ?></a>
            <a href="#wpuf-metabox-settings-display" class="nav-tab"><?php _e( 'Display Settings', 'best-contact-form' ); ?></a>

            <?php do_action( 'wpuf_contact_form_settings_tab' ); ?>

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

            <div id="wpuf-metabox-settings-display" class="group">
                <?php include_once dirname( __FILE__ ) . '/views/display-settings.php'; ?>
            </div>

            <?php do_action( 'wpuf_contact_form_settings_tab_content' ); ?>

        <?php
    }

    /**
     * Add Vue components
     *
     * @return void
     */
    public function add_form_components() {
        // get all vue component names
        $path = WPUF_CONTACT_FORM_ROOT . '/assets/components';

        $components = array();

        // directory handle
        $dir = dir( $path );

        while ( $entry = $dir->read() ) {
            if ( $entry !== '.' && $entry !== '..' ) {
               if ( is_dir( $path . '/' . $entry ) ) {
                    $components[] = $entry;
               }
            }
        }

        // html templates of vue components
        foreach ( $components as $component ) {
            WPUF_Admin_Form_Builder::include_js_template( $component, $path );
        }
    }

}
