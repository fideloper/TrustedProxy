<?php  namespace Fideloper\Proxy;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\Foundation\Application;

class TrustProxies implements Middleware {

    /**
     * The application implementation.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * Create a new filter instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Contracts\Config\Repository      $config
     */
    public function __construct(Application $app, Repository $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws HttpException
     */
    public function handle($request, Closure $next)
    {
        $trustedProxies = $this->config->get('trusted_proxy.proxies');

        $trustedHeaderNames = $this->config->get('trusted_proxy.proxies');
        $trustedHeaderNames = (is_array($trustedHeaderNames)) ? $trustedHeaderNames : [];

        // Set trusted header names
        foreach( $trustedHeaderNames as $symfonyHeaderKey => $headerName )
        {
            $request->setTrustedHeaderName($symfonyHeaderKey, $headerName);
        }

        // Set trusted proxies
        $request->setTrustedProxies($trustedProxies);

        return $next($request);
    }

}