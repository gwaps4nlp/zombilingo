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

mix.js('resources/assets/js/app.js', 'public/build/js')
   .sass('resources/assets/sass/app.scss', 'public/build/css')
   .version();
mix.scripts([
    'public/build/js/app.js',
    'public/js/master.js',
    'public/js/game.js',
    'public/js/compte.js',
    'public/js/counter.js',
    "public/js/svg/jquery.svg.min.js",
    "public/js/brat/client/src/configuration.js",
    "public/js/brat/client/src/util.js",
    "public/js/brat/client/lib/webfont.js",
    "public/js/brat/client/src/dispatcher.js",
    "public/js/brat/client/src/url_monitor.js",        
    "public/js/brat/client/src/visualizer.js",
    'public/js/visualization.js',
], 'public/build/js/all.js').version();
mix.js('public/build/js/all.js', 'public/build/js')
   .version();
