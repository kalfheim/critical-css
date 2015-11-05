<?php

namespace Krisawzm\CriticalCss\Console;

use Artisan;

class CriticalCssMake extends CriticalCssCommand
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'criticalcss:make';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Generate critical-path CSS';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        parent::handle();

        $this->removeOldCss();

        foreach ($this->getUris() as $uri) {
            $this->info(sprintf('Processing URI [%s]', $uri));

            $this->processUri($uri);
        }

        if ($this->laravel->config->get('criticalcss.clear_views')) {
            $this->info('Running view:clear');

            Artisan::call('view:clear');
        }
    }
}
