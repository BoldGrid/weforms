Vue.component( 'wpuf-table', {
    template: '#tmpl-wpuf-component-table',
    mixins: [weForms.mixins.Loading, weForms.mixins.Paginate, weForms.mixins.BulkAction],
    props: {
        action: String,
        delete: String,
        id: [String, Number]
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
        }
    },

    watch: {
        id: function(){
            this.fetchData();
        }
    }
} );
