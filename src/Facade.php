<?php

namespace Sebdesign\SM;

use Illuminate\Support\Facades\Facade as BaseFacade;

/**
 * @see \SM\Factory\Factory
 *
 * @method static \SM\StateMachine\StateMachineInterface get(object $object, string $graph = 'default')
 */
class Facade extends BaseFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'sm.factory';
    }
}
