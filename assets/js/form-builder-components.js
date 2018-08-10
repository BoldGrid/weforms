'use strict';

/* assets/js/utils/jquery-siaf-start.js */

;(function ($) {
    'use strict';

    /* ./assets/components/dynamic-field/index.js */

    Vue.component('field-dynamic-field', {
        mixins: [wpuf_mixins.option_field_mixin],
        template: '#tmpl-wpuf-dynamic-field',
        data: function data() {
            return {
                dynamic: {
                    status: false,
                    param_name: ''
                }
            };
        },
        computed: {
            dynamic: function dynamic() {
                return this.editing_form_field.dynamic;
            },
            editing_field: function editing_field() {
                return this.editing_form_field;
            }
        },

        created: function created() {
            this.dynamic = $.extend(false, this.dynamic, this.editing_form_field.dynamic);
        },

        methods: {},

        watch: {
            dynamic: function dynamic() {
                this.update_value('dynamic', this.dynamic);
            }
        }
    });

    /* ./assets/components/field-name/index.js */
    Vue.component('field-name', {
        template: '#tmpl-wpuf-field-name',

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
            },
            insertValue: function insertValue(type, field, property) {
                var value = field !== undefined ? '{' + type + ':' + field + '}' : '{' + type + '}';
                this.editing_form_field[property.name][property.type] = value;
            }
        }
    });

    /* ./assets/components/field-text-with-tag/index.js */
    Vue.component('field-text-with-tag', {
        template: '#tmpl-wpuf-field-text-with-tag',

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
            },
            insertValue: function insertValue(type, field, property) {
                var value = field !== undefined ? '{' + type + ':' + field + '}' : '{' + type + '}';
                this.value = value;
            }
        }
    });

    /* ./assets/components/form-date_field/index.js */
    /**
     * Field template: Date
     */
    Vue.component('form-date_field', {
        template: '#tmpl-wpuf-form-date_field',

        mixins: [wpuf_mixins.form_field_mixin]
    });

    /* ./assets/components/form-name_field/index.js */
    /**
     * Field template: First Name
     */
    Vue.component('form-name_field', {
        template: '#tmpl-wpuf-form-name_field',

        mixins: [wpuf_mixins.form_field_mixin]
    });

    /* ./assets/components/form-notification/index.js */
    Vue.component('wpuf-cf-form-notification', {
        template: '#tmpl-wpuf-form-notification',
        mixins: [weForms.mixins.Loading],
        data: function data() {
            return {
                editing: false,
                editingIndex: 0
            };
        },

        computed: {
            is_pro: function is_pro() {
                return 'true' === weForms.is_pro;
            },
            has_sms: function has_sms() {
                return 'true' === weForms.has_sms;
            },
            pro_link: function pro_link() {
                return wpuf_form_builder.pro_link;
            },
            notifications: function notifications() {
                return this.$store.state.notifications;
            },

            hasNotifications: function hasNotifications() {
                return Object.keys(this.$store.state.notifications).length;
            }
        },

        methods: {
            addNew: function addNew() {
                this.$store.commit('addNotification', wpuf_form_builder.defaultNotification);
            },

            editItem: function editItem(index) {
                this.editing = true;
                this.editingIndex = index;
            },

            editDone: function editDone() {
                this.editing = false;

                this.$store.commit('updateNotification', {
                    index: this.editingIndex,
                    value: this.notifications[this.editingIndex]
                });

                jQuery('.advanced-field-wrap').slideUp('fast');
            },

            deleteItem: function deleteItem(index) {
                if (confirm('Are you sure')) {
                    this.editing = false;
                    this.$store.commit('deleteNotification', index);
                    this.$emit('deleteNotification', index);
                }
            },

            toggelNotification: function toggelNotification(index) {
                this.$store.commit('updateNotificationProperty', {
                    index: index,
                    property: 'active',
                    value: !this.notifications[index].active
                });
            },

            duplicate: function duplicate(index) {
                this.$store.commit('cloneNotification', index);
            },

            toggleAdvanced: function toggleAdvanced() {
                jQuery('.advanced-field-wrap').slideToggle('fast');
            },

            insertValue: function insertValue(type, field, property) {
                var notification = this.notifications[this.editingIndex],
                    value = field !== undefined ? '{' + type + ':' + field + '}' : '{' + type + '}';

                notification[property] = notification[property] + value;
            },

            insertValueEditor: function insertValueEditor(type, field, property) {
                var value = field !== undefined ? '{' + type + ':' + field + '}' : '{' + type + '}';
                this.$emit('insertValueEditor', value);
            }
        }
    });

    /* ./assets/components/integration/index.js */
    Vue.component('wpuf-integration', {
        template: '#tmpl-wpuf-integration',

        computed: {

            integrations: function integrations() {
                return wpuf_form_builder.integrations;
            },

            hasIntegrations: function hasIntegrations() {
                return Object.keys(this.integrations).length;
            },

            store: function store() {
                return this.$store.state.integrations;
            },

            pro_link: function pro_link() {
                return wpuf_form_builder.pro_link;
            }
        },

        methods: {

            getIntegration: function getIntegration(id) {
                return this.integrations[id];
            },

            getIntegrationSettings: function getIntegrationSettings(id) {
                // find settings in store, otherwise take from default integration settings
                return this.store[id] || this.getIntegration(id).settings;
            },

            isActive: function isActive(id) {
                if (!this.isAvailable(id)) {
                    return false;
                }

                return this.getIntegrationSettings(id).enabled === true;
            },

            isAvailable: function isAvailable(id) {
                return this.integrations[id] && this.integrations[id].pro ? false : true;
            },

            toggleState: function toggleState(id, target) {
                if (!this.isAvailable(id)) {
                    this.alert_pro_feature(id);
                    return;
                }

                // toggle the enabled state
                var settings = this.getIntegrationSettings(id);

                settings.enabled = !this.isActive(id);

                this.$store.commit('updateIntegration', {
                    index: id,
                    value: settings
                });

                $(target).toggleClass('checked');
            },

            alert_pro_feature: function alert_pro_feature(id) {
                var title = this.getIntegration(id).title;

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

            showHide: function showHide(target) {
                $(target).closest('.wpuf-integration').toggleClass('collapsed');
            }
        }
    });

    /* ./assets/components/integration-erp/index.js */
    Vue.component('wpuf-integration-erp', {
        template: '#tmpl-wpuf-integration-erp',
        mixins: [wpuf_mixins.integration_mixin],

        methods: {
            insertValue: function insertValue(type, field, property) {
                var value = field !== undefined ? '{' + type + ':' + field + '}' : '{' + type + '}';

                this.settings.fields[property] = value;
            }
        }
    });
    /* ./assets/components/integration-slack/index.js */
    Vue.component('wpuf-integration-slack', {
        template: '#tmpl-wpuf-integration-slack',
        mixins: [wpuf_mixins.integration_mixin]
    });
    /* ./assets/components/merge-tags/index.js */
    Vue.component('wpuf-merge-tags', {
        template: '#tmpl-wpuf-merge-tags',
        props: {
            field: [String, Number, Object],
            filter: {
                type: String,
                default: null
            },
            fieldsonly: {
                type: Boolean,
                default: false
            }
        },

        data: function data() {
            return {
                type: null
            };
        },

        mounted: function mounted() {

            // hide if clicked outside
            $('body').on('click', function (event) {
                if (!$(event.target).closest('.wpuf-merge-tag-wrap').length) {
                    $(".wpuf-merge-tags").hide();
                }
            });
        },

        computed: {
            form_fields: function form_fields() {
                var template = this.filter,
                    fields = this.$store.state.form_fields;

                if (template !== null) {
                    return fields.filter(function (item) {
                        return item.template === template;
                    });
                }

                // remove the action/hidden fields
                return fields.filter(function (item) {
                    return !_.contains(['action_hook', 'custom_hidden_field'], item.template);
                });
            }

        },

        methods: {
            toggleFields: function toggleFields(event) {
                $(event.target).parent().siblings('.wpuf-merge-tags').toggle('fast');
            },

            insertField: function insertField(type, field) {
                this.$emit('insert', type, field, this.field);
            }
        }
    });

    /* ./assets/components/modal/index.js */
    Vue.component('wpuf-modal', {
        template: '#tmpl-wpuf-modal',
        props: {
            show: Boolean,
            onClose: Function
        },

        mounted: function mounted() {
            var self = this;

            $('body').on('keydown', function (e) {
                if (self.show && e.keyCode === 27) {
                    self.closeModal();
                }
            });
        },

        methods: {
            closeModal: function closeModal() {
                if (typeof this.onClose !== 'undefined') {
                    this.onClose();
                } else {
                    this.$emit('hideModal');
                }
            }
        }
    });
    /* ./assets/components/template-modal/index.js */
    Vue.component('wpuf-template-modal', {
        template: '#tmpl-wpuf-template-modal',

        props: {
            show: Boolean,
            onClose: Function
        },

        data: function data() {
            return {
                loading: false,
                category: 'all'
            };
        },

        methods: {

            blankForm: function blankForm(target) {
                this.createForm('blank', target);
            },

            createForm: function createForm(form, target) {
                var self = this;

                // already on a request?
                if (self.loading) {
                    return;
                }

                self.loading = true;

                $(target).addClass('updating-message');

                wp.ajax.send('weforms_contact_form_template', {
                    data: {
                        template: form,
                        _wpnonce: weForms.nonce
                    },

                    success: function success(response) {
                        self.$router.push({
                            name: 'edit',
                            params: { id: response.id }
                        });
                    },

                    error: function error(_error) {},

                    complete: function complete() {
                        self.loading = false;

                        $(target).removeClass('updating-message');
                    }
                });
            }
        }
    });

    /* ./assets/components/weforms-text-editor/index.js */
    Vue.component('weforms-text-editor', {
        template: '#tmpl-wpuf-weforms-text-editor',

        props: {
            value: {
                type: String,
                required: true
            },

            i18n: {
                type: Object,
                required: true
            },

            editingIndex: {
                type: Number,
                required: true
            }
        },

        data: function data() {
            return {
                editorId: _.clone(Date.now()),
                fileFrame: null,
                shortcodes: weForms.shortcodes
            };
        },
        mounted: function mounted() {
            var vm = this;
            this.setupEditor();

            this.$parent.$on('deleteNotification', function () {
                setTimeout(function () {
                    if (vm.editor) {
                        vm.editor.setContent(vm.value);
                    }
                }, 500);
            });
        },
        beforeDestroy: function beforeDestroy() {
            this.$parent.$off('insertValueEditor');
            this.$parent.$off('deleteNotification');
        },

        methods: {
            setupEditor: function setupEditor() {
                var vm = this;
                window.tinymce.init({
                    selector: '#wefroms-tinymce-' + this.editorId,
                    branding: false,
                    height: 150,
                    menubar: false,
                    convert_urls: false,
                    theme: 'modern',
                    skin: 'lightgray',
                    content_css: weForms.assetsURL + '/css/customizer.css',
                    fontsize_formats: '10px 11px 13px 14px 16px 18px 22px 25px 30px 36px 40px 45px 50px 60px 65px 70px 75px 80px',
                    font_formats: 'Arial=arial,helvetica,sans-serif;' + 'Comic Sans MS=comic sans ms,sans-serif;' + 'Courier New=courier new,courier;' + 'Georgia=georgia,palatino;' + 'Lucida=Lucida Sans Unicode, Lucida Grande, sans-serif;' + 'Tahoma=tahoma,arial,helvetica,sans-serif;' + 'Times New Roman=times new roman,times;' + 'Trebuchet MS=trebuchet ms,geneva;' + 'Verdana=verdana,geneva;',
                    plugins: 'textcolor colorpicker wplink wordpress code hr wpeditimage',
                    toolbar: ['shortcodes bold italic underline bullist numlist alignleft aligncenter alignjustify alignright link image', 'formatselect forecolor backcolor blockquote hr code', 'fontselect fontsizeselect removeformat undo redo'],
                    setup: function setup(editor) {

                        vm.editor = editor;

                        var shortcodeMenuItems = [];

                        _.forEach(vm.shortcodes, function (shortcodeObj, shortcodeType) {
                            shortcodeMenuItems.push({
                                text: shortcodeObj.title,
                                classes: 'menu-section-title'
                            });

                            _.forEach(shortcodeObj.codes, function (codeObj, shortcode) {
                                shortcodeMenuItems.push({
                                    text: codeObj.title,
                                    onclick: function onclick() {
                                        var code = '[' + shortcodeType + ':' + shortcode + ']';

                                        if (codeObj.default) {
                                            code = '[' + shortcodeType + ':' + shortcode + ' default="' + codeObj.default + '"]';
                                        }

                                        if (codeObj.text) {
                                            code = '[' + shortcodeType + ':' + shortcode + ' text="' + codeObj.text + '"]';
                                        }

                                        if (codeObj.plainText) {
                                            code = codeObj.text;
                                        }

                                        editor.insertContent(code);
                                    }
                                });
                            });
                        });

                        // editor.addButton('shortcodes', {
                        //     type: 'menubutton',
                        //     icon: 'shortcode dashicons dashicons-editor-code',
                        //     tooltip: 'Shortcodes',
                        //     menu: shortcodeMenuItems
                        // });

                        // editor.addButton('image', {
                        //     icon: 'image',
                        //     onclick() {
                        //         vm.browseImage(editor);
                        //     }
                        // });

                        // editor change triggers
                        editor.on('change keyup NodeChange', function () {
                            vm.$emit('input', editor.getContent());
                        });

                        vm.$parent.$on('insertValueEditor', function (value) {
                            editor.insertContent(value);
                        });
                    }
                });
            },
            browseImage: function browseImage(editor) {
                var vm = this;
                var selectedFile = {
                    id: 0,
                    url: '',
                    type: ''
                };

                if (vm.fileFrame) {
                    vm.fileFrame.open();
                    return;
                }

                var fileStates = [new wp.media.controller.Library({
                    library: wp.media.query(),
                    multiple: false,
                    title: vm.i18n.selectAnImage,
                    priority: 20,
                    filterable: 'uploaded'
                })];

                vm.fileFrame = wp.media({
                    title: vm.i18n.selectAnImage,
                    library: {
                        type: ''
                    },
                    button: {
                        text: vm.i18n.selectAnImage
                    },
                    multiple: false,
                    states: fileStates
                });

                vm.fileFrame.on('select', function () {
                    var selection = vm.fileFrame.state().get('selection');

                    selection.map(function (image) {
                        image = image.toJSON();

                        if (image.id) {
                            selectedFile.id = image.id;
                        }

                        if (image.url) {
                            selectedFile.url = image.url;
                        }

                        if (image.type) {
                            selectedFile.type = image.type;
                        }

                        vm.insertImage(editor, selectedFile);

                        return null;
                    });
                });

                vm.fileFrame.on('ready', function () {
                    vm.fileFrame.uploader.options.uploader.params = {
                        type: 'wefroms-image-uploader'
                    };
                });

                vm.fileFrame.open();
            },
            insertImage: function insertImage(editor, image) {
                if (!image.id || image.type !== 'image') {
                    this.alert({
                        type: 'error',
                        text: this.i18n.pleaseSelectAnImage
                    });

                    return;
                }

                var img = '<img src="' + image.url + '" alt="' + image.alt + '" title="' + image.title + '" style="max-width: 100%; height: auto;">';

                editor.insertContent(img);
            }
        },

        watch: {
            editingIndex: function editingIndex(new_val, old_val) {
                if (!this.editor) {
                    this.setupEditor();
                } else {
                    this.editor.setContent(this.value);
                }
            }
        }
    });

    /* assets/js/utils/jquery-siaf-end.js */
})(jQuery);
