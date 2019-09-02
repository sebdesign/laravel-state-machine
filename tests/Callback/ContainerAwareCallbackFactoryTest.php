<?php

namespace Sebdesign\SM\Test\Callback;

use SM\SMException;
use Sebdesign\SM\Test\TestCase;
use Sebdesign\SM\Callback\GateCallback;
use SM\Callback\CallbackFactoryInterface;
use Sebdesign\SM\Callback\ContainerAwareCallback;
use Sebdesign\SM\Callback\ContainerAwareCallbackFactory;

class ContainerAwareCallbackFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_implements_the_callback_factory_interface()
    {
        // Assert

        $this->assertContains(CallbackFactoryInterface::class, class_implements(ContainerAwareCallbackFactory::class));
    }

    /**
     * @test
     */
    public function it_accepts_the_container()
    {
        // Act

        $factory = new TestContainerAwareCallbackFactory(ContainerAwareCallback::class, $this->app);

        // Assert

        $this->assertEquals($this->app, $factory->getContainer());
    }

    /**
     * @test
     */
    public function it_throws_an_exception_on_invalid_specs()
    {
        // Arrange

        $this->expectException(SMException::class);

        $factory = new ContainerAwareCallbackFactory(ContainerAwareCallback::class, $this->app);

        // Act

        $factory->get([]);
    }

    /**
     * @test
     */
    public function it_creates_a_gate_callback()
    {
        // Arrange

        $factory = new ContainerAwareCallbackFactory(ContainerAwareCallback::class, $this->app);

        // Act

        $callback = $factory->get(['can' => 'do']);

        // Assert

        $this->assertInstanceOf(GateCallback::class, $callback);
    }
}

class TestContainerAwareCallbackFactory extends ContainerAwareCallbackFactory
{
    public function getContainer()
    {
        return $this->container;
    }
}
