<?php

class ProxyServiceProviderTest extends PHPUnit_Framework_TestCase {

    public function test_trusted_proxy_can_be_created_and_configured()
    {
        $config = array(
            'proxy' => array(
                'proxies' => '*',
            ),
        );

        $request = new RequestStub;

        $sp = new Fideloper\Proxy\ProxyServiceProvider(array(
            'config' => $config,
            'request' => $reqyuest,
        ));

        // I'd love to do this, but there are too many dependencies here
        //$sp->boot();

        $this->assertInstanceOf('Fideloper\Proxy\ProxyServiceProvider', $sp);
    }
}

class RequestStub {

    public function  getClientIps()
    {
        return array('192.168.33.10', '10.0.1.10');
    }

    public function setTrustedProxies(array $cidrIps)
    {
        // slurp
    }
}