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

        isPendingForm: function( scheduleStart ) {
            var currentTime = Math.round((new Date()).getTime() / 1000),
                startTime   = Math.round((new Date( scheduleStart )).getTime() / 1000);

            if ( currentTime < startTime ) {
                return true;
            }
            return false;
        },

        isExpiredForm: function( scheduleEnd ) {
            var currentTime = Math.round((new Date()).getTime() / 1000),
                endTime   = Math.round((new Date( scheduleEnd )).getTime() / 1000);

            if ( currentTime > endTime ) {
                return true;
            }
            return false;
        },

        isOpenForm: function ( scheduleStart, scheduleEnd ) {
            var currentTime = Math.round((new Date()).getTime() / 1000),
                startTime   = Math.round((new Date( scheduleStart )).getTime() / 1000),
                endTime     = Math.round((new Date( scheduleEnd )).getTime() / 1000);

            if ( currentTime > startTime &&  currentTime < endTime ) {
                return true;
            }
            return false;
        },

        isFormStatusClosed: function(formSettings, entries) {
            if ( formSettings.schedule_form === 'true' && this.isPendingForm(formSettings.schedule_start) ) {
                return true;
            }

            if ( formSettings.schedule_form === 'true' && this.isExpiredForm(formSettings.schedule_end) ) {
                return true;
            }

            if ( formSettings.limit_entries  === 'true' && entries >= formSettings.limit_number ) {
                return true;
            }
            return;
        },

        formatTime: function ( time ) {
            var date    = new Date( time ),
                month   = date.toLocaleString('en-us', { month: 'long' });

            return `${month} ${date.getDate()}, ${date.getFullYear()}`;
        }
    }
});