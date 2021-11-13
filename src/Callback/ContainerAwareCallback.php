<?php

/*
 * This file is part of the StateMachine package.
 *
 * (c) Alexandre Bacco
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sebdesign\SM\Callback;

use Illuminate\Contracts\Container\Container as ContainerInterface;
use SM\Callback\Callback;
use SM\Event\TransitionEvent;

class ContainerAwareCallback extends Callback
{
    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * @param  array  $specs  Specification for the callback to be called
     * @param  mixed  $callable  Closure, callable or string that will be called if specifications pass
     * @param  \Illuminate\Contracts\Container\Container  $container  The service container that will be used to resolve the callable
     */
    public function __construct(array $specs, $callable, ContainerInterface $container)
    {
        parent::__construct($specs, $callable);

        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function call(TransitionEvent $event)
    {
        // Load the services only now (when the callback is actually called)

        // Resolve 'Class@method' callables
        if ($this->isCallableWithAtSign()) {
            $this->callable = explode('@', $this->callable);
        }

        // Resolve ['Class', 'method'] callables from the container
        if (is_array($this->callable) && is_string($this->callable[0])) {
            $id = $this->callable[0];

            // If the class has no bindings in the container, call method statically.
            if (! $this->container->bound($id)) {
                return parent::call($event);
            }

            $this->callable[0] = $this->container->make($id);
        }

        return parent::call($event);
    }

    /**
     * Determine if the given string is in Class@method syntax.
     */
    protected function isCallableWithAtSign(): bool
    {
        return is_string($this->callable) && strpos($this->callable, '@') !== false;
    }
}
