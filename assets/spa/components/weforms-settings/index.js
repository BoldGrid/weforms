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

                    weForms.settings = response;
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
