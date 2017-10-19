weForms.routeComponents.Entries = {
    template: '#tmpl-wpuf-entries',
    data: function() {
        return {
            selected: 0,
            forms: {},
            form_title: 'Loading...',
        };
    },

    created: function(){
        this.get_forms();
    },

    methods: {
        get_forms: function(){
            var self = this;

            wp.ajax.send( 'weforms_form_list', {
                data: {
                    _wpnonce: weForms.nonce,
                    page: self.currentPage,
                },
                success: function(response) {
                    self.forms = response.forms;
                    self.selected = self.forms[0].id;

                    // self.totalItems = response.meta.total;
                    // self.totalPage = response.meta.pages;
                },
                error: function(error) {
                    alert(error);
                }
            });
        },
    },
};
