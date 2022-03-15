<?php

namespace TypiCMS\Modules\Core\Facades;

use Illuminate\Support\Facades\Facade;

class Terms extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Terms';
    }
}
