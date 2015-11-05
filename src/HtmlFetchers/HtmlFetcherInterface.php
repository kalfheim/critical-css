<?php

namespace Krisawzm\CriticalCss\HtmlFetchers;

/**
 * The purpose of this interface is to fetch, or generate, the full HTML
 * contents of a given URI.
 *
 * This could be achieved by simply dispatching an HTTP request to the host
 * at the given URI and collecting the response body, or in the case of Laravel
 * (@see LaravelHtmlFetcher.php) this can be done by mocking a Request and
 * letting a new Application instance handle it and return a Response.
 */
interface HtmlFetcherInterface
{
    /**
     * Returns the full HTML contents of a given URI.
     *
     * @param  string $uri  The URI to fetch HTML from.
     *
     * @return string       The HTML contents.
     *
     * @throws \Krisawzm\CriticalCss\HtmlFetchers\HtmlFetchingException
     */
    public function fetch($uri);
}
