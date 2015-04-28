@if (isset($model) and $model->id)
<a href="{{ url($model->uri(config('app.locale'))) }}">
@elseif (isset($module) and $page = TypiCMS::getPageLinkedToModule($module))
<a href="{{ url($page->uri(config('app.locale'))) }}">
@else
<a href="/">
@endif
{{ ucfirst(trans('global.view website', [], null, config('typicms.admin_locale'))) }}</a>
