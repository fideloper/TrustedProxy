<?php

use Fideloper\Proxy\TrustProxies;

class ProxyServiceProviderTest extends PHPUnit_Framework_TestCase {

    public function test_pretty_much_nothing()
    {
        // Preferably I test a true Laravel/Symfony Request/Response object
        // to ensure that the proper headers are set and read from
        // Hopefully symfony can read in a request from a string or something fancy
        $this->assertTrue(true);
    }
}