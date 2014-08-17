<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @if (isset($pageTitle))
    <title>{{ $pageTitle }}</title>
    @else
    <title>{{ Config::get('packages/l4mod/sentryuser/sentryuser.site-title') }}</title>
    @endif
    <!-- Bootstrap -->
    {{ HTML::style('packages/l4mod/sentryuser/bootstrap-ubuntu.min.css') }}
    {{ HTML::style('packages/l4mod/sentryuser/sentryuser-style.css') }}
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    @section('scripts')
    <script type="text/javascript" src="{{ asset('packages/l4mod/sentryuser/jquery-1.11.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('packages/l4mod/sentryuser/bootstrap.min.js') }}"></script>
    @show

    <script type="text/javascript">
        var base_url = "{{ URL::to('/') }}/";
    </script>
</head>
<body>
@if (!isset($menuSkip))
@if (Config::get('packages/l4mod/sentryuser/sentryuser.nav-tpl') == '')
@include('sentryuser::nav')
@else
@include(Config::get('packages/l4mod/sentryuser/sentryuser.nav-tpl'))
@endif
@endif
<div class="container">
    @if (Session::get('message'))
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-{{ Session::get('message-flag') }}">{{ Session::get('message') }}</div>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            @if (!isset($menuSkip))
            @include('sentryuser::secondary-menu')
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @yield('content')
        </div>
    </div>
</div>
</body>
</html>