<?php

namespace Krisawzm\CriticalCss\Console;

use Illuminate\Console\Command;

abstract class CriticalCssCommand extends Command
{
    /** @var \Illuminate\Filesystem\Filesystem */
    protected $filesystem;

    /** @var string */
    protected $storagePath;

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        if (!$this->laravel->isLocal()) {
            $this->error('Critical CSS can not be generated in production.');
            return 1;
        }

        $this->setupFilesystem();

        $this->setupStorage();
    }

    /**
     * Set up the file system.
     *
     * @return void
     */
    protected function setupFilesystem()
    {
        $this->filesystem = $this->laravel->make('files');
    }

    /**
     * Gets the storage path from the config and makes sure it exists.
     *
     * @return void
     */
    protected function setupStorage()
    {
        $this->storagePath = $this->laravel->config->get('criticalcss.storage');

        if (!$this->filesystem->exists($this->storagePath)) {
            $this->filesystem->makeDirectory($this->storagePath);
        }
    }

    /**
     * Remove old critical-path CSS from the storage.
     *
     * @return bool
     */
    protected function removeOldCss()
    {
        $this->info('Removing old critical-path CSS.');

        return $this->filesystem->deleteDirectory($this->storagePath, true);
    }

    /**
     * Returns a list of URIs to generate critical-path CSS for.
     *
     * @return array
     */
    protected function getUris()
    {
        $uris = $this->laravel['config']->get('criticalcss.routes');

        // If null, return all 'GET' routes.
        if (is_null($uris)) {
            $uris = [];
            $router = $this->laravel['router'];

            foreach ($router->getRoutes() as $route) {
                if ($route->getMethods()[0] === 'GET') {
                    $uris[] = $route->getUri();
                }
            }
        }

        return $uris;
    }

    /**
     * Process a given URI.
     *
     * @param  string $uri
     *
     * @return bool
     */
    protected function processUri($uri)
    {
        $cssGenerator = $this->laravel['criticalcss.cssgenerator'];

        $css = $cssGenerator->generate($uri);

        return $this->filesystem->put(
            $this->storagePath.'/'.urlencode($uri).'.css',
            $css
        );
    }
}
