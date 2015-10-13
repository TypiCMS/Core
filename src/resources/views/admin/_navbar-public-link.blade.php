@if (isset($model) and $model->id)
<a href="{{ url($model->uri()) }}">
@elseif (isset($module) and Route::has(config('app.locale') . '.' . $module))
<a href="{{ route(config('app.locale') . '.' . $module) }}">
@else
<a href="{{ url('/') }}">
@endif
@lang('global.View website', [], config('typicms.admin_locale'))</a>
