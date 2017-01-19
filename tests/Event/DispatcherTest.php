<?php

namespace Sebdesign\SM\Test\Event;

use SM\Event\SMEvents;
use Sebdesign\SM\Test\Article;
use Sebdesign\SM\Test\TestCase;
use Symfony\Component\EventDispatcher\Event;

class DispatcherTest extends TestCase
{
    /**
     * @test
     */
    public function it_dispatches_an_event()
    {
        // Arrange

        \Event::shouldReceive('fire')->with('foo', \Mockery::type(Event::class));

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $event = $dispatcher->dispatch('foo');

        // Assert

        $this->assertInstanceOf(Event::class, $event);
    }

    /**
     * @test
     */
    public function it_adds_a_listener()
    {
        // Arrange

        $listenerA = function () {
            return 'a';
        };

        $listenerB = function () {
            return 'b';
        };

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $dispatcher->addListener('foo', $listenerA);
        $dispatcher->addListener('foo', $listenerB);

        // Assert

        $this->assertEquals([$listenerA, $listenerB], $dispatcher->getListeners('foo'));
    }

    /**
     * @test
     */
    public function it_adds_a_subscriber()
    {
        // Arrange

        $subscriber = new Subscriber();

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $dispatcher->addSubscriber($subscriber);

        // Assert

        $this->assertEquals([[$subscriber, 'fooListener']], $dispatcher->getListeners('foo'));
        $this->assertEquals([[$subscriber, 'barListener']], $dispatcher->getListeners('bar'));
        $this->assertEquals([[$subscriber, 'bazListener']], $dispatcher->getListeners('baz'));
    }

    /**
     * @test
     */
    public function it_removes_a_listener()
    {
        // Arrange

        $listenerA = function () {
            return 'a';
        };

        $listenerB = function () {
            return 'b';
        };

        $dispatcher = $this->app->make('sm.event.dispatcher');

        $dispatcher->addListener('foo', $listenerA);
        $dispatcher->addListener('foo', $listenerB);

        // Act

        $dispatcher->removeListener('foo', $listenerA);

        // Assert

        $this->assertEquals([$listenerB], $dispatcher->getListeners('foo'));
    }

    /**
     * @test
     */
    public function it_removes_a_subscriber()
    {
        // Arrange

        $subscriber = new Subscriber();

        $dispatcher = $this->app->make('sm.event.dispatcher');

        $dispatcher->addSubscriber($subscriber);

        // Act

        $dispatcher->removeSubscriber($subscriber);

        $this->assertEmpty($dispatcher->getListeners());
    }

    /**
     * @test
     */
    public function it_gets_the_listeners()
    {
        // Arrange

        $listenerA = function () {
            return 'a';
        };

        $listenerB = function () {
            return 'b';
        };

        $dispatcher = $this->app->make('sm.event.dispatcher');

        $dispatcher->addListener('foo', $listenerA, 2);
        $dispatcher->addListener('bar', $listenerB, 2);

        // Act

        $allListeners = $dispatcher->getListeners();
        $listenersA = $dispatcher->getListeners('foo');

        // Assert

        $this->assertEquals([$listenerA, $listenerB], $allListeners);
        $this->assertEquals([$listenerA], $listenersA);
    }

    /**
     * @test
     */
    public function it_gets_the_listener_priority()
    {
        // Arrange

        $listenerA = function () {
            return 'a';
        };

        $listenerB = function () {
            return 'b';
        };

        $dispatcher = $this->app->make('sm.event.dispatcher');
        $dispatcher->addListener('foo', $listenerA);
        $dispatcher->addListener('foo', $listenerB);

        // Act

        $priority = $dispatcher->getListenerPriority('foo', $listenerB);

        // Assert

        $this->assertEquals(1, $priority);
    }

    /**
     * @test
     */
    public function it_checks_if_it_has_listeners()
    {
        // Arrange

        $listener = function () {
        };

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $hasNoListeners = $dispatcher->hasListeners();

        $dispatcher->addListener('foo', $listener);
        $fooHasListeners = $dispatcher->hasListeners('foo');
        $hasListeners = $dispatcher->hasListeners();

        // Assert

        $this->assertFalse($hasNoListeners);
        $this->assertTrue($fooHasListeners);
        $this->assertTrue($hasListeners);
    }

    /**
     * @test
     */
    public function it_fires_transition_events()
    {
        // Arrange

        $this->expectsEvents([
            SMEvents::TEST_TRANSITION,
            SMEvents::PRE_TRANSITION,
            SMEvents::POST_TRANSITION,
        ]);

        $this->app['config']->set('state-machine.graphA.class', Article::class);
        $article = new Article();

        $factory = $this->app->make('sm.factory');
        $sm = $factory->get($article, 'graphA');

        // Act

        $sm->can('create');
        $sm->apply('create');
    }
}
