@if (isset($model))
<a href="{{ route('admin.' . $model->getTable() . '.edit', $model->id) }}">
@elseif (isset($page) and $page->module)
<a href="{{ route('admin.' . $page->module . '.index') }}">
@elseif (isset($page))
<a href="{{ route('admin.pages.edit', $page->id) }}">
@else
<a href="{{ route('dashboard') }}">
@endif
{{ ucfirst(trans('global.admin side', [], null, config('typicms.admin_locale'))) }}</a>
