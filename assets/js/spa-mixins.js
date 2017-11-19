/* ./assets/spa/mixins/bulk-action.js */
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
                    permanent: this.status == 'trash' ? 1 : 0,
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
        },
        restoreBulk: function() {
            var self = this;

            self.loading = true;

            wp.ajax.send('weforms_form_entry_restore_bulk', {
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

/* ./assets/spa/mixins/cookie.js */
weForms.mixins.Cookie = {

    methods: {

        setCookie: function (cname, cvalue, exdays) {
		    var d = new Date();
		    d.setTime(d.getTime() + (exdays*24*60*60*1000));
		    var expires = "expires="+ d.toUTCString();
		    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
		},

		getCookie: function (cname) {
		    var name = cname + "=";
		    var decodedCookie = decodeURIComponent(document.cookie);
		    var ca = decodedCookie.split(';');
		    for(var i = 0; i <ca.length; i++) {
		        var c = ca[i];
		        while (c.charAt(0) == ' ') {
		            c = c.substring(1);
		        }
		        if (c.indexOf(name) == 0) {
		            return c.substring(name.length, c.length);
		        }
		    }
		    return "";
		}
    }
};

/* ./assets/spa/mixins/loading.js */
weForms.mixins.Loading = {
    watch: {
        loading: function(value) {
            if ( value ) {
                NProgress.configure({ parent: '#wpadminbar' });
                NProgress.start();
            } else {
                NProgress.done();
            }
        }
    }
}

/* ./assets/spa/mixins/paginate.js */
weForms.mixins.Paginate = {
    data: function() {
        return {
            totalItems: 0,
            totalPage: 1,
            currentPage: 1,
            pageNumberInput: 1
        };
    },

    methods: {
        isFirstPage: function() {
            return this.currentPage == 1;
        },

        isLastPage: function() {
            return this.currentPage == this.totalPage;
        },

        goFirstPage: function() {
            this.currentPage = 1;
            this.pageNumberInput = this.currentPage;

            this.goToPage();
        },

        goLastPage: function() {
            this.currentPage = this.totalPage;
            this.pageNumberInput = this.currentPage;

            this.goToPage();
        },

        goToPage: function(direction) {
            if ( direction == 'prev' ) {
                this.currentPage--;
            } else if ( direction == 'next' ) {
                this.currentPage++;
            } else {
                if ( ! isNaN( direction ) && ( direction <= this.totalPage ) ) {
                    this.currentPage = direction;
                }
            }

            this.pageNumberInput = this.currentPage;
            this.fetchData();
        },
    }
};

/* ./assets/spa/mixins/tabs.js */
weForms.mixins.Tabs = {

    methods: {
        makeActive: function(val) {
            this.activeTab = val;
        },

        isActiveTab: function(val) {
          return this.activeTab === val;
        }
    }
};
