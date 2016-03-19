<?php

namespace Alfheim\CriticalCss\CssGenerators;

use Alfheim\CriticalCss\Storage\StorageInterface;
use Alfheim\CriticalCss\HtmlFetchers\HtmlFetcherInterface;

/**
 * The purpose of this interface is to generate and store critical-path CSS.
 */
interface CssGeneratorInterface
{
    /**
     * This class generates critical-path CSS.
     *
     * @param  array $css  Files to extract CSS from.
     *
     * @param  \Alfheim\CriticalCss\HtmlFetchers\HtmlFetcherInterface $htmlFetcher
     *         Provides the HTML source to be operated against.
     *
     * @param \Alfheim\CriticalCss\Storage\StorageInterface $storage
     *         Provides a storage driver to write the output to.
     *
     * @return void
     */
    public function __construct(array $css,
                                HtmlFetcherInterface $htmlFetcher,
                                StorageInterface $storage);

    /**
     * Generate critical-path CSS for a given URI.
     *
     * @param  string $uri  The given URI to generate critical-path CSS for.
     *
     * @return bool         Indicating successful write to the StorageInterface.
     *
     * @throws \Alfheim\CriticalCss\CssGenerators\CssGeneratorException
     */
    public function generate($uri);
}
