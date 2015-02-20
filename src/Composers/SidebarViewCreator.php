<?php
namespace TypiCMS\Composers;

use Illuminate\View\View;
use Maatwebsite\Sidebar\SidebarManager;

class SidebarViewCreator
{
    /**
     * @var SidebarManager
     */
    private $manager;

    /**
     * @param SidebarManager $manager
     */
    public function __construct(SidebarManager $manager)
    {
        $this->manager = $manager;
    }

    public function create(View $view)
    {
        $view->prefix = 'admin';
        $view->sidebar = $this->manager->build();
    }
}
