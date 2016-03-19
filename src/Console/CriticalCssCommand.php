<?php

namespace Alfheim\CriticalCss\Console;

use Illuminate\Console\Command;

abstract class CriticalCssCommand extends Command
{
    /** @var \Alfheim\CriticalCss\Storage\StorageInterface */
    protected $storage;

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $this->setupStorage();

        $this->removeOldCss();
    }

    /**
     * Set up the storage.
     *
     * @return void
     */
    protected function setupStorage()
    {
        $this->storage = $this->laravel->make('criticalcss.storage');

        $this->storage->validateStoragePath();
    }

    /**
     * Remove old critical-path CSS.
     *
     * @return void
     */
    protected function removeOldCss()
    {
        $this->info('Removing old critical-path CSS.');

        $this->storage->clearCss();
    }
}
