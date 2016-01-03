@if (isset($model))
<a href="{{ $model->editUrl() }}?locale={{ config('app.locale') }}">
@elseif (isset($page) and $page->module)
<a href="{{ route('admin.' . $page->module . '.index') }}?locale={{ config('app.locale') }}">
@elseif (isset($page))
<a href="{{ $page->editUrl() }}?locale={{ config('app.locale') }}">
@else
<a href="{{ route('dashboard') }}">
@endif
@lang('global.Admin side', [], config('typicms.admin_locale'))
</a>
