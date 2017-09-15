@extends('proxy::layout')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Proxy Debugger</div>
                <div class="panel-body">
                    <div class="debug-info">
                        <h3>General</h3>
                        <ul>
                            <li><strong>Client</strong>: {{ request()->getClientIp() }}</li>
                            <li><strong>Scheme</strong>: {{ request()->getScheme() }}</li>
                            <li><strong>Host</strong>: {{ request()->getHost() }}</li>
                            <li><strong>Home URL</strong>: {{ url('/foo-bar') }}</li>
                        </ul>
                    </div>

                    <div class="debug-info">
                        <h3>HTTP Headers</h3>
                        <ul>
                            @foreach(request()->headers as $header => $value)
                                @if($header == 'cookie')
                                    <li><strong>{{ $header }}</strong>: {{ substr(implode(', ', $value), 0, 75) . '...' }}</li>
                                @else
                                    <li><strong>{{ $header }}</strong>: {{ implode(', ', $value) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection