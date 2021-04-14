'use strict';

;(function ($) {
    'use strict';

    Vue.component('builder-stage', {
        template: '#tmpl-wpuf-builder-stage',

        mixins: wpuf_form_builder_mixins(wpuf_mixins.builder_stage),

        computed: {
            form_fields: function form_fields() {
                return this.$store.state.form_fields;
            },

            field_settings: function field_settings() {
                return this.$store.state.field_settings;
            },

            hidden_fields: function hidden_fields() {
                return this.$store.state.form_fields.filter(function (item) {
                    return 'custom_hidden_field' === item.template || 'humanpresence' === item.template;
                });
            },

            editing_form_id: function editing_form_id() {
                return this.$store.state.editing_field_id;
            },

            pro_link: function pro_link() {
                return wpuf_form_builder.pro_link;
            }
        },

        created: function created() {
            var self = this,
                humanpresence_field_id = 0,
                i = 0;
            for (i = 0; i < self.$store.state.form_fields.length; i++) {
                if (self.$store.state.form_fields[i].template === 'humanpresence') {
                    humanpresence_field_id = self.$store.state.form_fields[i].id;
                }
            }

            wpuf_form_builder.event_hub.$on('humanpresence-changed', this.humanpresence_changed);
            wpuf_form_builder.event_hub.$on('humanpresence-disabled', this.delete_humanpresence_field);
        },

        mounted: function mounted() {
            var self = this,
                in_column_field = false;

            // bind jquery ui sortable
            $('#form-preview-stage .wpuf-form.sortable-list').sortable({
                placeholder: 'form-preview-stage-dropzone',
                items: '.field-items',
                handle: '.control-buttons .move',
                scroll: true,
                over: function over() {
                    in_column_field = false;

                    // if the field drop in column field, then stop field rendering in the builder stage
                    $(".wpuf-column-inner-fields").on("drop", function (event) {
                        var targetColumn = event.currentTarget.classList,
                            isColumnExist = $.inArray(".wpuf-column-inner-fields", targetColumn);

                        if (isColumnExist) {
                            in_column_field = true;
                        }
                    });
                },
                update: function update(e, ui) {
                    var item = ui.item[0],
                        data = item.dataset,
                        source = data.source,
                        toIndex = parseInt($(ui.item).index()),
                        payload = {
                        toIndex: toIndex
                    };

                    if ('panel' === source) {
                        // prepare the payload to add new form element
                        var field_template = ui.item[0].dataset.formField,
                            field = $.extend(true, {}, self.field_settings[field_template].field_props);

                        // check if these are already inserted
                        if (self.isSingleInstance(field_template) && self.containsField(field_template)) {
                            swal({
                                title: "Oops...",
                                text: "You already have this field in the form"
                            });

                            $(this).find('.button.ui-draggable.ui-draggable-handle').remove();
                            return;
                        }

                        // add a random integer id
                        field.id = self.get_random_id();

                        // add meta key
                        if ('yes' === field.is_meta && !field.name) {
                            field.name = field.label.replace(/\W/g, '_').toLowerCase() + '_' + field.id;
                        }

                        payload.field = field;

                        // add new form element
                        if (!in_column_field) {
                            self.$store.commit('add_form_field_element', payload);
                        }

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

            open_field_settings: function open_field_settings(field_id) {
                this.$store.commit('open_field_settings', field_id);
            },

            clone_field: function clone_field(field_id, index) {
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
                if (this.isSingleInstance(field.template) && this.containsField(field.template)) {
                    swal({
                        title: "Oops...",
                        text: "You already have this field in the form"
                    });
                    return;
                }

                this.$store.commit('clone_form_field_element', payload);
            },

            delete_field: function delete_field(index) {
                var self = this;

                swal({
                    text: self.i18n.delete_field_warn_msg,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d54e21',
                    confirmButtonText: self.i18n.yes_delete_it,
                    cancelButtonText: self.i18n.no_cancel_it,
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger'
                }).then(function () {
                    self.$store.commit('delete_form_field_element', index);
                }, function () {});
            },

            delete_field_no_confirm: function delete_field_no_confirm(index) {
                this.$store.commit('delete_form_field_element', index);
            },

            delete_hidden_field: function delete_hidden_field(field_id) {
                var i = 0;

                for (i = 0; i < this.form_fields.length; i++) {
                    if (parseInt(field_id) === parseInt(this.form_fields[i].id)) {
                        if (this.form_fields[i].template === 'humanpresence') {
                            this.delete_field_no_confirm(i);
                            this.disable_humanpresence_setting();
                        } else {
                            this.delete_field(i);
                        }
                    }
                }
            },

            delete_humanpresence_field: function delete_humanpresence_field(data) {
                var i = 0;
                if (data.$store.state.form_fields.length) {
                    for (i = 0; i < data.$store.state.form_fields.length; i++) {
                        if (data.$store.state.form_fields[i].template === 'humanpresence') {
                            this.delete_field_no_confirm(i);
                            this.disable_humanpresence_setting();
                        }
                    }
                }
            },

            disable_humanpresence_setting: function disable_humanpresence_setting() {
                var settings = this.$store.state.settings;
                settings.humanpresence_enabled = false;
                this.$store.commit('set_form_settings', settings);
            },

            humanpresence_changed: function humanpresence_changed(e, data) {
                if (data.$store.state.settings.humanpresence_enabled === 'true') {
                    wpuf_form_builder.event_hub.$emit('humanpresence-enabled', data);
                } else {
                    wpuf_form_builder.event_hub.$emit('humanpresence-disabled', data);
                }
            },

            is_pro_feature: function is_pro_feature(template) {
                return this.field_settings[template] && this.field_settings[template].pro_feature ? true : false;
            },

            is_template_available: function is_template_available(field) {
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

            is_full_width: function is_full_width(template) {
                if (this.field_settings[template] && this.field_settings[template].is_full_width) {
                    return true;
                }

                return false;
            },

            is_invisible: function is_invisible(field) {
                return field.recaptcha_type && 'invisible_recaptcha' === field.recaptcha_type ? true : false;
            },

            get_field_name: function get_field_name(template) {
                return this.field_settings[template].title;
            }
        }
    });

    Vue.component('field-checkbox', {
        template: '#tmpl-wpuf-field-checkbox',

        mixins: [wpuf_mixins.option_field_mixin],

        computed: {
            value: {
                get: function get() {
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

                set: function set(value) {
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

        mixins: [wpuf_mixins.option_field_mixin]
    });

    Vue.component('field-multiselect', {
        template: '#tmpl-wpuf-field-multiselect',

        mixins: [wpuf_mixins.option_field_mixin],

        computed: {
            value: {
                get: function get() {
                    return this.editing_form_field[this.option_field.name];
                },

                set: function set(value) {
                    this.$store.commit('update_editing_form_field', {
                        editing_field_id: this.editing_form_field.id,
                        field_name: this.option_field.name,
                        value: value
                    });
                }
            }
        },

        mounted: function mounted() {
            this.bind_selectize();
        },

        methods: {
            bind_selectize: function bind_selectize() {
                var self = this;

                $(this.$el).find('.term-list-selector').selectize({}).on('change', function () {
                    var data = $(this).val();

                    self.value = data;
                });
            }
        }

    });

    /**
     * Common settings component for option based fields
     * like select, multiselect, checkbox, radio
     */
    Vue.component('field-option-data', {
        template: '#tmpl-wpuf-field-option-data',

        mixins: [wpuf_mixins.option_field_mixin],

        data: function data() {
            return {
                show_value: false,
                options: [],
                selected: []
            };
        },

        computed: {
            field_options: function field_options() {
                return this.editing_form_field.options;
            },

            field_selected: function field_selected() {
                return this.editing_form_field.selected;
            }
        },

        mounted: function mounted() {
            var self = this;

            this.set_options();

            $(this.$el).find('.option-field-option-chooser').sortable({
                items: '.option-field-option',
                handle: '.sort-handler',
                update: function update(e, ui) {
                    var item = ui.item[0],
                        data = item.dataset,
                        toIndex = parseInt($(ui.item).index()),
                        fromIndex = parseInt(data.index);

                    self.options.swap(fromIndex, toIndex);
                }
            });
        },

        methods: {
            set_options: function set_options() {
                var self = this;
                var field_options = $.extend(true, {}, this.editing_form_field.options);

                _.each(field_options, function (label, value) {
                    self.options.push({ label: label, value: value, id: self.get_random_id() });
                });

                if (this.option_field.is_multiple && !_.isArray(this.field_selected)) {
                    this.selected = [this.field_selected];
                } else {
                    this.selected = this.field_selected;
                }
            },

            // in case of select or radio buttons, user should deselect default value
            clear_selection: function clear_selection() {
                this.selected = null;
            },

            add_option: function add_option() {
                var count = this.options.length,
                    new_opt = this.i18n.option + '-' + (count + 1);

                this.options.push({
                    label: new_opt, value: new_opt, id: this.get_random_id()
                });
            },

            delete_option: function delete_option(index) {
                if (this.options.length === 1) {
                    this.warn({
                        text: this.i18n.last_choice_warn_msg,
                        showCancelButton: false,
                        confirmButtonColor: "#46b450"
                    });

                    return;
                }

                this.options.splice(index, 1);
            },

            set_option_label: function set_option_label(index, label) {
                this.options[index].value = label.toLocaleLowerCase().replace(/\s/g, '_');
            }
        },

        watch: {
            options: {
                deep: true,
                handler: function handler(new_opts) {
                    var options = {},
                        i = 0;

                    for (i = 0; i < new_opts.length; i++) {
                        options[new_opts[i].value] = new_opts[i].label;
                    }

                    this.update_value('options', options);
                }
            },

            selected: function selected(new_val) {
                this.update_value('selected', new_val);
            }
        }
    });

    Vue.component('field-option-pro-feature-alert', {
        template: '#tmpl-wpuf-field-option-pro-feature-alert',

        mixins: [wpuf_mixins.option_field_mixin],

        computed: {
            pro_link: function pro_link() {
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

        data: function data() {
            return {
                show_basic_settings: true,
                show_advanced_settings: false,
                show_quiz_settings: false,
                show_humanpresence_settings: false
            };
        },

        computed: {
            editing_field_id: function editing_field_id() {
                this.show_basic_settings = true;
                this.show_advanced_settings = false;
                this.show_quiz_settings = false;
                this.show_humanpresence_settings = false;

                return parseInt(this.$store.state.editing_field_id);
            },

            editing_form_field: function editing_form_field() {
                var self = this,
                    i = 0;

                for (i = 0; i < self.$store.state.form_fields.length; i++) {
                    // check if the editing field exist in normal fields
                    if (self.$store.state.form_fields[i].id === parseInt(self.editing_field_id)) {
                        return self.$store.state.form_fields[i];
                    }

                    // check if the editing field belong to column field
                    if (self.$store.state.form_fields[i].template === 'column_field') {
                        var innerColumnFields = self.$store.state.form_fields[i].inner_fields;

                        for (var columnFields in innerColumnFields) {
                            if (innerColumnFields.hasOwnProperty(columnFields)) {
                                var columnFieldIndex = 0;

                                while (columnFieldIndex < innerColumnFields[columnFields].length) {
                                    if (innerColumnFields[columnFields][columnFieldIndex].id === self.editing_field_id) {
                                        return innerColumnFields[columnFields][columnFieldIndex];
                                    }
                                    columnFieldIndex++;
                                }
                            }
                        }
                    }
                }
            },

            settings: function settings() {
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

            basic_settings: function basic_settings() {
                return this.settings.filter(function (item) {
                    return 'basic' === item.section;
                });
            },

            advanced_settings: function advanced_settings() {
                return this.settings.filter(function (item) {
                    return 'advanced' === item.section;
                });
            },

            quiz_settings: function quiz_settings() {
                return this.settings.filter(function (item) {
                    return 'quiz' === item.section;
                });
            },

            humanpresence_settings: function humanpresence_settings() {
                return this.settings.filter(function (item) {
                    return 'humanpresence' === item.section;
                });
            },

            form_field_type_title: function form_field_type_title() {
                var template = this.editing_form_field.template;

                if (_.isFunction(this['form_field_' + template + '_title'])) {
                    return this['form_field_' + template + '_title'].call(this, this.editing_form_field);
                }

                return this.$store.state.field_settings[template].title;
            },

            form_settings: function form_settings() {
                return this.$store.state.settings;
            }
        },

        watch: {
            form_settings: function form_settings() {
                return this.$store.state.settings;
            }
        }
    });

    Vue.component('field-radio', {
        template: '#tmpl-wpuf-field-radio',

        mixins: [wpuf_mixins.option_field_mixin],

        computed: {
            value: {
                get: function get() {
                    return this.editing_form_field[this.option_field.name];
                },

                set: function set(value) {
                    this.$store.commit('update_editing_form_field', {
                        editing_field_id: this.editing_form_field.id,
                        field_name: this.option_field.name,
                        value: value
                    });
                }
            }
        }
    });

    Vue.component('field-range', {
        template: '#tmpl-wpuf-field-range',

        mixins: [wpuf_mixins.option_field_mixin],

        computed: {
            value: {
                get: function get() {
                    return this.editing_form_field[this.option_field.name];
                },

                set: function set(value) {
                    this.update_value(this.option_field.name, value);
                }
            },

            minColumn: function minColumn() {
                return this.editing_form_field.min_column;
            },

            maxColumn: function maxColumn() {
                return this.editing_form_field.max_column;
            }
        },

        methods: {}
    });

    Vue.component('field-select', {
        template: '#tmpl-wpuf-field-select',

        mixins: [wpuf_mixins.option_field_mixin],

        computed: {
            value: {
                get: function get() {
                    return this.editing_form_field[this.option_field.name];
                },

                set: function set(value) {
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

        mixins: [wpuf_mixins.option_field_mixin],

        computed: {
            value: {
                get: function get() {
                    return this.editing_form_field[this.option_field.name];
                },

                set: function set(value) {
                    this.update_value(this.option_field.name, value);
                }
            }
        },

        methods: {
            on_focusout: function on_focusout(e) {
                wpuf_form_builder.event_hub.$emit('field-text-focusout', e, this);
            },
            on_keyup: function on_keyup(e) {
                wpuf_form_builder.event_hub.$emit('field-text-keyup', e, this);
            }
        }
    });

    Vue.component('field-text-meta', {
        template: '#tmpl-wpuf-field-text-meta',

        mixins: [wpuf_mixins.option_field_mixin],

        computed: {
            value: {
                get: function get() {
                    return this.editing_form_field[this.option_field.name];
                },

                set: function set(value) {
                    this.update_value(this.option_field.name, value);
                }
            }
        },

        created: function created() {
            if ('yes' === this.editing_form_field.is_meta) {
                if (!this.value) {
                    this.value = this.editing_form_field.label.replace(/\W/g, '_').toLowerCase();
                }

                wpuf_form_builder.event_hub.$on('field-text-keyup', this.meta_key_autocomplete);
            }
        },

        methods: {
            meta_key_autocomplete: function meta_key_autocomplete(e, label_vm) {
                if ('label' === label_vm.option_field.name && parseInt(this.editing_form_field.id) === parseInt(label_vm.editing_form_field.id)) {
                    this.value = label_vm.value.replace(/\W/g, '_').toLowerCase();
                }
            }
        }
    });

    Vue.component('field-textarea', {
        template: '#tmpl-wpuf-field-textarea',

        mixins: [wpuf_mixins.option_field_mixin],

        computed: {
            value: {
                get: function get() {
                    return this.editing_form_field[this.option_field.name];
                },

                set: function set(value) {
                    this.update_value(this.option_field.name, value);
                }
            }
        }
    });

    Vue.component('field-visibility', {
        template: '#tmpl-wpuf-field-visibility',

        mixins: [wpuf_mixins.option_field_mixin],

        computed: {
            selected: {
                get: function get() {

                    return this.editing_form_field[this.option_field.name].selected;
                },

                set: function set(value) {

                    this.$store.commit('update_editing_form_field', {
                        editing_field_id: this.editing_form_field.id,
                        field_name: this.option_field.name,
                        value: {
                            selected: value,
                            choices: []
                        }
                    });
                }
            },

            choices: {
                get: function get() {
                    return this.editing_form_field[this.option_field.name].choices;
                },

                set: function set(value) {

                    this.$store.commit('update_editing_form_field', {
                        editing_field_id: this.editing_form_field.id,
                        field_name: this.option_field.name,
                        value: {
                            selected: this.selected,
                            choices: value
                        }
                    });
                }
            }

        },

        methods: {},

        watch: {
            selected: function selected(new_val) {
                this.update_value('selected', new_val);
            }
        }
    });
    /**
     * Field template: Checkbox
     */
    Vue.component('form-checkbox_field', {
        template: '#tmpl-wpuf-form-checkbox_field',

        mixins: [wpuf_mixins.form_field_mixin]
    });

    /**
     * Field template: Column Field
     */
    var mixins = [wpuf_mixins.form_field_mixin];

    if (window.wpuf_forms_mixin_builder_stage) {
        mixins.push(window.wpuf_forms_mixin_builder_stage);
    }

    if (window.weforms_mixin_builder_stage) {
        mixins.push(window.weforms_mixin_builder_stage);
    }

    Vue.component('form-column_field', {
        template: '#tmpl-wpuf-form-column_field',

        mixins: mixins,

        data: function data() {
            return {
                columnClasses: ['column-1', 'column-2', 'column-3'] // don't edit class names
            };
        },
        mounted: function mounted() {
            this.resizeColumns(this.field.columns);

            // bind jquery ui draggable
            var self = this,
                sortableFields = $(self.$el).find('.wpuf-column-inner-fields .wpuf-column-fields-sortable-list'),
                sortableTriggered = 1,
                columnFieldArea = $('.wpuf-field-columns'),
                columnFields = $(self.$el).find(".wpuf-column-field-inner-columns .wpuf-column-inner-fields");

            columnFieldArea.mouseenter(function () {
                self.resizeColumns(self.field.columns);
            });

            columnFieldArea.mouseleave(function () {
                columnFields.unbind("mouseup");
                columnFields.unbind("mousemove");
            });

            // bind jquery ui sortable
            $(sortableFields).sortable({
                placeholder: 'form-preview-stage-dropzone',
                connectWith: sortableFields,
                items: '.column-field-items',
                handle: '.wpuf-column-field-control-buttons .move',
                scroll: true,
                stop: function stop(event, ui) {
                    var data_source = $(ui.item).attr('data-source');

                    if ('panel' === data_source) {
                        var payload = {
                            toIndex: parseInt($(ui.item).index()),
                            field_template: $(ui.item).attr('data-form-field'),
                            to_column: $(this).parent()[0].classList[0]
                        };

                        self.add_column_inner_field(payload);

                        // remove button from stage
                        $(this).find('.button.ui-draggable.ui-draggable-handle').remove();
                    }
                },
                update: function update(e, ui) {
                    var item = ui.item[0],
                        data = item.dataset,
                        source = data.source,
                        toIndex = parseInt($(item).index()),
                        payload = {
                        toIndex: toIndex
                    };

                    if ('column-field-stage' === source) {
                        payload.field_id = self.field.id;
                        payload.fromIndex = parseInt($(item).attr('column-field-index'));
                        payload.fromColumn = $(item).attr('in-column');
                        payload.toColumn = $(item).parent().parent()[0].classList[0];

                        // when drag field one column to another column, sortable event trigger twice and try to swap field twice.
                        // So the following conditions are needed to check and run swap_column_field_elements commit only once
                        if (payload.fromColumn !== payload.toColumn && sortableTriggered === 1) {
                            sortableTriggered = 0;
                        } else {
                            sortableTriggered++;
                        }

                        if (payload.fromColumn === payload.toColumn) {
                            sortableTriggered = 1;
                        }

                        if (sortableTriggered === 1) {
                            self.$store.commit('swap_column_field_elements', payload);
                        }
                    }
                }
            });
        },

        computed: {
            column_fields: function column_fields() {
                return this.field.inner_fields;
            },

            innerColumns: function innerColumns() {
                return this.field.columns;
            },

            editing_form_id: function editing_form_id() {
                return this.$store.state.editing_field_id;
            },

            field_settings: function field_settings() {
                return this.$store.state.field_settings;
            }
        },

        methods: {
            is_template_available: function is_template_available(field) {
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

            is_pro_feature: function is_pro_feature(template) {
                return this.field_settings[template] && this.field_settings[template].pro_feature ? true : false;
            },

            get_field_name: function get_field_name(template) {
                return this.field_settings[template].title;
            },

            is_full_width: function is_full_width(template) {
                if (this.field_settings[template] && this.field_settings[template].is_full_width) {
                    return true;
                }

                return false;
            },

            is_invisible: function is_invisible(field) {
                return field.recaptcha_type && 'invisible_recaptcha' === field.recaptcha_type ? true : false;
            },

            isAllowedInClolumnField: function isAllowedInClolumnField(field_template) {
                var restrictedFields = ['column_field', 'custom_hidden_field', 'step_start'];

                if ($.inArray(field_template, restrictedFields) >= 0) {
                    return true;
                }

                return false;
            },

            add_column_inner_field: function add_column_inner_field(data) {
                var payload = {
                    toWhichColumnField: this.field.id,
                    toWhichColumnFieldMeta: this.field.name,
                    toIndex: data.toIndex,
                    toWhichColumn: data.to_column
                };

                if (this.isAllowedInClolumnField(data.field_template)) {
                    swal({
                        title: "Oops...",
                        text: "You cannot add this field as inner column field"
                    });
                    return;
                }

                // check if these are already inserted
                if (this.isSingleInstance(data.field_template) && this.containsField(data.field_template)) {
                    swal({
                        title: "Oops...",
                        text: "You already have this field in the form"
                    });
                    return;
                }

                var field = $.extend(true, {}, this.$store.state.field_settings[data.field_template].field_props),
                    form_fields = this.$store.state.form_fields;

                field.id = this.get_random_id();

                if ('yes' === field.is_meta && !field.name && field.label) {
                    field.name = field.label.replace(/\W/g, '_').toLowerCase();

                    var same_template_fields = form_fields.filter(function (form_field) {
                        return form_field.template === field.template;
                    });

                    if (same_template_fields) {
                        field.name += '_' + this.get_random_id();
                    }
                }

                payload.field = field;

                // add new form element
                this.$store.commit('add_column_inner_field_element', payload);
            },
            moveFieldsTo: function moveFieldsTo(column) {
                var payload = {
                    field_id: this.field.id,
                    move_to: column,
                    inner_fields: this.getInnerFields()
                };

                // clear inner fields & push mergedFields to column-1
                this.$store.commit('move_column_inner_fields', payload);
            },
            getInnerFields: function getInnerFields() {
                return this.field.inner_fields;
            },

            open_column_field_settings: function open_column_field_settings(field, index, column) {
                var self = this,
                    payload = {
                    field_id: self.field.id,
                    column_field: field,
                    index: index,
                    column: column
                };
                self.$store.commit('open_column_field_settings', payload);
            },

            clone_column_field: function clone_column_field(field, index, column) {
                var self = this,
                    payload = {
                    field_id: self.field.id,
                    column_field_id: field.id,
                    index: index,
                    toColumn: column,
                    new_id: self.get_random_id()
                };

                // check if the field is allowed to duplicate
                if (self.isSingleInstance(field.template)) {
                    swal({
                        title: "Oops...",
                        text: "You already have this field in the form"
                    });
                    return;
                }

                self.$store.commit('clone_column_field_element', payload);
            },

            delete_column_field: function delete_column_field(index, fromColumn) {
                var self = this,
                    payload = {
                    field_id: self.field.id,
                    index: index,
                    fromColumn: fromColumn
                };

                swal({
                    text: self.i18n.delete_field_warn_msg,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d54e21',
                    confirmButtonText: self.i18n.yes_delete_it,
                    cancelButtonText: self.i18n.no_cancel_it,
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger'
                }).then(function () {
                    self.$store.commit('delete_column_field_element', payload);
                }, function () {});
            },

            resizeColumns: function resizeColumns(columnsNumber) {
                var self = this;
                (function () {
                    var columnElement;
                    var startOffset;
                    var columnField = $(self.$el).parent();
                    var total_width = parseInt($(columnField).width());

                    Array.prototype.forEach.call($(self.$el).find(".wpuf-column-field-inner-columns .wpuf-column-inner-fields"), function (column) {
                        column.style.position = 'relative';

                        var grip = document.createElement('div');
                        grip.innerHTML = "&nbsp;";
                        grip.style.top = 0;
                        grip.style.right = 0;
                        grip.style.bottom = 0;
                        grip.style.width = '5px';
                        grip.style.position = 'absolute';
                        grip.style.cursor = 'col-resize';
                        grip.addEventListener('mousedown', function (e) {
                            columnElement = column;
                            startOffset = column.offsetWidth - e.pageX;
                        });

                        column.appendChild(grip);
                    });

                    $(self.$el).find(".wpuf-column-field-inner-columns .wpuf-column-inner-fields").mousemove(function (e) {
                        if (columnElement) {
                            var currentColumnWidth = startOffset + e.pageX;

                            columnElement.style.width = 100 * currentColumnWidth / total_width + '%';
                        }
                    });

                    $(self.$el).find(".wpuf-column-field-inner-columns .wpuf-column-inner-fields").mouseup(function () {
                        var colOneWidth = 0,
                            colTwoWidth = 0,
                            colThreeWidth = 0;

                        if (columnsNumber === 3) {
                            colOneWidth = 100 / columnsNumber;
                            colTwoWidth = 100 / columnsNumber;
                            colThreeWidth = 100 / columnsNumber;
                        } else if (columnsNumber === 2) {
                            colOneWidth = 100 / columnsNumber;
                            colTwoWidth = 100 / columnsNumber;
                            colThreeWidth = 0;
                        } else {
                            colOneWidth = $(columnField).find(".column-1").width();
                            colTwoWidth = $(columnField).find(".column-2").width();
                            colThreeWidth = 0;
                        }

                        self.field.inner_columns_size['column-1'] = colOneWidth + '%';
                        self.field.inner_columns_size['column-2'] = colTwoWidth + '%';
                        self.field.inner_columns_size['column-3'] = colThreeWidth + '%';

                        columnElement = undefined;
                    });
                })();
            }
        },

        watch: {
            innerColumns: function innerColumns(new_value) {
                var columns = parseInt(new_value),
                    columns_size = this.field.inner_columns_size;

                Object.keys(columns_size).forEach(function (column) {
                    if (columns === 1) {
                        columns_size[column] = '100%';
                    }

                    if (columns === 2) {
                        columns_size[column] = '50%';
                    }

                    if (columns === 3) {
                        columns_size[column] = '33.33%';
                    }
                });

                // if columns number reduce to 1 then move other column fields to the first column
                if (columns === 1) {
                    this.moveFieldsTo("column-1");
                }

                // if columns number reduce to 2 then move column-2 and column-3 fields to the column-2
                if (columns === 2) {
                    this.moveFieldsTo("column-2");
                }

                this.resizeColumns(columns);
            }
        }
    });

    /**
     * Field template: Hidden
     */
    Vue.component('form-custom_hidden_field', {
        template: '#tmpl-wpuf-form-custom_hidden_field',

        mixins: [wpuf_mixins.form_field_mixin]
    });

    /**
     * Field template: Custom HTML
     */
    Vue.component('form-custom_html', {
        template: '#tmpl-wpuf-form-custom_html',

        mixins: [wpuf_mixins.form_field_mixin],

        data: function data() {
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

        mixins: [wpuf_mixins.form_field_mixin]
    });

    /**
     * Field template: Email
     */
    Vue.component('form-email_address', {
        template: '#tmpl-wpuf-form-email_address',

        mixins: [wpuf_mixins.form_field_mixin]
    });

    /**
     * Field template: Featured Image
     */
    Vue.component('form-featured_image', {
        template: '#tmpl-wpuf-form-featured_image',

        mixins: [wpuf_mixins.form_field_mixin]
    });

    /**
     * Sidebar form fields panel
     */
    Vue.component('form-fields', {
        template: '#tmpl-wpuf-form-fields',

        mixins: wpuf_form_builder_mixins(wpuf_mixins.form_fields),

        computed: {
            panel_sections: function panel_sections() {
                return this.$store.state.panel_sections;
            },

            field_settings: function field_settings() {
                return this.$store.state.field_settings;
            },

            form_fields: function form_fields() {
                return this.$store.state.form_fields;
            }
        },

        created: function created() {
            wpuf_form_builder.event_hub.$on('humanpresence-enabled', this.add_humanpresence_field);
        },

        mounted: function mounted() {
            // bind jquery ui draggable
            $(this.$el).find('.panel-form-field-buttons .button').not('[data-form-field="humanpresence"]').draggable({
                connectToSortable: '#form-preview-stage .wpuf-form, .wpuf-column-inner-fields .wpuf-column-fields-sortable-list',
                helper: 'clone',
                revert: 'invalid',
                cancel: '.button-faded'
            }).disableSelection();
        },

        methods: {
            panel_toggle: function panel_toggle(index) {
                this.$store.commit('panel_toggle', index);
            },

            add_form_field: function add_form_field(field_template) {
                var payload = {
                    toIndex: this.$store.state.form_fields.length
                };

                // check if these are already inserted
                if (this.isSingleInstance(field_template) && this.containsField(field_template)) {
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
                        return form_field.template === field.template;
                    });

                    if (same_template_fields.length) {
                        field.name += '_' + same_template_fields.length;
                    }
                }

                if (field_template === 'humanpresence') {
                    var settings = this.$store.state.settings;
                    settings.humanpresence_enabled = true;
                    this.$store.commit('set_form_settings', settings);
                }

                payload.field = field;

                // add new form element
                this.$store.commit('add_form_field_element', payload);
            },

            add_humanpresence_field: function add_humanpresence_field(data) {
                if (!this.containsField('humanpresence')) {
                    this.add_form_field('humanpresence');
                }
            },

            is_pro_feature: function is_pro_feature(field) {
                return this.field_settings[field].pro_feature;
            },

            alert_pro_feature: function alert_pro_feature(field) {
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
                }, function () {});
            },

            alert_invalidate_msg: function alert_invalidate_msg(field) {
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

            get_invalidate_btn_class: function get_invalidate_btn_class(field) {
                return this.field_settings[field].validator.button_class;
            }
        }
    });

    /**
     * Field template: Image Upload
     */
    Vue.component('form-image_upload', {
        template: '#tmpl-wpuf-form-image_upload',

        mixins: [wpuf_mixins.form_field_mixin]
    });

    /**
     * Field template: Multi-Select
     */
    Vue.component('form-multiple_select', {
        template: '#tmpl-wpuf-form-multiple_select',

        mixins: [wpuf_mixins.form_field_mixin]
    });

    /**
     * Field Template: Post Content
     */
    Vue.component('form-post_content', {
        template: '#tmpl-wpuf-form-post_content',

        mixins: [wpuf_mixins.form_field_mixin]
    });

    /**
     * Field Template: Post Excerpt
     */
    Vue.component('form-post_excerpt', {
        template: '#tmpl-wpuf-form-post_excerpt',

        mixins: [wpuf_mixins.form_field_mixin]
    });

    /**
     * Field template: post_tags
     */
    Vue.component('form-post_tags', {
        template: '#tmpl-wpuf-form-post_tags',

        mixins: [wpuf_mixins.form_field_mixin]
    });

    /**
     * Field template: Post Title
     */
    Vue.component('form-post_title', {
        template: '#tmpl-wpuf-form-post_title',

        mixins: [wpuf_mixins.form_field_mixin]
    });

    /**
     * Field template: Radio
     */
    Vue.component('form-radio_field', {
        template: '#tmpl-wpuf-form-radio_field',

        mixins: [wpuf_mixins.form_field_mixin]
    });

    /**
     * Field template: Recaptcha
     */
    Vue.component('form-recaptcha', {
        template: '#tmpl-wpuf-form-recaptcha',

        mixins: [wpuf_mixins.form_field_mixin],

        computed: {
            has_recaptcha_api_keys: function has_recaptcha_api_keys() {
                return wpuf_form_builder.recaptcha_site && wpuf_form_builder.recaptcha_secret ? true : false;
            },

            no_api_keys_msg: function no_api_keys_msg() {
                return wpuf_form_builder.field_settings.recaptcha.validator.msg;
            }
        }
    });

    /**
     * Field template: HumanPresence
     */
    Vue.component('form-humanpresence', {
        template: '#tmpl-wpuf-form-humanpresence',

        mixins: [wpuf_mixins.form_field_mixin],

        computed: {
            has_humanpresence_installed: function has_humanpresence_installed() {
                return wpuf_form_builder.humanpresence_installed;
            },

            no_humanpresence_installed_msg: function no_humanpresence_installed_msg() {
                return wpuf_form_builder.field_settings.humanpresence.validator.msg;
            }
        }

    });

    /**
     * Field template: Section Break
     */
    Vue.component('form-section_break', {
        template: '#tmpl-wpuf-form-section_break',

        mixins: [wpuf_mixins.form_field_mixin]
    });

    /**
     * Field template: taxonomy
     */
    Vue.component('form-taxonomy', {
        template: '#tmpl-wpuf-form-taxonomy',

        mixins: [wpuf_mixins.form_field_mixin],

        computed: {
            terms: function terms() {
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

            sorted_terms: function sorted_terms() {
                var self = this;
                var terms = $.extend(true, [], this.terms);

                // selection type and terms
                if (this.field.exclude_type && this.field.exclude) {

                    if (this.field.exclude.length > 1) {
                        var filter_ids = this.field.exclude.split(',').map(function (id) {
                            id = id.trim();
                            id = parseInt(id);
                            return id;
                        }).filter(function (id) {
                            return isFinite(id);
                        });
                    }

                    terms = terms.filter(function (term) {

                        switch (self.field.exclude_type) {
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
            get_child_terms: function get_child_terms(parent_id, terms) {
                var self = this;

                var child_terms = terms.filter(function (term) {
                    return parseInt(term.parent) === parseInt(parent_id);
                });

                child_terms.map(function (child) {
                    child.children = self.get_child_terms(child.term_id, terms);
                });

                return child_terms;
            },

            get_term_dropdown_options: function get_term_dropdown_options() {
                var self = this,
                    options = '';

                if (this.field.type === 'select') {
                    options = '<option value="">' + this.field.first + '</option>';
                }

                _.each(self.sorted_terms, function (term) {
                    options += self.get_term_dropdown_options_children(term, 0);
                });

                return options;
            },

            get_term_dropdown_options_children: function get_term_dropdown_options_children(term, level) {
                var self = this,
                    option = '';

                var indent = '',
                    i = 0;

                for (i = 0; i < level; i++) {
                    indent += '&nbsp;&nbsp;';
                }

                option += '<option value="' + term.id + '">' + indent + term.name + '</option>';

                if (term.children.length) {
                    _.each(term.children, function (child_term) {
                        option += self.get_term_dropdown_options_children(child_term, level + 1);
                    });
                }

                return option;
            },

            get_term_checklist: function get_term_checklist() {
                var self = this,
                    checklist = '';

                checklist += '<ul class="wpuf-category-checklist">';

                _.each(this.sorted_terms, function (term) {
                    checklist += self.get_term_checklist_li(term);
                });

                checklist += '</ul>';

                return checklist;
            },

            get_term_checklist_li: function get_term_checklist_li(term) {
                var self = this,
                    li = '';

                li += '<li><label class="selectit"><input type="checkbox"> ' + term.name + '</label></li>';

                if (term.children.length) {
                    li += '<ul class="children">';

                    _.each(term.children, function (child_term) {
                        li += self.get_term_checklist_li(child_term);
                    });

                    li += '</ul>';
                }

                return li;
            },

            get_term_checklist_inline: function get_term_checklist_inline() {
                var self = this,
                    checklist = '';

                _.each(this.sorted_terms, function (term) {
                    checklist += self.get_term_checklist_li_inline(term);
                });

                return checklist;
            },

            get_term_checklist_li_inline: function get_term_checklist_li_inline(term) {
                var self = this,
                    li_inline = '';

                li_inline += '<label class="wpuf-checkbox-inline"><input type="checkbox"> ' + term.name + '</label>';

                if (term.children.length) {
                    _.each(term.children, function (child_term) {
                        li_inline += self.get_term_checklist_li_inline(child_term);
                    });
                }

                return li_inline;
            }
        }
    });

    /**
     * Field template: Text
     */
    Vue.component('form-text_field', {
        template: '#tmpl-wpuf-form-text_field',

        mixins: [wpuf_mixins.form_field_mixin]
    });

    Vue.component('form-textarea_field', {
        template: '#tmpl-wpuf-form-textarea_field',

        mixins: [wpuf_mixins.form_field_mixin]
    });

    /**
     * Field template: Website URL
     */
    Vue.component('form-website_url', {
        template: '#tmpl-wpuf-form-website_url',

        mixins: [wpuf_mixins.form_field_mixin]
    });

    Vue.component('help-text', {
        template: '#tmpl-wpuf-help-text',

        props: {
            text: {
                type: String,
                default: ''
            }
        },

        mounted: function mounted() {
            $(".wpuf-tooltip").tooltip();
        }
    });

    Vue.component('text-editor', {
        template: '#tmpl-wpuf-text-editor',

        props: ['rich', 'default_text'],

        computed: {
            site_url: function site_url() {
                return wpuf_form_builder.site_url;
            },

            is_full: function is_full() {
                return 'yes' === this.rich;
            }
        }
    });
})(jQuery);
