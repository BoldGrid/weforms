;(function($) {
'use strict';

Vue.component('builder-stage', {
    template: '#tmpl-wpuf-builder-stage',

    mixins: wpuf_form_builder_mixins(wpuf_mixins.builder_stage),

    computed: {
        form_fields: function () {
            return this.$store.state.form_fields;
        },

        field_settings: function () {
            return this.$store.state.field_settings;
        },

        hidden_fields: function () {
            return this.$store.state.form_fields.filter(function (item) {
                return 'custom_hidden_field' === item.template;
            });
        },

        editing_form_id: function () {
            return this.$store.state.editing_field_id;
        },

        pro_link: function () {
            return wpuf_form_builder.pro_link;
        }
    },

    mounted: function () {
        var self = this;

        // bind jquery ui sortable
        $('#form-preview-stage .wpuf-form.sortable-list').sortable({
            placeholder: 'form-preview-stage-dropzone',
            items: '.field-items',
            handle: '.control-buttons .move',
            scroll: true,
            update: function (e, ui) {
                var item    = ui.item[0],
                    data    = item.dataset,
                    source  = data.source,
                    toIndex = parseInt($(ui.item).index()),
                    payload = {
                        toIndex: toIndex
                    };

                if ('panel' === source) {
                    // prepare the payload to add new form element
                    var field_template  = ui.item[0].dataset.formField,
                        field           = $.extend(true, {}, self.field_settings[field_template].field_props);

                    // add a random integer id
                    field.id = self.get_random_id();

                    // add meta key
                    if ('yes' === field.is_meta && !field.name) {
                        field.name = field.label.replace(/\W/g, '_').toLowerCase() + '_' + field.id;
                    }

                    payload.field = field;

                    // add new form element
                    self.$store.commit('add_form_field_element', payload);

                    // remove button from stage
                    $(this).find('.button.ui-draggable.ui-draggable-handle').remove();

                } else if ('stage' === source) {
                    payload.fromIndex = parseInt(data.index);

                    self.$store.commit('swap_form_field_elements', payload);
                }

            }
        });
    },

    methods: {

        open_field_settings: function(field_id) {
            this.$store.commit('open_field_settings', field_id);
        },

        clone_field: function(field_id, index) {
            var payload = {
                field_id: field_id,
                index: index,
                new_id: this.get_random_id()
            };

            // single instance checking
            var field = _.find(this.$store.state.form_fields, function (item) {
                return parseInt(item.id) === parseInt(payload.field_id);
            });

            // check if these are already inserted
            if ( this.isSingleInstance( field.template ) && this.containsField( field.template ) ) {
                swal({
                    title: "Oops...",
                    text: "You already have this field in the form"
                });
                return;
            }

            this.$store.commit('clone_form_field_element', payload);
        },

        delete_field: function(index) {
            var self = this;

            swal({
                text: self.i18n.delete_field_warn_msg,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d54e21',
                confirmButtonText: self.i18n.yes_delete_it,
                cancelButtonText: self.i18n.no_cancel_it,
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
            }).then(function () {
                self.$store.commit('delete_form_field_element', index);
                swal(
                    'Deleted!',
                    'The field has been deleted.',
                    'success'
                );
            }, function() {

            });
        },

        delete_hidden_field: function (field_id) {
            var i = 0;

            for (i = 0; i < this.form_fields.length; i++) {
                if (parseInt(field_id) === parseInt(this.form_fields[i].id)) {
                    this.delete_field(i);
                }
            }
        },

        is_pro_feature: function (template) {
            return (this.field_settings[template] && this.field_settings[template].pro_feature) ? true : false;
        },

        is_template_available: function (field) {
            var template = field.template;

            if (this.field_settings[template]) {
                if (this.is_pro_feature(template)) {
                    return false;
                }

                return true;
            }

            // for example see 'mixin_builder_stage' mixin's 'is_taxonomy_template_available' method
            if (_.isFunction(this['is_' + template + '_template_available'])) {
                return this['is_' + template + '_template_available'].call(this, field);
            }

            return false;
        },

        is_full_width: function (template) {
            if (this.field_settings[template] && this.field_settings[template].is_full_width) {
                return true;
            }

            return false;
        },

        is_invisible: function (field) {
            return ( field.recaptcha_type && 'invisible_recaptcha' === field.recaptcha_type ) ? true : false;
        },

        get_field_name: function (template) {
            return this.field_settings[template].title;
        }
    }
});

Vue.component('field-checkbox', {
    template: '#tmpl-wpuf-field-checkbox',

    mixins: [
        wpuf_mixins.option_field_mixin
    ],

    computed: {
        value: {
            get: function () {
                var value = this.editing_form_field[this.option_field.name];

                if (this.option_field.is_single_opt) {
                    var option = Object.keys(this.option_field.options)[0];

                    if (value === option) {
                        return true;

                    } else {
                        return false;
                    }
                }

                return this.editing_form_field[this.option_field.name];
            },

            set: function (value) {
                if (this.option_field.is_single_opt) {
                    value = value ? Object.keys(this.option_field.options)[0] : '';
                }


                this.$store.commit('update_editing_form_field', {
                    editing_field_id: this.editing_form_field.id,
                    field_name: this.option_field.name,
                    value: value
                });
            }
        }
    }
});

Vue.component('field-html_help_text', {
    template: '#tmpl-wpuf-field-html_help_text',

    mixins: [
        wpuf_mixins.option_field_mixin
    ],
});

/**
 * Common settings component for option based fields
 * like select, multiselect, checkbox, radio
 */
Vue.component('field-option-data', {
    template: '#tmpl-wpuf-field-option-data',

    mixins: [
        wpuf_mixins.option_field_mixin
    ],

    data: function () {
        return {
            show_value: false,
            options: [],
            selected: []
        };
    },

    computed: {
        field_options: function () {
            return this.editing_form_field.options;
        },

        field_selected: function () {
            return this.editing_form_field.selected;
        }
    },

    mounted: function () {
        var self = this;

        this.set_options();

        $(this.$el).find('.option-field-option-chooser').sortable({
            items: '.option-field-option',
            handle: '.sort-handler',
            update: function (e, ui) {
                var item        = ui.item[0],
                    data        = item.dataset,
                    toIndex     = parseInt($(ui.item).index()),
                    fromIndex   = parseInt(data.index);

                self.options.swap(fromIndex, toIndex);
            }
        });
    },

    methods: {
        set_options: function () {
            var self = this;
            var field_options = $.extend(true, {}, this.editing_form_field.options);

            _.each(field_options, function (label, value) {
                self.options.push({label: label, value: value, id: self.get_random_id()});
            });

            if (this.option_field.is_multiple && !_.isArray(this.field_selected)) {
                this.selected = [this.field_selected];
            } else {
                this.selected = this.field_selected;
            }
        },

        // in case of select or radio buttons, user should deselect default value
        clear_selection: function () {
            this.selected = null;
        },

        add_option: function () {
            var count   = this.options.length,
                new_opt = this.i18n.option + '-' + (count + 1);

            this.options.push({
                label: new_opt , value: new_opt, id: this.get_random_id()
            });
        },

        delete_option: function (index) {
            if (this.options.length === 1) {
                this.warn({
                    text: this.i18n.last_choice_warn_msg,
                    showCancelButton: false,
                    confirmButtonColor: "#46b450",
                });

                return;
            }

            this.options.splice(index, 1);
        },

        set_option_label: function (index, label) {
            this.options[index].value = label.toLocaleLowerCase().replace( /\s/g, '_' );
        }
    },

    watch: {
        options: {
            deep: true,
            handler: function (new_opts) {
                var options = {},
                    i = 0;

                for (i = 0; i < new_opts.length; i++) {
                    options[new_opts[i].value] = new_opts[i].label;
                }

                this.update_value('options', options);
            }
        },

        selected: function (new_val) {
            this.update_value('selected', new_val);
        }
    }
});

Vue.component('field-option-pro-feature-alert', {
    template: '#tmpl-wpuf-field-option-pro-feature-alert',

    mixins: [
        wpuf_mixins.option_field_mixin
    ],

    computed: {
        pro_link: function () {
            return wpuf_form_builder.pro_link;
        }
    }
});

/**
 * Sidebar field options panel
 */
Vue.component('field-options', {
    template: '#tmpl-wpuf-field-options',

    mixins: wpuf_form_builder_mixins(wpuf_mixins.field_options),

    data: function() {
        return {
            show_basic_settings: true,
            show_advanced_settings: false
        };
    },

    computed: {
        editing_field_id: function () {
            this.show_basic_settings = true;
            this.show_advanced_settings = false;

            return parseInt(this.$store.state.editing_field_id);
        },

        editing_form_field: function () {
            var self = this;
            return _.find(this.$store.state.form_fields, function (item) {
                return parseInt(item.id) === parseInt(self.editing_field_id);
            });
        },

        settings: function() {
            var settings = [],
                template = this.editing_form_field.template;

            if (_.isFunction(this['settings_' + template])) {
                settings = this['settings_' + template].call(this, this.editing_form_field);
            } else {
                settings = this.$store.state.field_settings[template].settings;
            }

            return _.sortBy(settings, function (item) {
                return parseInt(item.priority);
            });
        },

        basic_settings: function () {
            return this.settings.filter(function (item) {
                return 'basic' === item.section;
            });
        },

        advanced_settings: function () {
            return this.settings.filter(function (item) {
                return 'advanced' === item.section;
            });
        },

        form_field_type_title: function() {
            var template = this.editing_form_field.template;

            if (_.isFunction(this['form_field_' + template + '_title'])) {
                return this['form_field_' + template + '_title'].call(this, this.editing_form_field);
            }

            return this.$store.state.field_settings[template].title;
        }
    }
});

Vue.component('field-radio', {
    template: '#tmpl-wpuf-field-radio',

    mixins: [
        wpuf_mixins.option_field_mixin
    ],

    computed: {
        value: {
            get: function () {
                return this.editing_form_field[this.option_field.name];
            },

            set: function (value) {
                this.$store.commit('update_editing_form_field', {
                    editing_field_id: this.editing_form_field.id,
                    field_name: this.option_field.name,
                    value: value
                });
            }
        }
    }
});

Vue.component('field-select', {
    template: '#tmpl-wpuf-field-select',

    mixins: [
        wpuf_mixins.option_field_mixin
    ],

    computed: {
        value: {
            get: function () {
                return this.editing_form_field[this.option_field.name];
            },

            set: function (value) {
                this.$store.commit('update_editing_form_field', {
                    editing_field_id: this.editing_form_field.id,
                    field_name: this.option_field.name,
                    value: value
                });
            }
        }
    }
});

Vue.component('field-text', {
    template: '#tmpl-wpuf-field-text',

    mixins: [
        wpuf_mixins.option_field_mixin
    ],

    computed: {
        value: {
            get: function () {
                return this.editing_form_field[this.option_field.name];
            },

            set: function (value) {
                this.update_value(this.option_field.name, value);
            }
        }
    },

    methods: {
        on_focusout: function (e) {
            wpuf_form_builder.event_hub.$emit('field-text-focusout', e, this);
        },
        on_keyup: function (e) {
            wpuf_form_builder.event_hub.$emit('field-text-keyup', e, this);
        }
    }
});

Vue.component('field-text-meta', {
    template: '#tmpl-wpuf-field-text-meta',

    mixins: [
        wpuf_mixins.option_field_mixin
    ],

    computed: {
        value: {
            get: function () {
                return this.editing_form_field[this.option_field.name];
            },

            set: function (value) {
                this.update_value(this.option_field.name, value);
            }
        }
    },

    created: function () {
        if ('yes' === this.editing_form_field.is_meta) {
            if (!this.value) {
                this.value = this.editing_form_field.label.replace(/\W/g, '_').toLowerCase();
            }

            wpuf_form_builder.event_hub.$on('field-text-keyup', this.meta_key_autocomplete);
        }
    },

    methods: {
        meta_key_autocomplete: function (e, label_vm) {
            if (
                'label' === label_vm.option_field.name &&
                parseInt(this.editing_form_field.id) === parseInt(label_vm.editing_form_field.id)
            ) {
                this.value = label_vm.value.replace(/\W/g, '_').toLowerCase();
            }
        }
    }
});

Vue.component('field-textarea', {
    template: '#tmpl-wpuf-field-textarea',

    mixins: [
        wpuf_mixins.option_field_mixin
    ],

    computed: {
        value: {
            get: function () {
                return this.editing_form_field[this.option_field.name];
            },

            set: function (value) {
                this.update_value(this.option_field.name, value);
            }
        }
    },
});

/**
 * Field template: Checkbox
 */
Vue.component('form-checkbox_field', {
    template: '#tmpl-wpuf-form-checkbox_field',

    mixins: [
        wpuf_mixins.form_field_mixin
    ]
});

/**
 * Field template: Hidden
 */
Vue.component('form-custom_hidden_field', {
    template: '#tmpl-wpuf-form-custom_hidden_field',

    mixins: [
        wpuf_mixins.form_field_mixin
    ]
});

/**
 * Field template: Custom HTML
 */
Vue.component('form-custom_html', {
    template: '#tmpl-wpuf-form-custom_html',

    mixins: [
        wpuf_mixins.form_field_mixin
    ],

    data: function () {
        return {
            raw_html: '<p>from data</p>'
        };
    }
});

/**
 * Field template: Dropdown/Select
 */
Vue.component('form-dropdown_field', {
    template: '#tmpl-wpuf-form-dropdown_field',

    mixins: [
        wpuf_mixins.form_field_mixin
    ]
});

/**
 * Field template: Email
 */
Vue.component('form-email_address', {
    template: '#tmpl-wpuf-form-email_address',

    mixins: [
        wpuf_mixins.form_field_mixin
    ]
});

/**
 * Field template: Featured Image
 */
Vue.component('form-featured_image', {
    template: '#tmpl-wpuf-form-featured_image',

    mixins: [
        wpuf_mixins.form_field_mixin
    ]
});

/**
 * Sidebar form fields panel
 */
Vue.component('form-fields', {
    template: '#tmpl-wpuf-form-fields',

    mixins: wpuf_form_builder_mixins(wpuf_mixins.form_fields),

    computed: {
        panel_sections: function () {
            return this.$store.state.panel_sections;
        },

        field_settings: function () {
            return this.$store.state.field_settings;
        },

        form_fields: function () {
            return this.$store.state.form_fields;
        }
    },

    mounted: function () {
        // bind jquery ui draggable
        $(this.$el).find('.panel-form-field-buttons .button').draggable({
            connectToSortable: '#form-preview-stage .wpuf-form',
            helper: 'clone',
            revert: 'invalid',
            cancel: '.button-faded',
        }).disableSelection();
    },

    methods: {
        panel_toggle: function (index) {
            this.$store.commit('panel_toggle', index);
        },

        add_form_field: function (field_template) {
            var payload = {
                toIndex: this.$store.state.form_fields.length,
            };

            // check if these are already inserted
            if ( this.isSingleInstance( field_template ) && this.containsField( field_template ) ) {
                swal({
                    title: "Oops...",
                    text: "You already have this field in the form"
                });
                return;
            }

            var field = $.extend(true, {}, this.$store.state.field_settings[field_template].field_props);

            field.id = this.get_random_id();

            if (!field.name && field.label) {
                field.name = field.label.replace(/\W/g, '_').toLowerCase();

                var same_template_fields = this.form_fields.filter(function (form_field) {
                   return (form_field.template === field.template);
                });

                if (same_template_fields.length) {
                    field.name += '_' + same_template_fields.length;
                }
            }

            payload.field = field;

            // add new form element
            this.$store.commit('add_form_field_element', payload);
        },

        is_pro_feature: function (field) {
            return this.field_settings[field].pro_feature;
        },

        alert_pro_feature: function (field) {
            var title = this.field_settings[field].title;

            swal({
                title: '<i class="fa fa-lock"></i> ' + title + ' <br>' + this.i18n.is_a_pro_feature,
                text: this.i18n.pro_feature_msg,
                type: '',
                showCancelButton: true,
                cancelButtonText: this.i18n.close,
                confirmButtonColor: '#46b450',
                confirmButtonText: this.i18n.upgrade_to_pro
            }).then(function (is_confirm) {
                if (is_confirm) {
                    window.open(wpuf_form_builder.pro_link, '_blank');
                }

            }, function() {});
        },

        alert_invalidate_msg: function (field) {
            var validator = this.field_settings[field].validator;

            if (validator && validator.msg) {
                this.warn({
                    title: validator.msg_title || '',
                    html: validator.msg,
                    type: 'warning',
                    showCancelButton: false,
                    confirmButtonColor: '#46b450',
                    confirmButtonText: this.i18n.ok
                });
            }
        },

        get_invalidate_btn_class: function (field) {
            return this.field_settings[field].validator.button_class;
        }
    }
});

/**
 * Field template: Image Upload
 */
Vue.component('form-image_upload', {
    template: '#tmpl-wpuf-form-image_upload',

    mixins: [
        wpuf_mixins.form_field_mixin
    ]
});

/**
 * Field template: Multi-Select
 */
Vue.component('form-multiple_select', {
    template: '#tmpl-wpuf-form-multiple_select',

    mixins: [
        wpuf_mixins.form_field_mixin
    ]
});

/**
 * Field Template: Post Content
 */
Vue.component('form-post_content', {
    template: '#tmpl-wpuf-form-post_content',

    mixins: [
        wpuf_mixins.form_field_mixin
    ]
});

/**
 * Field Template: Post Excerpt
 */
Vue.component('form-post_excerpt', {
    template: '#tmpl-wpuf-form-post_excerpt',

    mixins: [
        wpuf_mixins.form_field_mixin
    ]
});

/**
 * Field template: post_tags
 */
Vue.component('form-post_tags', {
    template: '#tmpl-wpuf-form-post_tags',

    mixins: [
        wpuf_mixins.form_field_mixin
    ]
});

/**
 * Field template: Post Title
 */
Vue.component('form-post_title', {
    template: '#tmpl-wpuf-form-post_title',

    mixins: [
        wpuf_mixins.form_field_mixin
    ]
});

/**
 * Field template: Radio
 */
Vue.component('form-radio_field', {
    template: '#tmpl-wpuf-form-radio_field',

    mixins: [
        wpuf_mixins.form_field_mixin
    ]
});

/**
 * Field template: Recaptcha
 */
Vue.component('form-recaptcha', {
    template: '#tmpl-wpuf-form-recaptcha',

    mixins: [
        wpuf_mixins.form_field_mixin
    ],

    computed: {
        has_recaptcha_api_keys: function () {
            return (wpuf_form_builder.recaptcha_site && wpuf_form_builder.recaptcha_secret) ? true : false;
        },

        no_api_keys_msg: function () {
            return wpuf_form_builder.field_settings.recaptcha.validator.msg;
        }
    }
});

/**
 * Field template: Section Break
 */
Vue.component('form-section_break', {
    template: '#tmpl-wpuf-form-section_break',

    mixins: [
        wpuf_mixins.form_field_mixin
    ]
});

/**
 * Field template: taxonomy
 */
Vue.component('form-taxonomy', {
    template: '#tmpl-wpuf-form-taxonomy',

    mixins: [
        wpuf_mixins.form_field_mixin
    ],

    computed: {
        terms: function () {
            var i;

            for (i in wpuf_form_builder.wp_post_types) {
                var taxonomies = wpuf_form_builder.wp_post_types[i];

                if (taxonomies.hasOwnProperty(this.field.name)) {
                    var tax_field = taxonomies[this.field.name];

                    if (tax_field.terms) {
                        return tax_field.terms;
                    }
                }
            }

            return [];
        },

        sorted_terms: function () {
            var self  = this;
            var terms = $.extend(true, [], this.terms);

            // selection type and terms
            if (this.field.exclude_type && this.field.exclude) {
                var filter_ids = this.field.exclude.split(',').map(function (id) {
                    id = id.trim();
                    id = parseInt(id);
                    return id;
                }).filter(function (id) {
                    return isFinite(id);
                });

                terms = terms.filter(function (term) {

                    switch(self.field.exclude_type) {
                        case 'exclude':
                            return _.indexOf(filter_ids, term.term_id) < 0;

                        case 'include':
                            return _.indexOf(filter_ids, term.term_id) >= 0;

                        case 'child_of':
                            return _.indexOf(filter_ids, parseInt(term.parent)) >= 0;
                    }
                });
            }

            // order
            terms = _.sortBy(terms, function (term) {
                return term[self.field.orderby];
            });

            if ('DESC' === this.field.order) {
                terms = terms.reverse();
            }

            var parent_terms = terms.filter(function (term) {
                return !term.parent;
            });

            parent_terms.map(function (parent) {
                parent.children = self.get_child_terms(parent.term_id, terms);
            });

            return parent_terms.length ? parent_terms : terms;
        }
    },

    methods: {
        get_child_terms: function (parent_id, terms) {
            var self = this;

            var child_terms = terms.filter(function (term) {
                return parseInt(term.parent) === parseInt(parent_id);
            });

            child_terms.map(function (child) {
                child.children = self.get_child_terms(child.term_id, terms);
            });

            return child_terms;
        },

        get_term_dropdown_options: function () {
            var self    = this,
                options = '';

            _.each(self.sorted_terms, function (term) {
                options += self.get_term_dropdown_options_children(term, 0);
            });

            return options;
        },

        get_term_dropdown_options_children: function (term, level) {
            var self   = this,
                option = '';

            var indent = '',
                i = 0;

            for (i = 0; i < level; i++) {
                indent += '&nbsp;&nbsp;';
            }

            option += '<option value="' + term.id + '">' + indent + term.name + '</option>';

            if (term.children.length) {
                _.each(term.children, function (child_term) {
                    option += self.get_term_dropdown_options_children(child_term, (level + 1));
                });
            }

            return option;
        },

        get_term_checklist: function () {
            var self      = this,
                checklist = '';


            checklist += '<ul class="wpuf-category-checklist">';

            _.each(this.sorted_terms, function (term) {
                checklist += self.get_term_checklist_li(term);
            });

            checklist += '</ul>';

            return checklist;
        },

        get_term_checklist_li: function (term) {
            var self = this,
                li   = '';

            li += '<li><label class="selectit"><input type="checkbox"> ' + term.name + '</label></li>';

            if (term.children.length) {
                li += '<ul class="children">';

                _.each(term.children, function (child_term) {
                    li += self.get_term_checklist_li(child_term);
                });

                li += '</ul>';
            }

            return li;
        }
    }
});

/**
 * Field template: Text
 */
Vue.component('form-text_field', {
    template: '#tmpl-wpuf-form-text_field',

    mixins: [
        wpuf_mixins.form_field_mixin
    ]
});

Vue.component('form-textarea_field', {
    template: '#tmpl-wpuf-form-textarea_field',

    mixins: [
        wpuf_mixins.form_field_mixin
    ]
});

/**
 * Field template: Website URL
 */
Vue.component('form-website_url', {
    template: '#tmpl-wpuf-form-website_url',

    mixins: [
        wpuf_mixins.form_field_mixin
    ]
});

Vue.component('help-text', {
    template: '#tmpl-wpuf-help-text',

    props: {
        text: {
            type: String,
            default: ''
        }
    },

    mounted: function () {
        $(".wpuf-tooltip").tooltip();
    }
});

Vue.component('text-editor', {
    template: '#tmpl-wpuf-text-editor',

    props: ['rich'],

    computed: {
        site_url: function () {
            return wpuf_form_builder.site_url;
        },

        is_full: function () {
            return 'yes' === this.rich;
        }
    }
});

})(jQuery);
