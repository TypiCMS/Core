<script src="{{ asset('js/admin/components.min.js') }}"></script>

@if(Config::get('typicms.admin_locale') != 'en')
    <script src="{{ asset('js/angular-locales/angular-locale_' . Config::get('typicms.admin_locale') . '-' . Config::get('typicms.admin_locale') . '.js') }}"></script>
    <script src="{{ asset('js/pickadate-locales/' . Config::get('typicms.admin_locale') . '_' . strtoupper(Config::get('typicms.admin_locale')) . '.js') }}"></script>
@endif

@yield('js')
