<?php

namespace Sebdesign\SM\Test\Statable;

use Sebdesign\SM\Services\StateHistoryManager;
use Sebdesign\SM\Test\TestCase;
use SM\StateMachine\StateMachine;

class StatableTest extends TestCase
{
    /** @var  StatableArticle */
    public $article;

    protected function setUp()
    {
        parent::setUp();
        $this->app['config']->set('state-machine.graphA.class', StatableArticle::class);
        $this->app['config']->set('state-machine.graphA.callbacks.after.history.do', [StateHistoryManager::class, 'storeHistory']);

        $this->article = new StatableArticle('new', 2);
    }
    /**
     * @test
     */
    public function testInstantiateSM()
    {
        $this->assertInstanceOf(StateMachine::class, $this->article->stateMachine());
    }

    public function testReturnCurrentState()
    {
        $this->assertEquals('new', $this->article->stateIs());
    }

    public function testTransition()
    {
        $articleStateMock = \Mockery::mock(ArticleState::class);
        $articleStateMock->shouldReceive('create')->once()->with([
            'article_id' => 2,
            'transition' => 'create',
            'to' => 'pending_review',
            'user_id' => null
        ]);
        $articleStateMock->shouldReceive('where');

        $this->app->bind(ArticleState::class, function () use ($articleStateMock) {
            return $articleStateMock;
        });

        $this->article->transition('create');

        $this->assertEquals('pending_review', $this->article->stateIs());
    }

    public function testTransitionAllowed()
    {
        $this->assertTrue($this->article->transitionAllowed('create'));
        $this->assertFalse($this->article->transitionAllowed('approve'));
    }

    public function testInvalidTransition()
    {
        $this->expectException('SM\SMException');

        $this->article->transition('approve');
    }
}
