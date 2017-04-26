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
        components: ['assets/js/utils/jquery-siaf-start.js']
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
        }
    });

    paths.mixins.push('assets/js/utils/jquery-siaf-end.js');
    paths.components.push('assets/js/utils/jquery-siaf-end.js');

    return paths;
}

module.exports = assets();
