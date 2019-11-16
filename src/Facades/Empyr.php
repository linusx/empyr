<?php

namespace Linusx\Empyr\Facades;

use Illuminate\Support\Facades\Facade;

class Empyr extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @throws \Exception
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return new \Linusx\Empyr\Empyr( false );
    }
}
