<?php

use Illuminate\Support\Facades\Route;

Route::get('/', '\Fideloper\Proxy\Http\Controllers\ProxyController@index');
