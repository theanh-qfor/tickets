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
    mix.sass('app.scss');//create app.css
    mix.stylesIn('public/css');//combine to all.css
    mix.scripts('*.*');//combine to all.js
    mix.version(['public/css/all.css','public/js/all.js']);
});
