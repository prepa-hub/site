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

mix.js('resources/js/app.js', 'public/js')
    .styles([
        'resources/css/bootstrap.min.css',
    ], 'public/css/framework.css')
    .sass('resources/sass/app.scss', 'public/css')
    .copy('resources/js/bootstrap.min.js', 'public/js/bootstrap.min.js')
    .styles([
        'resources/css/bootstrap-rtl.min.css',
    ], 'public/css/framework-rtl.css')
    .sass('resources/sass/app-rtl.scss', 'public/css')
    .sass('resources/sass/auth.scss', 'public/css')
    .copy('resources/js/bootstrap-rtl.min.js', 'public/js/bootstrap-rtl.min.js')
    .version();
