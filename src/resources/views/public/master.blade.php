<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title')</title>
    <meta name="description" content="@yield('description')">

    <meta property="og:site_name" content="{{ $websiteTitle }}">
    <meta property="og:title" content="@yield('ogTitle')">
    <meta property="og:description" content="@yield('description')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ URL::full() }}">
    <meta property="og:image" content="@yield('image')">

    <link href="{{ elixir('css/public.css') }}" rel="stylesheet">

    @yield('css')

    @if(config('typicms.typekit_code'))
    <script type="text/javascript" src="//use.typekit.net/{{ config('typicms.typekit_code') }}.js"></script>
    <script type="text/javascript">try{Typekit.load();}catch(e){}</script>
    @endif

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body class="body-{{ $lang }} @yield('bodyClass') @if(Sentry::getUser() and Sentry::getUser()->hasAccess('admin') and ! Input::get('preview'))has-navbar @endif">

    <a href="#content" class="skip-to-content">@lang('db.Skip to content')</a>

@if(Sentry::getUser() and Sentry::getUser()->hasAccess('admin') and ! Input::get('preview'))
    @include('core::_navbar')
@endif

    <div class="container" id="content">

        @section('header')
        <header class="header-main">
            <h1>
                <a href="{{ TypiCMS::homepage() }}">
                    {!! TypiCMS::logoOrTitle() !!}
                </a>
            </h1>
        </header>
        @show

        @section('languagesMenu')
        <nav class="nav-languages" role="navigation">
            {!! TypiCMS::languagesMenu(array('class' => 'nav nav-pills pull-right')) !!}
        </nav>
        @show

        @section('mainMenu')
        <nav class="nav-main" role="navigation">
            {!! Menus::render('main') !!}
        </nav>
        @show

        @include('core::public._alert')

        @yield('main')

        @section('footer')
        <footer class="footer-main">
            <nav class="nav-social">
                {!! Menus::render('social') !!}
            </nav>
            <nav class="nav-footer" role="navigation">
                {!! Menus::render('footer') !!}
            </nav>
        </footer>
        @show

    </div>

    @if(App::environment('production') and config('typicms.google_analytics_code'))

    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', '{{ config('typicms.google_analytics_code') }}', 'auto');
        ga('send', 'pageview');
    </script>

    @endif

    <script src="{{ elixir('js/public/components.min.js') }}"></script>
    <script src="{{ elixir('js/public/master.js') }}"></script>
    @if (Input::get('preview'))
    <script src="{{ asset('js/public/previewmode.js') }}"></script>
    @endif

    @yield('js')

</body>

</html>
