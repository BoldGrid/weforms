Vue.component('wpuf-integration-sprout-invoices', {
    template: '#tmpl-wpuf-integration-sprout-invoices',
    mixins: [wpuf_mixins.integration_mixin],

    methods: {
        insertValue: function(type, field, property) {
            var value = ( field !== undefined ) ? '{' + type + ':' + field + '}' : '{' + type + '}';
            console.log(value)
            this.settings.fields[property] = value;
        }
    }
});
