<a href="{{ TypiCMS::homeUrl() }}">
    @if (TypiCMS::hasLogo())
        <img class="logo" src="{{ url('uploads/settings/'.config('typicms.image')) }}" width="" height="150" alt="{{ TypiCMS::title() }}">
    @else
        {{ TypiCMS::title() }}
    @endif
</a>
