<?php

namespace Sebdesign\SM\Test\Callback;

use Sebdesign\SM\Test\Article;
use Sebdesign\SM\Test\TestCase;
use SM\Factory\FactoryInterface;
use SM\Callback\CallbackInterface;
use Illuminate\Support\Facades\Gate;
use Sebdesign\SM\Test\ArticlePolicy;
use Sebdesign\SM\Callback\GateCallback;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;

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
        $callback = new GateCallback(['can' => 'submit_changes'], $this->app[GateContract::class]);

        $this->assertObjectHasAttribute('specs', $callback);

        $specs = $this->getObjectAttribute($callback, 'specs');

        $this->assertArraySubset([
            'can' => 'submit_changes',
            'args' => ['object'],
        ], $specs);
    }

    /**
     * @test
     */
    public function it_accepts_the_gate()
    {
        $callback = new GateCallback(['can' => 'submit_changes'], $this->app[GateContract::class]);

        $this->assertAttributeEquals($this->app[GateContract::class], 'gate', $callback);
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
