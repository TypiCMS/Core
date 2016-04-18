<!doctype html>
<html lang="{{ config('typicms.admin_locale') }}">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

    <title>[admin] @yield('title') – {{ config('typicms.'.config('typicms.admin_locale').'.website_title') }}</title>

    @yield('css')

    <link href="{{ app()->isLocal() ? asset('css/admin.css') : asset(elixir('css/admin.css')) }}" rel="stylesheet">

</head>

<body class="@if(auth()->user())has-navbar @endif @yield('bodyClass')">

@section('navbar')
    @if (auth()->user())
        @include('core::_navbar')
    @endif
@show

@section('otherSideLink')
    @include('core::admin._navbar-public-link')
@endsection

<div class="container-fluid">

    <div class="row row-offcanvas row-offcanvas-left">

        @section('sidebar')
            @include('core::admin._sidebar')
        @show

        <div class="@section('mainClass')col-xs-12 col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main @show">

            @section('errors')
                @if (!$errors->isEmpty())
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

        @include('core::admin._javascript')

        <script>
            var tablesConfig = {
                compileTemplates: true,
                highlightMatches: true,
                pagination: {
                    // dropdown:true
                    // chunk:5
                },
                texts: {
                    count: "@lang('global.table.count Records')",
                    filter: "@lang('global.table.Filter Results:')",
                    filterPlaceholder: "@lang('global.table.Search query')",
                    limit: "@lang('global.table.Records:')",
                    page: "@lang('global.table.Page:')",
                    noResults: "@lang('global.table.No matching records')",
                    filterBy: "@lang('global.table.Filter by column')"
                },
                skin:'table-condensed'
            };
            options.templates = {
                id: '<a href="javascript:void(0);" @click="$parent.deleteMe({id})"><span class="fa fa-remove"></span></a>&nbsp;&nbsp;&nbsp;<a class="btn btn-default btn-xs" href="{{ url()->current() }}/{id}/edit">Edit</i></a>',
                status: '<div @click="$parent.toggle({id})">' +
                    '<span class="fa switch" :class="{status} ? \'fa-toggle-on\' : \'fa-toggle-off\'"></span>' +
                '</div>',
                thumb: '<img src="{thumb}">'
            };
            options.perPage = 25;
            options.perPageValues = [25, 50, 100, 500, 1000, 5000];
            options.dateFormat = 'yyyy mm dd';
            options.headings = Object.assign({}, options.headings, {
                id: '',
                status: '@lang("validation.attributes.status")',
                thumb: '@lang("validation.attributes.image")',
                date: '@lang("validation.attributes.date")',
                title: '@lang("validation.attributes.title")',
            });

        </script>

        <script src="@if(app()->environment('production')){{ asset(elixir('js/admin/main.js')) }}@else{{ asset('js/admin/main.js') }}@endif"></script>

        @yield('js')

        <script type="text/javascript">
            {!! Notification::showError('alertify.error(\':message\');') !!}
            {!! Notification::showInfo('alertify.log(\':message\');') !!}
            {!! Notification::showSuccess('alertify.success(\':message\');') !!}
        </script>

    </div>

</div>

</body>

</html>
