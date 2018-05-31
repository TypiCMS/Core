@if (count($locales) > 1)
    <div class="btn-group btn-group-sm ml-auto">
        <button class="btn btn-light dropdown-toggle" type="button" id="dropdownLangSwitcher" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span id="active-locale">@lang('languages.'.$locale)</span> <span class="caret"></span>
        </button>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownLangSwitcher">
            @foreach ($locales as $lang)
            <a class="dropdown-item @if ($lang == $locale)active @endif" href="?{{ http_build_query(Request::except('locale') + ['locale' => $lang]) }}" data-locale="{{ $lang }}">@lang('languages.'.$lang)</a>
            @endforeach
        </div>
    </div>
@endif
