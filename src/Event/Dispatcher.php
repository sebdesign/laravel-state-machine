<?php

namespace Sebdesign\SM\Event;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Dispatcher implements EventDispatcherInterface
{
    /**
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * @param  \Illuminate\Contracts\Events\Dispatcher  $dispatcher
     */
    public function __construct(DispatcherContract $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(object $event, ?string $eventName = null): object
    {
        if (is_null($eventName)) {
            $this->dispatcher->dispatch($event);
        } else {
            $this->dispatcher->dispatch($eventName, $event);
        }

        return $event;
    }

    /**
     * {@inheritdoc}
     */
    public function addListener(string $eventName, callable $listener, int $priority = 0): void
    {
        throw new \Exception('Please use `Event::listen()`.');
    }

    /**
     * {@inheritdoc}
     */
    public function addSubscriber(EventSubscriberInterface $subscriber): void
    {
        throw new \Exception('Please use `Event::subscribe()`.');
    }

    /**
     * {@inheritdoc}
     */
    public function removeListener(string $eventName, callable $listener): void
    {
        throw new \Exception('Please use `Event::forget()`.');
    }

    /**
     * {@inheritdoc}
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber): void
    {
        throw new \Exception('Removing event subscribers is not supported');
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners(?string $eventName = null): array
    {
        throw new \Exception('Please use `Event::getListeners()`.');
    }

    /**
     * {@inheritdoc}
     */
    public function getListenerPriority(string $eventName, $listener): ?int
    {
        throw new \Exception('Event priority is not supported anymore in Laravel.');
    }

    /**
     * {@inheritdoc}
     */
    public function hasListeners(?string $eventName = null): bool
    {
        throw new \Exception('Please use `Event::hasListeners()`.');
    }
}
