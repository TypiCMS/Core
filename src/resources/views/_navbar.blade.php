@if($navbar)
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle navbar-toggle-left" data-toggle="offcanvas">
                    <span class="fa fa-bars fa-fw fa-inverse"></span>
                    <span class="sr-only">{{ __('Toggle navigation') }}</span>
                </button>
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
                    <span class="fa fa-chevron-down fa-fw fa-inverse"></span>
                    <span class="sr-only">{{ __('Toggle navigation') }}</span>
                </button>
                <a class="navbar-brand" href="{{ route('dashboard') }}">{{ str_limit(TypiCMS::title(), 50, '…') }}</a>
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
                        <a href="{{ route('admin::index-users') }}" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-user fa-fw"></span> {{ auth()->user()->first_name.' '.auth()->user()->last_name }} <b class="caret"></b></a>
                        <div class="dropdown-menu dropdown-user">
                            <div class="img pull-left">
                                <img src="https://www.gravatar.com/avatar/{{ md5(auth()->user()->email) }}?d=mm" class="pull-left">
                            </div>
                            <div class="info">
                                <p>{{ auth()->user()->email }}</p>
                                @can('update-user')
                                <p>
                                    <a href="{{ route('admin::edit-user', Auth::id()) }}">{{ __('Profile', [], config('typicms.admin_locale')) }}</a>
                                </p>
                                @endcan
                                <p>
                                    <a href="{{ route('logout') }}">{{ __('Log out', [], config('typicms.admin_locale')) }}</a>
                                </p>
                            </div>
                        </div>
                    </li>
                    @can('see-settings')
                        <li><a href="{{ route('admin::index-settings') }}"><span class="fa fa-cog fa-fw"></span> <span class="hidden-sm">{{ __('Settings', [], config('typicms.admin_locale')) }}</span></a></li>
                    @endcan
                </ul>
            </div>
        </div>
    </nav>
@endif
