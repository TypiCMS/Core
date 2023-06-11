@if ($navbar)
    <nav class="typicms-navbar navbar navbar-expand bg-dark justify-content-between sticky-top" data-bs-theme="dark">
        <div class="container-fluid">
            @if (Request::segment(1) === 'admin')
                <button
                    class="btn btn-dark d-lg-none"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasResponsive"
                    aria-controls="offcanvasResponsive"
                    aria-label="{{ __('Toggle navigation') }}"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>
            @endif

            <a class="typicms-navbar-brand navbar-brand" href="{{ route('admin::dashboard') }}">
                {{ TypiCMS::title(config('typicms.navbar_locale')) }}
            </a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    @section('otherSideLink')
                    @if (Request::segment(1) === 'admin')
                        @include('core::admin._navbar-public-link')
                    @else
                        @include('core::public._navbar-admin-link')
                    @endif
                    @show
                </li>
                <li class="nav-item dropdown">
                    <a
                        class="nav-link dropdown-toggle"
                        data-bs-toggle="dropdown"
                        href="#"
                        role="button"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        <svg
                            class="bi bi-person-circle me-2"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            fill="currentColor"
                            viewBox="0 0 16 16"
                        >
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                            <path
                                fill-rule="evenodd"
                                d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"
                            />
                        </svg>
                        <span class="d-none d-lg-inline">
                            {{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-user">
                        <div class="dropdown-user-wrapper">
                            <div class="img">
                                <img
                                    src="https://www.gravatar.com/avatar/{{ md5(auth()->user()->email) }}?d=mm"
                                    class="pull-left"
                                />
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
                                    <form action="{{ route(TypiCMS::mainLocale() . '::logout') }}" method="post">
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
                            <svg
                                class="me-2"
                                width="1em"
                                height="1em"
                                viewBox="0 0 16 16"
                                fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 0 0-5.86 2.929 2.929 0 0 0 0 5.858z"
                                />
                            </svg>
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
