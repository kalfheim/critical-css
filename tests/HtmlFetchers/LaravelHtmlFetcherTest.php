<?php

use Mockery as m;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Contracts\Foundation\Application;
use Krisawzm\CriticalCss\HtmlFetchers\LaravelHtmlFetcher;

class LaravelHtmlFetcherTest extends TestCase
{
    protected static $html_basic =
'<html lang="en">
    <html>
        <meta charset="utf-8">
        <title>foo</title>
    </html>
    <body>
        <h1>foo</h1>
    </body>
</html>
';

    protected static $html_with_css =
'<html lang="en">
    <html>
        <meta charset="utf-8">
        <title>foo</title>
<style data-inlined>h1{color:red}
.foo{color:green}</style>
    </html>
    <body>
        <h1>foo</h1>
    </body>
</html>
';

    protected static $html_with_css_removed =
'<html lang="en">
    <html>
        <meta charset="utf-8">
        <title>foo</title>

    </html>
    <body>
        <h1>foo</h1>
    </body>
</html>
';

    public function testBasicFetch()
    {
        $f = $this->mockLaravelHtmlFetcher(
            $this->mockHttpKernel(static::$html_basic)
        );

        $this->assertEquals(static::$html_basic, $f->fetch('foo'));
    }

    public function testStripCss()
    {
        $f = $this->mockLaravelHtmlFetcher(
            $this->mockHttpKernel(static::$html_with_css)
        );

        $this->assertEquals(static::$html_with_css_removed, $f->fetch('foo'));
    }

    /**
     * @expectedException \Krisawzm\CriticalCss\HtmlFetchers\HtmlFetchingException
     */
    public function testFailingFetch()
    {
        $f = $this->mockLaravelHtmlFetcher($this->mockHttpKernel(false));

        $f->fetch('500');
    }

    /**
     * @param  \Illuminate\Contracts\Http\Kernel $kernel
     *
     * @return \Krisawzm\CriticalCss\HtmlFetchers\LaravelHtmlFetcher
     */
    protected function mockLaravelHtmlFetcher(Kernel $kernel)
    {
        return new LaravelHtmlFetcher(function () use ($kernel) {
            $app = m::mock(Application::class);
            $app->shouldReceive('make')->once()
                ->with(Kernel::class)
                ->andReturn($kernel);

            return $app;
        });
    }

    /**
     * @param  bool|string $returnResponse
     *
     * @return \Illuminate\Contracts\Http\Kernel
     */
    protected function mockHttpKernel($returnResponse)
    {
        $kernel = m::mock(Kernel::class);

        $response = m::mock(Response::class);
        $response->shouldReceive('isOk')->once()
                 ->andReturn((bool)$returnResponse);

        if ($returnResponse) {
            $response->shouldReceive('getContent')->once()
                     ->andReturn($returnResponse);
        }

        $kernel->shouldReceive('handle')->once()
               ->with(m::type(Request::class))
               ->andReturn($response);

        $kernel->shouldReceive('terminate')->once()
               ->with(
                    m::type(Request::class),
                    m::type(Response::class)
                );

        return $kernel;
    }
}
