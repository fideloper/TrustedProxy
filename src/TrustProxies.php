<?php

namespace Fideloper\Proxy;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\Middleware;

class TrustProxies implements Middleware
{
    /**
     * The config repository instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * Create a new trusted proxies middleware instance.
     *
     * @param  \Illuminate\Contracts\Config\Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Set trusted header names
        foreach ($this->getTrustedHeaders() as $headerKey => $headerName) {
            $request->setTrustedHeaderName($headerKey, $headerName);
        }

        $request->setTrustedProxies($this->getTrustedProxies($request->getClientIps()));

        return $next($request);
    }

    /**
     * Return an array of trusted proxy IP addresses.
     *
     * @param array $clientIpAddresses Array of client IP addresses retrieved
     *                                 *prior* to setting trusted proxy
     *
     * @return array
     */
    protected function getTrustedProxies(array $clientIpAddresses = [])
    {
        $trustedProxies = $this->config->get('trustedproxy.proxies');

        // To trust all proxies, we set trusted proxies to all IP addresses.
        if ($trustedProxies === '*') {
            return $clientIpAddresses;
        }

        return (array) $trustedProxies;
    }

    /**
     * Get trusted header names.
     *
     * @return array
     */
    protected function getTrustedHeaders()
    {
        $trustedHeaderNames = $this->config->get('trustedproxy.headers');

        /*
         * In case the user does not pass an array of header names we
         * will default to an empty array. This will force defaults from
         * class \Symfony\Component\HttpFoundation\Request::$trustedHeaders
         */
        $trustedHeaderNames = is_array($trustedHeaderNames) ? $trustedHeaderNames : [];

        return $trustedHeaderNames;
    }
}
