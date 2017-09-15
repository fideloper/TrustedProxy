<?php

namespace Fideloper\Proxy;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;

class TrustedProxyServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $source = realpath(__DIR__.'/../config/trustedproxy.php');

        $this->registerPublishing($source);
        $this->configure($source);
        $this->registerRoutes();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }


    /**
     * Register published assets / config
     * @param  [type] $source [description]
     * @return [type]         [description]
     */
    protected function registerPublishing($source)
    {
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('trustedproxy.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('trustedproxy');
        }
    }

    /**
     * Configure TrustedProxy
     * @param  [type] $source [description]
     * @return [type]         [description]
     */
    protected function configure($source)
    {
        $this->mergeConfigFrom($source, 'trustedproxy');
    }

    /**
     * Register Proxy routes
     * @return [type] [description]
     */
    protected function registerRoutes()
    {
        Route::group([
            'prefix' => 'trustedproxy',
            'namespace' => 'Fideloper\Proxy\Http\Controllers',
            'middleware' => 'web',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    /**
     * Register the Proxy resources.
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'proxy');
    }
}
