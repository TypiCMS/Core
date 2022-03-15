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
    <meta property="og:image" content="@yield('ogImage')">

    @if (config('typicms.twitter_site') !== null)
    <meta name="twitter:site" content="{{ config('typicms.twitter_site') }}">
    <meta name="twitter:card" content="summary_large_image">
    @endif

    @if (config('typicms.facebook_app_id') !== null)
    <meta property="fb:app_id" content="{{ config('typicms.facebook_app_id') }}">
    @endif

    <link href="{{ App::environment('production') ? mix('css/public.css') : asset('css/public.css') }}" rel="stylesheet">

    @include('core::public._feed-links')

    @stack('css')

</head>

<body class="body-{{ $lang }} @yield('bodyClass') @if ($navbar)has-navbar @endif" id="top">

    @section('skip-links')
    <div class="skip-to-content">
        <a href="#main" class="skip-to-content-link">@lang('Skip to content')</a>
    </div>
    @show

    @include('core::_navbar')

    <div class="site-container">

        @section('site-header')
        <header class="site-header" id="site-header">
            <div class="site-header-container">
                @section('site-title')
                <div class="site-title">@include('core::public._site-title')</div>
                @show
                <a href="#navigation" class="d-flex d-lg-none btn-offcanvas" data-toggle="offcanvas" title="@lang('Open navigation')" aria-label="@lang('Open navigation')" role="button" aria-controls="navigation" aria-expanded="false">
                    <svg width="2.5em" height="2.5em" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M2.5 11.5A.5.5 0 0 1 3 11h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 7h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 3h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                </a>
                <div class="site-header-offcanvas" id="navigation">
                    <button class="d-flex d-lg-none btn-offcanvas btn-offcanvas-close" type="button" data-toggle="offcanvas" title="@lang('Close navigation')" aria-label="@lang('Close navigation')" aria-controls="navigation" aria-expanded="false">
                        <svg width="2.5em" height="2.5em" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </button>
                    @section('primary-nav')
                    <nav class="primary-nav" id="primary-nav">
                        @menu('primary')
                    </nav>
                    @show
                    @include('search::public._form')
                    @section('lang-switcher')
                        @include('core::public._lang-switcher')
                    @show
                </div>
            </div>
        </header>
        @show

        @if (session('verified'))
            <div class="alert alert-success">@lang('Your email address has been verified.')</div>
        @endif

        <main class="main" id="main">
            @yield('content')
        </main>

        @section('site-footer')
        <footer class="site-footer">
            <div class="site-footer-container">
                <nav class="social-nav">
                    @menu('social')
                </nav>
                <nav class="footer-nav">
                    @menu('footer')
                </nav>
                <nav class="legal-nav">
                    @menu('legal')
                </nav>
            </div>
        </footer>
        @show

        <a href="#top" class="smooth-scroll anchor-top disabled" id="anchor-top" aria-label="@lang('Back to top')">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-up" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"/>
            </svg>
        </a>

    </div>

    <script src="{{ App::environment('production') ? mix('js/public.js') : asset('js/public.js') }}"></script>
    @can('see unpublished items')
    @if (request('preview'))
    <script src="{{ asset('js/previewmode.js') }}"></script>
    @endif
    @endcan

    @stack('js')

</body>

</html>
