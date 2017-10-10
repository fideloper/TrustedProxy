<?php

namespace Fideloper\Proxy\Http\Controllers;

#use Illuminate\Routing\Controller as BaseController;

class ProxyController /*extends BaseController*/
{
    /**
     * Show the Proxy debugger dashboard
     */
    public function index()
    {
        $laravel55Middleware = app()->getNamespace().'Http\Middleware\TrustProxies';

        if( class_exists($laravel55Middleware) )
        {
            $headers = app($laravel55Middleware)->getTrustedHeaderNames();
        } else {
            $headers = app(\Fideloper\Proxy\TrustProxies::class)->getTrustedHeaderNames();
        }

        $headers = collect($headers)->map(function($item, $key) {
            return str_replace('_', '-', strtolower($item));
        })->toArray();

        return view('proxy::dashboard', [
            'headers' => $headers,
            'request' => app(\Illuminate\Http\Request::class)
        ]);
    }
}