weForms.routeComponents.Transactions = {
    template: '#tmpl-wpuf-transactions',
    data: function() {
        return {
            selected: 0,
            no_transactions: false,
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
                    filter: 'transactions',
                },
                success: function(response) {

                    if ( Object.keys(response.forms).length ) {
                        self.forms = response.forms;
                        self.selected = self.forms[Object.keys(self.forms)[0]].id;
                    } else {
                        self.form_title = 'No transaction found';
                        self.no_transactions = true;
                    }
                },
                error: function(error) {
                    alert(error);
                }
            });
        },
    },
};
