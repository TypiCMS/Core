@foreach (TypiCMS::feeds() as $feed)
    <link rel="alternate" type="atom" href="{{ $feed['url'] }}" title="{{ $feed['title'] }}">
@endforeach
