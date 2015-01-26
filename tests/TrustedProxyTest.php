<?php

use Fideloper\Proxy\TrustProxies;
use Illuminate\Http\Request;

class TrustedProxyTest extends PHPUnit_Framework_TestCase {

    /**
     * Test that Symfony does indeed NOT trust X-Forwarded-*
     * headers when not given trusted proxies
     *
     * This re-tests Symfony's Request class, but hopefully provides
     * some clarify to developers looking at the tests.
     *
     * Also, thanks for looking at the tests.
     */
    public function test_request_does_not_trust()
    {
        $req = $this->createProxiedRequest();

        $this->assertEquals('192.168.10.10', $req->getClientIp(), 'Assert untrusted proxy x-forwarded-for header not used');
        $this->assertEquals('http', $req->getScheme(), 'Assert untrusted proxy x-forwarded-proto header not used');
        $this->assertEquals('localhost', $req->getHost(), 'Assert untrusted proxy x-forwarded-host header not used');
        $this->assertEquals(8888, $req->getPort(), 'Assert untrusted proxy x-forwarded-port header not used');
    }

    /**
     * Test that Symfony DOES indeed trust X-Forwarded-*
     * headers when given trusted proxies
     *
     * Again, this re-tests Symfony's Request class
     */
    public function test_does_trust_trusted_proxy()
    {
        $req = $this->createProxiedRequest();
        $req->setTrustedProxies(['192.168.10.10']);

        $this->assertEquals('173.174.200.38', $req->getClientIp(), 'Assert trusted proxy x-forwarded-for header used');
        $this->assertEquals('https', $req->getScheme(), 'Assert trusted proxy x-forwarded-proto header used');
        $this->assertEquals('serversforhackers.com', $req->getHost(), 'Assert trusted proxy x-forwarded-host header used');
        $this->assertEquals(443, $req->getPort(), 'Assert trusted proxy x-forwarded-port header used');
    }

    /**
     * Test the most typical usage of TrustProxies:
     * Trusted X-Forwarded-For header
     */
    public function test_trusted_proxy_sets_trusted_proxies()
    {
        $trustedProxy = $this->createTrustedProxy([], ['192.168.10.10']);
        $request = $this->createProxiedRequest();

        $trustedProxy->handle($request, function($request)
        {
            $this->assertEquals('173.174.200.38', $request->getClientIp(), 'Assert trusted proxy x-forwarded-for header used');
        });
    }

    /**
     * Test renaming the X-Forwarded-For header
     */
    public function test_can_rename_forwarded_for_header()
    {
        $trustedProxy = $this->createTrustedProxy([
            \Illuminate\Http\Request::HEADER_CLIENT_IP => 'x-fidelopers-whacky-http-proxy',
        ], ['192.168.10.10']);

        $request = $this->createProxiedRequest(['HTTP_X_FIDELOPERS_WHACKY_HTTP_PROXY' => '173.174.200.38']);

        $trustedProxy->handle($request, function($request)
        {
            $this->assertEquals('173.174.200.38', $request->getClientIp(), 'Assert trusted proxy x-fidelopers-whacky-http-proxy header used');
        });
    }

    /**
     * Test renaming *all* the headers
     */
    public function test_can_rename_forwarded_proto_header()
    {
        $trustedProxy = $this->createTrustedProxy([
            \Illuminate\Http\Request::HEADER_CLIENT_IP    => 'x-fideloper-troll-for',
            \Illuminate\Http\Request::HEADER_CLIENT_HOST  => 'x-fideloper-troll-host',
            \Illuminate\Http\Request::HEADER_CLIENT_PROTO => 'x-fideloper-troll-proto',
            \Illuminate\Http\Request::HEADER_CLIENT_PORT  => 'x-fideloper-troll-port',
        ], ['192.168.10.10']);

        $request = $this->createProxiedRequest([
            'HTTP_X_FIDELOPER_TROLL_FOR' => '173.174.200.38',
            'HTTP_X_FIDELOPER_TROLL_HOST' => 'serversforhackers.com',
            'HTTP_X_FIDELOPER_TROLL_PORT' => '443',
            'HTTP_X_FIDELOPER_TROLL_PROTO' => 'https',
        ]);

        $trustedProxy->handle($request, function($request)
        {
            $this->assertEquals('173.174.200.38', $request->getClientIp(), 'Assert trusted proxy x-fideloper-troll-for header used');
            $this->assertEquals('https', $request->getScheme(), 'Assert trusted proxy x-fideloper-troll-proto header used');
            $this->assertEquals('serversforhackers.com', $request->getHost(), 'Assert trusted proxy x-fideloper-troll-host header used');
            $this->assertEquals(443, $request->getPort(), 'Assert trusted proxy x-fideloper-troll-port header used');
        });
    }

    /**
     * Fake an HTTP request by generating a Symfony Request object
     *
     * @param array $serverOverRides
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function createProxiedRequest($serverOverRides=[])
    {
        // Add some X-Forwarded headers and over-ride
        // defaults, simulating a request made over a proxy
        $serverOverRides = array_replace([
            'HTTP_X_FORWARDED_FOR' => '173.174.200.38',         // X-Forwarded-For   -- getClientIp()
            'HTTP_X_FORWARDED_HOST' => 'serversforhackers.com', // X-Forwarded-Host  -- getHosts()
            'HTTP_X_FORWARDED_PORT' => '443',                   // X-Forwarded-Port  -- getPort()
            'HTTP_X_FORWARDED_PROTO' => 'https',                // X-Forwarded-Proto -- getScheme() / isSecure()
            'SERVER_PORT' => 8888,
            'HTTP_HOST' => 'localhost',
            'REMOTE_ADDR' => '192.168.10.10',
        ], $serverOverRides);

        // Create a fake request made over "http", one that we'd get over a proxy
        // which is likely something like this:
        $request = Request::create('http://localhost:8888/tag/proxy','GET',[],[],[],$serverOverRides,null);

        return $request;
    }

    /**
     * Retrieve a TrustProxies object, with dependencies mocked
     *
     * @param $trustedHeaders
     * @param $trustedProxies
     * @return TrustProxies
     */
    protected function createTrustedProxy($trustedHeaders, $trustedProxies)
    {
        // Mock TrustProxies dependencies and calls for config values
        $app = Mockery::mock('Illuminate\Contracts\Foundation\Application');
        $config = Mockery::mock('Illuminate\Contracts\Config\Repository')
            ->shouldReceive('get')
            ->with('trusted-proxy.headers')
            ->andReturn($trustedHeaders)
            ->shouldReceive('get')
            ->with('trusted-proxy.proxies')
            ->andReturn($trustedProxies)
            ->getMock();

        return new TrustProxies($app, $config);
    }

}