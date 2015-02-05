<script src="{{ asset('js/admin/components.min.js') }}"></script>

@if(Config::get('typicms.adminLocale') != 'en')
    <script src="{{ asset('js/angular-locales/angular-locale_' . Config::get('typicms.adminLocale') . '-' . Config::get('typicms.adminLocale') . '.js') }}"></script>
    <script src="{{ asset('js/pickadate-locales/' . Config::get('typicms.adminLocale') . '_' . strtoupper(Config::get('typicms.adminLocale')) . '.js') }}"></script>
@endif

@yield('js')
