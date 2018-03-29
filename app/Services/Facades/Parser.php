<?php

namespace App\Services\Facades;

/**
 * @see \Illuminate\Translation\Translator
 */
class Parser extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'parser';
    }
}
