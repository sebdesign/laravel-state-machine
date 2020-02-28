<?php

namespace Sebdesign\SM\Test\Commands;

use Illuminate\Contracts\Console\Kernel;
use Sebdesign\SM\Test\ConsoleHelpers;
use Sebdesign\SM\Test\TestCase;

class VisualizeTest extends TestCase
{
    use ConsoleHelpers;

    /**
     * @test
     */
    public function it_generates_an_image()
    {
        if (! `which dot`) {
            $this->markTestSkipped('Dot executable not found.');
        }

        // Arrange

        $config = $this->app['config']->get('state-machine', []);
        $command = \Mockery::spy('\Sebdesign\SM\Commands\Visualize[choice]', [$config]);

        $this->app[Kernel::class]->registerCommand($command);

        // Act

        $outputImage = tempnam(sys_get_temp_dir(), 'smv');
        $this->artisan('winzou:state-machine:visualize', [
            'graph' => 'graphA',
            '--no-interaction' => true,
            '--output' => $outputImage,
        ]);

        // Assert
        $this->withSuccessCode();

        $this->assertTrue(file_exists($outputImage));
    }
}
