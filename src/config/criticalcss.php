<?php

/**
 * Some of the following configuration options are the same as the ones you'll
 * find in the Critical tool.
 *
 * @see https://github.com/addyosmani/critical  For more info.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    |
    | Routes (URIs) to generate critical-path CSS for.
    | If 'null' is specified, all 'GET' routes will be used automatically. Use
    | this option with caution.
    |
    */
    'routes' => null,

    /*
    |--------------------------------------------------------------------------
    | CSS file(s)
    |--------------------------------------------------------------------------
    |
    | CSS files to extract from. (Usually the application's main CSS file(s).)
    |
    | The file is relative to the public path, i.e., `public_path($css)`.
    |
    */
    'css' => ['css/app.css', 'css/app2.css'],

    /*
    |--------------------------------------------------------------------------
    | Storage Path
    |--------------------------------------------------------------------------
    |
    | The directory which the generated critical-path CSS is stored.
    | This can really be anywhere, but it is recommended to keep it in the
    | default resources directory.
    |
    */
    'storage' => base_path('resources/critical-css'),

    /*
    |--------------------------------------------------------------------------
    | Viewport
    |--------------------------------------------------------------------------
    |
    | Width and height of the target viewport.
    |
    */
    'width'  => 900,
    'height' => 1300,

    /*
    |--------------------------------------------------------------------------
    | Ingore Rules
    |--------------------------------------------------------------------------
    |
    | CSS rules to ignore. See filter-css for usage examples. You will also
    | find some commented-out examples below.
    | @see https://github.com/bezoerb/filter-css
    |
    */
    'ignore' => [
        // Removes @font-face blocks
        // '@font-face',

        // Removes CSS selector
        // '.selector',

        // JS Regex, matches url(..) rules
        // '/url(/',
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-Clear Views
    |--------------------------------------------------------------------------
    |
    | The critical-path CSS is injected directly into each Blade view.
    | When changes are made to the critical-path CSS files, the views which
    | include critical-path CSS must be refreshed for the changes to be take
    | effect.
    | With this option enabled, `php artisan view:clear` will be executed
    | automatically after critical-path CSS is generated.
    |
    */
    'clear_views' => true,

    /*
    |--------------------------------------------------------------------------
    | Critical Path
    |--------------------------------------------------------------------------
    |
    | Path to the Critical executable. If you have installed Critical in the
    | project only, the default should be used. However, if Critical is
    | installed globally, you can simply use 'critical'.
    |
    */
    'critical_bin' => base_path('node_modules/.bin/critical'),

];
