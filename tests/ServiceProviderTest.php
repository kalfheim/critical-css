<?php

use Mockery as m;
use Krisawzm\CriticalCss\CriticalCssServiceProvider;
use Illuminate\Contracts\Foundation\Application;

class ServiceProviderTest extends TestCase
{
    public function testRegister()
    {
        $app = m::mock(Application::class);

        $app->shouldReceive('singleton')->once()
            ->with('criticalcss.storage', m::type('Closure'));

        $app->shouldReceive('singleton')->once()
            ->with('criticalcss.htmlfetcher', m::type('Closure'));

        $app->shouldReceive('singleton')->once()
            ->with('criticalcss.cssgenerator', m::type('Closure'));

        $provider = new CriticalCssServiceProvider($app);

        $provider->register();
    }

    public function testProvides()
    {
        $provider = new CriticalCssServiceProvider(m::mock(Application::class));

        $provides = $provider->provides();

        $this->assertArraySubset(
            ['criticalcss.storage', 'criticalcss.htmlfetcher', 'criticalcss.cssgenerator'],
            $provider->provides()
        );
    }
}
