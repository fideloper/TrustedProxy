# Laravel Trusted Proxies

[![Total Downloads](https://poser.pugx.org/fideloper/proxy/downloads.png)](https://packagist.org/packages/fideloper/proxy)

Allows correct URL generation, redirecting, session handling and logging to Laravel when behind a proxy.

Useful if your web servers sit behind a load balancer, http cache, reverse proxy or other intermediary.

## TL;DR Setup:

    # Install Trusted Proxy:
    $ composer require fideloper/proxy:dev-master

    # Add the Service Provider:
    'providers' => array(
        ... other providers ...
        Fideloper\Proxy\ProxyServiceProvider,
    );

    # Publish the config file:
    $ php artisan config:publish fideloper/proxy

    # Edit the config file:
    <?php
    return array(
        'proxies' => array( '10.1.28.234' )
    );

## What's This Do?
If your site sits behind a load balancer, gateway cache or other "reverse proxy", each web request has the potential to appear to always come from that proxy, rather than the client actually making requests on your site.

To fix that, this package allows you to take advantage of [Symfony's](https://github.com/symfony/symfony/blob/master/src/Symfony/Component/HttpFoundation/Request.php#L524) knowledge of proxies. See below for more explanation on the topic of "trusted proxies".

## Installation

Installation is simple:

1. Install the package
2. Add the Service Provider
3. Configure your Trusted Proxies

### Install the package

This package lives inside of Packagist and is therefore easily installable via Composer

**Method One:**

    $ composer require fideloper/proxy:dev-master

**Method Two:**

```json
{
    "require": {
        "fideloper/proxy": "dev-master"
    }
}
```
Once that's added, run `$ composer update` to download the files.

### Add the Service Provider

The next step to installation is to add the Service Provider.

Edit `app/config/app.php` and add the provided Service Provider:

```php
'providers' => array(
    ... other providers ...
    Fideloper\Proxy\ProxyServiceProvider,
);
```

### Setup the Configuration

This package expects the `proxies` configuration item to be set. You can do this by creating a proxy configuration file via `artisan`:

    $ php artisan config:publish fideloper/proxy

Once that's finished, there will be a new configuration file to edit at `app/config/packages/fideloper/proxy/config.php`:

```php
<?php
return array(

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Set an array of trusted proxies, so Laravel knows to grab the client's
    | information via the X-Forwarded-* headers.
    |
    | To trust all proxies, use the value '*':
    |
    | 'proxies' => '*'
    |
    |
    | To trust only specific proxies (recommended), set an array of those
    | proxies' IP addresses:
    |
    | 'proxies' => array('192.168.1.1', '192.168.1.2')
    |
    |
    | Or use CIDR notation:
    |
    | 'proxies' => array('192.168.12.0/23')
    |
    */

    'proxies' => array(
        '10.1.28.234'
    ),

);
```

In the example above, we are pretending we have a load balancer or other proxy which lives at `10.1.28.234`.

**Note:** If you use Rackspace, Amazon AWS or other PaaS "cloud" services which provide load balancers, the IP adddress of the load balancer *may not be known*. This means that every IP address would need to be trusted.

**In that case, you can set the 'proxies' variable to '*':**

```php
<?php
return array(

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Set an array of trusted proxies, so Laravel knows to grab the client's
    | information via the X-Forwarded-* headers.
    |
    | To trust all proxies, use the value '*':
    |
    | 'proxies' => '*'
    |
    |
    | To trust only specific proxies (recommended), set an array of those
    | proxies' IP addresses:
    |
    | 'proxies' => array('192.168.1.1', '192.168.1.2')
    |
    |
    | Or use CIDR notation:
    |
    | 'proxies' => array('192.168.12.0/23')
    |
    */

    'proxies' => '*',

);
```

Using `*` will tell Laravel to trust all IP addresses as a proxy.

### CIDR Notation

Symfony will accept [CIDR](http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing "this is confusing as shit") [notation](http://compnetworking.about.com/od/workingwithipaddresses/a/cidr_notation.htm "seriously, wtf bitwise math") for configuring trusted proxies as well. This means you can set trusted proxies to address ranges such as `192.168.12.0/23`.

Check that out [here](https://github.com/symfony/symfony/blob/2.4/src/Symfony/Component/HttpFoundation/Request.php#L787) and [here](https://github.com/symfony/symfony/blob/2.4/src/Symfony/Component/HttpFoundation/IpUtils.php#L56) to see how that is implemented in Symfony.

## Why Does This Matter?

If your site is behind a proxy such as a load balancer, your web application may have some of the following issues:

1. Redirects and PHP-generated URLs may be inaccurate in terms of web address, protocol and/or port.
2. Unique sessions might not be created for each user, leading to possible access to incorrect accounts, or an inability for a user to log in at all
3. Logging or other data-collection processes data may appear to come from one location (the proxy itself) leaving you with no way to distinguish between traffic/actions taken by individual clients.

We can work around those issues by listening for the `X-Forwarded-*` headers. These headers are often added by proxies to let your web application know details about the originator of the request.

Common headers included are:

* **X-Forwarded-For** - The IP address of the client
* **X-Forwarded-Proto** - The schema/protocol (http/https) used by the client
* **X-Forwarded-Port** - The port used by the client (typically 80 or 443)

Laravel uses [Symfony](https://github.com/symfony/symfony/tree/master/src/Symfony/Component/HttpFoundation) for handling Requests and Responses. These classes have the means to handle proxies. However, for security reasons, they must be informed of which proxies to "trust" before they will attempt to read the `X-Forwarded-*` headers.

Laravel does not have a simple configuration option for "trusting" proxies out of the box. This package simply provides one.

### Proxies in Symfony and Laravel

In order for Laravel to check for the forwarded IP address, schema/protocol  and port, we need tell Laravel the IP addresses of our proxies, so the application knows to "trust" them. If it finds the IP address received is a trusted IP, it will look for the `X-Forwarded-*` headers. Otherwise, it will ignore.

If we do not tell Laravel what the IP address of our proxy (or proxies) is, it will ignore it for security reasons.
