@if ($enabledLocales = TypiCMS::enabledLocales() and count($enabledLocales) > 1)
<nav class="lang-switcher dropdown">
    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        {{ $lang }}
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        @foreach ($enabledLocales as $locale)
        <li class="@if($locale == config('app.locale'))active @endif" role="menuitem">
            @if (isset($model) and isset($page))
                @if ($model->category and $model->translate('status', $locale))
                    <a href="{{ url($page->uri($locale).'/'.$model->category->translate('slug', $locale).'/'.$model->translate('slug', $locale)) }}">{{ $locale }}</a>
                @elseif ($model->translate('status', $locale))
                    <a href="{{ url($page->uri($locale).'/'.$model->translate('slug', $locale)) }}">{{ $locale }}</a>
                @else
                    <a href="{{ url($page->uri($locale)) }}">{{ $locale }}</a>
                @endif
            @elseif (isset($category))
            <a href="{{ url($page->uri($locale).'/'.$category->translate('slug', $locale)) }}">{{ $locale }}</a>
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
