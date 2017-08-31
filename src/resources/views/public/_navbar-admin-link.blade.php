@if (isset($model))
<a href="{{ $model->editUrl() }}?locale={{ config('app.locale') }}">
@elseif (isset($page) and $page->module)
<a href="{{ route('admin::index-'.$page->module) }}?locale={{ config('app.locale') }}">
@elseif (isset($page))
<a href="{{ $page->editUrl() }}?locale={{ config('app.locale') }}">
@else
<a href="{{ route('dashboard') }}">
@endif
{{ __('Back-office', [], config('typicms.admin_locale')) }}
</a>
