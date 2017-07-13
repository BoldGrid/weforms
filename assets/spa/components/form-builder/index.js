const FormEditComponent = {
    template: '#tmpl-wpuf-form-builder',
    mixins: wpuf_form_builder_mixins(wpuf_mixins.root),
    data: function() {
        return {
            is_form_saving: false,
            is_form_saved: false,
            is_form_switcher: false,
            post_title_editing: false,
        }
    },

    created: function() {
        this.fetchForm();
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
        }
    },

    mounted: function () {
        // primary nav tabs and their contents
        this.bind_tab_on_click($('#wpuf-form-builder > fieldset > .nav-tab-wrapper > a'), '#wpuf-form-builder');

        // secondary settings tabs and their contents
        var settings_tabs = $('#wpuf-form-builder-settings .nav-tab'),
            settings_tab_contents = $('#wpuf-form-builder-settings .tab-contents .group');

        settings_tabs.first().addClass('nav-tab-active');
        settings_tab_contents.first().addClass('active');

        this.bind_tab_on_click(settings_tabs, '#wpuf-form-builder-settings');

        var clipboard = new window.Clipboard('.form-id');
        $(".form-id").tooltip();

        var self = this;

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

        // window.onbeforeunload = function () {
        //     if (!self.is_form_saved) {
        //         return self.i18n.unsaved_changes;
        //     }
        // };
    },

    methods: {

        fetchForm: function() {
            var self = this;

            wp.ajax.send( 'bcf_get_form', {
                data: {
                    form_id: this.$route.params.id,
                    _wpnonce: wpufContactForm.nonce
                },
                success: function(response) {
                    // console.log(response);
                    self.loading = false

                    self.$store.commit('set_form_post', response.post);
                    self.$store.commit('set_form_fields', response.form_fields);
                    self.$store.commit('set_form_notification', response.notifications);
                },
                error: function(error) {
                    self.loading = false;
                    alert(error);
                }
            });
        },

        // tabs and their contents
        bind_tab_on_click: function (tabs, scope) {
            tabs.on('click', function (e) {
                e.preventDefault();

                var button = $(this),
                    tab_contents = $(scope + ' > fieldset > .tab-contents'),
                    group_id = button.attr('href');

                button.addClass('nav-tab-active').siblings('.nav-tab-active').removeClass('nav-tab-active');

                tab_contents.children().removeClass('active');
                $(group_id).addClass('active');
            });
        },

        // switch form
        switch_form: function () {
            this.is_form_switcher = (this.is_form_switcher) ? false : true;
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
                    notifications: JSON.stringify(self.notifications)
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
