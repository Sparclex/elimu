let mix = require('laravel-mix')

mix.js('resources/js/tool.js', 'dist/js')
    .js('resources/js/fields/HtmlReadonly/field.js', 'dist/fields/html-readonly/js')
    .webpackConfig({
        resolve: {
            symlinks: false
        }
    })
