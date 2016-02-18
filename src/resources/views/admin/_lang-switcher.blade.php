@if (count($locales) > 1)
    <div class="btn-group pull-right">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span id="active-locale">@lang('global.languages.'.($locale?:'all'))</span> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            @foreach ($locales as $lang)
            <li><a class="@if(isset($js) and $js)btn-lang-js @endif @if($lang == $locale)active @endif" href="?{{ http_build_query(Request::except('locale') + ['locale' => $lang]) }}" data-locale="{{ $lang }}">@lang('global.languages.'.$lang)</a></li>
            @endforeach
            @if(isset($js))
            <li class="divider"></li>
            <li><a class="@if(isset($js) and $js)btn-lang-js @endif" href="#" data-locale="all">@lang('global.languages.all')</a></li>
            @endif
        </ul>
    </div>
@endif
