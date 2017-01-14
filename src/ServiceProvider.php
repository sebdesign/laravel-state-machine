<?php

namespace Sebdesign\SM;

use SM\Factory\Factory;
use Sebdesign\SM\Commands\Debug;
use SM\Factory\FactoryInterface;
use SM\Callback\CallbackFactoryInterface;
use SM\Callback\CascadeTransitionCallback;
use Sebdesign\SM\Callback\ContainerAwareCallback;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Sebdesign\SM\Callback\ContainerAwareCallbackFactory;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
               __DIR__.'/../config/state-machine.php' => config_path('state-machine.php'),
           ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/state-machine.php', 'state-machine');

        $this->registerCallbackFactory();
        $this->registerFactory();
        $this->registerCascadeTransitionCallback();
        $this->registerCommands();
    }

    protected function registerCallbackFactory()
    {
        $this->app->bind('sm.callback.factory', function () {
            return new ContainerAwareCallbackFactory(ContainerAwareCallback::class, $this->app);
        });

        $this->app->alias('sm.callback.factory', CallbackFactoryInterface::class);
    }

    protected function registerFactory()
    {
        $this->app->singleton('sm.factory', function () {
            return new Factory(
                $this->app['config']['state-machine'],
                new EventDispatcher(),
                $this->app->make('sm.callback.factory')
            );
        });

        $this->app->alias('sm.factory', FactoryInterface::class);
    }

    protected function registerCascadeTransitionCallback()
    {
        $this->app->bind(CascadeTransitionCallback::class, function () {
            return new CascadeTransitionCallback($this->app->make('sm.factory'));
        });
    }

    protected function registerCommands()
    {
        $this->app->bind(Debug::class, function () {
            return new Debug($this->app['config']['state-machine']);
        });

        $this->commands([
            Debug::class,
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'sm.factory',
            'sm.callback.factory',
            Debug::class,
        ];
    }
}
