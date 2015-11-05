<?php

namespace Krisawzm\CriticalCss\Console;

class CriticalCssClear extends CriticalCssCommand
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'criticalcss:clear';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Clear critical-path CSS';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        parent::handle();

        $this->removeOldCss();
    }
}
