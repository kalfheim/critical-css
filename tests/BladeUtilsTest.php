<?php

namespace Alfheim\CriticalCss;

use Mockery as m;
use Alfheim\CriticalCss\BladeUtils;
use Illuminate\View\Compilers\BladeCompiler;

class BladeUtilsTest extends \TestCase
{
    public function testBladeDirectiveRegistration()
    {
        $blade = m::mock(BladeCompiler::class);

        $blade->shouldReceive('directive')->once()
              ->with('criticalCss', m::type('callable'));

        BladeUtils::registerBladeDirective($blade);
    }

    public function testBladeDirectiveParser()
    {
        $this->assertEquals('foo', BladeUtils::parseBladeDirective('(/foo)'));
    }

    public function testUriParser()
    {
        $tests = [
            '(/)'         => '/',
            '(/foo)'      => 'foo',
            '(foo)'       => 'foo',
            '(foo/bar/)'  => 'foo/bar',
            '(foo/bar)'   => 'foo/bar',
            '(/foo/bar/)' => 'foo/bar',
            '/foo/bar/'   => 'foo/bar',
        ];

        foreach ($tests as $expects => $test) {
            $this->assertEquals(
                $test,
                BladeUtils::parseUriFromBladeExpression($test)
            );
        }
    }

    public function testUriParserWithNull()
    {
        $this->assertEquals('uri/current', BladeUtils::parseUriFromBladeExpression(null));
    }
}

function app($a)
{
    if ($a === 'criticalcss.storage') {

        static $storage = null;

        if (is_null($storage)) {
            $storage = m::mock('Alfheim\CriticalCss\Storage\StorageInterface');

            $storage->shouldReceive('css')->once()
                    ->with(m::type('string'))
                    ->andReturn('foo');
        }

        return $storage;

    } elseif ($a === 'router') {

        $router = m::mock('router');

        $router->shouldReceive('current')->once()
               ->andReturn(m::self())
               ->getMock()
               ->shouldReceive('getUri')->once()
               ->andReturn('uri/current');

        return $router;

    } else {

        return false;

    }
}
