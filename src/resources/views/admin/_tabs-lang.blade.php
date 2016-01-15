@if (count($locales) > 1)
<div class="alert alert-warning">
    <p>The partial <code>core::admin._tabs-lang</code> is deprecated, please remove its inclusion from every <code>_form.blade.php</code> views and from <code>_menulink-form.blade.php</code>.</p>
    <hr>
    <ul class="nav nav-tabs" id="locale-changer">
        @foreach ($locales as $lang)
        <li class="@if ($lang == $locale)active @endif">
            <a href="#{{ $lang }}" data-target="#{{ $lang }}" data-locale="{{ $lang }}" data-toggle="tab">@lang('global.languages.'.$lang)</a>
        </li>
        @endforeach
    </ul>
</div>
@endif
