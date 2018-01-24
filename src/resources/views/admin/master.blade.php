<!doctype html>
<html lang="{{ config('typicms.admin_locale') }}">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>[admin] @yield('title') – {{ config('typicms.'.config('typicms.admin_locale').'.website_title') }}</title>

    @stack('css')

    <link href="{{ mix('/css/admin.css') }}" rel="stylesheet">

</head>

<body ontouchstart="" class="@can ('see-navbar')has-navbar @endcan @yield('bodyClass')">

@section('navbar')
    @include('core::_navbar')
@show

@section('otherSideLink')
    @include('core::admin._navbar-public-link')
@endsection

<div class="container-fluid">

    <div class="row row-offcanvas row-offcanvas-left">

        @section('sidebar')
            @include('core::admin._sidebar')
        @show

        <div ng-app="typicms" class="@section('mainClass')col-xs-12 col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main @show">

            @section('errors')
                @if (!$errors->isEmpty())
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{ __('The form contains errors:') }}
                        <ul>
                            @foreach ($errors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @show

            @yield('content')

        </div>

        @include('core::admin._javascript')

        <script src="{{ mix('/js/admin.js') }}"></script>

        @stack('js')

        <script type="text/javascript">
            @if (session('message'))
                alertify.success('{{ session('message') }}');
            @endif
        </script>

    </div>

</div>

</body>

</html>
