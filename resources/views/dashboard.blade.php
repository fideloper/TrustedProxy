@extends('proxy::layout')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Application</div>
                <div>
                    <div class="flex">
                        <div class="col bold tright pad pad-wide-3x">Client</div>
                        <div class="col pad"><code>{{ $request->getClientIp() }}</code></div>
                        <div class="col pad tsm">
                            <p>The IP address the app thinks is the client. If TrustedProxy is misconfigured, this will be the IP address of an intermediary server such as a load balancer, cache, or CDN.</p>
                            <p>If this shows your IP address (your home or office network public address), then this is correctly configured.</p>
                        </div>
                    </div>
                    <div class="flex odd">
                        <div class="col bold tright pad pad-wide-3x min-wide">Scheme</div>
                        <div class="col pad"><code>{{ $request->getScheme() }}</code></div>
                        <div class="col pad tsm">
                            <p>If your site is using an SSL certificate, this should say <code>https</code>.</p>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="col bold tright pad pad-wide-3x min-wide">Host</div>
                        <div class="col pad"><code>{{ $request->getHost() }}</code></div>
                        <div class="col pad tsm">
                            <p>The hostname the application thinks your application is running as.</p>
                            <p>If this does not match the hostname used in your browser, than your proxy is not passing the correct <code>Host</code> header along to the application server.</p>
                        </div>
                    </div>
                    <div class="flex odd">
                        <div class="col bold tright pad pad-wide-3x min-wide">Generated URL</div>
                        <div class="col pad"><code>{{ url('/foo-bar') }}</code></div>
                        <div class="col pad tsm">
                            <p>Test if Laravel helpers such as <code>action()</code> or <code>url()</code> are creating the correct URI.</p>
                        </div>
                    </div>
                </div>
                <div class="panel-heading marg-top">TrustedProxy Configuration</div>
                <div class="pad tsm odd pad-wide-3x">
                    <p>TrustedProxy is configured to listen for the following headers.</p>
                    <ul class="block max-width">
                        @foreach($headers as $header)
                        <li class="flex lh-2x">
                            <strong class="col">{{ $header }}</strong>
                            @if( array_key_exists($header, $request->headers->all()) ) <span class="col text-success">✔ found</span> @else <span class="col text-danger">𝗫 not found</span>  @endif</li>
                        @endforeach
                    </ul>
                    <p>Note that not every header needs to be found. For example, the <code>forwarded</code> header is not yet commonly used.<br />
                       The most important headers are typically the following, which identify the client IP address and scheme (<code>http</code> vs <code>https</code>).</p>
                    <ul>
                        <li><code>Request::HEADER_X_FORWARDED_FOR</code></li>
                        <li><code>Request::HEADER_X_FORWARDED_PROTO</code></li>
                    </ul>
                </div>
                <div class="panel-heading marg-top">HTTP Request Headers</div>
                <div>

                    @foreach($request->headers as $header => $value)
                    <div class="flex">
                        <div class="col bold tright pad pad-wide-3x min-wide">{{ $header }}</div>
                        @if($header == 'cookie')
                        <div class="col-wide pad"> @if(count($value)) <code>{{ substr(implode(', ', $value), 0, 75) . '...' }}</code> @endif </div>
                        @else
                        <div class="col-wide pad"> @if(count($value) && ! empty($value[0])) <code>{{ implode(', ', $value) }}</code> @endif </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection