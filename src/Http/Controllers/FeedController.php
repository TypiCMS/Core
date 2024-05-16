<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Spatie\Feed\Feed;

class FeedController
{
    public function __invoke(string $module)
    {
        abort_unless(config('typicms.modules.' . $module . '.has_feed', false), 404);

        $page = getPageLinkedToModule($module);
        abort_if(is_null($page), 404);

        $model = app(ucfirst($module));
        $items = $model->published()
            ->order()
            ->take(10)
            ->get();

        $feed = [
            'title' => websiteTitle() . ' – ' . $page->title,
            'description' => strip_tags($page->body),
            'language' => localeAndRegion('-'),
            'image' => $page->image !== null ? $page->present()->image(1200, 630) : null,
        ];

        return new Feed(
            $feed['title'],
            $items,
            request()->url(),
            'feed::atom',
            $feed['description'] ?? '',
            $feed['language'] ?? 'en-US',
            $feed['image'] ?? '',
            'atom',
        );
    }
}
