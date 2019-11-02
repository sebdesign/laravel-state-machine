<?php

namespace Sebdesign\SM\Test\StateMachine;

use SM\SMException;
use Sebdesign\SM\Test\Article;
use Sebdesign\SM\Test\TestCase;
use Sebdesign\SM\Metadata\MetadataStore;
use Sebdesign\SM\StateMachine\StateMachine;

class StateMachineTest extends TestCase
{
    /**
     * @test
     */
    public function it_sets_the_state()
    {
        // Arrange

        $sm = new StateMachine(
            new Article(),
            [
                'graph' => 'default',
                'states' => [
                    ['name' => 'new'],
                    ['name' => 'pending_review'],
                ],
                'transitions' => [
                    'create' => ['from' => ['new'], 'to' => 'pending_review'],
                ],
            ],
            $this->app->make('sm.event.dispatcher'),
            $this->app->make('sm.callback.factory')
        );

        // Act

        $sm->apply('create');

        // Assert

        $this->assertEquals('pending_review', $sm->getState());
    }

    /**
     * @test
     */
    public function it_cant_set_an_invalid_state()
    {
        // Arrange

        $sm = new StateMachine(
            new Article(),
            [
                'graph' => 'default',
                'states' => [
                    ['name' => 'new'],
                    ['name' => 'pending_review'],
                ],
                'transitions' => [
                    'invalid' => ['from' => ['new'], 'to' => 'invalid'],
                ],
            ],
            $this->app->make('sm.event.dispatcher'),
            $this->app->make('sm.callback.factory')
        );

        $this->expectException(SMException::class);
        $this->expectExceptionMessage('Cannot set the state to "invalid" to object "Sebdesign\SM\Test\Article" with graph default because it is not pre-defined.');

        // Act

        $sm->apply('invalid');
    }

    /**
     * @test
     */
    public function it_gets_the_metadata_store()
    {
        $sm = new StateMachine(
            new Article(),
            [
                'graph' => 'default',
            ],
            $this->app->make('sm.event.dispatcher'),
            $this->app->make('sm.callback.factory')
        );

        $metadata = $sm->metadata();

        $this->assertInstanceOf(MetadataStore::class, $metadata);
    }

    /**
     * @test
     */
    public function it_gets_metadata_from_the_graph()
    {
        $sm = new StateMachine(
            new Article(),
            [
                'graph' => 'default',
                'metadata' => ['title' => 'Graph'],
            ],
            $this->app->make('sm.event.dispatcher'),
            $this->app->make('sm.callback.factory')
        );

        $this->assertEquals(['title' => 'Graph'], $sm->metadata('graph'));
        $this->assertEquals('Graph', $sm->metadata('graph', 'title'));
        $this->assertEquals('Graph', $sm->metadata('title'));

        $this->assertNull($sm->metadata('graph', 'description'));
        $this->assertFalse($sm->metadata('graph', 'description', false));

        $this->assertNull($sm->metadata('description'));
        $this->assertFalse($sm->metadata('description', false));
    }

    /**
     * @test
     */
    public function it_gets_metadata_from_a_state()
    {
        $sm = new StateMachine(
            new Article(),
            [
                'graph' => 'default',
                'states' => [
                    ['name' => 'new', 'metadata' => ['title' => 'New']],
                    ['name' => 'pending_review'],
                ],
            ],
            $this->app->make('sm.event.dispatcher'),
            $this->app->make('sm.callback.factory')
        );

        $this->assertEquals(['title' => 'New'], $sm->metadata('state'));
        $this->assertEquals('New', $sm->metadata('state', 'title'));

        $this->assertEquals(['title' => 'New'], $sm->metadata('state', 'new'));
        $this->assertEquals('New', $sm->metadata('state', 'new', 'title'));

        $this->assertNull($sm->metadata('state', 'description'));
        $this->assertFalse($sm->metadata('state', 'description', false));

        $this->assertNull($sm->metadata('state', 'new', 'description'));
        $this->assertFalse($sm->metadata('state', 'new', 'description', false));

        $this->assertNull($sm->metadata('state', 'pending_review', 'color'));
        $this->assertFalse($sm->metadata('state', 'pending_review', 'color', false));
    }

    /**
     * @test
     */
    public function it_gets_metadata_from_the_current_state()
    {
        // Arrange

        $sm = new StateMachine(
            new Article(),
            [
                'graph' => 'default',
                'states' => [
                    ['name' => 'new', 'metadata' => ['title' => 'New']],
                ],
            ],
            $this->app->make('sm.event.dispatcher'),
            $this->app->make('sm.callback.factory')
        );

        $this->assertEquals(['title' => 'New'], $sm->metadata('state'));
        $this->assertEquals('New', $sm->metadata('state', null, 'title'));
        $this->assertEquals('New', $sm->metadata('state', 'title'));

        $this->assertNull($sm->metadata('state', null, 'description'));
        $this->assertNull($sm->metadata('state', 'description'));
        $this->assertFalse($sm->metadata('state', null, 'description', false));
    }

    /**
     * @test
     */
    public function it_gets_metadata_from_a_transition()
    {
        $sm = new StateMachine(
            new Article(),
            [
                'graph' => 'default',
                'states' => [
                    ['name' => 'new', 'metadata' => ['title' => 'New']],
                    ['name' => 'pending_review'],
                ],
                'transitions' => [
                    'create' => [
                        'from' => ['new'],
                        'to' => 'pending_review',
                        'metadata' => ['title' => 'Create'],
                    ],
                    'ask_for_changes' => [
                        'from' =>  ['pending_review', 'accepted'],
                        'to' => 'awaiting_changes',
                    ],
                ],
            ],
            $this->app->make('sm.event.dispatcher'),
            $this->app->make('sm.callback.factory')
        );

        $this->assertEquals(['title' => 'Create'], $sm->metadata('transition', 'create'));
        $this->assertEquals('Create', $sm->metadata('transition', 'create', 'title'));

        $this->assertNull($sm->metadata('transition', 'create', 'description'));
        $this->assertFalse($sm->metadata('transition', 'create', 'description', false));
        $this->assertNull($sm->metadata('transition', 'ask_for_changes', 'color'));
        $this->assertFalse($sm->metadata('transition', 'ask_for_changes', 'color', false));
    }
}
