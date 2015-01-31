<?php

namespace Fideloper\Proxy;

use Illuminate\Support\ServiceProvider;

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

        $this->publishes([$source => config_path('trustedproxy.php')]);

        $this->mergeConfigFrom($source, 'trustedproxy');
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
}
