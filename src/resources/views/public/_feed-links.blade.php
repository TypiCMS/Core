@foreach(config('typicms.feeds', []) as $feed)
<<<<<<< HEAD
    {!! app('feed')->link(route($lang.'.'.$feed['module'].'.feed'), 'atom', trans($feed['module'].'::global.feed').' – '.$websiteTitle, $lang) !!}
=======
    {!! Feed::link(route($lang.'.'.$feed['module'].'.feed'), 'atom', trans($feed['module'].'::global.feed').' – '.$websiteTitle, $lang) !!}
>>>>>>> 2.7
@endforeach
