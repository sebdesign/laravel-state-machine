<?php

namespace Sebdesign\SM\Test\Callback;

use SM\SMException;
use Sebdesign\SM\Test\TestCase;
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

        $factory = new ContainerAwareCallbackFactory(ContainerAwareCallback::class, $this->app);

        // Assert

        $this->assertAttributeEquals($this->app, 'container', $factory);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_on_invalid_specs()
    {
        // Arrange

        $factory = new ContainerAwareCallbackFactory(ContainerAwareCallback::class, $this->app);
        $this->setExpectedException(SMException::class);

        // Act

        $factory->get([]);
    }
}
