<?php

namespace Alfheim\CriticalCss\HtmlFetchers;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel as HttpKernel;

/**
 * This implementation fetches HTML for a given URI by mocking a Request and
 * letting a new instance of the Laravel Application handle it.
 */
class LaravelHtmlFetcher implements HtmlFetcherInterface
{
    /** @var \Illuminate\Contracts\Foundation\Application */
    protected $app = null;

    /** @var array */
    public $credentials;

    /**
     * Create a new instance.
     *
     * @param  \Closure $appMaker
     *
     * @return void
     */
    public function __construct(Closure $appMaker)
    {
        $this->app = $appMaker();
        $this->credentials = [];
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($uri)
    {
        $response = $this->call($uri, $this->credentials);

        if (!$response->isOk()) {
            throw new HtmlFetchingException(
                sprintf('Invalid response from URI [%s].', $uri)
            );
        }

        return $this->stripCss($response->getContent());
    }

    /**
     * Remove any existing inlined critical-path CSS that has been generated
     * previously. Old '<style>' tags should be tagged with a `data-inline`
     * attribute.
     *
     * @param  string $html
     *
     * @return string
     */
    protected function stripCss($html)
    {
        return preg_replace('/\<style data-inlined\>.*\<\/style\>/s', '', $html);
    }

    /**
     * Call the given URI and return a Response.
     *
     * @param  string $uri
     *
     * @return \Illuminate\Http\Response
     */
    protected function call($uri, $credentials = [])
    {
        $request = Request::create($uri, 'GET');

        $kernel = $this->app->make(HttpKernel::class);

        if (!empty($credentials)) {
            Auth::attempt($credentials);
        }

        $response = $kernel->handle($request);

        $kernel->terminate($request, $response);

        return $response;
    }
}
