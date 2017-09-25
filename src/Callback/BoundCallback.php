<?php

namespace Sebdesign\SM\Callback;

use Illuminate\Container\BoundMethod;
use SM\Event\TransitionEvent;

class BoundCallback extends BoundMethod
{
    /**
     * {@inheritdoc}
     */
    protected static function addDependencyForCallParameter($container, $parameter,
                                                            array &$parameters, &$dependencies)
    {
        // If the call parameter is a type-hinted object,
        // add the dependency from the first parameter
        // that matches the type-hint.
        if ($parameter->getClass()) {
            foreach ($parameters as $key => $value) {
                if ($parameter->getClass()->isInstance($value)) {
                    $dependencies[] = $value;
                    unset($parameters[$key]);
                    return;
                }
            }
        }

        parent::addDependencyForCallParameter($container, $parameter, $parameters, $dependencies);
    }
}
