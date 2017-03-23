<?php

namespace Fideloper\Proxy;

use Closure;
use Illuminate\Contracts\Config\Repository;

class TrustProxies
{
    /**
     * The config repository instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * The trusted proxies for the application.
     *
     * @var array
     */
    protected $proxies;

    /**
     * The proxy header mappings.
     *
     * @var array
     */
    protected $headers;

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
        $this->setTrustedProxyHeaderNames($request);
        $this->setTrustedProxyIpAddresses($request);
        return $next($request);
    }

    /**
     * Sets the trusted proxies on the request to the value of trustedproxy.proxies
     *
     * @param \Illuminate\Http\Request $request
     */
    protected function setTrustedProxyIpAddresses($request)
    {
        $trustedIps = $this->proxies ?: $this->config->get('trustedproxy.proxies');

        // We only trust specific IP addresses
        if(is_array($trustedIps)) {
            return $this->setTrustedProxyIpAddressesToSpecificIps($request, $trustedIps);
        }

        // We trust any IP address that calls us, but not proxies further
        // up the forwarding chain.
        if ($trustedIps === '*') {
            return $this->setTrustedProxyIpAddressesToTheCallingIp($request);
        }

        // We trust all proxies. Those that call us, and those that are
        // further up the calling chain (e.g., where the X-FORWARDED-FOR
        // header has multiple IP addresses listed);
        if ($trustedIps === '**') {
            return $this->setTrustedProxyIpAddressesToAllIps($request);
        }
    }

    private function setTrustedProxyIpAddressesToSpecificIps($request, $trustedIps)
    {
        $request->setTrustedProxies((array) $trustedIps);
    }

    private function setTrustedProxyIpAddressesToTheCallingIp($request) {
        $request->setTrustedProxies($request->getClientIps());
    }

    private function setTrustedProxyIpAddressesToAllIps($request)
    {
        // 0.0.0.0/0 is the CIDR for all ipv4 addresses
        // 2000:0:0:0:0:0:0:0/3 is the CIDR for all ipv6 addresses currently
        // allocated http://www.iana.org/assignments/ipv6-unicast-address-assignments/ipv6-unicast-address-assignments.xhtml
        $request->setTrustedProxies(['0.0.0.0/0', '2000:0:0:0:0:0:0:0/3']);
    }

    /**
     * Set the trusted header names based on teh content of trustedproxy.headers
     *
     * @param \Illuminate\Http\Request $request
     */
    protected function setTrustedProxyHeaderNames($request)
    {
        $trustedHeaderNames = $this->headers ?: $this->config->get('trustedproxy.headers');

        if(!is_array($trustedHeaderNames)) { return; } // Leave the defaults

        foreach ($trustedHeaderNames as $headerKey => $headerName) {
            $request->setTrustedHeaderName($headerKey, $headerName);
        }
    }
}
