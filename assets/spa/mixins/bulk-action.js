weForms.mixins.BulkAction = {
    data: function() {
        return {
            bulkAction: '-1',
            checkedItems: []
        }
    },

    computed: {
        selectAll: {
            get: function () {
                return this.items ? this.checkedItems.length == this.items.length : false;
            },

            set: function (value) {
                var selected = [],
                    self = this;

                if (value) {
                    this.items.forEach(function (item) {
                        if( item[self.index] !== undefined ) {
                            selected.push(item[self.index]);
                        } else {
                            selected.push(item.id);
                        }
                    });
                }

                this.checkedItems = selected;
            }
        }
    },

    methods: {
        deleteBulk: function() {
            var self = this;

            self.loading = true;

            wp.ajax.send( self.bulkDeleteAction, {
                data: {
                    ids: this.checkedItems,
                    _wpnonce: weForms.nonce
                },
                success: function(response) {
                    self.checkedItems = [];
                    self.fetchData();
                },
                error: function(error) {
                    alert(error);
                },

                complete: function(resp) {
                    self.loading = false;
                }
            });
        }
    }
};
