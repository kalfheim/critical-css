<?php

use Mockery as m;
use Krisawzm\CriticalCss\Facades\Critical;
use Illuminate\Contracts\Foundation\Application;
use Krisawzm\CriticalCss\Storage\StorageInterface;

class FacadesTest extends TestCase
{
    public function testCriticalFacade()
    {
        $storage = m::mock(StorageInterface::class);
        $storage->shouldReceive('readCss')->once()
                ->with('foo')
                ->andReturn('.css{}');

        // $app = m::mock(Application::class);

        // $app->shouldReceive('make')->once()
        //     ->with('criticalcss.storage')
        //     ->andReturn($storage);

        $app = new ApplicationStub;
        $app->setAttributes(['criticalcss.storage' => $storage]);

        Critical::setFacadeApplication($app);

        $this->assertEquals('.css{}', Critical::readCss('foo'));
    }
}

class ApplicationStub implements ArrayAccess
{
    protected $attributes = [];

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function instance($key, $instance)
    {
        $this->attributes[$key] = $instance;
    }

    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    public function offsetGet($key)
    {
        return $this->attributes[$key];
    }

    public function offsetSet($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function offsetUnset($key)
    {
        unset($this->attributes[$key]);
    }
}
