@if (count(config('translatable.locales')) > 1)
<div class="btn-toolbar">
    <div class="btn-group pull-right">
    @foreach (config('translatable.locales') as $lang)
        <a class="btn btn-default btn-xs @if ($lang == config('translatable.locale', config('app.locale')))active @endif" href="?locale={{ $lang }}">@lang('global.languages.'.$lang)</a>
    @endforeach
    </div>
</div>
@endif
