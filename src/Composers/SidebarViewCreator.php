<?php

namespace TypiCMS\Modules\Core\Composers;

use Illuminate\View\View;
use Maatwebsite\Sidebar\SidebarManager;

readonly class SidebarViewCreator
{
    public function __construct(private SidebarManager $manager) {}

    public function create(View $view): void
    {
        $view->with('sidebar', $this->manager->build());
    }
}
