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

/* ./assets/components/integration/index.js */
Vue.component('wpuf-integration', {
    template: '#tmpl-wpuf-integration',

    computed: {

        integrations: function() {
            return wpuf_form_builder.integrations;
        },

        hasIntegrations: function() {
            return Object.keys(this.integrations).length;
        },

        store: function() {
            return this.$store.state.integrations;
        },

        pro_link: function() {
            return wpuf_form_builder.pro_link;
        }
    },

    methods: {

        getIntegration: function(id) {
            return this.integrations[id];
        },

        getIntegrationSettings: function(id) {
            // find settings in store, otherwise take from default integration settings
            return this.store[id] || this.getIntegration(id).settings;
        },

        isActive: function(id) {
            if ( !this.isAvailable(id) ) {
                return false;
            }

            return this.getIntegrationSettings(id).enabled === true;
        },

        isAvailable: function(id) {
            return ( this.integrations[id] && this.integrations[id].pro ) ? false : true;
        },

        toggleState: function(id, target) {
            if ( ! this.isAvailable(id) ) {
                this.alert_pro_feature( id );
                return;
            }

            // toggle the enabled state
            var settings = this.getIntegrationSettings(id);

            settings.enabled = !this.isActive(id);

            this.$store.commit('updateIntegration', {
                index: id,
                value: settings
            });

            $(target).toggleClass('checked');
        },

        alert_pro_feature: function (id) {
            var title = this.getIntegration(id).title;

            swal({
                title: '<i class="fa fa-lock"></i> ' + title + ' <br>' + this.i18n.is_a_pro_feature,
                text: this.i18n.pro_feature_msg,
                type: '',
                showCancelButton: true,
                cancelButtonText: this.i18n.close,
                confirmButtonColor: '#46b450',
                confirmButtonText: this.i18n.upgrade_to_pro
            }).then(function (is_confirm) {
                if (is_confirm) {
                    window.open(wpuf_form_builder.pro_link, '_blank');
                }

            }, function() {});
        },

        showHide: function(target) {
            $(target).closest('.wpuf-integration').toggleClass('collapsed');
        },
    }
});

/* ./assets/components/integration-erp/index.js */
Vue.component('wpuf-integration-erp', {
    template: '#tmpl-wpuf-integration-erp',
    mixins: [wpuf_mixins.integration_mixin],

    methods: {
        insertValue: function(type, field, property) {
            var value = ( field !== undefined ) ? '{' + type + ':' + field + '}' : '{' + type + '}';

            this.settings.fields[property] = value;
        }
    }
});
/* ./assets/components/integration-slack/index.js */
Vue.component('wpuf-integration-slack', {
    template: '#tmpl-wpuf-integration-slack',
    mixins: [wpuf_mixins.integration_mixin]
});
/* ./assets/components/merge-tags/index.js */
Vue.component('wpuf-merge-tags', {
    template: '#tmpl-wpuf-merge-tags',
    props: {
        field: String,
        filter: {
            type: String,
            default: null
        }
    },

    data: function() {
        return {
            type: null,
        };
    },

    mounted: function() {

        // hide if clicked outside
        $('body').on('click', function(event) {
            if ( !$(event.target).closest('.wpuf-merge-tag-wrap').length) {
                $(".wpuf-merge-tags").hide();
            }
        });
    },

    computed: {
        form_fields: function () {
            var template = this.filter,
                fields = this.$store.state.form_fields;

            if (template !== null) {
                return fields.filter(function(item) {
                    return item.template === template;
                });
            }

            // remove the action/hidden fields
            return fields.filter(function(item) {
                return !_.contains( [ 'action_hook', 'custom_hidden_field'], item.template );
            });
        },
    },

    methods: {
        toggleFields: function(event) {
            $(event.target).parent().siblings('.wpuf-merge-tags').toggle('fast');
        },

        insertField: function(type, field) {
            this.$emit('insert', type, field, this.field);
        }
    }
});
/* ./assets/components/modal/index.js */
Vue.component('wpuf-modal', {
    template: '#tmpl-wpuf-modal',
    props: {
        show: Boolean,
        onClose: Function
    },

    mounted: function () {
        var self = this;

        $('body').on( 'keydown', function(e) {
            if (self.show && e.keyCode === 27) {
                self.closeModal();
            }
        });
    },

    methods: {
        closeModal: function() {
            if ( typeof this.onClose !== 'undefined' ) {
                this.onClose();
            } else {
                this.$emit('hideModal');
            }
        }
    }
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
                    _wpnonce: weForms.nonce
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
