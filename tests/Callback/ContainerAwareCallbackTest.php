<?php

namespace Sebdesign\SM\Test\Callback;

use Sebdesign\SM\Callback\ContainerAwareCallback;
use Sebdesign\SM\Test\Article;
use Sebdesign\SM\Test\Service;
use Sebdesign\SM\Test\TestCase;
use SM\Event\TransitionEvent;
use SM\Factory\FactoryInterface;
use SM\Callback\CallbackInterface;

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

        $service = \Mockery::spy(Service::class);
        $this->instance(Service::class, $service);

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

        $service = \Mockery::spy(Service::class);
        $this->instance(Service::class, $service);

        // Act

        $sm = $this->app[FactoryInterface::class]->get($article, 'graphA');
        $sm->can('submit_changes');

        // Assert

        $service->shouldHaveReceived('guardOnSubmitting')->once()->with($article);
    }

    /**
     * @test
     */
    public function it_injects_the_callback_dependencies()
    {
        // Arrange

        $callable = [Service::class, 'guardOnApproving'];

        $this->app['config']->set('state-machine.graphA.class', Article::class);
        $this->app['config']->set('state-machine.graphA.callbacks.guard.guard_on_approving', [
            'on' => 'approve',
            'do' => [Service::class, 'guardOnApproving'],
        ]);

        $article = new Article('pending_review');

        $service = \Mockery::spy(Service::class);
        $this->instance(Service::class, $service);

        // Act

        $sm = $this->app[FactoryInterface::class]->get($article, 'graphA');
        $sm->can('approve');

        // Assert

        $service->shouldHaveReceived('guardOnApproving')
            ->once()
            ->with(
                $article,
                \Mockery::on(function ($event) {
                    return $event instanceof TransitionEvent
                        && $event->getTransition() == 'approve'
                        && $event->getState() == 'pending_review';
                }),
                $this->app,
                true
            );
    }
}
