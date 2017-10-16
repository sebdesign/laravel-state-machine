<?php

namespace Sebdesign\SM\Test\Statable;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Sebdesign\SM\Test\TestCase;
use SM\StateMachine\StateMachine;
use Sebdesign\SM\Services\StateHistoryManager;

class StatableTest extends TestCase
{
    /** @var Article */
    public $article;

    public function setUp()
    {
        parent::setUp();
        $this->app['config']->set('state-machine.graphA.class', StatableArticle::class);
        $this->app['config']->set('state-machine.graphA.callbacks.after.history.do', [StateHistoryManager::class, 'storeHistory']);

        $this->article = StatableArticle::firstOrCreate([
            'title' => 'Test Article',
            'state' => 'new'
        ]);
    }

    /**
     * @test
     */
    public function it_initiates_the_state_machine()
    {
        $this->assertInstanceOf(StateMachine::class, $this->article->stateMachine());
    }

    /**
     * @test
     */
    public function it_returns_current_state()
    {
        $this->assertEquals('new', $this->article->stateIs());
    }

    /**
     * @test
     */
    public function it_applies_transition()
    {
        $this->article->transition('create');

        $this->assertEquals('pending_review', $this->article->stateIs());

        $this->assertEquals('create', $this->article->history()->first()->transition);
    }

    /**
     * @test
     */
    public function it_saves_history_with_actor()
    {
        Auth::login(User::first());

        $this->article->transition('create');

        $this->assertEquals('create', $this->article->history()->first()->transition);

        $this->assertEquals(Auth::id(), $this->article->history()->first()->actor_id);
    }

    /**
     * @test
     */
    public function it_tests_transition_applicable()
    {
        $this->assertTrue($this->article->transitionAllowed('create'));
        $this->assertFalse($this->article->transitionAllowed('approve'));
    }

    /**
     * @test
     */
    public function it_throws_exception_if_transition_not_applicable()
    {
        $this->expectException('SM\SMException');

        $this->article->transition('approve');
    }
}
