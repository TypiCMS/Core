<a href="{{ TypiCMS::homeUrl() }}">
    @if (TypiCMS::hasLogo())
        <img class="logo" src="{{ url('uploads/settings/'.config('typicms.image')) }}" alt="{{ TypiCMS::title() }}" height="150">
    @else
        {{ TypiCMS::title() }}
    @endif
</a>
