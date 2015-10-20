@if (count($locales) > 1)
<div class="clearfix" id="locale-changer">
    <div class="btn-group pull-right" id="btn-group-form-locales">
    @foreach ($locales as $lang)
        <a class="btn btn-default btn-xs @if($lang == $locale)active @endif" href="?{{ http_build_query(Input::except('locale') + ['locale' => $lang]) }}" data-locale="{{ $lang }}" data-target="#{{ $target }}-{{ $lang }}" data-toggle="tab">@lang('global.languages.'.$lang)</a>
    @endforeach
    </div>
</div>
@endif
