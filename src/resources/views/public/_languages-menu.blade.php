@if ($onlineLocales = TypiCMS::getOnlineLocales() and count($onlineLocales) > 1)
<nav class="nav-languages">
    <ul role="menu">
        @foreach ($onlineLocales as $locale)
        <li class="@if($locale == config('app.locale'))active @endif" role="menuitem">
            @if (isset($model) and isset($page) and $model->hasTranslation())
            <a href="{{ url($page->uri($locale) . '/' . $model->translate($locale)->slug) }}">{{ $locale }}</a>
            @elseif (isset($page))
            <a href="{{ url($page->uri($locale)) }}">{{ $locale }}</a>
            @else
            <a href="{{ url('/') }}">{{ $locale }}</a>
            @endif
        </li>
        @endforeach
    </ul>
</nav>
@endif
