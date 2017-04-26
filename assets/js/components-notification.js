;(function($) {

Vue.component('wpuf-cf-form-notification', {
    template: '#tmpl-wpuf-form-notification',
    data: function() {
        return {
            editing: false,
            editingIndex: 0,
        }
    },

    computed: {

        notifications: function() {
            return this.$store.state.notifications;
        },

        hasNotifications: function() {
            return Object.keys( this.$store.state.notifications ).length;
        }
    },

    methods: {
        addNew: function() {
            this.$store.commit('addNotification', wpufCFBuilderNotification.defaultNotification);
        },

        editItem: function(index) {
            this.editing = true;
            this.editingIndex = index;
        },

        editDone: function() {
            this.editing = false;

            this.$store.commit('updateNotification', {
                index: this.editingIndex,
                value: this.notifications[this.editingIndex]
            });

            jQuery('.advanced-field-wrap').slideUp('fast');
        },

        deleteItem: function(index) {
            if ( confirm( 'Are you sure' ) ) {
                this.editing = false;
                this.$store.commit( 'deleteNotification', index);
            }
        },

        toggelNotification: function(index) {
            this.$store.commit('updateNotificationProperty', {
                index: index,
                property: 'active',
                value: !this.notifications[index].active
            });
        },

        duplicate: function(index) {
            this.$store.commit('cloneNotification', index);
        },

        toggleAdvanced: function() {
            jQuery('.advanced-field-wrap').slideToggle('fast');
        },

        insertValue: function(type, field, property) {
            var notification = this.notifications[this.editingIndex],
                value = ( field !== undefined ) ? '{' + type + ':' + field + '}' : '{' + type + '}';

            notification[property] = notification[property] + value;
        }
    }
});

Vue.component('wpuf-merge-tags', {
    template: '#tmpl-wpuf-merge-tags',
    props: {
        field: String,
        filter: {
            type: String,
            default: null
        }
    },

    data: function() {
        return {
            showing: false,
            type: null,
        }
    },

    computed: {
        form_fields: function () {
            var template = this.filter;

            if (template !== null) {
                return this.$store.state.form_fields.filter(function(item) {
                    return item.template === template;
                });
            }

            return this.$store.state.form_fields;
        },
    },

    methods: {
        toggleFields: function() {
            $(event.target).parent().siblings('.merge-tags').slideToggle('fast');
        },

        insertField: function(type, field) {
            this.$emit('insert', type, field, this.field);
        }
    }
});

})(jQuery);