# Laravel Trusted Proxies

[![Build Status](https://github.com/fideloper/TrustedProxy/workflows/Tests/badge.svg)](https://github.com/fideloper/TrustedProxy/actions) [![Total Downloads](https://poser.pugx.org/fideloper/proxy/downloads.png)](https://packagist.org/packages/fideloper/proxy)

**Setting a trusted proxy allows for correct URL generation, redirecting, session handling and logging in Laravel when behind a reverse proxy such as a load balancer or cache.**

---

## Installation

**Laravel 5.5+ comes with this package**. If you are using Laravel 5.5 or greater, you **do not** need to add this to your project separately.

* [Laravel 5.5](https://laravel.com/docs/5.5/requests#configuring-trusted-proxies) Docs (`fideloper/proxy:~3.3`)
* [Laravel 5.6](https://laravel.com/docs/5.6/requests#configuring-trusted-proxies) Docs (`fideloper/proxy:^4.0`)
* [Laravel 5.7](https://laravel.com/docs/5.7/requests#configuring-trusted-proxies) Docs (`fideloper/proxy:^4.0`)
* [Laravel 5.8](https://laravel.com/docs/5.8/requests#configuring-trusted-proxies) Docs (`fideloper/proxy:^4.0`)
* [Laravel 6.x](https://laravel.com/docs/6.x/requests#configuring-trusted-proxies) Docs (`fideloper/proxy:^4.0`)
* [Laravel 7.x](https://laravel.com/docs/7.x/requests#configuring-trusted-proxies) Docs (`fideloper/proxy:^4.2`)
* [Laravel 8.x](https://laravel.com/docs/8.x/requests#configuring-trusted-proxies) Docs (`fideloper/proxy:^4.3`)
* [Laravel 9.x](https://laravel.com/docs/9.x/requests#configuring-trusted-proxies) Docs (**Don't use this package**. [Explanation here](https://github.com/fideloper/TrustedProxy/issues/152), [Upgrade docs here](https://laravel.com/docs/9.x/upgrade))

### Laravel 5.0 - 5.4

To install Trusted Proxy, use:

```
composer require fideloper/proxy:^3.3
```

### Laravel 4

```
composer require fideloper/proxy:^2.0
```

## Setup

Refer to the docs above for using Trusted Proxy in Laravel 5.5+. For Laravel 4.0 - 5.4, refer to [the wiki](https://github.com/fideloper/TrustedProxy/wiki).

## What Does This Do?

Setting a trusted proxy allows for correct URL generation, redirecting, session handling and logging in Laravel when behind a reverse proxy.

This is useful if your web servers sit behind a load balancer (Nginx, HAProxy, Envoy, ELB/ALB, etc), HTTP cache (CloudFlare, Squid, Varnish, etc), or other intermediary (reverse) proxy.

## How Does This Work?

Applications behind a reverse proxy typically read some HTTP headers such as `X-Forwarded`, `X-Forwarded-For`, `X-Forwarded-Proto` (and more) to know about the real end-client making an HTTP request.

> If those headers were not set, then the application code would think every incoming HTTP request would be from the proxy.

Laravel (technically the Symfony HTTP base classes) have a concept of a "trusted proxy", where those `X-Forwarded` headers will only be used if the source IP address of the request is known. In other words, it only trusts those headers if the proxy is trusted.

This package creates an easier interface to that option. You can set the IP addresses of the proxies (that the application would see, so it may be a private network IP address), and the Symfony HTTP classes will know to use the `X-Forwarded` headers if an HTTP requets containing those headers was from the trusted proxy.

## Why Does This Matter?

A very common load balancing approach is to send `https://` requests to a load balancer, but send `http://` requests to the application servers behind the load balancer.

For example, you may send a request in your browser to `https://example.org`. The load balancer, in turn, might send requests to an application server at `http://192.168.1.23`. 

What if that server returns a redirect, or generates an asset url? The users's browser would get back a redirect or HTML that includes `http://192.168.1.23` in it, which is clearly wrong.

What happens is that the application thinks its hostname is `192.168.1.23` and the schema is `http://`. It doesn't know that the end client used `https://example.org` for its web request.

So the application needs to know to read the `X-Forwarded` headers to get the correct request details (schema `https://`, host `example.org`).

Laravel/Symfony automatically reads those headers, but only if the trusted proxy configuration is set to "trust" the load balancer/reverse proxy.

> Note: Many of us use hosted load balancers/proxies such as AWS ELB/ALB, etc. We don't know the IP address of those reverse proxies, and so you need to trusted **all** proxies in that case. 
> 
> The trade-off there is running the security risk of allowing people to potentially spoof the `X-Forwarded` headers.

## IP Addresses by Service

[This Wiki page](https://github.com/fideloper/TrustedProxy/wiki/IP-Addresses-of-Popular-Services) has a list of popular services and their IP addresses of their servers, if available. Any updates or suggestions are welcome!
