# Laravel Trusted Proxies

[![Total Downloads](https://poser.pugx.org/fideloper/proxy/downloads.png)](https://packagist.org/packages/fideloper/proxy)

Allows correct session handling and logging by adding Trusted Proxies to Laravel.

Useful if you're web server sits behind a load balancer, reverse proxy or other intermediary.

## The Problem
If your site sits behind a load balancer, gateway cache or other "reverse proxy" type of setup, each web request has the potential to appear to always come from that proxy, rather than the users interfacing with your site.

This library allows you to take advantage of Symfony's knowledge of proxies. See below for more explanation on the topic of "trusted proxies".

## Installation

Installation is pretty easy:

1. Install the package
2. Add the Service Provider
3. Setup the configuration

### Install the package

This package lives inside of Packagist and is therefore easily installable via Composer

Method One:

    $ composer require fideloper/proxy:dev-master

Method Two:

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
    | IP address via the HTTP_X_FORWARDED_FOR header.
    |
    | To trust all proxies, use the value '*':
    |
    | 'proxies' => '*'
    |
    | To trust only certain proxies, set an array of those proxies'
    | IP addresses
    |
    | 'proxies' => array('192.168.1.1', '192.168.1.2')
    |
    */

    'proxies' => array(
        '10.1.28.234'
    ),

);
```
In the example above, we are pretending we have a load balancer or other proxy which lives at 10.1.28.234.

**Note:** If you use Rackspace or other PaaS "cloud" services which provide load balancers, the IP adddress of the load balancer may not be known. Rackspace uses many load balancers, and so you never know the IP address of the load balancer. This means every IP address would need to be trusted - any of them could be from the load balancer, and therefore the `X-Forwarded-For` header must always be trusted.

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
    | IP address via the HTTP_X_FORWARDED_FOR header.
    |
    | To trust all proxies, use the value '*':
    |
    | 'proxies' => '*'
    |
    | To trust only certain proxies, set an array of those proxies'
    | IP addresses
    |
    | 'proxies' => array('192.168.1.1', '192.168.1.2')
    |
    */

    'proxies' => '*',

);
```

This will tell Laravel to trust all IP addresses as a proxy.


## Some Explanation

If your site is behind a proxy, your web application may have issues where the site cannot distinguish between users:

1. Users may not have unique sessions - This can lead to possible access to incorrect account, or an inability to log in at all
2. Logging or other data-collection processes may appear to come from one location (the proxy itself) leaving you with no way to distinguish traffic/actions taken by individuals.

We can work around that by listening for the `X-Forwarded-For` header. This header is often added by proxies to let your web application know details about the originator of the request (the client's IP address).

Laravel uses Symfony for handling Requests and Responses. These classes have means to handle proxies, however Laravel doesn't have a configuration option for this out of the box.

That's not necessarily bad, but the need for it will arise if and when your Laravel app web server sits behind a load balancer or uses a reverse-proxy such as Varnish.

### Proxies in Laravel

In order for Laravel to check for the forwarded IP address, we need tell Laravel what IP addresses to "trust" as a proxy. If it finds the IP address received is a trusted IP, it will look for the forwarded IP address and set it as the client's true IP address.

If we do not tell Laravel what the IP address of our proxy (or proxies) is, it will ignore it for security reasons.