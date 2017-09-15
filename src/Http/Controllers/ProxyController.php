<?php

namespace Fideloper\Proxy\Http\Controllers;

use Fideloper\Proxy\Http\Middleware\Authenticate;
use Illuminate\Routing\Controller as BaseController;

class ProxyController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(Authenticate::class);
    }

    /**
     * Show the Proxy debugger dashboard
     */
    public function index()
    {
        return view('proxy::dashboard');
    }
}