<?php

namespace Sebdesign\SM\Test\Statable;

use Sebdesign\SM\Test\TestCase;
use SM\StateMachine\StateMachine;
use Sebdesign\SM\Services\StateHistoryManager;

class StatableTest extends TestCase
{
    /** @var StatableArticle */
    public $article;

    public function setUp()
    {
        parent::setUp();
        $this->app['config']->set('state-machine.graphA.class', StatableArticle::class);
        $this->app['config']->set('state-machine.graphA.callbacks.after.history.do', [StateHistoryManager::class, 'storeHistory']);

        $this->article = new StatableArticle('new', 2);
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
        $articleStateMock = \Mockery::mock(ArticleState::class);
        $articleStateMock->shouldReceive('create')->once()->with([
            'article_id' => 2,
            'transition' => 'create',
            'to' => 'pending_review',
            'user_id' => null,
        ]);
        $articleStateMock->shouldReceive('where');

        $this->app->bind(ArticleState::class, function () use ($articleStateMock) {
            return $articleStateMock;
        });

        $this->article->transition('create');

        $this->assertEquals('pending_review', $this->article->stateIs());
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
