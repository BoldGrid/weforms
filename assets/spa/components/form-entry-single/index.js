weForms.routeComponents.FormEntriesSingle = {
    template: '#tmpl-wpuf-form-entry-single',
    mixins: [weForms.mixins.Loading],
    data: function() {
        return {
            loading: false,
            entry: {
                form_fields: {},
                meta_data: {}
            }
        };
    },
    created: function() {
        this.fetchData();
    },
    computed: {
        hasFormFields: function() {
            return Object.keys(this.entry.form_fields).length;
        }
    },
    methods: {
        fetchData: function() {
            var self = this;

            this.loading = true;

            wp.ajax.send( 'weforms_form_entry_details', {
                data: {
                    entry_id: self.$route.params.entryid,
                    form_id: self.$route.params.id,
                    _wpnonce: weForms.nonce
                },
                success: function(response) {
                    // console.log(response);
                    self.loading = false;
                    self.entry = response;
                },
                error: function(error) {
                    self.loading = false;
                    alert(error);
                }
            });
        },

        trashEntry: function() {
            var self = this;

            if ( !confirm( weForms.confirm ) ) {
                return;
            }

            wp.ajax.send( 'weforms_form_entry_trash', {
                data: {
                    entry_id: self.$route.params.entryid,
                    _wpnonce: weForms.nonce
                },

                success: function() {
                    self.loading = false;

                    self.$router.push({ name: 'formEntries', params: { id: self.$route.params.id }});
                },
                error: function(error) {
                    self.loading = false;
                    alert(error);
                }
            });
        }
    }
};
