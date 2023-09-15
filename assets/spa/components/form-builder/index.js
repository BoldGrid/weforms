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
                    form_data: $('#wpuf-form-builder').weSerialize(),
                    form_fields: JSON.stringify(self.form_fields),
                    notifications: JSON.stringify(self.notifications),
                    settings: JSON.stringify(self.settings),
                    payment: JSON.stringify(self.payment),
                    integrations: JSON.stringify(self.integrations),
                },

                success: function (response) {
                    self.$store.state.settings = response.settings;
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
