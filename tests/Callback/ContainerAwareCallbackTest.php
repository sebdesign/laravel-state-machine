<?php

namespace Sebdesign\SM\Test\Callback;

use Sebdesign\SM\Callback\ContainerAwareCallback;
use Sebdesign\SM\Test\Article;
use Sebdesign\SM\Test\Service;
use Sebdesign\SM\Test\TestCase;
use SM\Callback\CallbackInterface;
use SM\Factory\FactoryInterface;

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

        $callback = new ContainerAwareCallback([], function () {
        }, $this->app);

        // Assert

        $this->assertAttributeEquals($this->app, 'container', $callback);
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

        $this->app->singleton(Service::class, new Service());
        $service = \Mockery::spy(Service::class);
        $this->app->instance(Service::class, $service);

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

        $this->app->singleton(Service::class, new Service());
        $service = \Mockery::spy(Service::class);
        $this->app->instance(Service::class, $service);

        // Act

        $sm = $this->app[FactoryInterface::class]->get($article, 'graphA');
        $sm->can('submit_changes');

        // Assert

        $service->shouldHaveReceived('guardOnSubmitting')->once()->with($article);
    }
}
