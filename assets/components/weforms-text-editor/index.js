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

        editorId: {
            type: Number,
            required: true
        }
    },

    data() {
        return {
            fileFrame: null,
            shortcodes: weForms.shortcodes,
        };
    },

    mounted() {
        const vm = this;

        window.tinymce.init({
            selector: `#wefroms-tinymce-${this.editorId}`,
            branding: false,
            height: 300,
            menubar: false,
            convert_urls: false,
            theme: 'modern',
            skin: 'lightgray',
            content_css: `${weForms.assetsURL}/css/customizer.css`,
            fontsize_formats: '10px 11px 13px 14px 16px 18px 22px 25px 30px 36px 40px 45px 50px 60px 65px 70px 75px 80px',
            font_formats : 'Arial=arial,helvetica,sans-serif;' +
                'Comic Sans MS=comic sans ms,sans-serif;' +
                'Courier New=courier new,courier;' +
                'Georgia=georgia,palatino;' +
                'Lucida=Lucida Sans Unicode, Lucida Grande, sans-serif;' +
                'Tahoma=tahoma,arial,helvetica,sans-serif;' +
                'Times New Roman=times new roman,times;' +
                'Trebuchet MS=trebuchet ms,geneva;' +
                'Verdana=verdana,geneva;',
            plugins: 'textcolor colorpicker wplink wordpress code hr wpeditimage',
            toolbar: [
                'shortcodes bold italic underline bullist numlist alignleft aligncenter alignjustify alignright link image',
                'formatselect forecolor backcolor blockquote hr code',
                'fontselect fontsizeselect removeformat undo redo'
            ],
            setup(editor) {

                vm.editor = editor;

                const shortcodeMenuItems = [];

                _.forEach(vm.shortcodes, (shortcodeObj, shortcodeType) => {
                    shortcodeMenuItems.push({
                        text: shortcodeObj.title,
                        classes: 'menu-section-title'
                    });

                    _.forEach(shortcodeObj.codes, (codeObj, shortcode) => {
                        shortcodeMenuItems.push({
                            text: codeObj.title,
                            onclick() {
                                let code = `[${shortcodeType}:${shortcode}]`;

                                if (codeObj.default) {
                                    code = `[${shortcodeType}:${shortcode} default="${codeObj.default}"]`;
                                }

                                if (codeObj.text) {
                                    code = `[${shortcodeType}:${shortcode} text="${codeObj.text}"]`;
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

                editor.addButton('image', {
                    icon: 'image',
                    onclick() {
                        vm.browseImage(editor);
                    }
                });

                // editor change triggers
                editor.on('change keyup NodeChange', () => {
                    vm.$emit('input', editor.getContent());
                });

                vm.$parent.$on('insertValueEditor', (value) => {
                   editor.insertContent(value);
                });
            }
        });
    },

    methods: {
        browseImage(editor) {
            const vm = this;
            const selectedFile = {
                id: 0,
                url: '',
                type: ''
            };

            if (vm.fileFrame) {
                vm.fileFrame.open();
                return;
            }

            const fileStates = [
                new wp.media.controller.Library({
                    library: wp.media.query(),
                    multiple: false,
                    title: vm.i18n.selectAnImage,
                    priority: 20,
                    filterable: 'uploaded'
                })
            ];

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

            vm.fileFrame.on('select', () => {
                const selection = vm.fileFrame.state().get('selection');

                selection.map((image) => {
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

            vm.fileFrame.on('ready', () => {
                vm.fileFrame.uploader.options.uploader.params = {
                    type: 'wefroms-image-uploader'
                };
            });

            vm.fileFrame.open();
        },

        insertImage(editor, image) {
            if (!image.id || (image.type !== 'image')) {
                this.alert({
                    type: 'error',
                    text: this.i18n.pleaseSelectAnImage
                });

                return;
            }

            const img = `<img src="${image.url}" alt="${image.alt}" title="${image.title}" style="max-width: 100%; height: auto;">`;

            editor.insertContent(img);
        }
    },

    watch: {
        editorId: function(new_val, old_val){
            this.editor.setContent(this.value);
        },
    }
});


