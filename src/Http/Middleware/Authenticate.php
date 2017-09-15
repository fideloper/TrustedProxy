<?php
namespace Fideloper\Proxy\Http\Middleware;

use Fideloper\Proxy\Proxy;

class Authenticate
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|null
     */
    public function handle($request, $next)
    {
        return Proxy::check($request) ? $next($request) : abort(403);
    }
}