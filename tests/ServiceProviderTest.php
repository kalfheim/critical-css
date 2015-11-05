<?php

use Mockery as m;
use Krisawzm\CriticalCss\CriticalCssServiceProvider;

class ServiceProviderTest extends TestCase
{
    public function testRegister()
    {
        $app = m::mock('Illuminate\Contracts\Foundation\Application');

        $app->shouldReceive('singleton')->twice()
            ->with(
                m::anyOf('criticalcss.htmlfetcher', 'criticalcss.cssgenerator'),
                m::type('Closure')
            );

        $provider = new CriticalCssServiceProvider($app);

        $provider->register();
    }

    public function testBladeDirectiveParser()
    {
        $tests = [
            '/'         => '/',
            '/foo'      => 'foo',
            'foo'       => 'foo',
            'foo/bar/'  => 'foo/bar',
            'foo/bar'   => 'foo/bar',
        ];

        foreach ($tests as $expects => $test) {
            $this->assertEquals(
                $test,
                CriticalCssServiceProvider::parseUriFromExpression($test)
            );
        }
    }
}
