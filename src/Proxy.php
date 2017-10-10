<?php

namespace Fideloper\Proxy;

use Closure;
use Illuminate\Support\Facades\Route;

class Proxy {

    /**
     * The callback that should be used to authenticate Horizon users.
     *
     * @var \Closure
     */
    public static $authUsing;

    /**
     * Determine if the given request can access the Proxy debugger.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function check($request)
    {
        return (static::$authUsing ?: function () {
            return app()->environment('local');
        })($request);
    }

    /**
     * Set the callback that should be used to authenticate Proxy users.
     *
     * @param  \Closure  $callback
     * @return static
     */
    public static function auth(Closure $callback)
    {
        static::$authUsing = $callback;
        $registerRoutes = static::check(
            app(\Illuminate\Http\Request::class)
        );
        if( $registerRoutes )
        {
            static::routes();
        }
        return new static;
    }

    public static function routes()
    {
        $router = app();
        $router->group([
            'prefix' => 'trusted-proxy',
            /*'middleware' => 'web',*/
        ], function () use ($router) {
                require __DIR__.'/../routes/web.php';
        });
    }
}