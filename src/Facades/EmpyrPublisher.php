<?php
/**
 * Empyr Publisher Facade.
 */

namespace Linusx\Empyr\Facades;

use Exception;
use Illuminate\Support\Facades\Facade;

/**
 * Class EmpyrPublisher.
 */
class EmpyrPublisher extends Facade
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
        return new \Linusx\Empyr\Empyr(['publisher' => true]);
    }
}
