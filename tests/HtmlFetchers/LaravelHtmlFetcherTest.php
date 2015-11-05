<?php

use Mockery as m;
use Krisawzm\CriticalCss\HtmlFetchers\LaravelHtmlFetcher;

class LaravelHtmlFetcherTest extends TestCase
{
    public function testBasicFetch()
    {
        $f = new LaravelHtmlFetcher;
        $f->setApplication($this->mockApp());

        $this->assertEquals('bar', $f->fetch('foo'));
    }

    /**
     * @expectedException \Krisawzm\CriticalCss\HtmlFetchers\HtmlFetchingException
     */
    public function testFailingFetch()
    {
        $f = new LaravelHtmlFetcher;
        $f->setApplication($this->mockApp(false));

        $f->fetch('500');
    }

    protected function mockApp($isOk = true)
    {
        $app = m::mock('Illuminate\Contracts\Foundation\Application');

        $app->shouldReceive('make')->once()
            ->with('Illuminate\Contracts\Http\Kernel')
            ->andReturn($this->mockHttpKernel($isOk));

        return $app;
    }

    protected function mockHttpKernel($isOk)
    {
        $kernel = m::mock('Illuminate\Contracts\Http\Kernel');

        $kernel->shouldReceive('handle')->once()
               ->with(m::type('Illuminate\Http\Request'))
               ->andReturn($this->mockHttpResponse($isOk));

        $kernel->shouldReceive('terminate')->once()
               ->with(
                    m::type('Illuminate\Http\Request'),
                    m::type('Illuminate\Http\Response')
                );

        return $kernel;
    }

    protected function mockHttpResponse($isOk)
    {
        $response = m::mock('Illuminate\Http\Response');

        $response->shouldReceive('isOk')->once()
                 ->andReturn($isOk);

        if ($isOk) {
            $response->shouldReceive('getContent')->once()
                     ->andReturn('bar');
        }

        return $response;
    }
}
