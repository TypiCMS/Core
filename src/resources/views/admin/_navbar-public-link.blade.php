@if (isset($model) and $model->id)
<a href="{{ TypiCMS::isLocaleEnabled($locale) ? url($model->uri($locale)) : url($model->uri()) }}">
@elseif ($module = Request::segment(2) and Route::has($locale.'::index-'.$module))
<a href="{{ route($locale.'::index-'.$module) }}">
@else
<a href="{{ url('/') }}">
@endif
{{ __('View website', [], config('typicms.admin_locale')) }}
</a>
