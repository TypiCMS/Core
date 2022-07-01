<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-token" content="{{ auth()->user()->api_token ?? '' }}">

    <title>[admin] @yield('title') – {{ config('typicms.'.config('app.locale').'.website_title') }}</title>

    @stack('css')

    <link href="{{ App::environment('production') ? mix('/css/admin.css') : asset('/css/admin.css') }}" rel="stylesheet">

</head>

<body class="@can('see navbar')has-navbar @endcan @yield('bodyClass')">

@section('navbar')
    @include('core::_navbar')
@show

@section('otherSideLink')
    @include('core::admin._navbar-public-link')
@endsection

<div>

    <div class="row-offcanvas">

        @section('sidebar')
            @include('core::admin._sidebar')
        @show

        <div id="app" class="@section('mainClass')main @show">
            @yield('content')
        </div>

        @include('core::admin._javascript')

        <script src="{{ App::environment('production') ? mix('/js/admin.js') : asset('/js/admin.js') }}"></script>

        @stack('js')

        <script>
            alertify.logPosition('bottom right');
            @if (session('message'))
                alertify.success('{{ session('message') }}');
            @endif
            @if (session('success'))
                alertify.success('{{ session('success') }}');
            @endif
            @if (session('error'))
                alertify.error('{{ session('error') }}');
            @endif
        </script>

    </div>

</div>

</body>

</html>
