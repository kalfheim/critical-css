<?php

namespace Alfheim\CriticalCss\Facades;

use Illuminate\Support\Facades\Facade;

class Critical extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'criticalcss.storage';
    }
}
