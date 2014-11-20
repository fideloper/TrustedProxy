<?php

namespace Fideloper\Proxy;

use Illuminate\Support\ServiceProvider;

class ProxyServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Add trusted proxies from config.
     *
     * We do this on boot, to ensure all the configuration has been loaded
     * before we attempt to find configured trusted proxies.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('fideloper/proxy');

        $request = $this->app['request'];
        $proxies = $this->app['config']->get('proxy::proxies');

        if ($proxies === '*') {
            // Trust all proxies
            // Accept all current client IP addresses
            $proxies = $request->getClientIps();
        }

        $request->setTrustedProxies($proxies);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // No services registered
    }
}
