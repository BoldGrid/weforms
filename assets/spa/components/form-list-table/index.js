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