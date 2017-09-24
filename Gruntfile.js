module.exports = function (grunt) {
    'use strict';

    var formBuilderAssets = require('./assets/js/utils/form-builder-assets.js');

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
            template: 'assets/js-templates'
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
                    'concat:formBuilder', 'concat:formComponentTemplates', 'jshint:main'
                ]
            },

            spa: {
                files: [
                    'assets/spa/**/*',
                ],
                tasks: [
                    'concat:spa', 'concat:spaMixins', 'concat:spaComponentTemplates', 'jshint:main'
                ]
            }
        },

        // Generate POT files.
        makepot: {
            target: {
                options: {
                    exclude: ['build/.*', 'node_modules/*', 'assets/*'],
                    domainPath: '/languages/',
                    potFilename: 'weforms.pot',
                    type: 'wp-plugin',
                    potHeaders: {
                        'report-msgid-bugs-to': 'https://wedevs.com/contact/',
                        'language-team': 'LANGUAGE <EMAIL@ADDRESS>'
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
            }
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

    // grunt tasks
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('wpuf', ['clean:wpuf', 'copy:wpuf']);

    grunt.registerTask('release', [ 'wpuf', 'makepot']);


    grunt.registerTask('zip', ['clean:build', 'wpuf', 'copy', 'compress']);
};
