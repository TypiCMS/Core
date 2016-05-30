<<<<<<< HEAD
@foreach(config('typicms.feeds', []) as $feed)
    @if (Route::has($lang.'.'.$feed['module'].'.feed'))
        {!! app('feed')->link(route($lang.'.'.$feed['module'].'.feed'), 'atom', trans($feed['module'].'::global.feed').' – '.$websiteTitle, $lang) !!}
    @endif
=======
@foreach(TypiCMS::feeds() as $feed)
    {!! app('feed')->link($feed['url'], 'atom', $feed['title'], $lang) !!}
>>>>>>> master
@endforeach
