@if ($enabledLocales = TypiCMS::enabledLocales() and count($enabledLocales) > 1)
<nav class="lang-switcher dropdown">
    <button class="lang-switcher-btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="dropdownLangSwitcher">
        {{ $lang }}
    </button>
    <div class="lang-switcher-list dropdown-menu" aria-labelledby="dropdownLangSwitcher">
        @foreach ($enabledLocales as $locale)
            @if ($locale !== $lang)
                @isset($page)
                    @if ($page->isPublished($locale))
                        <a class="lang-switcher-item dropdown-item" href="{{ isset($model) ? url($model->uri($locale)) : url($page->uri($locale)) }}">{{ $locale }}</a>
                    @else
                        <a class="lang-switcher-item dropdown-item" href="{{ url('/'.$locale) }}">{{ $locale }}</a>
                    @endif
                @else
                    <a class="lang-switcher-item dropdown-item" href="{{ url('/'.$locale) }}">{{ $locale }}</a>
                @endisset
            @endif
        @endforeach
    </div>
</nav>
@endif
