<?php

use Mockery as m;
use Krisawzm\CriticalCss\CssGenerators\CriticalGenerator;

class CriticalGeneratorTest extends TestCase
{
    protected $css;
    protected $html;

    public function __construct()
    {
        $this->css = [realpath(__DIR__.'/stubs/app.css')];
        $this->html = realpath(__DIR__.'/stubs/foo.html');
    }

    public function testGenerate()
    {
        $g = $this->mockGenerator();

        $g->setOptions();

        $this->assertEquals(
            'html{font-size:16px}body{background-image:url(/some-image.jpg)}.class{color:lightred}.other-class{color:#90ee90}',
            $g->generate('foo')
        );
    }

    public function testGenerateWithIgnoredRules()
    {
        $g = $this->mockGenerator();

        $g->setOptions(900, 1300, ['.class', '/url(/']);

        $this->assertEquals(
            'html{font-size:16px}.other-class{color:#90ee90}',
            $g->generate('foo')
        );
    }

    /**
     * @expectedException \Krisawzm\CriticalCss\CssGenerators\CssGeneratorException
     */
    public function testFailingGeneration()
    {
        $g = $this->mockGenerator();

        $g->setOptions();

        $g->setCriticalBin('this-doesnt-exist');

        $g->generate('foo');
    }

    protected function mockGenerator()
    {
        $g = new CriticalGenerator($this->css, $this->mockHtmlFetcher());

        $g->setCriticalBin(realpath(__DIR__.'/../../node_modules/.bin/critical'));

        return $g;
    }

    protected function mockHtmlFetcher()
    {
        $fetcher = m::mock('Krisawzm\CriticalCss\HtmlFetchers\HtmlFetcherInterface');

        $fetcher->shouldReceive('fetch')->once()
                ->with('foo')
                ->andReturn(file_get_contents($this->html));

        return $fetcher;
    }
}
