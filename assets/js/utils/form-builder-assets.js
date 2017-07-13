/**
 * Returns file paths of vue assets
 */

/* global module, require */
function assets() {
    'use strict';

    // const grunt     = require('grunt');
    const fs        = require('fs');
    let paths       = {
        mixins:     ['assets/js/utils/jquery-siaf-start.js'],
        components: ['assets/js/utils/jquery-siaf-start.js'],
        componentTemplates: [],
        spa: {
            app: [],
            components: [],
            templates: []
        }
    };

    // mixins
    // const mixinsPath  = './admin/form-builder/assets/js/mixins/';
    // let mixins        = fs.readdirSync(mixinsPath);

    // mixins.forEach((mixin) => {
    //     const path = `${mixinsPath}${mixin}`;

    //     if (grunt.file.isFile(path)) {
    //         paths.mixins.push(path);
    //     }
    // });

    // components
    const componentPath  = './assets/components/';
    let components       = fs.readdirSync(componentPath);

    components.forEach((component) => {
        const path = `${componentPath}${component}`;

        if (fs.lstatSync(path).isDirectory()) {
            paths.components.push(path + '/index.js');
            paths.componentTemplates.push(path + '/template.php');
        }
    });

    paths.mixins.push('assets/js/utils/jquery-siaf-end.js');
    paths.components.push('assets/js/utils/jquery-siaf-end.js');

    // SPA paths
    const spaComponentPath = './assets/spa/components/';
    let spaComponents      = fs.readdirSync(spaComponentPath);

    spaComponents.forEach((component) => {
        const path = `${spaComponentPath}${component}`;

        if (fs.lstatSync(path).isDirectory()) {
            paths.spa.components.push(path + '/index.js');
            paths.spa.templates.push(path + '/template.php');
        }
    });

    const spaMixinsPath = './assets/spa/mixins/';
    let spaMixins      = fs.readdirSync(spaMixinsPath);

    spaMixins.forEach((mixin) => {
        const path = `${spaMixinsPath}${mixin}`;

        paths.spa.app.push(path);
    });

    let app = paths.spa.app.concat( paths.spa.components );
    app.push( './assets/spa/app.js' );

    paths.spa.app = app;

    return paths;
}

module.exports = assets();
