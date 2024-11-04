<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title')</title>
    <meta name="description" content="@yield('description')" />
    <meta name="keywords" content="@yield('keywords')" />

    <meta property="og:site_name" content="{{ $websiteTitle }}" />
    <meta property="og:title" content="@yield('ogTitle')" />
    <meta property="og:description" content="@yield('description')" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ URL::full() }}" />
    <meta property="og:image" content="@yield('ogImage')" />
    <meta name="twitter:card" content="summary_large_image" />

    @vite('resources/scss/public.scss')

    @include('core::public._feed-links')

    @stack('css')
</head>

<body class="body-{{ $lang }} @yield('bodyClass') @if ($navbar) has-navbar @endif" id="top">

@section('skip-links')
    <div class="skip-to-content">
        <a href="#main" class="skip-to-content-link">@lang('Skip to content')</a>
    </div>
@show

@include('core::_navbar')

@auth
    @if (auth()->user()->isImpersonating())
        <a class="stop-impersonation-button" href="{{ route($lang . '::stop-impersonation') }}">
            @lang('Stop impersonation')
        </a>
    @endif
@endauth

<div class="site-container">
    @section('header')
        <header class="header" id="header">
            <div class="header-container">
                @section('header-title')
                    <div class="header-title">@include('core::public._header-title')</div>
                @show
                <div class="header-offcanvas" id="navigation">
                    <button class="hamburger" type="button" id="menu-button" data-bs-toggle="collapse" data-bs-target="#navigation-container" aria-expanded="false" aria-controls="navigation-container">
                        Menu
                    </button>
                    <div class="navigation collapse fade" id="navigation-container" data-bs-parent="#navigation">
                        <nav class="primary-nav">
                            @menu('primary')
                        </nav>
                        @include('search::public._form')
                        @section('lang-switcher')
                            @include('core::public._lang-switcher')
                        @show
                    </div>
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

    @section('footer')
        <footer class="footer">
            <div class="footer-container">
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

    <a href="#top" class="anchor-top disabled" id="anchor-top" aria-label="@lang('Back to top')">
        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-up" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z" />
        </svg>
    </a>
</div>

@vite('resources/js/public.js')

@can('see unpublished items')
    @if (request('preview'))
        <script src="{{ asset('js/previewmode.js') }}"></script>
    @endif
@endcan

@stack('js')
</body>

</html>
