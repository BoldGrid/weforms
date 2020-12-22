weForms.routeComponents.Home = {
    template: '#tmpl-wpuf-home-page',

    data: function() {
        return {
            showTemplateModal: false
        };
    },

    methods: {
        displayModal: function() {
            this.showTemplateModal = true;
        },

        closeModal: function() {
            this.showTemplateModal = false;
        },
    }
};
