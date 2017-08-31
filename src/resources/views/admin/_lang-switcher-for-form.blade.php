@if (count($locales) > 1)
    <div class="btn-group pull-right">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span id="active-locale">{{ $locale ? : __('All languages') }}</span> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            @foreach ($locales as $lang)
            <li><a class="btn-lang-js @if (!session('allLocalesInForm') && $lang == $locale)active @endif" href="#" data-locale="{{ $lang }}">@lang('languages.'.$lang)</a></li>
            @endforeach
            <li class="divider"></li>
            <li><a class="btn-lang-js @if (session('allLocalesInForm'))active @endif" href="#" data-locale="all">@lang('All languages')</a></li>
        </ul>
    </div>
@endif
