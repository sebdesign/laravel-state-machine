<?php

namespace Sebdesign\SM\Test;

use StateMachine;

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

        StateMachine::shouldReceive('get')->with($article, 'graphA');

        // Act

        StateMachine::get($article, 'graphA');
    }
}
