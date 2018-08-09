module.exports = function (grunt) {
    'use strict';

    require('load-grunt-tasks')(grunt);

    var formBuilderAssets = require('./assets/js/utils/form-builder-assets.js');
    var vendorAssets      = require('./assets/js/utils/vendor-assets.js');
    var babelConfig       = {sourceMap: false, presets: ['env'] };

    function template_from_path(src, filepath) {
        var id = filepath.replace('/template.php', '').split('/').pop();

        return '<script type="text/x-template" id="tmpl-wpuf-' + id + '">\n' + src + '</script>\n';
    }

    function filename_on_concat(src, filepath) {
        return '/* ' + filepath + ' */\n' + src;
    }

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        // directory paths
        dirs: {
            css: 'assets/css',
            images: 'assets/images',
            js: 'assets/js',
            less: 'assets/less',
            spa: 'assets/spa',
            template: 'assets/js-templates',
            wpuf: 'assets/wpuf',
        },

        // jshint
        jshint: {
            options: {
                jshintrc: '.jshintrc',
                reporter: require('jshint-stylish')
            },

            main: [
                // 'assets/js/**/*.js',
                'assets/spa/components/**/*.js',
            ]
        },

        // Compile all .less files.
        less: {

            // one to one
            front: {
                files: {
                    // '<%= dirs.css %>/frontend.css': '<%= dirs.css %>/frontend.less'
                }
            },

            admin: {
                files: {
                    '<%= dirs.css %>/admin.css': ['<%= dirs.less %>/admin.less']
                }
            },
        },

        wp_readme_to_markdown: {
            your_target: {
                files: {
                    'README.md': 'readme.txt'
                }
            },
        },

        watch: {

            less: {
                files: ['<%= dirs.less %>/*.less'],
                tasks: ['less:admin']
            },

            components: {
                files: [
                    'assets/components/**/*',
                ],
                tasks: [
                    'concat:formBuilder', 'concat:formComponentTemplates', 'jshint:main','babel:components','uglify:components'
                ]
            },

            spa: {
                files: [
                    'assets/spa/**/*',
                ],
                tasks: [
                    'concat:spa', 'concat:spaMixins', 'concat:spaComponentTemplates', 'jshint:main','babel:spa','uglify:spa'
                ]
            }
        },

        addtextdomain: {
            options: {
                textdomain: 'weforms',
            },
            update_all_domains: {
                options: {
                    updateDomains: true
                },
                src: [ '*.php', '**/*.php', '!node_modules/**', '!php-tests/**', '!bin/**', '!build/**', '!assets/**' ]
            }
        },

        // Generate POT files.
        makepot: {
            target: {
                options: {
                    exclude: ['build/.*', 'node_modules/*', 'assets/*'],
                    mainFile: 'weforms.php',
                    domainPath: '/languages/',
                    potFilename: 'weforms.pot',
                    type: 'wp-plugin',
                    updateTimestamp: true,
                    potHeaders: {
                        'report-msgid-bugs-to': 'https://wedevs.com/contact/',
                        'language-team': 'LANGUAGE <EMAIL@ADDRESS>',
                        poedit: true,
                        'x-poedit-keywordslist': true
                    }
                }
            }
        },

        // concat/join files
        concat: {
            formBuilder: {
                options: {
                    process: filename_on_concat
                },

                files: {
                    '<%= dirs.js %>/form-builder-components.js': formBuilderAssets.components,
                }
            },

            formComponentTemplates: {
                options: {
                    process: template_from_path
                },
                files: {
                    '<%= dirs.template %>/form-components.php': formBuilderAssets.componentTemplates
                }
            },

            spaComponentTemplates: {
                options: {
                    process: template_from_path
                },
                files: {
                    '<%= dirs.template %>/spa-components.php': formBuilderAssets.spa.templates
                }
            },

            spaMixins: {
                options: {
                    process: filename_on_concat
                },
                files: {
                    '<%= dirs.js %>/spa-mixins.js': formBuilderAssets.spa.mixins
                }
            },

            spa: {
                options: {
                    banner: '/*!\n<%= pkg.name %> - v<%= pkg.version %>\n' +
                            'Generated: <%= grunt.template.today("yyyy-mm-dd") %> (<%= new Date().getTime() %>)\n*/\n\n' +
                            ';(function($) {\n',
                    footer: '\n})(jQuery);',
                    process: filename_on_concat
                },
                files: {
                    '<%= dirs.js %>/spa-app.js': formBuilderAssets.spa.app
                }
            },
            vendor: {
                options: {
                     process: filename_on_concat
                },
                files: {
                    '<%= dirs.js %>/vendor.js': vendorAssets,
                }
            },
        },

        // Clean up build directory
        clean: {
            build: ['build/'],
            wpuf: ['assets/wpuf']
        },

        // Copy the plugin into the build directory
        copy: {
            main: {
                src: [
                    '**',
                    '!node_modules/**',
                    '!assets/less/**',
                    '!assets/components/**',
                    '!assets/spa/**',
                    '!.codekit-cache/**',
                    '!.idea/**',
                    '!build/**',
                    '!bin/**',
                    '!.git/**',
                    '!Gruntfile.js',
                    '!package.json',
                    '!composer.json',
                    '!composer.lock',
                    '!debug.log',
                    '!phpunit.xml',
                    '!.gitignore',
                    '!.gitmodules',
                    '!npm-debug.log',
                    '!package-lock.json',
                    '!plugin-deploy.sh',
                    '!export.sh',
                    '!config.codekit',
                    '!gulpfile.js',
                    '!nbproject/*',
                    '!tests/**',
                    '!README.md',
                    '!CONTRIBUTING.md',
                    '!**/*~',
                    '!.csscomb.json',
                    '!.editorconfig',
                    '!.jshintrc',
                    '!.tmp',
                    '!assets/src/**',
                ],
                dest: 'build/'
            },

            wpuf: {
                files: [
                    {
                        expand: true,
                        flatten: false,
                        src: [
                            'js/wpuf-form-builder-components.js',
                            'js/wpuf-form-builder-mixins.js',
                            'js/jquery-ui-timepicker-addon.js',
                            'js/frontend-form.js',
                            'js/frontend-form.min.js',
                            'js/upload.js',
                            'js/upload.min.js',
                            'css/wpuf-form-builder.css',
                            'css/frontend-forms.css',
                            'css/jquery-ui-1.9.1.custom.css',
                            'css/images/**',
                            'images/wpspin_light.gif',
                            'js-templates/form-components.php',
                            'vendor/**',
                        ],
                        cwd: '../wp-user-frontend/assets',
                        dest: 'assets/wpuf/'
                    }
                ]
            }
        },

        // Compress build directory into <name>.zip and <name>-<version>.zip
        compress: {
            main: {
                options: {
                    mode: 'zip',
                    archive: './build/weforms-v<%= pkg.version %>.zip'
                },
                expand: true,
                cwd: 'build/',
                src: ['**/*'],
                dest: 'weforms'
            }
        },

        uglify: {
            components: {
                files: {
                    '<%= dirs.js %>/form-builder-components.min.js': ['<%= dirs.js %>/form-builder-components.js'],
                }
            },
            spa: {
                files: {
                    '<%= dirs.js %>/spa-app.min.js': '<%= dirs.js %>/spa-app.js',
                    '<%= dirs.js %>/spa-mixins.min.js': '<%= dirs.js %>/spa-mixins.js',
                }
            },
            main: {
                files: {
                    '<%= dirs.js %>/form-builder-components.min.js': ['<%= dirs.js %>/form-builder-components.js'],
                    '<%= dirs.js %>/spa-app.min.js': '<%= dirs.js %>/spa-app.js',
                    '<%= dirs.js %>/spa-mixins.min.js': '<%= dirs.js %>/spa-mixins.js',
                    '<%= dirs.js %>/wpuf-form-builder-contact-forms.min.js': '<%= dirs.js %>/wpuf-form-builder-contact-forms.js',
                    '<%= dirs.wpuf %>/js/frontend-form.min.js': '<%= dirs.wpuf %>/js/frontend-form.js',
                    '<%= dirs.wpuf %>/js/wpuf-form-builder-components.min.js': '<%= dirs.wpuf %>/js/wpuf-form-builder-components.js',
                    '<%= dirs.wpuf %>/js/upload.min.js': '<%= dirs.wpuf %>/js/upload.js',
                }
            },

            vendor: {
                files: {
                    '<%= dirs.js %>/vendor.min.js': '<%= dirs.js %>/vendor.js',
                }
            },

        },

        babel: {
            options: babelConfig,
            components: {
                files: {
                    '<%= dirs.js %>/form-builder-components.js': '<%= dirs.js %>/form-builder-components.js',
                }
            },
            spa: {
                files: {
                    '<%= dirs.js %>/spa-app.js': '<%= dirs.js %>/spa-app.js',
                    '<%= dirs.js %>/spa-mixins.js': '<%= dirs.js %>/spa-mixins.js',
                }
            },
            main: {
                files: {
                    '<%= dirs.js %>/form-builder-components.js': '<%= dirs.js %>/form-builder-components.js',
                    '<%= dirs.js %>/spa-app.js': '<%= dirs.js %>/spa-app.js',
                    '<%= dirs.js %>/spa-mixins.js': '<%= dirs.js %>/spa-mixins.js',
                    '<%= dirs.js %>/wpuf-form-builder-contact-forms.js': '<%= dirs.js %>/wpuf-form-builder-contact-forms.js',
                }
            },
            vendor: {
                files: {
                    '<%= dirs.js %>/vendor.js': '<%= dirs.js %>/vendor.js',
                }
            },
        }
    });

    // load npm tasks to be used here
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-compress');
    grunt.loadNpmTasks('grunt-notify');
    grunt.loadNpmTasks('grunt-wp-i18n');
    grunt.loadNpmTasks('grunt-contrib-less' );
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-wp-readme-to-markdown');

    // grunt tasks
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('wpuf', ['clean:wpuf', 'copy:wpuf']);

    // file auto generation
    grunt.registerTask('i18n', ['addtextdomain', 'makepot']);
    grunt.registerTask('readme', ['wp_readme_to_markdown']);
    grunt.registerTask('vendor', ['concat:vendor','uglify:vendor']);

    // build stuff
    grunt.registerTask('release', ['i18n', 'readme', 'babel:main', 'uglify:main','vendor']); // 'wpuf' removed
    grunt.registerTask('zip', ['clean:build', 'copy', 'compress']); // 'wpuf' removed

    grunt.util.linefeed = '\n';
};
