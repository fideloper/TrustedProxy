# Laravel Trusted Proxies

Allows correct session handling and logging by adding Trusted Proxies to Laravel.

Useful if you're web server sits behind a load balancer, reverse proxy or other intermediary.

## Installation

Installation is pretty easy:
1. Install the package
2. Add the Service Provider
3. Setup the configuration

### Install the package

This package lives inside of Packagist and is therefore easily installable via Composer

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
        ... other providers...
        Fideloper\Proxy\ProxyServiceProvider,
     );
```

### Setup the Configuration

This package expects the `proxy.proxies` configuration item to be set. You can do this by creating a proxy configuration file:

Create `app/config/proxy.php`:

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
        */

        'proxies' => array(
		'10.1.28.234',
	),

    );
```
In the example above, we are pretending we have a load balancer which lives at 10.1.28.234.

Note: If you use Rackspace or other PaaS "cloud" providers which provide load balancers, the IP adddress of the load balancer may not be known. Rackspace uses many load balancers, and so you never know what IP address the request will be coming from. This means every IP address would need to be trusted.

In that case, you can set the 'proxies' variable to '*':

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
        */

        'proxies' => '*',

    );
```

This will tell Laravel to trust all IP addresses as a proxy.


## Some Explanation

Laravel uses Symfony for handling Requests and Responses. These classes have means to handle proxies, however Laravel doesn't have a configuration option for this out of the box.

That's not necessarily bad, but the need for it will arise if and when your Laravel app web server sits behind a load balancer or uses a reverse-proxy such as Varnish.

> I'll refer to load balancers, reverse-proxies or similar as "intermediaries", as they sit between your clients and your web servers.

If you do have an intermediary between your clients and your web server(s), your web server will see each request as coming from the intermediary, rather than the client.

This means every request will come from the same location (same IP address), and potentially wreak havoc on session handling, logging, or anything that relies on having the client's IP address handy.

To combat this, common convention is for the intermediary to include a `HTTP_X_FORWARDED_FOR` header, with the client's IP address. If this header exists, the client IP address should be taken from that header, rather than the usual suspects (`REMOTE_ADDR` for instance).

### Proxies in Laravel

In order for Laravel to check for the forwarded IP address, we need tell Laravel what IP addresses to "trust" as a proxy. If it finds the IP address received is a trusted IP, it will look for the forwarded IP address and set it as the client's true IP address.

If we do not tell Laravel what the IP address of our proxy (or proxies) is, it will ignore it for security reasons.

> Note: If you use Rackspace or other PaaS "cloud" providers which provide load balancers, the IP adddress of the load balancer may not be known. For instance, rackspace uses many load balancers, and so you never know what IP address the request will be coming from. This means every IP address would need to be trusted.
> This is accomplished by setting 'proxies' to '*' as shown above.
