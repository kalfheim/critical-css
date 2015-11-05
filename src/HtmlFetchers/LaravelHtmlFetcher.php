<?php

namespace Krisawzm\CriticalCss\HtmlFetchers;

use Illuminate\Http\Request;
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

    /**
     * {@inheritdoc}
     */
    public function fetch($uri)
    {
        $response = $this->call($uri);

        if (!$response->isOk()) {
            throw new HtmlFetchingException(
                sprintf('Invalid response from URI [%s].', $uri)
            );
        }

        return $response->getContent();
    }

    /**
     * Call the given URI and return a Response.
     *
     * @param  string $uri
     *
     * @return \Illuminate\Http\Response
     */
    protected function call($uri)
    {
        if (is_null($this->app)) {
            $this->createApplication();
        }

        $request = Request::create($uri, 'GET');

        $kernel = $this->app->make(HttpKernel::class);

        $response = $kernel->handle($request);

        $kernel->terminate($request, $response);

        return $response;
    }

    /**
     * Creates an application instance.
     *
     * @return void
     */
    protected function createApplication()
    {
        $this->app = require base_path('bootstrap/app.php');
    }

    /**
     * Set application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application
     *
     * @return void
     */
    public function setApplication(Application $app)
    {
        $this->app = $app;
    }
}
