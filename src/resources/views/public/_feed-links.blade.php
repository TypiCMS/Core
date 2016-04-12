@foreach(config('typicms.feeds', []) as $feed)
    {!! Feed::link(route($feed['route']), 'atom', trans($feed['module'].'::global.feed').' – '.$websiteTitle, $lang) !!}
@endforeach
