<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Spatie\Feed\Feed;
use TypiCMS\Modules\Core\Facades\TypiCMS;

class FeedController
{
    public function __invoke(string $module)
    {
        abort_unless(config('typicms.modules.'.$module.'.has_feed', false), 404);

        $page = TypiCMS::getPageLinkedToModule($module);
        abort_if(is_null($page), 404);

        $model = app(ucfirst($module));
        $items = $model->published()
            ->order()
            ->take(10)
            ->get();

        $feed = [
            'title' => $page->title,
            'description' => $page->body,
            'language' => TypiCMS::localeAndRegion('-'),
            'image' => $page->present()->image(1200, 630),
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
