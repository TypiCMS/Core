<!doctype html>
<html lang="{{ config('typicms.admin_locale') }}">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

    <title>[admin] @yield('title') – {{ config('typicms.' . config('typicms.admin_locale') . '.website_title') }}</title>

    @yield('css')

    <link href="{{ elixir('css/admin.css') }}" rel="stylesheet">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body class="@section('bodyClass')has-navbar @show">

@section('navbar')
    @if (Sentry::getUser())
        @include('core::_navbar')
    @endif
@show

@section('otherSideLink')
    @include('core::admin._navbar-public-link')
@stop

<div class="container-fluid">

    <div class="row row-offcanvas row-offcanvas-left">

        @section('sidebar')
            @include('core::admin._sidebar')
        @show

        <div class="@section('mainClass')col-xs-12 col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main @show">

            @section('errors')
                @if (! $errors->isEmpty())
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        @lang('core::global.The form contains errors').
                        <ul>
                            @foreach ($errors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @show

            @yield('main')

        </div>

        @include('core::admin._footer')

        <script type="text/javascript">
            {{ Notification::showError('alertify.error(\':message\');') }}
            {{ Notification::showInfo('alertify.log(\':message\');') }}
            {{ Notification::showSuccess('alertify.success(\':message\');') }}
        </script>

    </div>

</div>

</body>

</html>
