<?php

namespace Krisawzm\CriticalCss;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Krisawzm\CriticalCss\HtmlFetchers\LaravelHtmlFetcher;
use Krisawzm\CriticalCss\CssGenerators\CriticalGenerator;

class CriticalCssServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->app->singleton('criticalcss.htmlfetcher', function ($app) {
            return new LaravelHtmlFetcher;
        });

        $this->app->singleton('criticalcss.cssgenerator', function ($app) {
            $generator = new CriticalGenerator(
                array_map('public_path', $app->config->get('criticalcss.css')),
                $app->make('criticalcss.htmlfetcher')
            );

            $generator->setCriticalBin(
                $app->config->get('criticalcss.critical_bin')
            );

            $generator->setOptions(
                $app->config->get('criticalcss.width'),
                $app->config->get('criticalcss.height'),
                $app->config->get('criticalcss.ignore')
            );

            return $generator;
        });
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();

        $this->registerBladeDirectives(
            $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler()
        );
    }

    /**
     * Set up the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $src = realpath(__DIR__.'/config/criticalcss.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                $src => config_path('criticalcss.php'),
            ]);
        }

        $this->mergeConfigFrom($src, 'criticalcss');
    }

    /**
     * Register the @criticalCss directive with Blade.
     *
     * @param  \Illuminate\View\Compilers\BladeCompiler $blade
     *
     * @return void
     */
    protected function registerBladeDirectives(BladeCompiler $blade)
    {
        $blade->directive('criticalCss', [static::class, 'parseBladeDirective']);
    }

    /**
     * Parse a Blade directive expression into inline CSS.
     *
     * @param  string $expr
     *
     * @return array
     *
     * @static
     */
    public static function parseBladeDirective($expr)
    {
        $uri  = static::parseUriFromExpression($expr);

        $path = realpath(sprintf('%s/%s.css',
                                  config('criticalcss.storage'),
                                  urlencode($uri)));

        if (!app('files')->exists($path)) {
            $msg = sprintf(
                'Critical-path CSS for URI [%s] not found at [%s]. '.
                'Try running php artisan criticalcss:make and php artisan view:clear',
                $uri,
                $path
            );

            app('log')->warning($msg);

            return '<!-- '.$msg.' -->';
        }

        return '<style>'.app('files')->get($path).'</style>';
    }

    /**
     * Parse the URI from a Blade expression.
     *
     * @param  string $expr
     *
     * @return string
     *
     * @static
     */
    public static function parseUriFromExpression($expr)
    {
        if (is_null($expr)) {
            // Return the current route if no argument is given.
            return app('router')->current()->getUri();
        }

        $expr = trim($expr, '()\'" ');

        if ($expr !== '/') {
            // Remove leading slash, if any.
            return ltrim($expr, '/');
        }

        return $expr;
    }

    /**
     * {@inheritdoc}
     */
    public function provides()
    {
        return [
            'criticalcss.htmlfetcher',
            'criticalcss.cssgenerator',
        ];
    }
}
