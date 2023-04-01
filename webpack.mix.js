const mix = require('laravel-mix');

mix.styles(['resources/css/app.css', 'resources/css/style.css'], 'public/assets/css/app.css');

mix.scripts([ 'resources/js/app.js', 'resources/js/custom.js'], 'public/assets/js/app.js');
