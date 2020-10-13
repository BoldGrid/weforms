;(function($) {

    Vue.component('wpuf-integration-mailpoet', {
        template: '#tmpl-wpuf-integration-mailpoet',
        mixins: [wpuf_mixins.integration_mixin],

        data: function() {
            return {
                lists: []
            };
        },

        computed: {

        },

        created: function() {
            this.fetchLists();
        },

        methods: {

            fetchLists: function(target) {
                var self = this;

                wp.ajax.send('wpuf_mailpoet_fetch_lists', {
                    data: {
                        _wpnonce: weForms.nonce
                    },

                    success: function(response) {
                        self.lists = response;
                    },
                });
            },

            insertValue: function(type, field, property) {
                var value = ( field !== undefined ) ? '{' + type + ':' + field + '}' : '{' + type + '}';

                this.settings.fields[property] = value;
            }
        }
    });

})(jQuery);