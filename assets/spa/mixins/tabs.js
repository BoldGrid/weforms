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
