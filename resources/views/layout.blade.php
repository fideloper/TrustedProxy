<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>TrustedProxy</title>

    <!-- Styles -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <style type="text/css">
    @import url("https://fonts.googleapis.com/css?family=Raleway:300,400,600");
    body {
        font-family: Raleway,"Helvetica Neue",Helvetica,Arial,sans-serif;
        background-color: #f5f8fa;
    }
    .block {
        display: block;
    }
    .max-width {
        max-width: 350px;
    }
    .flex {
        display: flex;
        flex-direction: row;
    }
    .col {
        flex: 1 0 0;
    }
    .col {
        flex: 1 0 0;
    }
    .col-wide {
        flex: 2 0 0;
    }
    .odd {
        background-color: #f8f8f8;
    }
    .bold {
        font-weight: bold;
    }
    .tright {
        text-align: right;
    }
    .navbar-default {
        background-color: #fff;
        border-color: #d3e0e9;
    }
    .pad {
        padding: 10px;
    }
    .pad-wide {
        padding-left: 10px;
        padding-right: 10px;
    }
    .pad-wide-3x {
        padding-left: 30px;
        padding-right: 30px;
    }
    .tsm {
        font-size: 12px;
    }
    .border-top {
        border-top: 1px solid transparent;
    }
    .marg-top {
        margin-top: 20px;
    }
    .lh-2x {
        line-height: 20px;
    }
    code {
        word-break: break-all;
    }
    .panel-default>.panel-heading {
        color: #333;
        background-color: #fff;
        border-color: #d3e0e9;
    }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/trustedproxy') }}">
                        TrustedProxy Debugger
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                </div>
            </div>
        </nav>

        @yield('content')
    </div>

    <!-- Scripts -->
    <!--
        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    -->
</body>
</html>