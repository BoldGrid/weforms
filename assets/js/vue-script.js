;(function($) {

Vue.component('form-list-table', {
    template: '#tmpl-wpuf-form-list-table',
    data: function() {
        return {
            loading: false,
            forms: []
        }
    },
    created: function() {
        this.fetchData();
    },
    watch: {
        // call again the method if the route changes
        '$route': 'fetchData'
    },
    computed: {

    },
    methods: {
        fetchData: function() {
            var self = this;

            this.loading = true

            wp.ajax.send( 'wpuf_contact_form_list', {
                success: function(response) {
                    self.loading = false
                    self.forms = response.forms;
                },
                error: function(error) {
                    self.loading = false;
                    alert(error);
                }
            });
        },

        deleteForm: function(index) {
            var self = this;

            if (confirm('Are you sure?')) {
                wp.ajax.send( 'wpuf_contact_form_delete', {
                    data: {
                        form_id: this.forms[index].ID
                    },
                    success: function(response) {
                        self.forms.splice(index, 1);
                    },
                    error: function(error) {
                        alert(error);
                    }
                });
            }
        }
    }
})

// 1. Define route components.
const Home = { template: '#tmpl-wpuf-home-page' };
const FormHome = { template: '<div><router-view class="child"></router-view></div>' };
const SingleForm = { template: '#tmpl-wpuf-form-editor' };
const FormEntriesHome = {
    template: '<div><router-view class="grand-child"></router-view></div>',
};
const FormEntriesSingle = {
    template: '#tmpl-wpuf-form-entry-single',
    data: function() {
        return {
            loading: false,
            entry: {},
        }
    },
    created: function() {
        this.fetchData();
    },
    methods: {
        fetchData: function() {
            var self = this;

            this.loading = true
            wp.ajax.send( 'wpuf_contact_form_entry_details', {
                data: {
                    entry_id: self.$route.params.entryid
                },
                success: function(response) {
                    console.log(response);
                    self.loading = false
                    self.entry = response;
                },
                error: function(error) {
                    self.loading = false;
                    alert(error);
                }
            });
        }
    }
};

Vue.component( 'wpuf-table', {
    template: '#tmpl-wpuf-component-table',
    props: {
        action: String,
        id: [String, Number]
    },
    data: function() {
        return {
            totalItems: 0,
            totalPage: 1,
            currentPage: 1,
            pageNumberInput: 1,
            loading: false,
            columns: [],
            rows: [],
            ajaxAction: this.action
        }
    },
    created: function() {
        this.fetchData();
    },
    computed: {
        columnLength: function() {
            return Object.keys(this.columns).length;
        }
    },
    methods: {
        isFirstPage: function() {
            return this.currentPage == 1;
        },

        isLastPage: function() {
            return this.currentPage == this.totalPage;
        },

        goFirstPage: function() {
            this.currentPage = 1;
            this.pageNumberInput = this.currentPage;
        },

        goLastPage: function() {
            this.currentPage = this.totalPage;
            this.pageNumberInput = this.currentPage;
        },

        goToPage: function(direction) {
            if ( direction == 'prev' ) {
                this.currentPage--;
            } else if ( direction == 'next' ) {
                this.currentPage++;
            } else {
                if ( ! isNaN( direction ) ) {
                    this.currentPage = direction;
                }
            }

            this.pageNumberInput = this.currentPage;
            this.fetchData();
        },

        fetchData: function() {
            var self = this;

            this.loading = true

            wp.ajax.send( self.action, {
                data: {
                    id: self.id,
                    page: self.currentPage
                },
                success: function(response) {
                    self.loading = false
                    self.columns = response.columns;
                    self.rows = response.entries;
                    self.form_title = response.form_title;
                    self.totalItems = response.pagination.total;
                    self.totalPage = response.pagination.pages;

                    self.$emit('ajaxsuccess', response);
                },
                error: function(error) {
                    self.loading = false;
                    alert(error);
                }
            });
        },
    }
} );

const FormEntries = {
    props: {
        id: [String, Number]
    },
    template: '#tmpl-wpuf-form-entries',
    data: function() {
        return {
            form_title: ''
        }
    },
};

// 2. Define some routes
const routes = [
    {
        path: '/',
        name: 'home',
        component: Home
    },
    {
        path: '/form/:id',
        component: FormHome,
        children: [
            {
                path: '',
                name: 'form',
                component: SingleForm
            },
            {
                path: 'entries',
                component: FormEntriesHome,
                children: [
                    {
                        path: '',
                        name: 'formEntries',
                        component: FormEntries,
                        props: true
                    },
                    {
                        path: ':entryid',
                        name: 'formEntriesSingle',
                        component: FormEntriesSingle
                    }
                ]
            },
        ]
    }
]

// 3. Create the router instance and pass the `routes` option
const router = new VueRouter({
    // mode: 'history',
    routes: routes
});

// 4. Create and mount the root instance.
const app = new Vue({
    router
}).$mount('#wpuf-contact-form-app')

})(jQuery);
