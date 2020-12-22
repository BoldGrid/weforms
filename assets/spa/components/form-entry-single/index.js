weForms.routeComponents.FormEntriesSingle = {
    template: '#tmpl-wpuf-form-entry-single',
    mixins: [weForms.mixins.Loading,weForms.mixins.Cookie],
    data: function() {
        return {
            loading: false,
            hideEmpty: true,
            hasEmpty: false,
            show_payment_data: false,
            entry: {
                form_fields: {},
                meta_data: {},
                payment_data: {},
            },
            form_settings: {},
            respondent_points: 0,
            answers: {},
            countries: weForms.countries
        };
    },
    created: function() {
        this.hideEmpty = this.hideEmptyStatus();
        this.fetchData();
    },
    computed: {
        hasFormFields: function() {
            return Object.keys(this.entry.form_fields).length;
        },
    },
    methods: {
        fetchData: function() {
            var self = this;

            this.loading = true;

            wp.ajax.send( 'weforms_form_entry_details', {
                data: {
                    entry_id: self.$route.params.entryid,
                    form_id: self.$route.params.id,
                    _wpnonce: weForms.nonce
                },
                success: function(response) {
                    self.loading = false;
                    self.entry   = response;
                    self.hasEmpty = response.has_empty;
                    self.form_settings = response.form_settings;
                    self.respondent_points = response.respondent_points;
                    self.answers = response.answers;
                },
                error: function(error) {
                    self.loading = false;
                    alert(error);
                }
            });
        },

        trashEntry: function() {
            var self = this;

            if ( !confirm( weForms.confirm ) ) {
                return;
            }

            wp.ajax.send( 'weforms_form_entry_trash', {
                data: {
                    entry_id: self.$route.params.entryid,
                    _wpnonce: weForms.nonce
                },

                success: function() {
                    self.loading = false;

                    self.$router.push({ name: 'formEntries', params: { id: self.$route.params.id }});
                },
                error: function(error) {
                    self.loading = false;
                    alert(error);
                }
            });
        },

        hideEmptyStatus: function(){
            return this.getCookie('weFormsEntryHideEmpty') === 'false' ? false : true;
        },

        findCountry: function( code ) {
            return this.countries.find( (country) => country.code === code );
        },

        getCountryName: function( code ) {
            if ( this.findCountry( code ) ) {
                return this.findCountry( code ).name;
            }
        },

        getAddressFieldValue: function( value ) {
            var self = this,
                countryString = value.match(/Country Select:(\s([A-Z])\w+)/g);

            if (countryString !== null) {
                var countryCode = countryString[0].substring(15, countryString[0].length).trim(),
                    countryName = self.getCountryName(countryCode),
                    strToReplace = countryCode;

                return value.replace(strToReplace, countryName);
            }

            return value;
        }
    },
    watch: {
        hideEmpty: function(value){
            this.setCookie('weFormsEntryHideEmpty',value,356);
        }
    }
};
