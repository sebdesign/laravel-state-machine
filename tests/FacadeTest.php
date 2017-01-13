<?php

namespace Sebdesign\SM\Test;

use StateMachine;
use SM\StateMachine\StateMachineInterface;

class FacadeTest extends TestCase
{
    /**
     * @test
     */
    public function it_provides_the_factory()
    {
        // Arrange

        $this->app['config']->set('state-machine.graphA.class', Article::class);
        $article = new Article();

        // Act

        $sm = StateMachine::get($article, 'graphA');

        // Assert

        $this->assertInstanceOf(StateMachineInterface::class, $sm);
    }
}
