<?php

use Mockery as m;

use Illuminate\Contracts\Filesystem\Filesystem;
use Alfheim\CriticalCss\Storage\LaravelStorage;

class LaravelStorageTest extends TestCase
{
    public function testValidateStoragePathDoesntExist()
    {
        $files = m::mock(Filesystem::class);

        $files->shouldReceive('exists')->once()
              ->with('dir')
              ->andReturn(false);

        $files->shouldReceive('makeDirectory')->once()
              ->with('dir')
              ->andReturn(true);

        $storage = new LaravelStorage('dir', $files, false);

        $this->assertTrue($storage->validateStoragePath());
    }

    public function testValidateStoragePathDoesExist()
    {
        $files = m::mock(Filesystem::class);

        $files->shouldReceive('exists')->once()
              ->with('dir')
              ->andReturn(true);

        $files->shouldNotReceive('makeDirectory');

        $storage = new LaravelStorage('dir', $files, false);

        $this->assertTrue($storage->validateStoragePath());
    }

    public function testReadCss()
    {
        $files = m::mock(Filesystem::class);

        $files->shouldReceive('exists')->once()
              ->with('dir/foo%2Fbar.css')
              ->andReturn(true);

        $files->shouldReceive('get')->once()
              ->with('dir/foo%2Fbar.css')
              ->andReturn('.css{}');

        $storage = new LaravelStorage('dir', $files, false);

        $this->assertEquals('.css{}', $storage->readCss('foo/bar'));
    }

    public function testReadCssWithAFileThatDoesntExist()
    {
        $files = m::mock(Filesystem::class);

        $files->shouldReceive('exists')->once()
              ->with('dir/foo.css')
              ->andReturn(false);

        $storage = new LaravelStorage('dir', $files, false);

        $this->assertEquals(
            '/* Critical-path CSS for URI [foo] not found at [dir/foo.css]. Check the config and run `php artisan criticalcss:make`. */',
            $storage->readCss('foo')
        );
    }

    public function testCss()
    {
        $files = m::mock(Filesystem::class);

        $files->shouldReceive('exists')->once()
              ->with('dir/foo.css')
              ->andReturn(true);

        $files->shouldReceive('get')->once()
              ->with('dir/foo.css')
              ->andReturn('.css{}');

        $storage = new LaravelStorage('dir', $files, false);

        $this->assertEquals('<style data-inlined>.css{}</style>', $storage->css('foo'));
    }

    public function testCssWithPretend()
    {
        $files = m::mock(Filesystem::class);

        $files->shouldNotReceive('exists');
        $files->shouldNotReceive('get');

        $storage = new LaravelStorage('dir', $files, true);

        $this->assertEquals('', $storage->css('foo'));
    }

    public function testWriteCss()
    {
        $files = m::mock(Filesystem::class);

        $files->shouldReceive('put')->once()
              ->with('dir/foo%2Fbar.css', '.css{}')
              ->andReturn(true);

        $storage = new LaravelStorage('dir', $files, false);

        $this->assertTrue($storage->writeCss('foo/bar', '.css{}'));
    }

    /**
     * @expectedException \Alfheim\CriticalCss\Storage\CssWriteException
     */
    public function testWriteCssWithFail()
    {
        $files = m::mock(Filesystem::class);

        $files->shouldReceive('put')->once()
              ->with('dir/foo.css', '.css{}')
              ->andReturn(false);

        $storage = new LaravelStorage('dir', $files, false);

        $storage->writeCss('foo', '.css{}');
    }

    public function testClearCss()
    {
        $files = m::mock(Filesystem::class);

        $files->shouldReceive('deleteDirectory')->once()
              ->with('dir', true)
              ->andReturn(true);

        $storage = new LaravelStorage('dir', $files, false);

        $this->assertTrue($storage->clearCss());
    }
}
