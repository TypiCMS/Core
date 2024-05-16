@if ($navbar)
    <nav class="typicms-navbar navbar navbar-expand bg-dark justify-content-between sticky-top" data-bs-theme="dark">
        <div class="container-fluid">
            @if (Request::segment(1) === 'admin')
                <button class="btn btn-dark d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasResponsive" aria-controls="offcanvasResponsive" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
            @endif

            <a class="typicms-navbar-brand navbar-brand" href="{{ route('admin::dashboard') }}">
                {{ websiteTitle(config('typicms.navbar_locale')) }}
            </a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    @if (Request::segment(1) === 'admin')
                        @include('core::admin._navbar-public-link')
                    @else
                        @include('core::public._navbar-admin-link')
                    @endif
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        <span class="bi bi-person-circle me-2"></span>
                        <span class="d-none d-lg-inline">
                            {{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-user">
                        <div class="dropdown-user-wrapper">
                            <div class="img">
                                <img src="https://www.gravatar.com/avatar/{{ md5(auth()->user()->email) }}?d=mm" class="pull-left"/>
                            </div>
                            <div class="info">
                                <div class="mt-1 mb-1">{{ auth()->user()->email }}</div>
                                @can('update users')
                                    <div class="mb-3">
                                        <a href="{{ route('admin::edit-user', Auth::id()) }}">
                                            {{ __('Profile', [], config('typicms.navbar_locale')) }}
                                        </a>
                                    </div>
                                @endcan

                                <div class="mb-2">
                                    <form action="{{ route(mainLocale() . '::logout') }}" method="post">
                                        {{ csrf_field() }}
                                        <button class="btn btn-light btn-sm" type="submit">
                                            @lang('Logout', [], config('typicms.navbar_locale'))
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @can('read settings')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin::index-settings') }}">
                            <span class="bi bi-gear-fill me-2"></span>
                            <span class="d-none d-lg-inline">
                                {{ __('Settings', [], config('typicms.navbar_locale')) }}
                            </span>
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
    </nav>
@endif
