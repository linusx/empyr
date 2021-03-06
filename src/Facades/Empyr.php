<?php
/**
 * Empyr Facade.
 */

namespace Linusx\Empyr\Facades;

use Exception;
use Illuminate\Support\Facades\Facade;

/**
 * Class Empyr.
 */
class Empyr extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws Exception
     */
    protected static function getFacadeAccessor()
    {
        return new \Linusx\Empyr\Empyr(false);
    }
}
