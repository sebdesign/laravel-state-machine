<?php

namespace Sebdesign\SM\Test\Callback;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Support\Facades\Gate;
use Sebdesign\SM\Callback\GateCallback;
use Sebdesign\SM\Test\Article;
use Sebdesign\SM\Test\ArticlePolicy;
use Sebdesign\SM\Test\TestCase;
use SM\Callback\CallbackInterface;
use SM\Factory\FactoryInterface;

class GateCallbackTest extends TestCase
{
    /**
     * @test
     */
    public function it_implements_the_callback_interface()
    {
        // Assert

        $this->assertContains(CallbackInterface::class, class_implements(GateCallback::class));
    }

    /**
     * @test
     */
    public function it_accepts_the_specs()
    {
        $callback = new TestGateCallback(['can' => 'submit_changes'], $this->app[GateContract::class]);

        $specs = $callback->getSpecs();

        $this->assertEquals('submit_changes', $specs['can']);
        $this->assertEquals(['object'], $specs['args']);
    }

    /**
     * @test
     */
    public function it_accepts_the_gate()
    {
        $callback = new TestGateCallback(['can' => 'submit_changes'], $this->app[GateContract::class]);

        $this->assertEquals($this->app[GateContract::class], $callback->getGate());
    }

    /**
     * @test
     */
    public function it_checks_the_gate()
    {
        // Arrange

        $article = new Article();

        Gate::policy(Article::class, ArticlePolicy::class);

        $this->actingAs(new \Illuminate\Foundation\Auth\User());

        $callback = new GateCallback(['can' => 'submit-changes'], $this->app[GateContract::class]);

        // Act

        $condition = $callback->check($article, 'foo');

        // Assert

        $this->assertTrue($condition);
    }

    /**
     * @test
     */
    public function it_creates_a_callback_that_uses_the_gate()
    {
        // Arrange

        $this->app['config']->set('state-machine.graphA.class', Article::class);

        $article = new Article('pending_review');

        $sm = $this->app[FactoryInterface::class]->get($article, 'graphA');

        Gate::define('approve', function ($user, $object) use ($article) {
            $this->assertEquals($article, $object);

            return true;
        });

        $this->actingAs(new \Illuminate\Foundation\Auth\User());

        // Act

        $condition = $sm->can('approve');

        // Assert

        $this->assertTrue($condition);
    }
}

class TestGateCallback extends GateCallback
{
    public function getSpecs()
    {
        return $this->specs;
    }

    public function getGate()
    {
        return $this->gate;
    }
}
