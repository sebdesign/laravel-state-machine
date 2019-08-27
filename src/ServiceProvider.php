<?php

namespace Sebdesign\SM;

use Sebdesign\SM\Commands\Debug;
use SM\Factory\FactoryInterface;
use Sebdesign\SM\Factory\Factory;
use Sebdesign\SM\Event\Dispatcher;
use SM\Callback\CallbackFactoryInterface;
use SM\Callback\CascadeTransitionCallback;
use Sebdesign\SM\Callback\ContainerAwareCallback;
use Laravel\Lumen\Application as LumenApplication;
use Sebdesign\SM\Callback\ContainerAwareCallbackFactory;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
            if ($this->app instanceof LaravelApplication) {
                $this->publishes([
                   __DIR__.'/../config/state-machine.php' => config_path('state-machine.php'),
               ], 'config');
            } elseif ($this->app instanceof LumenApplication) {
                $this->app->configure('state-machine');
            }
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->registerCallbackFactory();
        $this->registerEventDispatcher();
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

    protected function registerEventDispatcher()
    {
        $this->app->bind('sm.event.dispatcher', function () {
            return new Dispatcher($this->app->make('events'));
        });

        $this->app->alias('sm.event.dispatcher', EventDispatcherInterface::class);
    }

    protected function registerFactory()
    {
        $this->app->singleton('sm.factory', function () {
            return new Factory(
                $this->app->make('config')->get('state-machine'),
                $this->app->make('sm.event.dispatcher'),
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
            return new Debug($this->app->make('config')->get('state-machine', []));
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
            'sm.callback.factory',
            'sm.event.dispatcher',
            'sm.factory',
            Debug::class,
        ];
    }
}
