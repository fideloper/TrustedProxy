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
        // Set trusted header names
        foreach( $this->getTrustedHeaders() as $headerKey => $headerName )
        {
            $request->setTrustedHeaderName($headerKey, $headerName);
        }

        $request->setTrustedProxies($this->getTrustedProxies($request->getClientIps()));

        return $next($request);
    }

    /**
     * Return an array of trusted proxy IP addresses
     *
     * @param array $clientIpAddresses Array of client IP addresses retrieved
     *                                 *prior* to setting trusted proxy
     * @return array
     */
    protected function getTrustedProxies(array $clientIpAddresses=[])
    {
        $trustedProxies = $this->config->get('trusted-proxy.proxies');

        // To trust all proxies,
        // we set trusted proxies to
        // all given client IP addresses.
        if( $trustedProxies === '*' )
        {
            return $clientIpAddresses;
        }

        // If the user mistakenly passes
        // a value that is not an array, we
        // assume it's a string containing a
        // valid IPv4, IPv6, or CIDR address
        if( ! is_array($trustedProxies) )
        {
            return [$trustedProxies];
        }

        // Else it's an array as expected
        return $trustedProxies;
    }

    /**
     * Get trusted header names
     * @return array
     */
    protected function getTrustedHeaders()
    {
        $trustedHeaderNames = $this->config->get('trusted-proxy.headers');

        /*
         * In case the user does not pass an array of header names we
         * will default to an empty array. This will force defaults from
         * class \Symfony\Component\HttpFoundation\Request::$trustedHeaders
         */
        $trustedHeaderNames = (is_array($trustedHeaderNames)) ? $trustedHeaderNames : [];

        return $trustedHeaderNames;
    }

}