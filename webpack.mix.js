let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css')
    .js('resources/assets/js/tools/sample-batch-importer/tool.js', 'public/tools/sample-batch-importer/js')
    .js('resources/assets/js/tools/result-field/field.js', 'public/tools/result-field/js')
    .js('resources/assets/js/tools/status-field/field.js', 'public/tools/status-field/js')
