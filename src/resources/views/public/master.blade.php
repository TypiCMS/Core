<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>

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

    <link href="{{ App::environment('production') ? mix('css/public.css') : asset('css/public.css') }}" rel="stylesheet">

    @include('core::public._feed-links')

    @stack('css')

    @if (app()->environment('production') and config('typicms.google_analytics_code'))

    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
        ga('create', '{{ config('typicms.google_analytics_code') }}', 'auto');
        ga('send', 'pageview');
    </script>

    @endif

</head>

<body ontouchstart="" class="body-{{ $lang }} @yield('bodyClass') @if ($navbar)has-navbar @endif">

    @include('core::public._google_tag_manager_code')

    @section('skip-links')
    <a href="#main" class="skip-to-content">@lang('db.Skip to content')</a>
    <a href="#site-nav" class="d-block d-sm-none btn-offcanvas" data-toggle="offcanvas" title="@lang('db.Open navigation')" aria-label="@lang('db.Open navigation')" role="button" aria-controls="navigation" aria-expanded="false"><span class="fa fa-bars fa-fw" aria-hidden="true"></span></a>
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

            <button class="d-block d-sm-none btn-offcanvas btn-offcanvas-close" data-toggle="offcanvas" title="@lang('db.Close navigation')" aria-label="@lang('db.Close navigation')"><span class="fa fa-close fa-fw" aria-hidden="true"></span></button>

            @section('lang-switcher')
                @include('core::public._lang-switcher')
            @show

            @section('site-nav')
            <nav class="site-nav" id="site-nav">
                @menu('main')
            </nav>
            @show
            <nav class="site-nav" id="site-nav">
                @menu('main')
            </nav>

        </div>

        <main class="main" id="main" role="main">
            @yield('content')
        </main>

        @section('site-footer')
        <footer class="site-footer">
            <nav class="social-nav">
                @menu('social')
            </nav>
            <nav class="footer-nav">
                @menu('footer')
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
