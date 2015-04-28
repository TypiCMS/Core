@if (isset($model))
<a href="{{ route('admin.' . $model->getTable() . '.edit', $model->id) }}">@lang('global.admin side')</a>
@elseif (isset($page) and $page->module)
<a href="{{ route('admin.' . $page->module . '.index') }}">@lang('global.admin side')</a>
@elseif (isset($page))
<a href="{{ route('admin.pages.edit', $page->id) }}">@lang('global.admin side')</a>
@else
<a href="{{ route('dashboard') }}">@lang('global.admin side')</a>
@endif
