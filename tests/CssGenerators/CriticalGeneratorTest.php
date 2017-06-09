<?php

use Mockery as m;
use Alfheim\CriticalCss\Storage\StorageInterface;
use Alfheim\CriticalCss\CssGenerators\CriticalGenerator;
use Alfheim\CriticalCss\HtmlFetchers\HtmlFetcherInterface;

class CriticalGeneratorTest extends TestCase
{
    protected $css;
    protected $html;
    protected $html_with_css;

    public function __construct()
    {
        $this->css = [realpath(__DIR__.'/stubs/app.css')];
        $this->html = realpath(__DIR__.'/stubs/foo.html');
        $this->html_with_css = realpath(__DIR__.'/stubs/foo_with_css.html');
    }

    public function testGenerate()
    {
        $this->doATestWith(
            'foo',
            file_get_contents($this->html),
            'html{font-size:16px}body{background-image:url(/some-image.jpg)}.class{color:lightred}.other-class{color:#90ee90}'
        );
    }

    public function testGenerateWithIgnoredRules()
    {
        $this->doATestWith(
            'foo',
            file_get_contents($this->html),
            'html{font-size:16px}.other-class{color:#90ee90}',
            ['width' => 900, 'height' => 1300, 'ignore' => [
                '.class', '/url(/'
            ]]
        );
    }

    /**
     * @expectedException \Alfheim\CriticalCss\CssGenerators\CssGeneratorException
     */
    public function testFailingGeneration()
    {
        $fetcher = m::mock(HtmlFetcherInterface::class);
        $fetcher->shouldReceive('fetch')->once()
                ->with('foo')
                ->andReturn('<html>');

        $storage = m::mock(StorageInterface::class);

        $g = new CriticalGenerator($this->css, $fetcher, $storage);

        $g->setCriticalBin('this-doesnt-exist-'.mt_rand());

        $g->setOptions();

        $g->generate('foo');
    }

    /**
     * @param string $uri
     * @param string $rawHtml
     * @param string $expectedCss
     * @param array  $options
     */
    protected function doATestWith($uri, $rawHtml, $expectedCss, $options = [])
    {
        $fetcher = m::mock(HtmlFetcherInterface::class);
        $fetcher->shouldReceive('fetch')->once()
                ->with($uri)
                ->andReturn($rawHtml);

        $storage = m::mock(StorageInterface::class);
        $storage->shouldReceive('writeCss')->once()
                ->with($uri, $expectedCss)
                ->andReturn(true);

        $g = new CriticalGenerator($this->css, $fetcher, $storage);

        $g->setCriticalBin(
            realpath(__DIR__.'/../../node_modules/.bin/critical')
        );

        if ($options) {
            extract($options);
            $g->setOptions($width, $height, $ignore);
        } else {
            $g->setOptions();
        }

        $this->assertTrue($g->generate($uri));
    }

    public function testGenerateWithUriAlias()
    {
        $uri   = 'users/10';
        $alias = 'users/profile';

        $fetcher = m::mock(HtmlFetcherInterface::class);
        $fetcher->shouldReceive('fetch')->once()
                ->with($uri)
                ->andReturn(file_get_contents($this->html));

        $storage = m::mock(StorageInterface::class);
        $storage->shouldReceive('writeCss')->once()
                ->with($alias, 'html{font-size:16px}body{background-image:url(/some-image.jpg)}.class{color:lightred}.other-class{color:#90ee90}')
                ->andReturn(true);

        $g = new CriticalGenerator($this->css, $fetcher, $storage);

        $g->setCriticalBin(
            realpath(__DIR__.'/../../node_modules/.bin/critical')
        );

        $g->setOptions();

        $this->assertTrue($g->generate($uri, $alias));
    }

    public function testGenerateWithRouteCss()
    {
        $uri   = 'users/10';
        $alias = 'users/profile';
        $routeCss = $this->css[0];

        $fetcher = m::mock(HtmlFetcherInterface::class);
        $fetcher->shouldReceive('fetch')->once()
                ->with($uri)
                ->andReturn(file_get_contents($this->html));

        $storage = m::mock(StorageInterface::class);
        $storage->shouldReceive('writeCss')->once()
                ->with($alias, 'html{font-size:16px}body{background-image:url(/some-image.jpg)}.class{color:lightred}.other-class{color:#90ee90}')
                ->andReturn(true);

        $g = new CriticalGenerator($this->css, $fetcher, $storage);

        $g->setCriticalBin(
            realpath(__DIR__.'/../../node_modules/.bin/critical')
        );

        $g->setOptions();

        $this->assertTrue($g->generate($uri, $alias, $routeCss));
    }
}
