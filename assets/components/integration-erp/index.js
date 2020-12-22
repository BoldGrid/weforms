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