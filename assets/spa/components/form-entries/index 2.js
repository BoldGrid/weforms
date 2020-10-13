weForms.routeComponents.FormEntries = {
    props: {
        id: [String, Number]
    },
    template: '#tmpl-wpuf-form-entries',
    data: function() {
        return {
            selected: 0,
            form_title: 'Loading...',
            status: 'publish',
            total: 0,
            totalTrash: 0,
        };
    }
};
