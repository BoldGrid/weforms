/* assets/js/utils/jquery-siaf-start.js */
;(function($) {
'use strict';

/* ./assets/components/field-name/index.js */
Vue.component('field-name', {
    template: '#tmpl-wpuf-field-name',

    mixins: [
        wpuf_mixins.option_field_mixin
    ],

    computed: {
        value: {
            get: function () {
                return this.editing_form_field[this.option_field.name];
            },

            set: function (value) {
                this.update_value(this.option_field.name, value);
            }
        }
    },

    methods: {
        on_focusout: function (e) {
            wpuf_form_builder.event_hub.$emit('field-text-focusout', e, this);
        },
        on_keyup: function (e) {
            wpuf_form_builder.event_hub.$emit('field-text-keyup', e, this);
        }
    }
});

/* ./assets/components/form-name_field/index.js */
/**
 * Field template: First Name
 */
Vue.component('form-name_field', {
    template: '#tmpl-wpuf-form-name_field',

    mixins: [
        wpuf_mixins.form_field_mixin
    ]
});

/* ./assets/components/integration-slack/index.js */
Vue.component('wpuf-integration-slack', {
    template: '#tmpl-wpuf-integration-slack',
    mixins: [wpuf_mixins.integration_mixin]
});
/* ./assets/components/template-modal/index.js */
Vue.component('wpuf-template-modal', {
    template: '#tmpl-wpuf-template-modal',

    props: {
        show: Boolean,
        onClose: Function
    },

    data: function() {
        return {
            loading: false
        };
    },

    methods: {

        blankForm: function(target) {
            this.createForm('blank_form', target);
        },

        createForm: function(form, target) {
            var self = this,
                list = $(target).parents('li');

            // already on a request?
            if ( self.loading ) {
                return;
            }

            self.loading = true;

            if ( list ) {
                list.addClass('on-progress');
            }

            wp.ajax.send( 'weforms_contact_form_template', {
                data: {
                    template: form,
                    _wpnonce: wpufContactForm.nonce
                },

                success: function(response) {
                    self.$router.push({
                        name: 'edit',
                        params: { id: response.id }
                    });
                },

                error: function(error) {

                },

                complete: function() {
                    self.loading = false;

                    if ( list ) {
                        list.removeClass('on-progress');
                    }
                }
            });
        }
    }
});

/* assets/js/utils/jquery-siaf-end.js */
})(jQuery);
