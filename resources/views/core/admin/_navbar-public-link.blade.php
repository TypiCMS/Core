@php
    $contentLocale = config('typicms.content_locale');
    if (isset($model) and $model->id) {
        $url = isLocaleEnabled($contentLocale) ? url($model->uri($contentLocale)) : url($model->uri());
    } elseif (($module = Request::segment(2)) and Route::has($contentLocale . '::index-' . $module)) {
        $url = route($contentLocale . '::index-' . $module);
    } else {
        $url = url('/');
    }
@endphp

<a class="nav-link" href="{{ $url }}">
    <span class="bi bi-eye-fill me-2"></span>
    <span class="d-none d-lg-inline">{{ __('View website', [], config('typicms.navbar_locale')) }}</span>
</a>
