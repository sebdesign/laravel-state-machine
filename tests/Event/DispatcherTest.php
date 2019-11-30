<?php

namespace Sebdesign\SM\Test\Event;

use Exception;
use Illuminate\Support\Facades\Event as EventFacade;
use Sebdesign\SM\Event\TransitionEvent;
use Sebdesign\SM\Test\Article;
use Sebdesign\SM\Test\TestCase;
use SM\Event\SMEvents;
use Symfony\Component\EventDispatcher\Event;

class DispatcherTest extends TestCase
{
    /**
     * @test
     */
    public function it_dispatches_an_event()
    {
        // Arrange

        EventFacade::shouldReceive('dispatch')->once()->with('foo', \Mockery::type(Event::class));

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

        $this->expectException(Exception::class);

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $dispatcher->addListener('foo', function () {
        });
    }

    /**
     * @test
     */
    public function it_adds_a_subscriber()
    {
        // Arrange

        $this->expectException(Exception::class);

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $dispatcher->addSubscriber(new Subscriber());
    }

    /**
     * @test
     */
    public function it_removes_a_listener()
    {
        // Arrange

        $this->expectException(Exception::class);

        $dispatcher = $this->app->make('sm.event.dispatcher');

        $dispatcher->removeListener('foo', function () {
        });
    }

    /**
     * @test
     */
    public function it_removes_a_subscriber()
    {
        // Arrange

        $this->expectException(Exception::class);

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $dispatcher->removeSubscriber(new Subscriber());
    }

    /**
     * @test
     */
    public function it_gets_the_listeners()
    {
        // Arrange

        $this->expectException(Exception::class);

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $dispatcher->getListeners();
    }

    /**
     * @test
     */
    public function it_gets_the_listener_priority()
    {
        // Arrange

        $this->expectException(Exception::class);

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $dispatcher->getListenerPriority('foo', function () {
        });
    }

    /**
     * @test
     */
    public function it_checks_if_it_has_listeners()
    {
        // Arrange

        $this->expectException(Exception::class);

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $dispatcher->hasListeners();
    }

    /**
     * @test
     */
    public function it_dispatches_a_test_transition_event()
    {
        // Arrange

        EventFacade::fake();

        $this->app['config']->set('state-machine.graphA.class', Article::class);
        $article = new Article();

        $factory = $this->app->make('sm.factory');
        $sm = $factory->get($article, 'graphA');

        // Act

        $sm->can('create', ['foo' => 'bar']);

        EventFacade::assertDispatched(SMEvents::TEST_TRANSITION, function ($name, $event) {
            $this->assertInstanceOf(TransitionEvent::class, $event);
            $this->assertEquals(['foo' => 'bar'], $event->getContext());

            return true;
        });
    }

    /**
     * @test
     */
    public function it_dispatches_transition_events_before_and_after()
    {
        // Arrange

        EventFacade::fake();

        $this->app['config']->set('state-machine.graphA.class', Article::class);
        $article = new Article();

        $factory = $this->app->make('sm.factory');
        $sm = $factory->get($article, 'graphA');

        // Act

        $sm->apply('create', false, ['foo' => 'bar']);

        EventFacade::assertDispatched(SMEvents::TEST_TRANSITION, function ($name, $event) {
            $this->assertInstanceOf(TransitionEvent::class, $event);
            $this->assertEquals(['foo' => 'bar'], $event->getContext());

            return true;
        });

        EventFacade::assertDispatched(SMEvents::PRE_TRANSITION, function ($name, $event) {
            $this->assertInstanceOf(TransitionEvent::class, $event);
            $this->assertEquals(['foo' => 'bar'], $event->getContext());

            return true;
        });

        EventFacade::assertDispatched(SMEvents::POST_TRANSITION, function ($name, $event) {
            $this->assertInstanceOf(TransitionEvent::class, $event);
            $this->assertEquals(['foo' => 'bar'], $event->getContext());

            return true;
        });
    }
}
