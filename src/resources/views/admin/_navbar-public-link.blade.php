@if (isset($model) and $model->id)
<a href="{{ url($model->uri(config('app.locale'))) }}">@lang('global.view website')</a>
@elseif (isset($module) and $page = TypiCMS::getPageLinkedToModule($module))
<a href="{{ url($page->uri(config('app.locale'))) }}">@lang('global.view website')</a>
@else
<a href="/">@lang('global.view website')</a>
@endif
