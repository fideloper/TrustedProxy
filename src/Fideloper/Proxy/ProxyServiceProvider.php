<?php namespace Fideloper\Proxy;

use Illuminate\Support\ServiceProvider;

class ProxyServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$request = $this->app['request'];
		$proxies = $this->app['config']->get('proxy.proxies');

		if( $proxies === '*' )
		{
			// Trust all proxies - proxy is whatever
			// the current client IP address is
			$proxies = array( $request->getClientIp() );
		}

		$request->setTrustedProxies( $proxies );
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}