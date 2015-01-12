<?php

use Fideloper\Proxy\ProxyServiceProvider;
use Illuminate\Config\Repository;

class ProxyServiceProviderTest extends PHPUnit_Framework_TestCase {

    public function test_trusted_proxy_can_be_created_and_configured()
    {
        $app = new AppStub();
        $app->config = new Repository();
        $app->request = new RequestStub();

        $provider = new ProxyServiceProvider($app);

        $this->assertInstanceOf('Fideloper\Proxy\ProxyServiceProvider', $provider);

        $provider->register();

        $this->assertSame('*', $app->config->get('proxy.proxies'));

        $provider->boot();
    }
}

class RequestStub {

    public function getClientIps()
    {
        return ['192.168.33.10', '10.0.1.10'];
    }

    public function setTrustedProxies(array $cidrIps)
    {
        // slurp
    }
}

class AppStub
{
    public $config;
    public $request;
}
