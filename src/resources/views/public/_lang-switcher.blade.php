@if ($enabledLocales = TypiCMS::enabledLocales() and count($enabledLocales) > 1)
<nav class="lang-switcher dropdown">
    <button class="lang-switcher-btn btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        {{ $lang }}
        <span class="caret"></span>
    </button>
    <ul class="lang-switcher-list dropdown-menu" role="menu">
        @foreach ($enabledLocales as $locale)
        <li class="lang-switcher-item @if ($locale == config('app.locale'))active @endif" role="menuitem">
            @if (isset($model) and isset($page))
                @if ($model->category and $model->translate('status', $locale))
                    <a class="lang-switcher-link href="{{ url($page->uri($locale).'/'.$model->category->translate('slug', $locale).'/'.$model->translate('slug', $locale)) }}">{{ $locale }}</a>
                @elseif ($model->translate('status', $locale))
                    <a class="lang-switcher-link href="{{ url($page->uri($locale).'/'.$model->translate('slug', $locale)) }}">{{ $locale }}</a>
                @else
                    <a class="lang-switcher-link href="{{ url($page->uri($locale)) }}">{{ $locale }}</a>
                @endif
            @elseif (isset($category))
            <a class="lang-switcher-link href="{{ url($page->uri($locale).'/'.$category->translate('slug', $locale)) }}">{{ $locale }}</a>
            @elseif (isset($page))
            <a class="lang-switcher-link href="{{ url($page->uri($locale)) }}">{{ $locale }}</a>
            @else
            <a class="lang-switcher-link href="{{ url('/') }}">{{ $locale }}</a>
            @endif
        </li>
        @endforeach
    </ul>
</nav>
@endif
