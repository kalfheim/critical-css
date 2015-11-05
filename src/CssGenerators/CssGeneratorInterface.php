<?php

namespace Krisawzm\CriticalCss\CssGenerators;

use Krisawzm\CriticalCss\HtmlFetchers\HtmlFetcherInterface;

/**
 * The purpose of this interface is to generate critical-path CSS.
 */
interface CssGeneratorInterface
{
    /**
     * This class generates critical-path CSS.
     *
     * @param  array $css  Files to extract CSS from.
     * @param  \Krisawzm\CriticalCss\HtmlFetchers\HtmlFetcherInterface $htmlFetcher
     *         Provides the HTML source to be operated against.
     *
     * @return \Krisawzm\CriticalCss\CssGenerators\CssGeneratorInterface
     */
    public function __construct(array $css, HtmlFetcherInterface $htmlFetcher);

    /**
     * Generate critical-path CSS for a given URI.
     *
     * @param  string $uri  The given URI to generate critical-path CSS for.
     *
     * @return string       The critical-path CSS.
     *
     * @throws \Krisawzm\CriticalCss\CssGenerators\CssGeneratorException
     */
    public function generate($uri);
}
