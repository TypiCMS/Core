<script src="{{ asset(elixir('js/admin/components.min.js')) }}"></script>

@if(config('typicms.admin_locale') != 'en')
    <script src="{{ asset('js/angular-locales/angular-locale_' . config('typicms.admin_locale') . '-' . config('typicms.admin_locale') . '.js') }}"></script>
@endif

@yield('js')
