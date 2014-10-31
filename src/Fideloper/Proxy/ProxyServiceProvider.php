<?php namespace Fideloper\Proxy;

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
     * Add trusted proxies from config
     * On boot, to ensure configs are loaded
     * before we attempt to find configured
     * trusted proxies
     *
     * @return void
     */
    public function boot()
    {
        $this->package('fideloper/proxy');

        $request = $this->app['request'];
        $proxies = $this->app['config']->get('proxy::proxies');

        if ($proxies === '*') {
            // Trust all proxies - proxy is whatever
            // the current client IP address is
            $proxies = array($request->getClientIp());
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
