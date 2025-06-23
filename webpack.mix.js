const mix = require('laravel-mix');
const path = require('path');

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/login_app.js', 'public/js')
    .js('resources/js/frontend_app.js', 'public/js')
    .options({ processCssUrls: false });

// BACKEND THEME CSS FILES MIX
mix.styles([
    'resources/js/assets/backend/css/metisMenu.min.css',
    'resources/js/assets/backend/css/pace.min.css',
    'resources/js/assets/backend/css/bootstrap.min.css',
    'resources/js/assets/backend/css/app.css',
    'resources/js/assets/backend/css/mystyle.css',
    'resources/js/assets/backend/css/icons.css',
], 'public/css/backend_layouts.css');

// BACKEND THEME JS FILES MIX
mix.scripts([
    'resources/js/assets/backend/js/bootstrap.bundle.min.js',
    'resources/js/assets/backend/js/pace.min.js',
], 'public/js/backend_layouts.js');


// BACKEND LOGIN THEME CSS JS FILES MIX
mix.styles([
    "resources/js/assets/login/css/bootstrap.min.css",
    "resources/js/assets/login/css/style.css",
    "resources/js/assets/login/css/theme.css",
], 'public/css/login_layouts.css');

// FRONTEND THEME CSS FILES MIX
mix.styles([
    'resources/js/assets/web/css/bootstrap.min.css',
    'resources/js/assets/web/css/mystyle.css',
], 'public/css/frontend_layouts.css');

// FRONTEND THEME JS FILES MIX
mix.scripts([
    'resources/js/assets/web/js/bootstrap.bundle.min.js'
], 'public/js/frontend_layouts.js');


// COPY FILES / DIRECTORY
mix.copyDirectory('resources/js/assets/backend/fonts', 'public/fonts')
    .copyDirectory('resources/js/assets/public', 'public')
    .copyDirectory('resources/js/assets/backend/css/bootstrap.min.css.map', 'public/css')
    .copyDirectory('resources/js/assets/backend/js/bootstrap.bundle.min.js.map', 'public/js')
    .copyDirectory('resources/js/assets/backend/images', 'public/images');

// FOR CHANK
mix.webpackConfig({
    output: {
        chunkFilename: 'js/chunks/[name].js',
    },
    resolve: {
        extensions: ['.js', '.vue', '.json'],
        alias: {
            "@": path.resolve(__dirname, "resources/js"),
            "@views": path.resolve(__dirname, "resources/js/views"),
            "@components": path.resolve(__dirname, "resources/js/components"),
        }
    },
});

mix.vue({ version: 2 });
mix.version();