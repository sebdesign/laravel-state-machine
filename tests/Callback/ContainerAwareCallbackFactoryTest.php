<?php

namespace Sebdesign\SM\Test\Callback;

use Sebdesign\SM\Test\TestCase;
use Sebdesign\SM\Callback\GateCallback;
use SM\Callback\CallbackFactoryInterface;
use Illuminate\Contracts\Auth\Access\Gate;
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

        $factory = new ContainerAwareCallbackFactory(ContainerAwareCallback::class, $this->app);

        // Assert

        $this->assertAttributeEquals($this->app, 'container', $factory);
    }

    /**
     * @test
     * @expectedException SM\SMException
     */
    public function it_throws_an_exception_on_invalid_specs()
    {
        // Arrange

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
