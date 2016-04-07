@if (isset($model) and $model->id)
<a href="{{ TypiCMS::isLocaleOnline($locale) ? url($model->uri($locale)) : url($model->uri()) }}">
@elseif (isset($module) and Route::has($locale.'.'.$module))
<a href="{{ TypiCMS::isLocaleOnline($locale) ? route($locale.'.'.$module) : route($locale.'.'.$module) }}">
@else
<a href="{{ url('/') }}">
@endif
@lang('global.View website', [], config('typicms.admin_locale'))
</a>
