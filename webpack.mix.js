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
            'resources/css/vendor/style.css',
            'resources/css/style.css'
        ],
        'public/css/style.css'
    )
    .copyDirectory('resources/img', 'public/img');

if (mix.inProduction()) {
    mix.version()
} else {
    mix.sourceMaps().browserSync("127.0.0.1:8000");
}
