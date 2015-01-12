<?php

namespace Fideloper\Proxy;

use Illuminate\Support\ServiceProvider;

class ProxyServiceProvider extends ServiceProvider
{
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
        $proxies = $this->app->config->get('proxy.proxies');

        if ($proxies === '*') {
            // Trust all proxies
            // Accept all current client IP addresses
            $proxies = $this->app->request->getClientIps();
        }

        $this->app->request->setTrustedProxies($proxies);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // set the default config
        $this->app->config->set('proxy.proxies', '*');
    }
}
