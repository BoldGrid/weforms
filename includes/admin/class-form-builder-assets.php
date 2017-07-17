<?php

/**
 * The Assets Class
 */
class WPUF_Contact_Form_Builder_Assets {

    public function __construct() {
        $this->init_actions();
    }

    public function init_actions() {
        add_action( 'admin_enqueue_scripts', array( $this, 'builder_enqueue_scripts' ) );
        add_action( 'admin_print_scripts', array( $this, 'builder_mixins_script' ) );
        add_action( 'admin_footer', array( $this, 'admin_footer_js_templates' ) );

        add_action( 'wpuf-form-builder-enqueue-after-components', array( $this, 'admin_enqueue_scripts_components' ) );
        add_filter( 'wpuf-form-builder-field-settings', array( $this, 'add_field_settings' ) );
        add_filter( 'wpuf-form-builder-fields-custom-fields', array( $this, 'add_custom_fields' ) );
        add_action( 'wpuf-form-builder-js-builder-stage-mixins', array( $this, 'js_builder_stage_mixins' ) );
        add_action( 'wpuf-form-builder-template-builder-stage-submit-area', array( $this, 'add_form_submit_area' ) );

        add_action( 'wpuf-form-builder-tabs-contact_form', array( $this, 'add_primary_tabs' ) );
        add_action( 'wpuf-form-builder-tab-contents-contact_form', array( $this, 'add_primary_tab_contents' ) );

        add_action( 'wpuf-form-builder-settings-tabs-contact_form', array( $this, 'add_settings_tabs' ) );
        add_action( 'wpuf-form-builder-settings-tab-contents-contact_form', array( $this, 'add_settings_tab_contents' ) );
    }

    public function builder_enqueue_scripts() {
        $prefix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

        /**
         * All the styles
         */
        wp_enqueue_style( 'wpuf-css', WPUF_ASSET_URI . '/css/frontend-forms.css' );
        wp_enqueue_style( 'jquery-ui', WPUF_ASSET_URI . '/css/jquery-ui-1.9.1.custom.css' );
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
         * All the scripts
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
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_script( 'jquery-ui-timepicker', WPUF_ASSET_URI . '/js/jquery-ui-timepicker-addon.js', array('jquery-ui-datepicker') );

        $form_builder_js_deps = apply_filters( 'wpuf-form-builder-js-deps', array(
            'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'underscore',
            'wpuf-vue', 'wpuf-vuex', 'wpuf-sweetalert2', 'wpuf-jquery-scrollTo',
            'wpuf-selectize', 'wpuf-toastr', 'wpuf-clipboard', 'wpuf-tooltip'
        ) );

        wp_enqueue_script( 'wpuf-form-builder-mixins', WPUF_ASSET_URI . '/js/wpuf-form-builder-mixins.js', $form_builder_js_deps, WPUF_VERSION, true );
        wp_enqueue_script( 'wpuf-form-builder-mixins-form', WPUF_CONTACT_FORM_ASSET_URI . '/js/wpuf-form-builder-contact-forms.js', $form_builder_js_deps, WPUF_VERSION, true );

        do_action( 'wpuf-form-builder-enqueue-after-mixins' );

        wp_enqueue_script( 'wpuf-form-builder-components', WPUF_ASSET_URI . '/js/wpuf-form-builder-components.js', array( 'wpuf-form-builder-mixins' ), WPUF_VERSION, true );

        do_action( 'wpuf-form-builder-enqueue-after-components' );

        /*
         * Data required for building the form
         */
        require_once WPUF_ROOT . '/admin/form-builder/class-wpuf-form-builder-field-settings.php';
        require_once WPUF_ROOT . '/includes/free/prompt.php';

        $wpuf_form_builder = apply_filters( 'wpuf-form-builder-localize-script', array(
            'i18n'                => $this->i18n(),
            'panel_sections'      => $this->get_panel_sections(),
            'field_settings'      => WPUF_Form_Builder_Field_Settings::get_field_settings(),
            'pro_link'            => WPUF_Pro_Prompt::get_pro_url(),
            'site_url'            => site_url('/'),
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

        wp_localize_script( 'wpuf-form-builder-mixins', 'wpuf_form_builder', $wpuf_form_builder );

        // mixins
        $wpuf_mixins = array(
            'root'           => apply_filters( 'wpuf-form-builder-js-root-mixins', array() ),
            'builder_stage'  => apply_filters( 'wpuf-form-builder-js-builder-stage-mixins', array() ),
            'form_fields'    => apply_filters( 'wpuf-form-builder-js-form-fields-mixins', array() ),
            'field_options'  => apply_filters( 'wpuf-form-builder-js-field-options-mixins', array() ),
        );

        wp_localize_script( 'wpuf-form-builder-mixins', 'wpuf_mixins', $wpuf_mixins );

        /**
         * Contact form SPA scripts
         */
        wp_enqueue_script( 'wpuf-cf-spa', WPUF_CONTACT_FORM_ASSET_URI . '/js/spa-app.js', array( 'wpuf-vue-router', 'wp-util' ), false, true );
        wp_localize_script( 'wpuf-cf-spa', 'wpufContactForm', array(
            'nonce'   => wp_create_nonce( 'best-contact-form' ),
            'confirm' => __( 'Are you sure?', 'best-contact-form' )
        ) );
    }

    /**
     * Print js scripts in admin head
     *
     * @since 2.5
     *
     * @return void
     */
    public function builder_mixins_script() {
        ?>
            <script>
                if (!window.Promise) {
                    var promise_polyfill = document.createElement('script');
                    promise_polyfill.setAttribute('src','https://cdn.polyfill.io/v2/polyfill.min.js');
                    document.head.appendChild(promise_polyfill);
                }
            </script>

            <script>
                var wpuf_form_builder_mixins = function(mixins, mixin_parent) {
                    if (!mixins || !mixins.length) {
                        return [];
                    }

                    if (!mixin_parent) {
                        mixin_parent = window;
                    }

                    return mixins.map(function (mixin) {
                        return mixin_parent[mixin];
                    });
                };
            </script>
        <?php
    }

    /**
     * Include vue component templates
     *
     * @since 2.5
     *
     * @return void
     */
    public function admin_footer_js_templates() {
        include WPUF_ROOT . '/assets/js-templates/form-components.php';
        include WPUF_CONTACT_FORM_ROOT . '/assets/js-templates/form-components.php';
        include WPUF_CONTACT_FORM_ROOT . '/assets/js-templates/spa-components.php';

        do_action( 'wpuf-form-builder-add-js-templates' );
    }

    /**
     * i18n translatable strings
     *
     * @since 2.5
     *
     * @return array
     */
    private function i18n() {
        return apply_filters( 'wpuf-form-builder-i18n', array(
            'advanced_options'      => __( 'Advanced Options', 'wpuf' ),
            'delete_field_warn_msg' => __( 'Are you sure you want to delete this field?', 'wpuf' ),
            'yes_delete_it'         => __( 'Yes, delete it', 'wpuf' ),
            'no_cancel_it'          => __( 'No, cancel it', 'wpuf' ),
            'ok'                    => __( 'OK', 'wpuf' ),
            'cancel'                => __( 'Cancel', 'wpuf' ),
            'close'                 => __( 'Close', 'wpuf' ),
            'last_choice_warn_msg'  => __( 'This field must contain at least one choice', 'wpuf' ),
            'option'                => __( 'Option', 'wpuf' ),
            'column'                => __( 'Column', 'wpuf' ),
            'last_column_warn_msg'  => __( 'This field must contain at least one column', 'wpuf' ),
            'is_a_pro_feature'      => __( 'is available in Pro version', 'wpuf' ),
            'pro_feature_msg'       => __( 'Please upgrade to the Pro version to unlock all these awesome features', 'wpuf' ),
            'upgrade_to_pro'        => __( 'Get the Pro version', 'wpuf' ),
            'select'                => __( 'Select', 'wpuf' ),
            'saved_form_data'       => __( 'Saved form data', 'wpuf' ),
            'unsaved_changes'       => __( 'You have unsaved changes.', 'wpuf' ),
            'copy_shortcode'        => __( 'Click to copy shortcode', 'wpuf' ),
        ) );
    }

    /**
     * Add Fields panel sections
     *
     * @since 2.5
     *
     * @return array
     */
    private function get_panel_sections() {
        $before_custom_fields = apply_filters( 'wpuf-form-builder-fields-section-before', array() );

        $sections = array_merge( $before_custom_fields, $this->get_custom_fields() );
        $sections = array_merge( $sections, $this->get_others_fields() );

        $after_custom_fields = apply_filters( 'wpuf-form-builder-fields-section-after', array() );

        $sections = array_merge( $sections, $after_custom_fields );

        return $sections;
    }

    /**
     * Custom field section
     *
     * @since 2.5
     *
     * @return array
     */
    private function get_custom_fields() {
        $fields = apply_filters( 'wpuf-form-builder-fields-custom-fields', array(
            'text_field', 'textarea_field', 'dropdown_field', 'multiple_select',
            'radio_field', 'checkbox_field', 'website_url', 'email_address',
            'custom_hidden_field', 'image_upload'
        ) );

        return array(
            array(
                'title'     => __( 'Custom Fields', 'wpuf' ),
                'id'        => 'custom-fields',
                'fields'    => $fields
            )
        );
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
     * Others field section
     *
     * @since 2.5
     *
     * @return array
     */
    private function get_others_fields() {
        $fields = apply_filters( 'wpuf-form-builder-fields-others-fields', array(
            'section_break', 'custom_html'
        ) );

        return array(
            array(
                'title'     => __( 'Others', 'wpuf' ),
                'id'        => 'others',
                'fields'    => $fields
            )
        );
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
     * Enqueue form builder components
     *
     * @return void
     */
    public function admin_enqueue_scripts_components() {
        wp_enqueue_script( 'wpuf-cf-form-builder-components', WPUF_CONTACT_FORM_ASSET_URI . '/js/form-builder-components.js', array( 'wpuf-form-builder-components' ), WPUF_CONTACT_FORM_VERSION, true );
    }

    /**
     * Add buttons in form submit area
     *
     * @return void
     */
    public function add_form_submit_area() {
        ?>
            <input @click.prevent="" type="submit" name="submit" v-model="settings.submit_text">
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
            <a href="#wpuf-form-builder-tab-<?php echo $key; ?>" :class="['nav-tab', isActiveTab( '<?php echo $key; ?>' ) ? 'nav-tab-active' : '']" v-on:click.prevent="makeActive('<?php echo $key; ?>')"><?php echo $label; ?></a>
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

            <a href="#" :class="['nav-tab', isActiveSettingsTab( 'form' ) ? 'nav-tab-active' : '']" v-on:click.prevent="makeActiveSettingsTab( 'form' )" class="nav-tab"><?php _e( 'Form Settings', 'best-contact-form' ); ?></a>
            <a href="#" :class="['nav-tab', isActiveSettingsTab( 'restriction' ) ? 'nav-tab-active' : '']" v-on:click.prevent="makeActiveSettingsTab( 'restriction' )" class="nav-tab"><?php _e( 'Submission Restriction', 'best-contact-form' ); ?></a>
            <a href="#" :class="['nav-tab', isActiveSettingsTab( 'display' ) ? 'nav-tab-active' : '']" v-on:click.prevent="makeActiveSettingsTab( 'display' )" class="nav-tab"><?php _e( 'Display Settings', 'best-contact-form' ); ?></a>

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
            <div id="wpuf-metabox-settings" class="tab-content" v-show="isActiveSettingsTab('form')">
                <?php include_once dirname( __FILE__ ) . '/views/form-settings.php'; ?>
            </div>

            <div id="wpuf-metabox-settings-restriction" class="tab-content" v-show="isActiveSettingsTab('restriction')">
                <?php include_once dirname( __FILE__ ) . '/views/submission-restriction.php'; ?>
            </div>

            <div id="wpuf-metabox-settings-display" class="tab-content" v-show="isActiveSettingsTab('display')">
                <?php include_once dirname( __FILE__ ) . '/views/display-settings.php'; ?>
            </div>

            <?php do_action( 'wpuf_contact_form_settings_tab_content' ); ?>

        <?php
    }
}