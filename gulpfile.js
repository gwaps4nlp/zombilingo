var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.sass("zombilingo.scss");
    mix.styles([
        "master.css",
        "zombilingo.css",
        "jeu.css",
        "compte.css",
    ], 'public/css/app.css','public/css');
    mix.scripts([
        "jQuery.js",
        "jQueryUI.js",
        "bootstrap.min.js",
        "jquery.cookie.js",
        "master.js",
        "game.js",
        "compte.js",
    ], 'public/js/app.js','public/js');
    mix.version(["public/css/app.css", "public/js/app.js"]);
});
