<?php

namespace Sebdesign\SM\Callback;

use Illuminate\Contracts\Container\Container;
use SM\Callback\Callback;
use SM\Event\TransitionEvent;

class ContainerAwareCallback extends Callback
{
    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * @param array                                     $specs      Specification for the callback to be called
     * @param mixed                                     $callable   Closure, callable or string that will be called if specifications pass
     * @param \Illuminate\Contracts\Container\Container $container  The service container that will be used to resolve the callable
     */
    public function __construct(array $specs, $callable, Container $container)
    {
        parent::__construct($specs, $callable);

        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function call(TransitionEvent $event)
    {
        if (isset($this->specs['args'])) {
            return parent::call($event);
        }

        $callable = $this->filterCallable($this->callable, $event);

        return BoundCallback::call($this->container, $callable, [
            'event' => $event,
            $event->getStateMachine()->getObject(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function filterCallable($callable, TransitionEvent $event)
    {
        if ($this->isCallableWithAtSign($callable)) {
            $callable = explode('@', $callable);
        }

        $callable = parent::filterCallable($callable, $event);

        if (is_array($callable) && is_string($callable[0])) {
            return [$this->container->make($callable[0]), $callable[1]];
        }

        return $callable;
    }

    /**
     * Determine if the given string is in Class@method syntax.
     *
     * @param  mixed  $callback
     * @return bool
     */
    protected function isCallableWithAtSign($callback)
    {
        return is_string($callback) && strpos($callback, '@') !== false;
    }
}
