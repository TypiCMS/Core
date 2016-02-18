@if ($onlineLocales = TypiCMS::getOnlineLocales() and count($onlineLocales) > 1)
<nav class="lang-switcher dropdown">
    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        {{ $lang }}
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        @foreach ($onlineLocales as $locale)
        <li class="@if($locale == config('app.locale'))active @endif" role="menuitem">
            @if (isset($model) and isset($page) and $model->hasTranslation($locale))
                @if ($model->category and $model->translate($locale)->status)
                    <a href="{{ url($page->uri($locale).'/'.$model->category->translate($locale)->slug.'/'.$model->translate($locale)->slug) }}">{{ $locale }}</a>
                @elseif ($model->translate($locale)->status)
                    <a href="{{ url($page->uri($locale).'/'.$model->translate($locale)->slug) }}">{{ $locale }}</a>
                @else
                    <a href="{{ url($page->uri($locale)) }}">{{ $locale }}</a>
                @endif
            @elseif (isset($category))
            <a href="{{ url($page->uri($locale).'/'.$category->translate($locale)->slug) }}">{{ $locale }}</a>
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
