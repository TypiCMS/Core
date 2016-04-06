    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle navbar-toggle-left" data-toggle="offcanvas">
                    <span class="fa fa-bars fa-fw fa-inverse"></span>
                    <span class="sr-only">@lang('global.Toggle navigation')</span>
                </button>
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
                    <span class="fa fa-chevron-down fa-fw fa-inverse"></span>
                    <span class="sr-only">@lang('global.Toggle navigation')</span>
                </button>
                <a class="navbar-brand" href="{{ route('dashboard') }}">{{ config('typicms.' . config('typicms.admin_locale') . '.website_title') }}</a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        @section('otherSideLink')
                            @if (Request::segment(1) == 'admin')
                                @include('core::admin._navbar-public-link')
                            @else
                                @include('core::public._navbar-admin-link')
                            @endif
                        @show
                    </li>
                    <li class="dropdown">
                        <a href="{{ route('admin.users.index') }}" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-user fa-fw"></span> {{ Auth::user()->first_name.' '.Auth::user()->last_name }} <b class="caret"></b></a>
                        <div class="dropdown-menu dropdown-user">
                            <div class="img pull-left">
                                <span class="fa fa-user"></span>
                                {{--<img src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}" class="pull-left">--}}
                            </div>
                            <div class="info">
                                <p>{{ Auth::user()->email }}</p>
                                <div class="user-actions">

                                @if (Auth::user()->hasAccess('users.edit'))
                                    <a class="btn btn-secondary" href="{{ route('admin.users.edit', Auth::user()->id) }}">@lang('users::global.Profile', [], config('typicms.admin_locale'))</a>
                                @endif
                                    <a class="btn btn-danger" href="{{ route('logout') }}">@lang('users::global.Log out', [], config('typicms.admin_locale'))</a>
                                </div>
                            </div>
                        </div>
                    </li>
                    @if (Auth::user()->hasAccess('settings.index'))
                        <li><a href="{{ route('admin.settings.index') }}"><span class="fa fa-cog fa-fw"></span> <span class="hidden-sm">@lang('global.Settings', [], config('typicms.admin_locale'))</span></a></li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
