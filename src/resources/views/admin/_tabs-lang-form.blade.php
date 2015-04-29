@if (count($locales) > 1)
<div class="clearfix">
    <div class="btn-group pull-right" id="btn-group-form-locales">
        @foreach ($locales as $locale)
            <a class="btn btn-default btn-xs @if($locale == config('app.locale'))active @endif" href="?{{ http_build_query(Input::except('locale') + ['locale' => $locale]) }}" data-target="#{{ $target }}-{{ $locale }}" data-toggle="tab">@lang('global.languages.'.$locale)</a>
        @endforeach
    </div>
</div>
@endif
