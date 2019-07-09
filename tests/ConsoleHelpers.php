<?php

namespace Sebdesign\SM\Test;

use Illuminate\Contracts\Console\Kernel;

trait ConsoleHelpers
{
    /**
     * The output from the last command.
     *
     * @var string
     */
    protected $output;

    /**
     * Call artisan command and return code.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return int
     */
    public function artisan($command, $parameters = [])
    {
        $this->code = $this->app[Kernel::class]->call($command, $parameters);
        $this->output = $this->app[Kernel::class]->output();

        return $this->code;
    }

    /**
     * Assert that the given string is seen in the console output.
     *
     * @param  string  $text
     * @return $this
     */
    protected function seeInConsole($text)
    {
        $this->assertContains($text, $this->output);

        return $this;
    }

    /**
     * Assert that the given string is not seen in the console output.
     *
     * @param  string  $text
     * @return $this
     */
    protected function dontSeeInConsole($text)
    {
        $this->assertNotContains($text, $this->output);

        return $this;
    }

    /**
     * Assert that the command returned with a success code.
     *
     * @param  int  $code
     * @return $this
     */
    protected function withSuccessCode($code = 0)
    {
        return $this->withExitCode($code);
    }

    /**
     * Assert that the command didn't returned with a success code.
     *
     * @param  int  $code
     * @return $this
     */
    protected function withoutSuccessCode($code = 0)
    {
        return $this->withoutExitCode($code);
    }

    /**
     * Assert that the command returned with the given code.
     *
     * @param  int  $code
     * @return $this
     */
    protected function withExitCode($code)
    {
        $this->assertEquals($code, $this->code, "Exit code should be {$code}.");

        return $this;
    }

    /**
     * Assert that the command returned without the given code.
     *
     * @param  int  $code
     * @return $this
     */
    protected function withoutExitCode($code)
    {
        $this->assertNotEquals($code, $this->code, "Exit code shouldn't be {$code}.");

        return $this;
    }

    /**
     * Dump the output from the last command.
     *
     * @return $this
     */
    protected function dumpConsole()
    {
        dump($this->output);

        return $this;
    }
}
