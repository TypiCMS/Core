@php
    if (isset($model)) {
        $url = $model->editUrl();
    } elseif (isset($page) and $page->module and Route::has('admin::index-' . $page->module)) {
        $url = route('admin::index-' . $page->module);
    } elseif (isset($page)) {
        $url = $page->editUrl();
    } else {
        $url = route('admin::dashboard');
    }
@endphp

<a class="nav-link" href="{{ $url }}?locale={{ config('app.locale') }}">
    {{ __('Back-office', [], config('typicms.navbar_locale')) }}
</a>
