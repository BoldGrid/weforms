/*!
weForms - v1.0.0
Generated: 2017-07-21 (1500619632643)
*/

;(function($) {
/* ./assets/spa/mixins/bulk-action.js */
var BulkActionMixin = {
    data: function() {
        return {
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
                var selected = [],
                    self = this;

                if (value) {
                    this.items.forEach(function (item) {
                        selected.push(item[self.index]);
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

/* ./assets/spa/mixins/loading.js */
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

/* ./assets/spa/mixins/paginate.js */
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

/* ./assets/spa/mixins/tabs.js */
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

/* ./assets/spa/components/addons/index.js */
const Addons = {
    template: '#tmpl-wpuf-addons'
};
/* ./assets/spa/components/component-table/index.js */
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
            index: 'id',
            bulkDeleteAction: 'weforms_form_entry_trash_bulk'
        };
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

            this.loading = true;

            wp.ajax.send( self.action, {
                data: {
                    id: self.id,
                    page: self.currentPage,
                    _wpnonce: wpufContactForm.nonce
                },
                success: function(response) {
                    self.loading = false;
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

/* ./assets/spa/components/form-builder/index.js */
var FormEditComponent = {
    template: '#tmpl-wpuf-form-builder',
    mixins: wpuf_form_builder_mixins(wpuf_mixins.root),
    data: function() {
        return {
            is_form_saving: false,
            is_form_saved: false,
            is_form_switcher: false,
            post_title_editing: false,
            loading: false,
            activeTab: 'integration',
            activeSettingsTab: 'form',
        };
    },

    watch: {
        loading: function(value) {
            if ( value ) {
                NProgress.configure({ parent: '#wpadminbar' });
                NProgress.start();
            } else {
                NProgress.done();
            }
        },

    },

    created: function() {
        this.fetchForm();

        this.$store.commit('panel_add_show_prop');

        /**
         * This is the event hub we'll use in every
         * component to communicate between them
         */
        wpuf_form_builder.event_hub = new Vue();
    },

    computed: {
        current_panel: function () {
            return this.$store.state.current_panel;
        },

        post: function () {
            return this.$store.state.post;
        },

        form_fields_count: function () {
            return this.$store.state.form_fields.length;
        },

        form_fields: function () {
            return this.$store.state.form_fields;
        },

        notifications: function() {
            return this.$store.state.notifications;
        },

        integrations: function() {
            return this.$store.state.integrations;
        },

        settings: function() {
            return this.$store.state.settings;
        }
    },

    mounted: function () {

        var clipboard = new window.Clipboard('.form-id');
        $(".form-id").tooltip();

        var self = this;

        this.isDirty = false;
        this.started = true;

        clipboard.on('success', function(e) {
            // Show copied tooltip
            $(e.trigger)
                .attr('data-original-title', 'Copied!')
                .tooltip('show');

            // Reset the copied tooltip
            setTimeout(function() {
                $(e.trigger).tooltip('hide')
                .attr('data-original-title', self.i18n.copy_shortcode);
            }, 1000);

            e.clearSelection();
        });
    },

    methods: {

        makeActive: function(val) {
            this.activeTab = val;
        },

        isActiveTab: function(val) {
          return this.activeTab === val;
        },

        isActiveSettingsTab: function(val) {
            return this.activeSettingsTab === val;
        },

        makeActiveSettingsTab: function(val) {
            this.activeSettingsTab = val;
        },

        fetchForm: function() {
            var self = this;

            self.loading = true;

            wp.ajax.send( 'weforms_get_form', {
                data: {
                    form_id: this.$route.params.id,
                    _wpnonce: wpufContactForm.nonce
                },
                success: function(response) {

                    self.$store.commit('set_form_post', response.post);
                    self.$store.commit('set_form_fields', response.form_fields);
                    self.$store.commit('set_form_notification', response.notifications);
                    self.$store.commit('set_form_settings', response.settings);

                    // if nothing saved in the form, it provides an empty array
                    // but we expect to be an object
                    if ( response.integrations.length !== undefined ) {
                        self.$store.commit('set_form_integrations', {});
                    } else {
                        self.$store.commit('set_form_integrations', response.integrations);
                    }
                },
                error: function(error) {
                    alert(error);
                },

                complete: function() {
                    self.loading = false;
                }
            });
        },

        // set current sidebar panel
        set_current_panel: function (panel) {
            this.$store.commit('set_current_panel', panel);
        },

        // save form builder data
        save_form_builder: function () {
            var self = this;

            if (_.isFunction(this.validate_form_before_submit) && !this.validate_form_before_submit()) {

                this.warn({
                    text: this.validation_error_msg
                });

                return;
            }

            self.is_form_saving = true;
            self.set_current_panel('form-fields');

            wp.ajax.send('wpuf_form_builder_save_form', {
                data: {
                    form_data: $('#wpuf-form-builder').serialize(),
                    form_fields: JSON.stringify(self.form_fields),
                    notifications: JSON.stringify(self.notifications),
                    settings: JSON.stringify(self.settings),
                    integrations: JSON.stringify(self.integrations),
                },

                success: function (response) {
                    if (response.form_fields) {
                        self.$store.commit('set_form_fields', response.form_fields);
                    }

                    self.is_form_saving = false;
                    self.is_form_saved = true;

                    toastr.success(self.i18n.saved_form_data);
                },

                error: function () {
                    self.is_form_saving = false;
                }
            });
        }
    }
};

/* ./assets/spa/components/form-entries/index.js */
const FormEntries = {
    props: {
        id: [String, Number]
    },
    template: '#tmpl-wpuf-form-entries',
    data: function() {
        return {
            form_title: 'Loading...'
        };
    }
};
/* ./assets/spa/components/form-entry-single/index.js */
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
                    _wpnonce: wpufContactForm.nonce
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

            if ( !confirm( wpufContactForm.confirm ) ) {
                return;
            }

            wp.ajax.send( 'weforms_form_entry_trash', {
                data: {
                    entry_id: self.$route.params.entryid,
                    _wpnonce: wpufContactForm.nonce
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

/* ./assets/spa/components/form-list-table/index.js */
Vue.component('form-list-table', {
    template: '#tmpl-wpuf-form-list-table',
    mixins: [LoadingMixin, PaginateMixin, BulkActionMixin],
    data: function() {
        return {
            loading: false,
            index: 'ID',
            items: [],
            bulkDeleteAction: 'weforms_form_delete_bulk'
        };
    },

    created: function() {
        this.fetchData();
    },

    methods: {
        fetchData: function() {
            var self = this;

            this.loading = true;

            wp.ajax.send( 'weforms_form_list', {
                data: {
                    _wpnonce: wpufContactForm.nonce,
                    page: self.currentPage,
                },
                success: function(response) {
                    self.loading = false;
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

                wp.ajax.send( 'weforms_form_delete', {
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

            wp.ajax.send( 'weforms_form_duplicate', {
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
/* ./assets/spa/components/home-page/index.js */
const Home = {
    template: '#tmpl-wpuf-home-page',

    data: function() {
        return {
            showTemplateModal: false
        };
    },

    methods: {
        displayModal: function() {
            this.showTemplateModal = true;
        },

        closeModal: function() {
            this.showTemplateModal = false;
        },
    }
};

/* ./assets/spa/components/tools/index.js */
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
        };
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

            this.loading = true;

            wp.ajax.send( 'weforms_form_names', {
                data: {
                    _wpnonce: wpufContactForm.nonce
                },
                success: function(response) {
                    // console.log(response);
                    self.loading = false;
                    self.forms   = response;
                },
                error: function(error) {
                    self.loading = false;
                    alert(error);
                }
            });
        },

        importForm: function( fieldName, fileList, event ) {
            if ( !fileList.length ) {
                return;
            }

            var formData = new FormData();
            var self = this;

            formData.append( fieldName, fileList[0], fileList[0].name);
            formData.append( 'action', 'weforms_import_form' );
            formData.append( '_wpnonce', wpufContactForm.nonce );

            self.currentStatus = 1;

            $.ajax({
                type: "POST",
                url: window.ajaxurl,
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

/* ./assets/spa/app.js */
if (!Array.prototype.hasOwnProperty('swap')) {
    Array.prototype.swap = function (from, to) {
        this.splice(to, 0, this.splice(from, 1)[0]);
    };
}

Vue.component('datepicker', {
    template: '<input type="text" v-bind:value="value" v-on:input="$emit(\'input\', $event.target.value)" />',
    props: ['value'],
    mounted: function() {
        var self = this;

        $(this.$el).datetimepicker({
            dateFormat: 'yy-mm-dd',
            timeFormat: "HH:mm:ss",
            onClose: this.onClose
        });
    },

    methods: {
        onClose(date) {
            this.$emit('input', date);
        }
    },
});

// check if an element is visible in browser viewport
function is_element_in_viewport (el) {
    if (typeof jQuery === "function" && el instanceof jQuery) {
        el = el[0];
    }

    var rect = el.getBoundingClientRect();

    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && /*or $(window).height() */
        rect.right <= (window.innerWidth || document.documentElement.clientWidth) /*or $(window).width() */
    );
}

/**
 * Vuex Store data
 */
var wpuf_form_builder_store = new Vuex.Store({
    state: {
        post: {},
        form_fields: [],
        panel_sections: wpuf_form_builder.panel_sections,
        field_settings: wpuf_form_builder.field_settings,
        notifications: [],
        settings: {},
        integrations: {},
        current_panel: 'form-fields',
        editing_field_id: 0, // editing form field id
    },

    mutations: {
        set_form_fields: function (state, form_fields) {
            Vue.set(state, 'form_fields', form_fields);
        },

        set_form_post: function (state, post) {
            Vue.set(state, 'post', post);
        },

        set_form_notification: function (state, value) {
            Vue.set(state, 'notifications', value);
        },

        set_form_integrations: function (state, value) {
            Vue.set(state, 'integrations', value);
        },

        set_form_settings: function (state, value) {
            Vue.set(state, 'settings', value);
        },

        // set the current panel
        set_current_panel: function (state, panel) {
            if ('field-options' !== state.current_panel &&
                'field-options' === panel &&
                state.form_fields.length
            ) {
                state.editing_field_id = state.form_fields[0].id;
            }

            state.current_panel = panel;

            // reset editing field id
            if ('form-fields' === panel) {
                state.editing_field_id = 0;
            }
        },

        // add show property to every panel section
        panel_add_show_prop: function (state) {
            state.panel_sections.map(function (section, index) {
                if (!section.hasOwnProperty('show')) {
                    Vue.set(state.panel_sections[index], 'show', true);
                }
            });
        },

        // toggle panel sections
        panel_toggle: function (state, index) {
            state.panel_sections[index].show = !state.panel_sections[index].show;
        },

        // open field settings panel
        open_field_settings: function (state, field_id) {
            var field = state.form_fields.filter(function(item) {
                return parseInt(field_id) === parseInt(item.id);
            });

            if ('field-options' === state.current_panel && field[0].id === state.editing_field_id) {
                return;
            }

            if (field.length) {
                state.editing_field_id = 0;
                state.current_panel = 'field-options';

                setTimeout(function () {
                    state.editing_field_id = field[0].id;
                }, 400);
            }
        },

        update_editing_form_field: function (state, payload) {
            var editing_field = _.find(state.form_fields, function (item) {
                return parseInt(item.id) === parseInt(payload.editing_field_id);
            });

            editing_field[payload.field_name] = payload.value;
        },

        // add new form field element
        add_form_field_element: function (state, payload) {
            state.form_fields.splice(payload.toIndex, 0, payload.field);

            // bring newly added element into viewport
            Vue.nextTick(function () {
                var el = $('#form-preview-stage .wpuf-form .field-items').eq(payload.toIndex);

                if (el && !is_element_in_viewport(el.get(0))) {
                    $('#builder-stage section').scrollTo(el, 800, {offset: -50});
                }
            });
        },

        // sorting inside stage
        swap_form_field_elements: function (state, payload) {
            state.form_fields.swap(payload.fromIndex, payload.toIndex);
        },

        clone_form_field_element: function (state, payload) {
            var field = _.find(state.form_fields, function (item) {
                return parseInt(item.id) === parseInt(payload.field_id);
            });

            var clone = $.extend(true, {}, field),
                index = parseInt(payload.index) + 1;

            clone.id   = payload.new_id;
            clone.name = clone.name + '_copy';

            state.form_fields.splice(index, 0, clone);
        },

        // delete a field
        delete_form_field_element: function (state, index) {
            state.current_panel = 'form-fields';
            state.form_fields.splice(index, 1);
        },

        // set fields for a panel section
        set_panel_section_fields: function (state, payload) {
            var section = _.find(state.panel_sections, function (item) {
                return item.id === payload.id;
            });

            section.fields = payload.fields;
        },

        // notifications
        addNotification: function(state, payload) {
            state.notifications.push(payload);
        },

        deleteNotification: function(state, index) {
            state.notifications.splice(index, 1);
        },

        cloneNotification: function(state, index) {
            var clone = $.extend(true, {}, state.notifications[index]);

            index = parseInt(index) + 1;
            state.notifications.splice(index, 0, clone);
        },

        // update by it's property
        updateNotificationProperty: function(state, payload) {
            state.notifications[payload.index][payload.property] = payload.value;
        },

        updateNotification: function(state, payload) {
            state.notifications[payload.index] = payload.value;
        },

        updateIntegration: function(state, payload) {
            // console.log(payload);
            // console.log(state.integrations[payload.index]);
            // state.integrations[payload.index] = payload.value;
            Vue.set(state.integrations, payload.index, payload.value)

            // state.integrations.splice(payload.index, 0, payload.value);
        }
    }
});


// 1. Define route components.
const FormHome = { template: '<div><router-view class="child"></router-view></div>' };
const SingleForm = { template: '#tmpl-wpuf-form-editor' };
const FormEntriesHome = {
    template: '<div><router-view class="grand-child"></router-view></div>',
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
            {
                path: 'edit',
                name: 'edit',
                component: FormEditComponent
            }
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
    router,
    store: wpuf_form_builder_store
}).$mount('#wpuf-contact-form-app')

// Admin menu hack
var menuRoot = $('#toplevel_page_weforms');

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
    });
});

})(jQuery);