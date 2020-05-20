<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>

    @include('core::public._google_analytics_code')

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <meta name="description" content="@yield('description')">
    <meta name="keywords" content="@yield('keywords')">

    <meta property="og:site_name" content="{{ $websiteTitle }}">
    <meta property="og:title" content="@yield('ogTitle')">
    <meta property="og:description" content="@yield('description')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ URL::full() }}">
    <meta property="og:image" content="@yield('image')">

    {{-- <meta name="twitter:site" content=""> --}}
    <meta name="twitter:card" content="summary_large_image">

    <link href="{{ App::environment('production') ? mix('css/public.css') : asset('css/public.css') }}" rel="stylesheet">

    @include('core::public._feed-links')

    @stack('css')

</head>

<body ontouchstart="" class="body-{{ $lang }} @yield('bodyClass') @if ($navbar)has-navbar @endif">

    @include('core::public._google_tag_manager_code')

    @section('skip-links')
    <a href="#main" class="skip-to-content">@lang('db.Skip to content')</a>
    <a href="#site-nav" class="d-block d-md-none btn-offcanvas" data-toggle="offcanvas" title="@lang('db.Open navigation')" aria-label="@lang('db.Open navigation')" role="button" aria-controls="navigation" aria-expanded="false"><span class="fa fa-bars fa-fw" aria-hidden="true"></span></a>
    @show

    @include('core::_navbar')

    <div class="site-container">

        @section('site-header')
        <header class="site-header">
            @section('site-title')
            <div class="site-title">@include('core::public._site-title')</div>
            @show
            <p class="site-baseline">{{ TypiCMS::baseline() }}</p>
        </header>
        @show

        <div class="sidebar-offcanvas">

            <button class="d-block d-md-none btn-offcanvas btn-offcanvas-close" data-toggle="offcanvas" title="@lang('db.Close navigation')" aria-label="@lang('db.Close navigation')"><span class="fa fa-close fa-fw" aria-hidden="true"></span></button>

            @section('lang-switcher')
                @include('core::public._lang-switcher')
            @show

            @section('site-nav')
            <nav class="site-nav" id="site-nav">
                @menu('main')
            </nav>
            @show

        </div>

        @if (session('verified'))
            <div class="alert alert-success">@lang('Your email address has been verified.')</div>
        @endif

        <main class="main" id="main">
            @yield('content')
        </main>

        @section('site-footer')
        <footer class="site-footer">
            <nav class="site-footer-nav">
                @menu('social')
            </nav>
            <nav class="site-footer-nav">
                @menu('footer')
            </nav>
            <nav class="site-footer-nav">
                @menu('legal')
            </nav>
        </footer>
        @show

    </div>

    <script src="{{ App::environment('production') ? mix('js/public.js') : asset('js/public.js') }}"></script>
    @if (request('preview'))
    <script src="{{ asset('js/previewmode.js') }}"></script>
    @endif

    @stack('js')

</body>

</html>
