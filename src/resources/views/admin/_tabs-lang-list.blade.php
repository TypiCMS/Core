@if (count($locales) > 1)
<div class="btn-toolbar">
    <div class="btn-group pull-right">
    @foreach ($locales as $lang)
        <a class="btn btn-default btn-xs @if($lang == $locale)active @endif" href="?{{ http_build_query(Request::except('locale') + ['locale' => $lang]) }}">@lang('global.languages.'.$lang)</a>
    @endforeach
    </div>
</div>
@endif
