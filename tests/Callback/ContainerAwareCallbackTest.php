<?php

namespace Sebdesign\SM\Test\Callback;

use Sebdesign\SM\Test\Article;
use Sebdesign\SM\Test\Service;
use Sebdesign\SM\Test\TestCase;
use SM\Factory\FactoryInterface;
use SM\Callback\CallbackInterface;
use Sebdesign\SM\Callback\ContainerAwareCallback;

class ContainerAwareCallbackTest extends TestCase
{
    /**
     * @test
     */
    public function it_implements_the_callback_interface()
    {
        // Assert

        $this->assertContains(CallbackInterface::class, class_implements(ContainerAwareCallback::class));
    }

    /**
     * @test
     */
    public function it_accepts_the_container()
    {
        // Act

        $callback = new TestContainerAwareCallback([], function () {
        }, $this->app);

        // Assert

        $this->assertEquals($this->app, $callback->getContainer());
    }

    /**
     * @test
     */
    public function it_resolves_services_from_the_container()
    {
        // Arrange

        $callable = [Service::class, 'guardOnSubmitting'];

        $this->app['config']->set('state-machine.graphA.class', Article::class);
        $this->app['config']->set('state-machine.graphA.callbacks.guard.guard_on_submitting.do', $callable);

        $article = new Article('awaiting_changes');

        $this->app->instance(Service::class, $service = \Mockery::spy(Service::class));

        // Act

        $sm = $this->app[FactoryInterface::class]->get($article, 'graphA');
        $sm->can('submit_changes');

        // Assert

        $service->shouldHaveReceived('guardOnSubmitting')->once()->with($article);
    }

    /**
     * @test
     */
    public function it_accepts_callable_strings_with_at_sign()
    {
        // Arrange

        $callable = 'Sebdesign\SM\Test\Service@guardOnSubmitting';

        $this->app['config']->set('state-machine.graphA.class', Article::class);
        $this->app['config']->set('state-machine.graphA.callbacks.guard.guard_on_submitting.do', $callable);

        $article = new Article('awaiting_changes');

        $this->app->instance(Service::class, $service = \Mockery::spy(Service::class));

        // Act

        $sm = $this->app[FactoryInterface::class]->get($article, 'graphA');
        $sm->can('submit_changes');

        // Assert

        $service->shouldHaveReceived('guardOnSubmitting')->once()->with($article);
    }

    /**
     * @test
     */
    public function it_calls_methods_statically()
    {
        // Arrange

        $callable = [Service::class, 'guardApproval'];

        $this->app['config']->set('state-machine.graphA.class', Article::class);
        $this->app['config']->set('state-machine.graphA.callbacks.guard.guard_on_approving', [
            'on' => 'approve',
            'do' => $callable,
        ]);

        $article = new Article('pending_review');

        // Act

        $sm = $this->app[FactoryInterface::class]->get($article, 'graphA');
        $result = $sm->can('approve');

        // Assert

        $this->assertTrue($result);
    }
}

class TestContainerAwareCallback extends ContainerAwareCallback
{
    public function getContainer()
    {
        return $this->container;
    }
}
