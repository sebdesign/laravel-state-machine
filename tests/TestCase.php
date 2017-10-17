<?php

namespace Sebdesign\SM\Test;


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Sebdesign\SM\Facade;
use Sebdesign\SM\ServiceProvider;

abstract class TestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    /**
     * Get package aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return ['StateMachine' => Facade::class];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application   $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
      
        $config = $app['config']->get('state-machine', []);

        $path = __DIR__.'/../config/state-machine.php';

        $app['config']->set('state-machine', array_merge(require $path, $config));

        $app['config']->set('database.default', 'sqlite');

        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
     * Set up the database.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->timestamps();
        });

        $app['db']->connection()->getSchemaBuilder()->create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('state');
            $table->timestamps();
        });

        include_once __DIR__.'/../database/migrations/create_state_history_table.php.stub';

        (new \CreateStateHistoryTable())->up();

        $user = new User;
        $user->email = 'test@user.com';
        $user->save();
    }
}
