;(function($) {

var LoadingMixin = {
    watch: {
        loading: function(value) {
            if ( value ) {
                NProgress.configure({ parent: '#wpadminbar' });
                NProgress.start();
            } else {
                NProgress.done();
            }
        }
    }
}

var PaginateMixin = {
    data: function() {
        return {
            totalItems: 0,
            totalPage: 1,
            currentPage: 1,
            pageNumberInput: 1
        };
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

            this.goToPage();
        },

        goLastPage: function() {
            this.currentPage = this.totalPage;
            this.pageNumberInput = this.currentPage;

            this.goToPage();
        },

        goToPage: function(direction) {
            if ( direction == 'prev' ) {
                this.currentPage--;
            } else if ( direction == 'next' ) {
                this.currentPage++;
            } else {
                if ( ! isNaN( direction ) && ( direction <= this.totalPage ) ) {
                    this.currentPage = direction;
                }
            }

            this.pageNumberInput = this.currentPage;
            this.fetchData();
        },
    }
};

var TabsMixin = {

    methods: {
        makeActive: function(val) {
            this.activeTab = val;
        },

        isActiveTab: function(val) {
          return this.activeTab === val;
        }
    }
};

var BulkActionMixin = {
    data: function() {
        return {
            index: 'id',
            bulkAction: '-1',
            checkedItems: []
        }
    },

    computed: {
        selectAll: {
            get: function () {
                return this.items ? this.checkedItems.length == this.items.length : false;
            },

            set: function (value) {
                var selected = [];

                if (value) {
                    this.items.forEach(function (item) {
                        selected.push(item[index]);
                    });
                }

                this.checkedItems = selected;
            }
        }
    },

    methods: {
        deleteBulk: function() {
            var self = this;

            self.loading = true;

            wp.ajax.send( self.bulkDeleteAction, {
                data: {
                    ids: this.checkedItems,
                    _wpnonce: wpufContactForm.nonce
                },
                success: function(response) {
                    self.checkedItems = [];
                    self.fetchData();
                },
                error: function(error) {
                    alert(error);
                },

                complete: function(resp) {
                    self.loading = false;
                }
            });
        }
    }
};

Vue.component('form-list-table', {
    template: '#tmpl-wpuf-form-list-table',
    mixins: [LoadingMixin, PaginateMixin, BulkActionMixin],
    data: function() {
        return {
            loading: false,
            index: 'ID',
            items: [],
            bulkDeleteAction: 'bcf_contact_form_delete_bulk'
        }
    },

    created: function() {
        this.fetchData();
    },

    methods: {
        fetchData: function() {
            var self = this;

            this.loading = true

            wp.ajax.send( 'bcf_contact_form_list', {
                data: {
                    _wpnonce: wpufContactForm.nonce,
                    page: self.currentPage,
                },
                success: function(response) {
                    self.loading = false
                    self.items = response.forms;
                    self.totalItems = response.total;
                    self.totalPage = response.pages;
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
                self.loading = true;

                wp.ajax.send( 'bcf_contact_form_delete', {
                    data: {
                        form_id: this.items[index].ID,
                        _wpnonce: wpufContactForm.nonce
                    },
                    success: function(response) {
                        self.items.splice(index, 1);
                        self.loading = false;
                    },
                    error: function(error) {
                        alert(error);
                        self.loading = false;
                    }
                });
            }
        },

        duplicate: function(form_id, index) {
            var self = this;

            this.loading = true;

            wp.ajax.send( 'bcf_contact_form_duplicate', {
                data: {
                    form_id: form_id,
                    _wpnonce: wpufContactForm.nonce
                },
                success: function(response) {
                    self.items.splice(0, 0, response);
                    self.loading = false;
                },
                error: function(error) {
                    alert(error);
                    self.loading = false;
                }
            });
        },

        handleBulkAction: function() {
            if ( '-1' === this.bulkAction ) {
                alert( 'Please chose a bulk action to perform' );
                return;
            }

            if ( 'delete' === this.bulkAction ) {
                if ( ! this.checkedItems.length ) {
                    alert( 'Please select atleast one form to delete.' );
                    return;
                }

                if ( confirm( 'Are you sure to delete the forms?' ) ) {
                    this.deleteBulk();
                }
            }
        },
    }
});

Vue.component( 'wpuf-table', {
    template: '#tmpl-wpuf-component-table',
    mixins: [LoadingMixin, PaginateMixin, BulkActionMixin],
    props: {
        action: String,
        id: [String, Number]
    },

    data: function() {
        return {
            loading: false,
            columns: [],
            items: [],
            ajaxAction: this.action,
            nonce: wpufContactForm.nonce,
            bulkDeleteAction: 'bcf_contact_form_entry_trash_bulk'
        }
    },

    created: function() {
        this.fetchData();
    },

    computed: {
        columnLength: function() {
            return Object.keys(this.columns).length;
        },
    },
    methods: {

        fetchData: function() {
            var self = this;

            this.loading = true

            wp.ajax.send( self.action, {
                data: {
                    id: self.id,
                    page: self.currentPage,
                    _wpnonce: wpufContactForm.nonce
                },
                success: function(response) {
                    self.loading = false
                    self.columns = response.columns;
                    self.items = response.entries;
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

        handleBulkAction: function() {
            if ( '-1' === this.bulkAction ) {
                alert( 'Please chose a bulk action to perform' );
                return;
            }

            if ( 'delete' === this.bulkAction ) {
                if ( ! this.checkedItems.length ) {
                    alert( 'Please select atleast one entry to delete.' );
                    return;
                }

                if ( confirm( 'Are you sure to delete the entries?' ) ) {
                    this.deleteBulk();
                }
            }
        }
    }
} );

// 1. Define route components.
const Home = { template: '#tmpl-wpuf-home-page' };
const FormHome = { template: '<div><router-view class="child"></router-view></div>' };
const SingleForm = { template: '#tmpl-wpuf-form-editor' };
const FormEntriesHome = {
    template: '<div><router-view class="grand-child"></router-view></div>',
};
const FormEntriesSingle = {
    template: '#tmpl-wpuf-form-entry-single',
    mixins: [LoadingMixin],
    data: function() {
        return {
            loading: false,
            entry: {
                form_fields: {},
                meta_data: {},
                info: {}
            },
        }
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

            this.loading = true
            wp.ajax.send( 'bcf_contact_form_entry_details', {
                data: {
                    entry_id: self.$route.params.entryid,
                    _wpnonce: wpufContactForm.nonce
                },
                success: function(response) {
                    // console.log(response);
                    self.loading = false
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

            if ( !confirm( wpufContactForm.confirm ) ) {
                return;
            }

            wp.ajax.send( 'bcf_contact_form_entry_trash', {
                data: {
                    entry_id: self.$route.params.entryid,
                    _wpnonce: wpufContactForm.nonce
                },
                success: function(response) {
                    self.loading = false

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

const FormEntries = {
    props: {
        id: [String, Number]
    },
    template: '#tmpl-wpuf-form-entries',
    data: function() {
        return {
            form_title: 'Loading...'
        }
    },
};

const Tools = {
    template: '#tmpl-wpuf-tools',
    mixins: [TabsMixin, LoadingMixin],
    data: function() {
        return {
            activeTab: 'export',
            exportType: 'all',
            loading: false,
            forms: [],
            importButton: 'Import',
            currentStatus: 0,
            responseMessage: ''
        }
    },

    computed: {

        isInitial() {
            return this.currentStatus === 0;
        },

        isSaving() {
            return this.currentStatus === 1;
        },

        isSuccess() {
            return this.currentStatus === 2;
        },

        isFailed() {
            return this.currentStatus === 3;
        }
    },

    created: function() {
        this.fetchData();
    },

    methods: {
        fetchData: function() {
            var self = this;

            this.loading = true
            wp.ajax.send( 'bcf_contact_form_names', {
                data: {
                    _wpnonce: wpufContactForm.nonce
                },
                success: function(response) {
                    // console.log(response);
                    self.loading = false
                    self.forms   = response;
                },
                error: function(error) {
                    self.loading = false;
                    alert(error);
                }
            });
        },

        importForm: function( fieldName, fileList, event ) {
            if ( !fileList.length ) return;

            var formData = new FormData();
            var self = this;

            formData.append( fieldName, fileList[0], fileList[0].name);
            formData.append( 'action', 'bcf_import_form' );
            formData.append( '_wpnonce', wpufContactForm.nonce );

            self.currentStatus = 1;

            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    self.responseMessage = response.data;

                    if ( response.success ) {
                        self.currentStatus = 2;
                    } else {
                        self.currentStatus = 3;
                    }

                    // reset the value
                    $(event.target).val('');
                },

                error: function(errResponse) {
                    console.log(errResponse);
                    self.currentStatus = 3;
                },

                complete: function() {
                    $(event.target).val('');
                }
            });


        }
    }
};

const Addons = {
    template: '#tmpl-wpuf-addons'
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
    },
    {
        path: '/tools',
        name: 'tools',
        component: Tools
    },
    {
        path: '/extensions',
        name: 'addons',
        component: Addons
    },
];

// 3. Create the router instance and pass the `routes` option
const router = new VueRouter({
    // mode: 'history',
    routes: routes,
    scrollBehavior (to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition
        } else {
            return { x: 0, y: 0 }
        }
    }
});

// 4. Create and mount the root instance.
const app = new Vue({
    router
}).$mount('#wpuf-contact-form-app')

// Admin menu hack
var menuRoot = $('#toplevel_page_best-contact-forms');

menuRoot.on('click', 'a', function() {
    var self = $(this);

    $('ul.wp-submenu li', menuRoot).removeClass('current');

    if ( self.hasClass('wp-has-submenu') ) {
        $('li.wp-first-item', menuRoot).addClass('current');
    } else {
        self.parents('li').addClass('current');
    }
});

$(function() {

    // select the current sub menu on page load
    var current_url = window.location.href;
    var current_path = current_url.substr( current_url.indexOf('admin.php') );

    $('ul.wp-submenu a', menuRoot).each(function(index, el) {
        if ( $(el).attr( 'href' ) === current_path ) {
            $(el).parent().addClass('current');
            return;
        }
    });;
});

})(jQuery);
