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
    <span class="bi bi-eye-fill me-2"></span>
    <span class="d-none d-lg-inline">{{ __('View website', [], config('typicms.navbar_locale')) }}</span>
</a>
