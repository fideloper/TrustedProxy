# Laravel Trusted Proxies

[![Build Status](https://travis-ci.org/fideloper/TrustedProxy.svg?branch=master)](https://travis-ci.org/fideloper/TrustedProxy)

[![Total Downloads](https://poser.pugx.org/fideloper/proxy/downloads.png)](https://packagist.org/packages/fideloper/proxy)

## Laravel 5.6

Please use tag 4.0+ for Laravel 5.6:

```bash
composer require fideloper/proxy:~4.0
```

**TrustedProxy is still included with Laravel 5.6+.** To configure for this version of Laravel, please see: https://laravel.com/docs/5.6/requests#configuring-trusted-proxies

## Laravel 5.5

**TrustedProxy is now included with Laravel 5.5+.** To configure for this version of Laravel, please see: https://laravel.com/docs/5.5/requests#configuring-trusted-proxies

Please use tag 3.0+ for Laravel 5.5:

```bash
composer require fideloper/proxy:~3.0
```

## Laravel 5.0 - 5.4

For Laravel versions 5.0 - 5.4, please continue below.

New features include:

1. TrustedProxies are now set as in an HTTP Middleware, which makes more logical sense than the previous ServiceProvider. If you're unsure what that means, remember to "Just Trust Fideloper™".
2. You can now set the trusted header names. This is useful for proxies that don't use the usual `X-Forwarded-*` headers. See [issue #9](https://github.com/fideloper/TrustedProxy/issues/9) and [issue #7](https://github.com/fideloper/TrustedProxy/issues/7) for an example and discussion of that.

To use this with Laravel 5, run the following from your Laravel 5 project directory:

```bash
composer require fideloper/proxy
```

Or of course, you can edit your `composer.json` file directly:

```json
{
    "require": {
        "fideloper/proxy": "^3.3"
    }
}
```

## Laravel 4

You can still use this package for Version 4 of Laravel. See the latest v2 tag of this repository, which is compatible with version 4 of Laravel.

## WAT

Setting a trusted proxy allows for correct URL generation, redirecting, session handling and logging in Laravel when behind a proxy.

This is useful if your web servers sit behind a load balancer, HTTP cache, or other intermediary (reverse) proxy.

## TL;DR Setup:

Install Trusted Proxy:

```bash
$ composer require fideloper/proxy
```

Add the Service Provider (not necessary for Laravel 5.5+):

```php
'providers' => array(
    # other providers omitted
    'Fideloper\Proxy\TrustedProxyServiceProvider',
);
```

Publish the package config file to `config/trustedproxy.php`:

```bash
$ php artisan vendor:publish --provider="Fideloper\Proxy\TrustedProxyServiceProvider"
```

Register the HTTP Middleware in file `app/Http/Kernel.php`:

```php
    protected $middleware = [
        // Illuminate middlewares omitted for brevity

        'Fideloper\Proxy\TrustProxies',

```

Then edit the published configuration file `config/trustedproxy.php` as needed.

The below will trust a proxy, such as a load balancer or web cache, at IP address `192.168.10.10`:

```php
<?php

return [
    'proxies' => '192.168.10.10',

    // This default is already set in the config:
    'headers' => Illuminate\Http\Request::HEADER_X_FORWARDED_ALL,
];
```

Note: If you're using AWS Elastic Load Balancing or Heroku, the headers should be set to `Request::HEADER_X_FORWARDED_AWS_ELB` as `X-Forwarded-Host` is currently unsupported there.

## What's All This Do?

If your site sits behind a load balancer, gateway cache or other "reverse proxy", each web request has the potential to appear to always come from that proxy, rather than the client actually making requests on your site.

To fix that, this package allows you to take advantage of [Symfony's](https://github.com/symfony/symfony/blob/master/src/Symfony/Component/HttpFoundation/Request.php) knowledge of proxies. See below for more explanation on the topic of "trusted proxies".


## Slightly Longer Installation Instructions

Installation is typical of a Laravel 5 package:

1. Install the package
2. Add the Service Provider
3. Publish the configuration file
4. Add the Middleware
5. Configure your Trusted Proxies

### Install the Package

This package lives inside of Packagist and is therefore easily installable via Composer:

**Method One:**

    $ composer require fideloper/proxy

**Method Two:**

```json
{
    "require": {
        "fideloper/proxy": "^4.2"
    }
}
```
Once that's added, run `$ composer update` to download the files.

> If you want to develop on this, you'll need the dev dependencies, which you can get by adding the `--dev` flag to the `composer require` command.

### Add the Service Provider

The next step to installation is to add the Service Provider.

Edit `config/app.php` and add the provided Service Provider (not necessary for Laravel 5.5+):

```php
'providers' => array(
    # other providers omitted
    Fideloper\Proxy\TrustedProxyServiceProvider::class,
);
```

### Publish the configuration file

This package expects the `trustedproxy.php` configuration file be available at `/config/trustedproxy.php`. You can do this by copying the package configuration file via the new Laravel 5 `artisan` command:

```bash
$ php artisan vendor:publish --provider="Fideloper\Proxy\TrustedProxyServiceProvider"
```

Once that's finished, there will be a new configuration file to edit at `config/trustedproxy.php`.

### Register the middleware

Edit `app/Http/Kernel.php` and add the provided Middleware:

```php
    protected $middleware = [
        // Illuminate middlewares omitted for brevity

        'Fideloper\Proxy\TrustProxies',

```

### Configure Trusted Proxies

Edit the newly published `config/trustedproxy.php`:

```php
<?php

return [

    /*
     * Set trusted proxy IP addresses.
     *
     * Both IPv4 and IPv6 addresses are
     * supported, along with CIDR notation.
     *
     * The "*" character is syntactic sugar
     * within TrustedProxy to trust any proxy
     * that connects directly to your server,
     * a requirement when you cannot know the address
     * of your proxy (e.g. if using ELB or similar).
     *
     */
    'proxies' => [
        '192.168.1.10'
    ],

    /*
     * To trust one or more specific proxies that connect
     * directly to your server, use an array or a string separated by comma of IP addresses:
     */
    // 'proxies' => ['192.168.1.1'],
    // 'proxies' => '192.168.1.1, 192.168.1.2',

    /*
     * Or, to trust all proxies that connect
     * directly to your server, use a "*"
     */
    // 'proxies' => '*',

    /*
     * Which headers to use to detect proxy related data (For, Host, Proto, Port)
     *
     * Options include:
     *
     * - Illuminate\Http\Request::HEADER_X_FORWARDED_ALL (use all x-forwarded-* headers to establish trust)
     * - Illuminate\Http\Request::HEADER_FORWARDED (use the FORWARDED header to establish trust)
     * - Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB (If you are using AWS Elastic Load Balancer)
     *
     * - 'HEADER_X_FORWARDED_ALL' (use all x-forwarded-* headers to establish trust)
     * - 'HEADER_FORWARDED' (use the FORWARDED header to establish trust)
     * - 'HEADER_X_FORWARDED_AWS_ELB' (If you are using AWS Elastic Load Balancer)
     *
     * @link https://symfony.com/doc/current/deployment/proxies.html
     */
    'headers' => Illuminate\Http\Request::HEADER_X_FORWARDED_ALL,

];

```

In the example above, we are pretending we have a load balancer or other proxy which lives at `192.168.1.10`.

**Note:** If you use Rackspace, Amazon AWS or other PaaS "cloud" services which provide load balancers, the IP address of the load balancer *may not be known*. This means that every IP address would need to be trusted.

**In that case, you can set the 'proxies' variable to '*':**

```php
<?php

return [

     'proxies' => '*',

];
```

Using `*` will tell Laravel to trust all IP addresses as a proxy.

However, if you are in the situation where, say, you have a Content Distribution Network (like Amazon CloudFront) that passes to load balancer (like Amazon ELB)
then you may end up with a chain of unknown proxies forwarding from one to another. In that case, '*' above would only match
the final proxy (the load balancer in this case) which means that calling `$request->getClientIp()` would return the IP address 
of the next proxy in line (in this case one of the Content Distribution Network ips) rather than the original client IP.
To always get the original client IP, you need to trust all the proxies in the route to your request:

```php
<?php

return [
    
    'proxies' => [
        '*',
        '192.168.1.10',
    ],

];
```

In the example above, we are pretending we have a load balancer or other proxy behind a CDN proxy which lives at `192.168.1.10`.

### Configure Trusted Headers

By default all 'X-Forwarded-* headers' will be used to establish trust.

Some services don't support specific headers, so you need to untrust them. In particular, AWS ELB and Heroku don't support `FORWARDED` and `X_FORWARDED_HOST` so you should disable these headers in order to prevent users from spoofing trusted IPs.

```php
<?php

return [

    'headers' => Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB,

];
```

## Do you even CIDR, brah?

Symfony will accept [CIDR](http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing "this is confusing as shit") [notation](http://compnetworking.about.com/od/workingwithipaddresses/a/cidr_notation.htm "seriously, wtf bitwise math") for configuring trusted proxies as well. This means you can set trusted proxies to address ranges such as `192.168.12.0/23`.

Check that out [here](https://github.com/symfony/symfony/blob/3.0/src/Symfony/Component/HttpFoundation/Request.php) and [here](https://github.com/symfony/symfony/blob/3.0/src/Symfony/Component/HttpFoundation/IpUtils.php) to see how that is implemented in Symfony.

## Why Does This Matter?

If your site is behind a proxy such as a load balancer, your web application may have some of the following issues:

1. Redirects and PHP-generated URLs may be inaccurate in terms of its web address, protocol and/or port.
2. Unique sessions might not be created for each user, leading to possible access to incorrect accounts, or an inability for a user to log in at all
3. Logging or other data-collection processes data may appear to come from one location (the proxy itself) leaving you with no way to distinguish between traffic/actions taken by individual clients.

We can work around those issues by listening for the `X-Forwarded-*` headers. These headers are often added by proxies to let your web application know details about the originator of the request.

Common headers included are:

* **X-Forwarded-For** - The IP address of the client
* **X-Forwarded-Host** - The hostname used to access the site in the browser
* **X-Forwarded-Proto** - The schema/protocol (http/https) used by the client
* **X-Forwarded-Port** - The port used by the client (typically 80 or 443)

Laravel uses [Symfony](https://github.com/symfony/symfony/tree/master/src/Symfony/Component/HttpFoundation) for handling Requests and Responses. These classes have the means to handle proxies. However, for security reasons, they must be informed of which proxies to "trust" before they will attempt to read the `X-Forwarded-*` headers.

Laravel does not have a simple configuration option for "trusting" proxies out of the box. This package simply provides one.

### Proxies in Symfony and Laravel

In order for Laravel to check for the forwarded IP address, schema/protocol and port, we need tell Laravel the IP addresses of our proxies, so the application knows to "trust" them. If it finds the IP address received is a trusted IP, it will look for the `X-Forwarded-*` headers. Otherwise, it will ignore.

If we do not tell Laravel what the IP address of our proxy (or proxies) is, it will ignore it for security reasons.

## IP Addresses by Service

[This Wiki page](https://github.com/fideloper/TrustedProxy/wiki/IP-Addresses-of-Popular-Services) has a list of popular services and their IP addresses of their servers, if available. Any updates or suggestions are welcome!
