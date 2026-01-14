<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Observers;

use TypiCMS\Modules\Core\Models\Page;

class HomePageObserver
{
    /**
     * If a new homepage is defined, cancel previous homepage.
     */
    public function saving(Page $page): void
    {
        if ((bool) $page->is_home) {
            $query = Page::query()->where('is_home', 1);
            if ($page->id) {
                $query->where('id', '!=', $page->id);
            }

            $query->update(['is_home' => 0]);
        }
    }

    /**
     * If there is no homepage, set the first page as homepage.
     */
    public function saved(Page $page): void
    {
        if (!$page->is_home && Page::query()->where('is_home', 1)->count() === 0) {
            Page::query()
                ->whereNull('parent_id')
                ->orderBy('position')
                ->take(1)
                ->update(['is_home' => 1]);
        }
    }
}
