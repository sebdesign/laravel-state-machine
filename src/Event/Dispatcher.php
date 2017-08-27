<?php

namespace Sebdesign\SM\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

class Dispatcher implements EventDispatcherInterface
{
    /**
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * @var array
     */
    protected $events = [];

    /**
     * @param \Illuminate\Contracts\Events\Dispatcher $dispatcher
     */
    public function __construct(DispatcherContract $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, Event $event = null)
    {
        if (is_null($event)) {
            $event = new Event();
        }

        $this->dispatcher->fire($eventName, $event);

        return $event;
    }

    /**
     * {@inheritdoc}
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        throw new \Exception('Please use `Event::listen()`.');
    }

    /**
     * {@inheritdoc}
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        throw new \Exception('Please use `Event::subscribe()`.');
    }

    /**
     * {@inheritdoc}
     */
    public function removeListener($eventName, $listener)
    {
        throw new \Exception('Please use `Event::forget()`.');
    }

    /**
     * {@inheritdoc}
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        throw new \Exception('Removing event subscribers is not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners($eventName = null)
    {
        throw new \Exception('Please use `Event::getListeners()`.');
    }

    /**
     * {@inheritdoc}
     */
    public function getListenerPriority($eventName, $listener)
    {
        throw new \Exception('Event priority is not supported anymore in Laravel.');
    }

    /**
     * {@inheritdoc}
     */
    public function hasListeners($eventName = null)
    {
        throw new \Exception('Please use `Event::hasListeners()`.');
    }
}
