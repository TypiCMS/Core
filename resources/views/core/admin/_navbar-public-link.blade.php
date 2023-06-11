@php
    if (isset($model) and $model->id) {
        $url = TypiCMS::isLocaleEnabled($locale) ? url($model->uri($locale)) : url($model->uri());
    } elseif (($module = Request::segment(2)) and Route::has($locale . '::index-' . $module)) {
        $url = route($locale . '::index-' . $module);
    } else {
        $url = url('/');
    }
@endphp

<a class="nav-link" href="{{ $url }}">
    <svg
        class="bi bi-eye-fill me-2"
        xmlns="http://www.w3.org/2000/svg"
        width="1em"
        height="1em"
        fill="currentColor"
        viewBox="0 0 16 16"
    >
        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z" />
        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" />
    </svg>
    <span class="d-none d-lg-inline">{{ __('View website', [], config('typicms.navbar_locale')) }}</span>
</a>
