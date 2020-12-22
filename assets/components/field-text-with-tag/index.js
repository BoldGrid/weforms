Vue.component('field-text-with-tag', {
    template: '#tmpl-wpuf-field-text-with-tag',

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
        },
        insertValue: function(type, field, property) {
            var value = ( field !== undefined ) ? '{' + type + ':' + field + '}' : '{' + type + '}';
            this.value = value;
        },
    }
});
