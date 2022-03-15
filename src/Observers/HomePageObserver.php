<?php

namespace TypiCMS\Modules\Core\Observers;

use TypiCMS\Modules\Core\Models\Page;

class HomePageObserver
{
    /**
     * If a new homepage is defined, cancel previous homepage.
     */
    public function saving(Page $page)
    {
        if ((bool) $page->is_home) {
            $query = Page::where('is_home', 1);
            if ($page->id) {
                $query->where('id', '!=', $page->id);
            }
            $query->update(['is_home' => 0]);
        }
    }

    /**
     * If there is no homepage, set the first page as homepage.
     */
    public function saved(Page $page)
    {
        if ((bool) !$page->is_home) {
            if (Page::where('is_home', 1)->count() === 0) {
                Page::whereNull('parent_id')->orderBy('position')->take(1)->update(['is_home' => 1]);
            }
        }
    }
}
