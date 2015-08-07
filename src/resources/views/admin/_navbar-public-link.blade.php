@if (isset($model) and $model->id)
<a href="{{ url($model->uri(config('app.locale'))) }}">
@elseif (isset($module) and Route::has(config('app.locale') . '.' . $module))
<a href="{{ route(config('app.locale') . '.' . $module) }}">
@else
<a href="{{ url('/') }}">
@endif
{{ ucfirst(trans('global.view website', [], null, config('typicms.admin_locale'))) }}</a>
