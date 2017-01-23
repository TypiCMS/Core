<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title')</title>
    <meta name="description" content="@yield('description')">
    <meta name="keywords" content="@yield('keywords')">

    <meta property="og:site_name" content="{{ $websiteTitle }}">
    <meta property="og:title" content="@yield('ogTitle')">
    <meta property="og:description" content="@yield('description')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ URL::full() }}">
    <meta property="og:image" content="@yield('image')">

    <link href="{{ app()->isLocal() ? asset('css/public.css') : asset(mix('css/public.css')) }}" rel="stylesheet">

    @include('core::public._feed-links')

    @yield('css')

    @if(app()->environment('production') and config('typicms.google_analytics_code'))

    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', '{{ config('typicms.google_analytics_code') }}', 'auto');
        ga('send', 'pageview');
    </script>

    @endif

</head>

<body class="body-{{ $lang }} @yield('bodyClass') @if($navbar)has-navbar @endif">

    @section('skip-links')
    <a href="#main" class="skip-to-content">@lang('db.Skip to content')</a>
    <a href="#site-nav" class="btn-offcanvas" data-toggle="offcanvas" title="@lang('db.Open navigation')" aria-label="@lang('db.Open navigation')" role="button" aria-controls="navigation" aria-expanded="false"><span class="fa fa-bars fa-fw" aria-hidden="true"></span></a>
    @show

    @include('core::_navbar')

    <div class="site-container" id="main" role="main">

        @section('site-header')
        <header class="site-header">
            @section('site-title')
            <div class="site-title">@include('core::public._site-title')</div>
            @show
        </header>
        @show

        <div class="sidebar-offcanvas">

            <button class="btn-offcanvas btn-offcanvas-close" data-toggle="offcanvas" title="@lang('db.Close navigation')" aria-label="@lang('db.Close navigation')"><span class="fa fa-close fa-fw" aria-hidden="true"></span></button>

            @section('lang-switcher')
                @include('core::public._lang-switcher')
            @show

            @section('site-nav')
            <nav class="site-nav" id="site-nav">
                {!! Menus::render('main') !!}
            </nav>
            @show

        </div>

        @yield('content')

        @section('site-footer')
        <footer class="site-footer">
            <nav class="social-nav">
                {!! Menus::render('social') !!}
            </nav>
            <nav class="footer-nav">
                {!! Menus::render('footer') !!}
            </nav>
        </footer>
        @show

    </div>

    <script src="{{ asset(app()->isLocal() ? 'js/public.js' : mix('js/public.js')) }}"></script>
    @if (Request::input('preview'))
    <script src="{{ asset('js/public/previewmode.js') }}"></script>
    @endif

    @yield('js')

</body>

</html>
