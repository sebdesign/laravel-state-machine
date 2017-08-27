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
     * @expectedException Exception
     */
    public function it_adds_a_listener()
    {
        // Arrange

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $dispatcher->addListener('foo', function () {});
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_adds_a_subscriber()
    {
        // Arrange

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $dispatcher->addSubscriber(new Subscriber());
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_removes_a_listener()
    {
        // Arrange

        $dispatcher = $this->app->make('sm.event.dispatcher');

        $dispatcher->removeListener('foo', function () {});
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_removes_a_subscriber()
    {
        // Arrange

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $dispatcher->removeSubscriber(new Subscriber());
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_gets_the_listeners()
    {
        // Arrange

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $dispatcher->getListeners();
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_gets_the_listener_priority()
    {
        // Arrange

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $dispatcher->getListenerPriority('foo', function () {});
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_checks_if_it_has_listeners()
    {
        // Arrange

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $dispatcher->hasListeners();
    }

    /**
     * @test
     */
    public function it_fires_transition_events()
    {
        // Arrange

        \Event::fake();

        $this->app['config']->set('state-machine.graphA.class', Article::class);
        $article = new Article();

        $factory = $this->app->make('sm.factory');
        $sm = $factory->get($article, 'graphA');

        // Act

        $sm->can('create');

        \Event::assertDispatched(SMEvents::TEST_TRANSITION);

        $sm->apply('create');

        \Event::assertDispatched(SMEvents::PRE_TRANSITION);
        \Event::assertDispatched(SMEvents::POST_TRANSITION);
    }
}
