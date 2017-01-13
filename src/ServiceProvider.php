<?php

namespace Sebdesign\SM;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Sebdesign\SM\Callback\ContainerAwareCallback;
use Sebdesign\SM\Callback\ContainerAwareCallbackFactory;
use Sebdesign\SM\Commands\Debug;
use SM\Callback\CallbackFactoryInterface;
use SM\Callback\CascadeTransitionCallback;
use SM\Factory\Factory;
use SM\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

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
        $this->registerCascadeTransitionCallback();
        $this->registerFactory();
        $this->registerCommands();
    }

    protected function registerCallbackFactory()
    {
        $this->app->bind(CallbackFactoryInterface::class, function () {
            return new ContainerAwareCallbackFactory(ContainerAwareCallback::class, $this->app);
        });
    }

    protected function registerCascadeTransitionCallback()
    {
        $this->app->bind(CascadeTransitionCallback::class, function () {
            return new CascadeTransitionCallback($this->app->make(FactoryInterface::class));
        });
    }

    protected function registerFactory()
    {
        $this->app->singleton(FactoryInterface::class, function () {
            return new Factory(
                $this->app['config']['state-machine'],
                new EventDispatcher(),
                $this->app->make(CallbackFactoryInterface::class)
            );
        });

        $this->app->alias(FactoryInterface::class, 'sm.factory');
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
            FactoryInterface::class,
            Debug::class,
        ];
    }
}
