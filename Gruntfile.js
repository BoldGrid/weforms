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
                'assets/js/**/*.js',
                'assets/spa/**/*.js',
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
                    'concat:formBuilder', 'concat:formComponentTemplates'
                ]
            },

            spa: {
                files: [
                    'assets/spa/**/*',
                ],
                tasks: [
                    'concat:spa', 'concat:spaComponentTemplates'
                ]
            }
        },

        // Generate POT files.
        makepot: {
            target: {
                options: {
                    exclude: ['build/.*', 'node_modules/*', 'assets/*'],
                    domainPath: '/languages/',
                    potFilename: 'best-contact-form.pot',
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

            spa: {
                options: {
                    banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
                            '<%= grunt.template.today("yyyy-mm-dd") %> */\n' +
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
            main: ['build/']
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
            }
        },

        //Compress build directory into <name>.zip and <name>-<version>.zip
        compress: {
            main: {
                options: {
                    mode: 'zip',
                    archive: './build/best-contact-form-v<%= pkg.version %>.zip'
                },
                expand: true,
                cwd: 'build/',
                src: ['**/*'],
                dest: 'best-contact-form'
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

    grunt.registerTask('release', ['makepot']);

    grunt.registerTask('zip', ['clean', 'copy', 'compress']);
};
