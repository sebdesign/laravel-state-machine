<?php

namespace Sebdesign\SM\Test;

use Sebdesign\SM\ServiceProvider;
use Sebdesign\SM\Commands\Debug;
use Sebdesign\SM\Callback\ContainerAwareCallbackFactory;
use Sebdesign\SM\Callback\ContainerAwareCallback;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use SM\Callback\CallbackFactoryInterface;
use SM\Callback\CascadeTransitionCallback;

class ServiceProviderTest extends TestCase
{
    /**
     * @test
     */
    public function the_configuration_is_published()
    {
        // Act

        $this->registerServiceProvider();

        // Assert

        $path = $this->getConfigurationPath();

        $this->assertFileExists($path);
    }

    /**
     * @test
     */
    public function the_configuration_is_merged()
    {
        // Act

        $config = require $this->getConfigurationPath();

        // Assert

        // Load the configuration from the file

        $this->assertEquals($this->app['config']['state-machine'], $config);
    }

    /**
     * @test
     */
    public function is_deferred()
    {
        // Act

        $provider = new ServiceProvider($this->app);

        // Assert

        $this->assertTrue($provider->isDeferred());
    }

    /**
     * @test
     */
    public function the_callback_factory_is_registered()
    {
        // Act

        $factory = $this->app[CallbackFactoryInterface::class];
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

        $factory = $this->app[FactoryInterface::class];
        $sm = $factory->get($article, 'graphA');

        // Assert

        $this->assertInstanceOf(FactoryInterface::class, $factory);
        $this->assertInstanceOf(StateMachineInterface::class, $sm);
    }

    /**
     * @test
     */
    public function the_debug_command_is_registered()
    {
        // Arrange

        $this->registerServiceProvider();

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

        $this->assertContains(FactoryInterface::class, $provider->provides());
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
