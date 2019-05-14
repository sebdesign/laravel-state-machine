<?php

namespace Sebdesign\SM\Test;

use Sebdesign\SM\Commands\Debug;
use SM\Factory\FactoryInterface;
use Sebdesign\SM\Factory\Factory;
use Sebdesign\SM\ServiceProvider;
use SM\Callback\CascadeTransitionCallback;
use SM\StateMachine\StateMachineInterface;
use Sebdesign\SM\StateMachine\StateMachine;
use Sebdesign\SM\Callback\ContainerAwareCallback;
use Sebdesign\SM\Callback\ContainerAwareCallbackFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ServiceProviderTest extends TestCase
{
    /**
     * @test
     */
    public function the_configuration_is_published()
    {
        // Assert

        $path = key(ServiceProvider::pathsToPublish(null, 'config'));

        $this->assertFileExists($path);
    }

    /**
     * @test
     */
    public function is_not_deferred()
    {
        // Act

        $provider = new ServiceProvider($this->app);

        // Assert

        $this->assertFalse($provider->isDeferred());
    }

    /**
     * @test
     */
    public function the_callback_factory_is_registered()
    {
        // Act

        $factory = $this->app->make('sm.callback.factory');
        $callback = $factory->get([
            'do' => function () {
            },
        ]);

        // Assert

        $this->assertInstanceOf(ContainerAwareCallbackFactory::class, $factory);
        $this->assertInstanceOf(ContainerAwareCallback::class, $callback);
    }

    /**
     * @test
     */
    public function the_event_dispatcher_is_registered()
    {
        // Act

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Assert

        $this->assertInstanceOf(EventDispatcherInterface::class, $dispatcher);
    }

    /**
     * @test
     */
    public function the_cascade_transition_callback_is_registered()
    {
        // Act

        $callback = $this->app[CascadeTransitionCallback::class];

        // Assert

        $this->assertInstanceOf(CascadeTransitionCallback::class, $callback);
    }

    /**
     * @test
     */
    public function the_factory_is_registered()
    {
        // Arrange

        $this->app['config']->set('state-machine.graphA.class', Article::class);
        $article = new Article();

        // Act

        $factory = $this->app->make('sm.factory');
        $sm = $factory->get($article, 'graphA');

        // Assert

        $this->assertInstanceOf(FactoryInterface::class, $factory);
        $this->assertInstanceOf(Factory::class, $factory);
        $this->assertInstanceOf(StateMachineInterface::class, $sm);
        $this->assertInstanceOf(StateMachine::class, $sm);
    }

    /**
     * @test
     */
    public function the_debug_command_is_registered()
    {
        // Act

        $command = $this->app[Debug::class];

        // Assert

        $this->assertInstanceOf(Debug::class, $command);
    }

    /**
     * @test
     */
    public function it_provides_the_factory()
    {
        $provider = new ServiceProvider($this->app);

        $this->assertContains('sm.factory', $provider->provides());
    }

    /**
     * @test
     */
    public function it_provides_the_debug_command()
    {
        $provider = new ServiceProvider($this->app);

        $this->assertContains(Debug::class, $provider->provides());
    }

    /**
     * Get the path of the configuration file to be published.
     *
     * @return string
     */
    protected function getConfigurationPath()
    {
        return key(ServiceProvider::pathsToPublish(null, 'config'));
    }
}
