<?php

namespace Sebdesign\SM\Test\Factory;

use ReflectionObject;
use Sebdesign\SM\Factory\Factory;
use Sebdesign\SM\StateMachine\StateMachine;
use Sebdesign\SM\Test\Article;
use Sebdesign\SM\Test\TestCase;
use SM\SMException;
use SM\StateMachine\StateMachine as BaseStateMachine;

class FactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_gets_the_default_state_machine()
    {
        // Arrange

        $factory = new Factory(
            [],
            $this->app->make('sm.event.dispatcher'),
            $this->app->make('sm.callback.factory')
        );

        // Act

        $factory->addConfig([
            'class' => Article::class,
            'states' => [],
        ]);

        $sm = $factory->get(new Article());

        // Assert

        $this->assertInstanceOf(StateMachine::class, $sm);
    }

    /**
     * @test
     */
    public function it_gets_a_specific_state_machine()
    {
        // Arrange

        $factory = new Factory(
            [],
            $this->app->make('sm.event.dispatcher'),
            $this->app->make('sm.callback.factory')
        );

        // Act

        $factory->addConfig([
            'class' => Article::class,
            'state_machine_class' => BaseStateMachine::class,
            'states' => [],
        ]);

        $sm = $factory->get(new Article());

        // Assert

        $this->assertInstanceOf(BaseStateMachine::class, $sm);
    }

    /**
     * @test
     */
    public function it_fails_when_the_state_machine_class_doesnt_exist()
    {
        // Arrange

        $factory = new Factory(
            [],
            $this->app->make('sm.event.dispatcher'),
            $this->app->make('sm.callback.factory')
        );

        $this->expectException(SMException::class);
        $this->expectExceptionMessage('Class "InvalidStateMachine" for creating a new state machine does not exist.');

        // Act

        $factory->addConfig([
            'class' => Article::class,
            'state_machine_class' => 'InvalidStateMachine',
            'states' => [],
        ]);

        $sm = $factory->get(new Article());
    }

    /**
     * @test
     */
    public function it_normalizes_the_states()
    {
        // Arrange

        $factory = new Factory(
            [],
            $this->app->make('sm.event.dispatcher'),
            $this->app->make('sm.callback.factory')
        );

        // Act

        $factory->addConfig([
            'class' => Article::class,
            'states' => [
                'new',
                ['name' => 'pending_review'],
            ],
        ]);

        // Assert

        $reflector = new ReflectionObject($factory);
        $attribute = $reflector->getProperty('configs');
        $attribute->setAccessible(true);
        $configs = $attribute->getValue($factory);
        $attribute->setAccessible(false);

        $this->assertEquals([
            ['name' => 'new'],
            ['name' => 'pending_review'],
        ], $configs[0]['states']);
    }
}
