<?php

namespace Krisawzm\CriticalCss;

use Illuminate\View\Compilers\BladeCompiler;

/**
 * Utilities for the Blade compiler.
 *
 * @static
 */
class BladeUtils
{
    /**
     * Register the `@criticalCss($uri)` Blade directive.
     *
     * @return void
     */
    public static function registerBladeDirective(BladeCompiler $blade)
    {
        $blade->directive('criticalCss', [static::class, 'parseBladeDirective']);
    }

    /**
     * Parse the Blade directive.
     *
     * @param  string $expr
     *
     * @return string
     */
    public static function parseBladeDirective($expr)
    {
        return app('criticalcss.storage')->css(
            static::parseUriFromBladeExpression($expr)
        );
    }

    /**
     * Parse the URI from the Blade directive expression.
     *
     * @param  string $expr
     *
     * @return string
     */
    public static function parseUriFromBladeExpression($expr)
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
}
