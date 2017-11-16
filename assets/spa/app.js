if (!Array.prototype.hasOwnProperty('swap')) {
    Array.prototype.swap = function (from, to) {
        this.splice(to, 0, this.splice(from, 1)[0]);
    };
}

Vue.component('datepicker', {
    template: '<input type="text" v-bind:value="value" />',
    props: ['value'],
    mounted: function() {
        var self = this;

        $(this.$el).datetimepicker({
            dateFormat: 'yy-mm-dd',
            timeFormat: "HH:mm:ss",
            onClose: this.onClose
        });
    },

    methods: {
        onClose: function(date) {
            this.$emit('input', date);
        }
    },
});

Vue.component('weforms-colorpicker', {
    template: '<input type="text" v-bind:value="value" />',
    props: ['value'],
    mounted: function() {
        var self = this;

        $(this.$el).wpColorPicker({
            change: this.onChange
        });
    },

    methods: {
        onChange: function(event, ui) {
            this.$emit('input', ui.color.toString());
        }
    },
});

// check if an element is visible in browser viewport
function is_element_in_viewport (el) {
    if (typeof jQuery === "function" && el instanceof jQuery) {
        el = el[0];
    }

    var rect = el.getBoundingClientRect();

    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && /*or $(window).height() */
        rect.right <= (window.innerWidth || document.documentElement.clientWidth) /*or $(window).width() */
    );
}

/**
 * Vuex Store data
 */
var wpuf_form_builder_store = new Vuex.Store({
    state: {
        post: {},
        form_fields: [],
        panel_sections: wpuf_form_builder.panel_sections,
        field_settings: wpuf_form_builder.field_settings,
        notifications: [],
        settings: {},
        integrations: {},
        current_panel: 'form-fields',
        editing_field_id: 0, // editing form field id
    },

    mutations: {
        set_form_fields: function (state, form_fields) {
            Vue.set(state, 'form_fields', form_fields);
        },

        set_form_post: function (state, post) {
            Vue.set(state, 'post', post);
        },

        set_form_notification: function (state, value) {
            Vue.set(state, 'notifications', value);
        },

        set_form_integrations: function (state, value) {
            Vue.set(state, 'integrations', value);
        },

        set_form_settings: function (state, value) {
            Vue.set(state, 'settings', value);
        },

        // set the current panel
        set_current_panel: function (state, panel) {
            if ('field-options' !== state.current_panel &&
                'field-options' === panel &&
                state.form_fields.length
            ) {
                state.editing_field_id = state.form_fields[0].id;
            }

            state.current_panel = panel;

            // reset editing field id
            if ('form-fields' === panel) {
                state.editing_field_id = 0;
            }
        },

        // add show property to every panel section
        panel_add_show_prop: function (state) {
            state.panel_sections.map(function (section, index) {
                if (!section.hasOwnProperty('show')) {
                    Vue.set(state.panel_sections[index], 'show', true);
                }
            });
        },

        // toggle panel sections
        panel_toggle: function (state, index) {
            state.panel_sections[index].show = !state.panel_sections[index].show;
        },

        // open field settings panel
        open_field_settings: function (state, field_id) {
            var field = state.form_fields.filter(function(item) {
                return parseInt(field_id) === parseInt(item.id);
            });

            if ('field-options' === state.current_panel && field[0].id === state.editing_field_id) {
                return;
            }

            if (field.length) {
                state.editing_field_id = 0;
                state.current_panel = 'field-options';

                setTimeout(function () {
                    state.editing_field_id = field[0].id;
                }, 400);
            }
        },

        update_editing_form_field: function (state, payload) {
            var editing_field = _.find(state.form_fields, function (item) {
                return parseInt(item.id) === parseInt(payload.editing_field_id);
            });

            editing_field[payload.field_name] = payload.value;
        },

        // add new form field element
        add_form_field_element: function (state, payload) {
            state.form_fields.splice(payload.toIndex, 0, payload.field);

            // bring newly added element into viewport
            Vue.nextTick(function () {
                var el = $('#form-preview-stage .wpuf-form .field-items').eq(payload.toIndex);

                if (el && !is_element_in_viewport(el.get(0))) {
                    $('#builder-stage section').scrollTo(el, 800, {offset: -50});
                }
            });
        },

        // sorting inside stage
        swap_form_field_elements: function (state, payload) {
            state.form_fields.swap(payload.fromIndex, payload.toIndex);
        },

        clone_form_field_element: function (state, payload) {
            var field = _.find(state.form_fields, function (item) {
                return parseInt(item.id) === parseInt(payload.field_id);
            });

            var clone = $.extend(true, {}, field),
                index = parseInt(payload.index) + 1;

            clone.id     = payload.new_id;
            clone.name   = clone.name + '_copy';
            clone.is_new = true;

            state.form_fields.splice(index, 0, clone);
        },

        // delete a field
        delete_form_field_element: function (state, index) {
            state.current_panel = 'form-fields';
            state.form_fields.splice(index, 1);
        },

        // set fields for a panel section
        set_panel_section_fields: function (state, payload) {
            var section = _.find(state.panel_sections, function (item) {
                return item.id === payload.id;
            });

            section.fields = payload.fields;
        },

        // notifications
        addNotification: function(state, payload) {
            state.notifications.push(_.clone(payload));
        },

        deleteNotification: function(state, index) {
            state.notifications.splice(index, 1);
        },

        cloneNotification: function(state, index) {
            var clone = $.extend(true, {}, state.notifications[index]);

            index = parseInt(index) + 1;
            state.notifications.splice(index, 0, clone);
        },

        // update by it's property
        updateNotificationProperty: function(state, payload) {
            state.notifications[payload.index][payload.property] = payload.value;
        },

        updateNotification: function(state, payload) {
            state.notifications[payload.index] = payload.value;
        },

        updateIntegration: function(state, payload) {
            // console.log(payload);
            // console.log(state.integrations[payload.index]);
            // state.integrations[payload.index] = payload.value;
            Vue.set(state.integrations, payload.index, payload.value)

            // state.integrations.splice(payload.index, 0, payload.value);
        }
    }
});

// 1. Define route components.
weForms.routeComponents.FormHome = { template: '<div><router-view class="child"></router-view></div>' };
weForms.routeComponents.SingleForm = { template: '#tmpl-wpuf-form-editor' };
weForms.routeComponents.FormEntriesHome = {
    template: '<div><router-view class="grand-child"></router-view></div>',
};

/**
 * Parse the route array and bind required components
 *
 * This changes the weForms.routes array and changes the components
 * so we can use weForms.routeComponents.{compontent} component.
 *
 * @param  {array} routes
 *
 * @return {void}
 */
function parseRouteComponent(routes) {

    for (var i = 0; i < routes.length; i++) {
        if ( typeof routes[i].children === 'object' ) {

            parseRouteComponent( routes[i].children );

            if ( typeof routes[i].component !== 'undefined' ) {
                routes[i].component = weForms.routeComponents[ routes[i].component ];
            }

        } else {
            routes[i].component = weForms.routeComponents[ routes[i].component ];
        }
    }
}

// mutate the localized array
parseRouteComponent(weForms.routes);

// 3. Create the router instance and pass the `routes` option
var router = new VueRouter({
    routes: weForms.routes,
    scrollBehavior: function (to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition
        } else {
            return { x: 0, y: 0 }
        }
    }
});

window.weFormsBuilderisDirty = false;

router.beforeEach((to, from, next) => {
    // show warning if builder has unsaved changes
    if ( window.weFormsBuilderisDirty ) {
        if ( confirm( wpuf_form_builder.i18n.unsaved_changes + ' ' +wpuf_form_builder.i18n.areYouSureToLeave ) ) {
            window.weFormsBuilderisDirty = false;
        } else {
            next(from.path);
            return false;
        }
    }

    next();
});


var app = new Vue({
    router: router,
    store: wpuf_form_builder_store
}).$mount('#wpuf-contact-form-app')

// Admin menu hack
var menuRoot = $('#toplevel_page_weforms');

menuRoot.on('click', 'a', function() {
    var self = $(this);

    $('ul.wp-submenu li', menuRoot).removeClass('current');

    if ( self.hasClass('wp-has-submenu') ) {
        $('li.wp-first-item', menuRoot).addClass('current');
    } else {
        self.parents('li').addClass('current');
    }
});

$(function() {

    // select the current sub menu on page load
    var current_url = window.location.href;
    var current_path = current_url.substr( current_url.indexOf('admin.php') );

    $('ul.wp-submenu a', menuRoot).each(function(index, el) {
        if ( $(el).attr( 'href' ) === current_path ) {
            $(el).parent().addClass('current');
            return;
        }
    });
});
