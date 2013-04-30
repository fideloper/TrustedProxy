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

And that's it! 
