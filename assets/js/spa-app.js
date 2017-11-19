/*!
weForms - v1.2.1
Generated: 2017-11-16 (1510818342671)
*/

;(function($) {
/* ./assets/spa/components/component-table/index.js */
Vue.component( 'wpuf-table', {
    template: '#tmpl-wpuf-component-table',
    mixins: [weForms.mixins.Loading, weForms.mixins.Paginate, weForms.mixins.BulkAction],
    props: {
        has_export: String,
        action: String,
        delete: String,
        id: [String, Number],
        status: [String],
    },

    data: function() {
        return {
            loading: false,
            columns: [],
            items: [],
            ajaxAction: this.action,
            nonce: weForms.nonce,
            index: 'id',
            bulkDeleteAction: this.delete ? this.delete : 'weforms_form_entry_trash_bulk'
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
                    status: self.status,
                    _wpnonce: weForms.nonce
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

            if ( 'restore' === this.bulkAction ) {
                if ( ! this.checkedItems.length ) {
                    alert( 'Please select atleast one entry to restore.' );
                    return;
                }

                this.restoreBulk();
            }
        },
        restore: function(entry_id){
            var self = this;
            self.loading = true;

            wp.ajax.send( 'weforms_form_entry_restore', {
                data: {
                    entry_id: entry_id,
                    _wpnonce: weForms.nonce
                },
                success: function(response) {
                    self.loading = false;
                    self.fetchData();
                },
                error: function(error) {
                    self.loading = false;
                    alert(error);
                }
            });
        },
        deletePermanently: function(entry_id){

            if ( confirm( 'Are you sure to delete this entry?' ) ) {

                var self = this;
                self.loading = true;

                wp.ajax.send( 'weforms_form_entry_delete', {
                    data: {
                        entry_id: entry_id,
                        _wpnonce: weForms.nonce
                    },
                    success: function(response) {
                        self.loading = false;
                        self.fetchData();
                    },
                    error: function(error) {
                        self.loading = false;
                        alert(error);
                    }
                });
            }
        }
    },

    watch: {
        id: function(){
            this.fetchData();
        },
        status: function(){
            this.currentPage = 1;
            this.bulkAction = -1;
            this.fetchData();
        },
    }
} );

/* ./assets/spa/components/entries/index.js */
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

/* ./assets/spa/components/form-builder/index.js */
weForms.routeComponents.FormEditComponent = {
    template: '#tmpl-wpuf-form-builder',
    mixins: wpuf_form_builder_mixins(wpuf_mixins.root),
    data: function() {
        return {
            is_form_saving: false,
            is_form_saved: false,
            is_form_switcher: false,
            post_title_editing: false,
            loading: false,
            activeTab: 'editor',
            activeSettingsTab: 'form',
            activePaymentTab: 'paypal',
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
        form_fields: {
            handler: function() {
                window.weFormsBuilderisDirty = true;
            },
            deep: true
        },
        notifications: {
            handler: function() {
                window.weFormsBuilderisDirty = true;
            },
            deep: true
        },
        integrations: {
            handler: function() {
                window.weFormsBuilderisDirty = true;
            },
            deep: true
        },
        settings: {
            handler: function() {
                window.weFormsBuilderisDirty = true;
            },
            deep: true
        },
        payment: {
            handler: function() {
                window.weFormsBuilderisDirty = true;
            },
            deep: true
        },
    },

    created: function() {
        this.set_current_panel('form-fields');
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
        },

        payment: function() {
            return this.$store.state.payment;
        }
    },

    mounted: function () {

        var clipboard = new window.Clipboard('.form-id');
        $(".form-id").tooltip();

        var self = this;

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

        this.initSharingClipBoard();

        setTimeout(function(){
            window.weFormsBuilderisDirty = false;
        },500);

        window.onbeforeunload = function () {
            if ( window.weFormsBuilderisDirty ) {
                return self.i18n.unsaved_changes;
            }
        };
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

        isActivePaymentTab: function(val) {
            return this.activePaymentTab === val;
        },

        makeActivePaymentTab: function(val) {
            this.activePaymentTab = val;
        },

        fetchForm: function() {
            var self = this;

            self.loading = true;

            wp.ajax.send( 'weforms_get_form', {
                data: {
                    form_id: this.$route.params.id,
                    _wpnonce: weForms.nonce
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
                    payment: JSON.stringify(self.payment),
                    integrations: JSON.stringify(self.integrations),
                },

                success: function (response) {
                    if (response.form_fields) {
                        self.$store.commit('set_form_fields', response.form_fields);
                    }

                    self.is_form_saving = false;
                    self.is_form_saved = true;

                    setTimeout(function(){
                        window.weFormsBuilderisDirty = false;
                    },500);

                    toastr.success(self.i18n.saved_form_data);
                },

                error: function () {
                    self.is_form_saving = false;
                }
            });
        },


        save_settings: function () {
            toastr.options.preventDuplicates = true;
            this.save_form_builder();
        },

        shareForm: function( site_url, post ) {

            var self = this;

            if( self.settings.sharing_on === 'on'){

                var post_link = site_url + '?weforms=' + btoa( self.getSharingHash() + '_' + Math.floor(Date.now() / 1000)  + '_' + post.ID);

                swal({
                    title: self.i18n.shareYourForm,
                    html: '<p>'+self.i18n.shareYourFormText+'</p> <p><input onClick="this.setSelectionRange(0, this.value.length)" type="text" class="regular-text" value="' + post_link + '"/> <button class="anonymous-share-btn button button-primary" title="Copy URL" data-clipboard-text="' + post_link + '"><i class="fa fa-clipboard" aria-hidden="true"></i></button></p>',
                    showCloseButton: true,
                    showCancelButton: true,
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    confirmButtonColor: '#d54e21',
                    confirmButtonText: self.i18n.disableSharing,
                    cancelButtonText: self.i18n.close,
                    focusCancel: true,

                }).then(function () {
                    swal({
                        title: self.i18n.areYouSure,
                        html: "<p>"+self.i18n.areYouSureDesc+"</p>",
                        type: 'info',
                        confirmButtonColor: '#d54e21',
                        showCancelButton: true,
                        confirmButtonText: self.i18n.disable,
                        cancelButtonText: self.i18n.cancel,
                    }).then(function () {
                       self.disableSharing();
                    });
                });

            } else {

                swal({
                  title: self.i18n.shareYourForm,
                  html: self.i18n.shareYourFormDesc,
                  type: 'info',
                  showCancelButton: true,
                  confirmButtonText: 'Enable',
                  cancelButtonText: 'Cancel',
                }).then(function () {
                    self.enableSharing(site_url, post);
                });

            }
        },

        enableSharing: function(site_url, post){

            this.settings.sharing_on = 'on';
            this.save_settings();
            this.shareForm(site_url, post);
        },

        disableSharing: function(){
            this.settings.sharing_on = false;
            this.save_settings();
        },
        getSharingHash: function(){

            if( ! this.settings.sharing_hash ) {
                this.settings.sharing_hash = this.makeRandomString(8);
                this.save_settings();
            }

            return this.settings.sharing_hash;
        },

        makeRandomString: function(limit) {
          limit = limit || 8;
          var text = "";
          var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

          for (var i = 0; i < limit; i++){

            text += possible.charAt(Math.floor(Math.random() * possible.length));
          }

          return text;
        },

        initSharingClipBoard: function(val) {
            var clipboard2 = new window.Clipboard('.anonymous-share-btn');

            $(".anonymous-share-btn").tooltip();

            clipboard2.on('success', function(e) {
                // Show copied tooltip
                $(e.trigger)
                    .attr('data-original-title', 'Copied!')
                    .tooltip('show');

                // Reset the copied tooltip
                setTimeout(function() {
                    $(e.trigger).tooltip('hide')
                    .attr('data-original-title', 'Copy URL');
                }, 1000);

                e.clearSelection();
            });
        },

    }
};

/* ./assets/spa/components/form-entries/index.js */
weForms.routeComponents.FormEntries = {
    props: {
        id: [String, Number]
    },
    template: '#tmpl-wpuf-form-entries',
    data: function() {
        return {
            selected: 0,
            form_title: 'Loading...',
            status: 'publish',
            total: 0,
            totalTrash: 0,
        };
    }
};

/* ./assets/spa/components/form-entry-single/index.js */
weForms.routeComponents.FormEntriesSingle = {
    template: '#tmpl-wpuf-form-entry-single',
    mixins: [weForms.mixins.Loading,weForms.mixins.Cookie],
    data: function() {
        return {
            loading: false,
            hideEmpty: true,
            hasEmpty: false,
            show_payment_data: false,
            entry: {
                form_fields: {},
                meta_data: {},
                payment_data: {},
            }
        };
    },
    created: function() {
        this.hideEmpty = this.hideEmptyStatus();
        this.fetchData();
    },
    computed: {
        hasFormFields: function() {
            return Object.keys(this.entry.form_fields).length;
        },
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
                    self.loading = false;
                    self.entry = response;
                    self.hasEmpty = response.has_empty;
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
        },
        hideEmptyStatus: function(){
            return this.getCookie('weFormsEntryHideEmpty') === 'false' ? false : true;
        },
    },
    watch: {
        hideEmpty: function(value){
            this.setCookie('weFormsEntryHideEmpty',value,356);
        }
    }
};

/* ./assets/spa/components/form-list-table/index.js */
Vue.component('form-list-table', {
    template: '#tmpl-wpuf-form-list-table',
    mixins: [weForms.mixins.Loading, weForms.mixins.Paginate, weForms.mixins.BulkAction],
    data: function() {
        return {
            loading: false,
            index: 'ID',
            items: [],
            bulkDeleteAction: 'weforms_form_delete_bulk',
        };
    },
    created: function() {
        this.fetchData();
    },
    computed: {
        is_pro: function() {
            return 'true' === weForms.is_pro;
        },
        has_payment: function() {
            return 'true' === weForms.has_payment;
        },
    },

    methods: {
        fetchData: function() {
            var self = this;

            this.loading = true;

            wp.ajax.send( 'weforms_form_list', {
                data: {
                    _wpnonce: weForms.nonce,
                    page: self.currentPage,
                },
                success: function(response) {
                    self.loading = false;
                    self.items = response.forms;
                    self.totalItems = response.meta.total;
                    self.totalPage = response.meta.pages;
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
                        form_id: this.items[index].id,
                        _wpnonce: weForms.nonce
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
                    _wpnonce: weForms.nonce
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
/* ./assets/spa/components/form-payments/index.js */
weForms.routeComponents.FormPayments = {
    props: {
        id: [String, Number]
    },
    template: '#tmpl-wpuf-form-payments',
    data: function() {
        return {
            form_title: 'Loading...'
        };
    }
};

/* ./assets/spa/components/home-page/index.js */
weForms.routeComponents.Home = {
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
weForms.routeComponents.Tools = {
    template: '#tmpl-wpuf-tools',
    mixins: [weForms.mixins.Tabs, weForms.mixins.Loading],
    data: function() {
        return {
            activeTab: 'export',
            exportType: 'all',
            loading: false,
            forms: [],
            importButton: 'Import',
            currentStatus: 0,
            responseMessage: '',
            logs: [],
            ximport: {
                current: '',
                title: '',
                action: '',
                message: '',
                type: 'updated',
                refs: {}
            }
        };
    },

    computed: {

        isInitial: function() {
            return this.currentStatus === 0;
        },

        isSaving: function() {
            return this.currentStatus === 1;
        },

        isSuccess: function() {
            return this.currentStatus === 2;
        },

        isFailed: function() {
            return this.currentStatus === 3;
        },

        hasRefs: function() {
            return Object.keys(this.ximport.refs).length;
        },
        hasLogs: function() {
            return Object.keys(this.logs).length;
        }
    },

    created: function() {
        this.fetchData();
        this.fetchLogs();
    },

    methods: {
        fetchLogs: function(target) {
            var self = this;

            self.startLoading(target);

            wp.ajax.send( 'weforms_read_logs', {
                data: {
                    _wpnonce: weForms.nonce
                },
                success: function(response) {
                    self.stopLoading(target);
                    self.logs = response;
                },error: function(){
                    self.stopLoading(target);
                    self.logs = [];
                }
            });
        },
        deleteLogs: function(target) {
            var self = this;

            if ( confirm('Are you sure to clear the log file?') ) {

                self.startLoading(target);

                wp.ajax.send( 'weforms_delete_logs', {
                    data: {
                        _wpnonce: weForms.nonce
                    },
                    success: function(response) {
                        self.logs = [];
                        self.stopLoading(target);
                        self.fetchLogs();
                    },
                    error: function(response) {
                        self.logs = [];
                        self.stopLoading(target);
                        self.fetchLogs();
                    }
                });
            }
        },
        stopLoading: function(target){
            target = $(target);

            if (target.is('button')) {
                target.removeClass('updating-message').find('span').show();
            }else if(target.is('span')){
                target.show().parent().removeClass('updating-message');
            }
        },
        startLoading: function(target){

            target = $(target);

            if (target.is('button')) {
                target.addClass('updating-message').find('span').hide();
            }else if(target.is('span')){
                target.hide().parent().addClass('updating-message');
            }
        },
        fetchData: function() {
            var self = this;

            this.loading = true;

            wp.ajax.send( 'weforms_form_names', {
                data: {
                    _wpnonce: weForms.nonce
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
            formData.append( '_wpnonce', weForms.nonce );

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
        },

        importx: function(target, plugin) {
            var button = $(target);
            var self   = this;

            self.ximport.current = plugin;
            button.addClass('updating-message').text( button.data('importing') );

            wp.ajax.send( 'weforms_import_xforms_' + plugin, {
                data: {
                    _wpnonce: weForms.nonce
                },

                success: function(response) {
                    self.ximport.title   = response.title;
                    self.ximport.message = response.message;
                    self.ximport.action  = response.action;
                    self.ximport.refs    = response.refs;
                },

                error: function(error) {
                    alert(error.message);
                },

                complete: function() {
                    button.removeClass('updating-message').text( button.data('original') );
                }
            });
        },

        replaceX: function(target, type) {
            var button = $(target);
            var self   = this;

            button.addClass('updating-message');

            wp.ajax.send( 'weforms_import_xreplace_' + self.ximport.current, {
                data: {
                    type: type,
                    _wpnonce: weForms.nonce
                },

                success: function(response) {
                    if ( 'replace' === button.data('type') ) {
                        alert( response );
                    }
                },

                error: function(error) {
                    alert( error );
                },

                complete: function() {
                    self.ximport.current = '';
                    self.ximport.title   = '';
                }
            });
        }
    }
};

/* ./assets/spa/components/transactions/index.js */
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

/* ./assets/spa/components/weforms-page-help/index.js */
weForms.routeComponents.Help = {
    template: '#tmpl-wpuf-weforms-page-help'
};
/* ./assets/spa/components/weforms-premium/index.js */
weForms.routeComponents.Premium = {
    template: '#tmpl-wpuf-weforms-premium'
};
/* ./assets/spa/components/weforms-settings/index.js */
weForms.routeComponents.Settings = {
    template: '#tmpl-wpuf-weforms-settings',
    mixins: [weForms.mixins.Loading, weForms.mixins.Cookie],
    data: function() {
        return {
            loading: false,
            settings: {
                email_gateway: 'wordpress',
                credit: false,
                permission: 'manage_options',
                gateways: {
                    sendgrid: '',
                    mailgun: '',
                    sparkpost: ''
                },
                recaptcha: {
                    type: 'v2',
                    key: '',
                    secret: ''
                }
            },

            activeTab: 'general',
        };
    },

    computed: {

        is_pro: function() {
            return 'true' === weForms.is_pro;
        }
    },

    created: function() {
        this.fetchSettings();

        if ( this.getCookie('weforms_settings_active_tab') ) {
            this.activeTab = this.getCookie('weforms_settings_active_tab');
        }
    },

    methods: {

        makeActive: function(val) {
            this.activeTab = val;
        },

        isActiveTab: function(val) {
          return this.activeTab === val;
        },

        fetchSettings: function() {
            var self = this;

            self.loading = true;

            wp.ajax.send('weforms_get_settings', {
                data: {
                    _wpnonce: weForms.nonce
                },

                success: function(response) {

                    if ( response === undefined ){
                        return;
                    }

                    // set defaults if undefined
                    $.each( self.settings, function( key, value ) {
                        if( response[key] === undefined ) {
                            response[key] = value;
                        }
                    });

                    self.settings = response;
                },

                complete: function() {
                    self.loading = false;
                }
            });
        },

        saveSettings: function(target) {
            var self = this;

            $(target).addClass('updating-message');

            wp.ajax.send('weforms_save_settings', {
                data: {
                    settings: JSON.stringify(self.settings),
                    _wpnonce: weForms.nonce
                },

                success: function(response) {
                    toastr.options.timeOut = 1000;
                    toastr.success( 'Settings has been updated' );
                },

                error: function(error) {
                    console.log(error);
                },

                complete: function() {
                    $(target).removeClass('updating-message');
                }
            });

        },

        post: function( action, data , success ){
            data = data || {};
            success = success || function(){ };
            data._wpnonce =  weForms.nonce;

            wp.ajax.send(action, {
                data: data,

                success: function(response) {
                    success(response);
                },

                error: function(error) {
                    console.log(error);
                },

                complete: function() {

                }
            });
        },
    },

    watch: {
        activeTab: function(value){
            this.setCookie('weforms_settings_active_tab', value, '365');
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
    template: '<input type="text" v-bind:value="value" />',
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
        onClose: function(date) {
            this.$emit('input', date);
        }
    },
});

Vue.component('weforms-colorpicker', {
    template: '<input type="text" v-bind:value="value" />',
    props: ['value'],
    mounted: function() {
        var self = this;

        $(this.$el).wpColorPicker({
            change: this.onChange
        });
    },

    methods: {
        onChange: function(event, ui) {
            this.$emit('input', ui.color.toString());
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

            clone.id     = payload.new_id;
            clone.name   = clone.name + '_copy';
            clone.is_new = true;

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
            state.notifications.push(_.clone(payload));
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
weForms.routeComponents.FormHome = { template: '<div><router-view class="child"></router-view></div>' };
weForms.routeComponents.SingleForm = { template: '#tmpl-wpuf-form-editor' };
weForms.routeComponents.FormEntriesHome = {
    template: '<div><router-view class="grand-child"></router-view></div>',
};

/**
 * Parse the route array and bind required components
 *
 * This changes the weForms.routes array and changes the components
 * so we can use weForms.routeComponents.{compontent} component.
 *
 * @param  {array} routes
 *
 * @return {void}
 */
function parseRouteComponent(routes) {

    for (var i = 0; i < routes.length; i++) {
        if ( typeof routes[i].children === 'object' ) {

            parseRouteComponent( routes[i].children );

            if ( typeof routes[i].component !== 'undefined' ) {
                routes[i].component = weForms.routeComponents[ routes[i].component ];
            }

        } else {
            routes[i].component = weForms.routeComponents[ routes[i].component ];
        }
    }
}

// mutate the localized array
parseRouteComponent(weForms.routes);

// 3. Create the router instance and pass the `routes` option
var router = new VueRouter({
    routes: weForms.routes,
    scrollBehavior: function (to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition
        } else {
            return { x: 0, y: 0 }
        }
    }
});

window.weFormsBuilderisDirty = false;

router.beforeEach((to, from, next) => {
    // show warning if builder has unsaved changes
    if ( window.weFormsBuilderisDirty ) {
        if ( confirm( wpuf_form_builder.i18n.unsaved_changes + ' ' +wpuf_form_builder.i18n.areYouSureToLeave ) ) {
            window.weFormsBuilderisDirty = false;
        } else {
            next(from.path);
            return false;
        }
    }

    next();
});


var app = new Vue({
    router: router,
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