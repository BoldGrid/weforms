;(function($) {

const Forms = {
    data: [
        { id: 1, title: 'Form 1' },
        { id: 2, title: 'Form 2' }
    ]
};

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
// These can be imported from other files
const Home = { template: '#tmpl-wpuf-home-page' };
const Create = {
    template: '#tmpl-wpuf-create-page',
    data: function() {
        return {
            title: ''
        }
    },
    methods: {
        insertForm: function() {
            var promise = wp.ajax.send('wpuf_contact_form_create', {
                type: 'POST',
                data: {
                    form_name: this.title
                },
                success: function(response) {
                    console.log(response);
                    router.push({ name: 'home' });
                },
                error: function(error) {
                    alert(error);
                }
            });
        }
    }
};
const FormHome = { template: '<div><router-view class="child"></router-view></div>' };
const SingleForm = { template: '#tmpl-wpuf-form-editor' };
const FormEntries = { template: '#tmpl-wpuf-form-entries' };

// 2. Define some routes
// Each route should map to a component. The "component" can
// either be an actual component constructor created via
// Vue.extend(), or just a component options object.
// We'll talk about nested routes later.
const routes = [
    {
        path: '/',
        name: 'home',
        component: Home
    },
    {
        path: '/create', component: Create
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
                name: 'formEntries',
                component: FormEntries
            }
        ]
    }
]

// 3. Create the router instance and pass the `routes` option
// You can pass in additional options here, but let's
// keep it simple for now.
const router = new VueRouter({
    // mode: 'history',
    routes // short for routes: routes
});

// 4. Create and mount the root instance.
// Make sure to inject the router with the router option to make the
// whole app router-aware.
const app = new Vue({
    router
}).$mount('#wpuf-contact-form-app')

})(jQuery);