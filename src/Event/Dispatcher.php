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
        $this->events[] = $eventName;

        $this->dispatcher->listen($eventName, $listener, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_string($params)) {
                $this->addListener($eventName, [$subscriber, $params]);
            } elseif (is_string($params[0])) {
                $this->addListener($eventName, [$subscriber, $params[0]], isset($params[1]) ? $params[1] : 0);
            } else {
                foreach ($params as $listener) {
                    $this->addListener($eventName, [$subscriber, $listener[0]], isset($listener[1]) ? $listener[1] : 0);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeListener($eventName, $listener)
    {
        $listeners = $this->getListeners($eventName);

        $this->dispatcher->forget($eventName);

        foreach ($listeners as $l) {
            if ($l !== $listener) {
                $this->addListener($eventName, $l);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_array($params) && is_array($params[0])) {
                foreach ($params as $listener) {
                    $this->removeListener($eventName, [$subscriber, $listener[0]]);
                }
            } else {
                $this->removeListener($eventName, [$subscriber, is_string($params) ? $params : $params[0]]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners($eventName = null)
    {
        if (! is_null($eventName)) {
            return $this->dispatcher->getListeners($eventName);
        }

        $sorted = [];

        foreach ($this->events as $eventName) {
            $sorted = array_merge($sorted, $this->dispatcher->getListeners($eventName));
        }

        return array_filter($sorted);
    }

    /**
     * {@inheritdoc}
     */
    public function getListenerPriority($eventName, $listener)
    {
        return array_search($listener, $this->getListeners($eventName), true);
    }

    /**
     * {@inheritdoc}
     */
    public function hasListeners($eventName = null)
    {
        return (bool) count($this->getListeners($eventName));
    }
}
