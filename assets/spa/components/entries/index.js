weForms.routeComponents.Entries = {
    template: '#tmpl-wpuf-entries',
    data: function() {
        return {
            selected: 0,
            forms: {},
            form_title: 'Loading...',
            status: 'publish',
            total: 0,
            totalTrash: 0,
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
                    posts_per_page: -1,
                    filter: 'entries',
                },
                success: function(response) {
                    if ( Object.keys( response.forms ).length ) {
                        self.forms = response.forms;
                        self.selected = self.forms[Object.keys(self.forms)[0]].id;
                    } else {
                        self.form_title = 'No entry found';
                    }
                },
                error: function(error) {
                    alert(error);
                }
            });
        },
    },
};
