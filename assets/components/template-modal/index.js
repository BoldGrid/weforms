Vue.component('wpuf-template-modal', {
    template: '#tmpl-wpuf-template-modal',

    props: {
        show: Boolean,
        onClose: Function,
    },

    data: function() {
        return {
            loading: false,
            category: 'all',
        };
    },

    methods: {

        blankForm: function(target) {
            this.createForm( 'blank', target );
        },

        createForm: function(form, target) {
            var self = this;

            // already on a request?
            if ( self.loading ) {
                return;
            }

            self.loading = true;

            $(target).addClass('updating-message');

            wp.ajax.send( 'weforms_contact_form_template', {
                data: {
                    template: form,
                    _wpnonce: weForms.nonce
                },

                success: function(response) {
                    self.$router.push({
                        name: 'edit',
                        params: { id: response.id }
                    });
                },

                error: function(error) {

                },

                complete: function() {
                    self.loading = false;

                    $(target).removeClass('updating-message');
                }
            });
        }
    }
});
