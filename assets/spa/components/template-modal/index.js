Vue.component('wpuf-template-modal', {
    template: '#tmpl-wpuf-template-modal',

    props: {
        show: Boolean,
        onClose: Function
    },

    data: function() {
        return {
            loading: false
        }
    },

    methods: {

        blankForm: function(target) {
            this.createForm('blank_form', target);
        },

        createForm: function(form, target) {
            var self = this,
                list = $(target).parents('li');

            // already on a request?
            if ( self.loading ) {
                return;
            }

            self.loading = true;

            if ( list ) {
                list.addClass('on-progress');
            }

            wp.ajax.send( 'bcf_contact_form_template', {
                data: {
                    template: form,
                    _wpnonce: wpufContactForm.nonce
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

                    if ( list ) {
                        list.removeClass('on-progress');
                    }
                }
            });
        }
    }
});
