'use strict';

;(function ($) {
    'use strict';

    /**
     * Only proceed if current page is a 'Profile Forms' form builder page
     */

    if (!$('#wpuf-form-builder.wpuf-form-builder-contact_form').length) {
        // return;
    }

    window.weforms_mixin_builder_root = {
        data: function data() {
            return {
                validation_error_msg: wpuf_form_builder.i18n.email_needed
            };
        },

        methods: {
            // wpuf_profile must have 'user_email'
            // field template
            validate_form_before_submit: function validate_form_before_submit() {
                return true;
            }
        }
    };

    window.weforms_mixin_builder_stage = {

        computed: {
            settings: function settings() {
                return this.$store.state.settings;
            },

            label_type: function label_type() {
                return this.$store.state.settings.label_position;
            },

            use_theme_css: function use_theme_css() {
                return this.$store.state.settings.use_theme_css;
            }
        },

        mounted: function mounted() {
            var self = this;

            // $('.wpuf-input-datetime').datetimepicker({
            //     dateFormat: 'yy-mm-dd',
            //     timeFormat: "HH:mm:ss"
            // });
        }
    };
})(jQuery);
