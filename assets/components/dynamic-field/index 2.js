Vue.component('field-dynamic-field', {
    mixins: [wpuf_mixins.option_field_mixin],
    template: '#tmpl-wpuf-dynamic-field',
    data: function(){
        return {
            dynamic: {
                status: false,
                param_name: '',
            }
        }
    },
    computed: {
        dynamic: function(){
            return this.editing_form_field.dynamic;
        },
        editing_field: function(){
            return this.editing_form_field;
        },
    },

    created: function () {
        this.dynamic = $.extend(false, this.dynamic, this.editing_form_field.dynamic);
    },

    methods: {

    },

    watch: {
        dynamic: function(){
            this.update_value('dynamic', this.dynamic);
        }
    },
});
