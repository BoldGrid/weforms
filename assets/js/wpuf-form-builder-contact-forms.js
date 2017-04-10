;(function($) {
    'use strict';

    /**
     * Only proceed if current page is a 'Profile Forms' form builder page
     */
    if (!$('#wpuf-form-builder.wpuf-form-builder-contact_form').length) {
        // return;
    }

    window.wpuf_forms_mixin_root = {
        data: function () {
            return {
                validation_error_msg: wpuf_form_builder.i18n.email_needed,
            };
        },

        methods: {
            // wpuf_profile must have 'user_email'
            // field template
            validate_form_before_submit: function () {
                return true;
            }
        }
    };

    window.wpuf_forms_mixin_builder_stage = {
        data: function () {
            return {
                form_settings: {
                    submit_text: 'Submit'
                }
            };
        },

        mounted: function () {
            var self = this;

            // submit button text
            this.form_settings.submit_text = $('[name="wpuf_settings[submit_text]"]').val();

            $('[name="wpuf_settings[submit_text]"]').on('change', function () {
                self.form_settings.submit_text = $(this).val();
            });
        }
    };

})(jQuery);
