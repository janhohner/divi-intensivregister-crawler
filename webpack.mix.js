const mix = require('laravel-mix');

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

mix.scripts(
    [
        'resources/js/sorttable.js'
    ],
    'public/js/all.js'
)
    .styles(
        [
            'resources/css/style.css'
        ],
        'public/css/style.css'
    );