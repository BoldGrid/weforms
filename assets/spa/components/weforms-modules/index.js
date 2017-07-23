const Modules = {
    template: '#tmpl-wpuf-weforms-modules',
    mixins: [LoadingMixin],
    data: function() {
        return {
            requesting: false,
            loading: false,
            modules: {
                all: {},
                active: []
            }
        };
    },

    created: function() {
        this.fetchModules();
    },

    methods: {

        isActive: function( module ) {
            return _.contains( this.modules.active, module );
        },

        activateModule: function(module) {
            if ( !this.isActive(module) ) {
                this.modules.active.push(module);
            }
        },

        deactivateModule: function(module) {
            console.log(this.modules.active );

            if ( this.isActive(module) ) {
                this.modules.active.splice( this.modules.active.indexOf(module), 1 );
            }
        },

        fetchModules: function() {
            var self = this;

            self.loading = true;

            wp.ajax.send( 'weforms_get_modules', {
                data: {
                    _wpnonce: weForms.nonce
                },

                success: function(response) {
                    self.modules = response;
                },

                complete: function() {
                    self.loading = false;
                }
            });
        },

        toggleModule: function(module) {
            var self = this;
            var state = this.isActive(module) ? 'deactivate' : 'activate';

            // if we are already making a call
            if (self.requesting) {
                return;
            }

            self.requesting = true;
            self.loading    = true;

            wp.ajax.send( 'weforms_toggle_modules', {
                data: {
                    type: state,
                    module: module,
                    _wpnonce: weForms.nonce
                },

                success: function(response) {

                    if ( state === 'activate' ) {
                        self.activateModule(module);
                    } else {
                        self.deactivateModule(module);
                    }

                    toastr.options.timeOut = 1000;
                    toastr.success( response );
                },

                complete: function() {
                    self.requesting = false;
                    self.loading    = false;
                }
            });
        }
    }
};