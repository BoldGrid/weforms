Vue.component('wpuf-cf-form-notification', {
    template: '#tmpl-wpuf-form-notification',
    mixins: [weForms.mixins.Loading],
    data: function() {
        return {
            editing: false,
            editingIndex: 0,
        };
    },

    computed: {
        is_pro: function() {
            return 'true' === weForms.is_pro;
        },
        has_sms: function() {
            return 'true' === weForms.has_sms;
        },
        pro_link: function() {
            return wpuf_form_builder.pro_link;
        },
        notifications: function() {
            return this.$store.state.notifications;
        },

        hasNotifications: function() {
            return Object.keys( this.$store.state.notifications ).length;
        }
    },

    methods: {
        addNew: function() {
            this.$store.commit('addNotification', wpuf_form_builder.defaultNotification);
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
                this.$emit('deleteNotification', index);
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
        },

        insertValueEditor: function(type, field, property) {
            var value = ( field !== undefined ) ? '{' + type + ':' + field + '}' : '{' + type + '}';
            this.$emit('insertValueEditor', value);
        },
    }
});
