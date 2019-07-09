<?php

namespace Sebdesign\SM\Test;

use Sebdesign\SM\Facade;

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

        Facade::shouldReceive('get')->once()->with($article, 'graphA');

        // Act

        Facade::get($article, 'graphA');
    }
}
